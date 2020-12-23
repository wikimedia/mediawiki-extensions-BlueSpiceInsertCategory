Ext.define( 'BS.InsertCategory.panel.CategoryEditor', {
	extend: 'Ext.panel.Panel',
	requires: [ 'BS.form.field.CategoryTag' ],
	cls: 'bs-insertcategory-category-editor',
	title: mw.message(
		'bs-insertcategory-category-editor-title'
	).plain(),

	pageId: -1,
	allCategories: [],
	userCanEdit: false,
	parentFlyout: null,

	initComponent: function() {
		if( this.userCanEdit ) {
			/* Ugly hack: We want to have a little link directly behind the panel title text. The
			* title uses `flex:1` and it is hard to override this behavior. Therefore we inject our
			* own little link
			*/
			var me = this;
			var currentTitle = this.getTitle();
			var editToolLink = '<a class="tool-link edit" title="{0}" href="#">{1}</a>'.format(
				mw.message( 'bs-insertcategory-category-editor-explicit-categories-edit-tooltip' ).plain(),
				mw.message( 'bs-insertcategory-category-editor-explicit-categories-edit-label' ).plain()
			);
			$(document).on(
				'click',
				'.bs-insertcategory-category-editor .tool-link.edit',
				function ( e ) {
					me.switchToEditor();
					e.defaultPrevented = true;
					return false;
			} );

			this.setTitle( currentTitle + editToolLink );
		}

		this.btnSave = new Ext.button.Button( {
			text: mw.message( 'bs-extjs-save' ).plain(),
			disabled: true,
			cls: 'editor-button'
		} );
		this.btnSave.on( 'click', this.btnSaveClick, this );

		this.btnCancel = new Ext.button.Button( {
			text: mw.message( 'bs-extjs-cancel' ).plain(),
			cls: 'editor-button'
		} );
		this.btnCancel.on( 'click', this.btnCancelClick, this );

		this.btnClearAll = new Ext.button.Button( {
			text: mw.message( 'bs-insertcategory-category-editor-button-label-clear-all' ).plain(),
			cls: 'editor-button'
		} );
		this.btnClearAll.on( 'click', this.btnClearAllClick, this );

		this.cbCategories = new BS.form.field.CategoryTag({
			showTreeTrigger: true
		});
		this.cbCategories.on( 'change', this.cbCategoriesChange, this );

		this.pnlExplcitCategoriesList = new Ext.panel.Panel();
		this.pnlExplcitCategoriesEditor = new Ext.form.Panel( {
			cls: 'category-editor-form',
			items: [
				this.cbCategories,
				this.btnSave,
				this.btnCancel,
				this.btnClearAll
			],
			hidden: true
		} );

		this.pnlExplcitCategories = new Ext.panel.Panel( {
			items: [
				this.pnlExplcitCategoriesList,
				this.pnlExplcitCategoriesEditor
			]
		} );

		this.pnlImplcitCategories = new Ext.panel.Panel( {
			title: mw.message(
				'bs-insertcategory-category-editor-implicit-categories-title'
			).plain(),
			tools: [{
				type: 'help',
				tooltip: mw.message(
					'bs-insertcategory-category-editor-implicit-categories-help'
				).plain()
			}],
			hidden: true
		} );

		this.items = [
			this.pnlExplcitCategories,
			this.pnlImplcitCategories
		];

		this.loadCategories();

		this.callParent( arguments );
	},

	loadCategories: function() {
		this.store = Ext.create( 'BS.store.BSApi', {
			apiAction: 'bs-categorylinks-store',
			fields: [ 'category_title', 'category_link', 'category_is_explicit' ],
			filters: [ {
				"type": "numeric",
				"operator": "eq",
				"property": "page_id",
				"value": mw.config.get( 'wgArticleId' )
			} ],
			limit: -1,
			autoLoad: false
		});

		this.store.on( 'load', this.onStoreLoad, this );
		this.store.load();
	},

	onStoreLoad: function ( store, records, successful, eOpts ) {
		var explicitCategoryTitles = [];
		var explicitCategoryLinks = [];
		var implicitCategoryLinks = [];

		for ( var i = 0; i < records.length; i++ ) {
			var record = records[i];
			var isexplicit = record.get( 'category_is_explicit' );

			if ( isexplicit === true ) {
				explicitCategoryTitles.push( record.get( 'category_title' ) );
				explicitCategoryLinks.push( record.get( 'category_link' ) );
			} else {
				implicitCategoryLinks.push( record.get( 'category_link' ) );
			}
		}

		this.cbCategories.suspendEvent( 'change' );
		this.cbCategories.setValue( explicitCategoryTitles );
		this.cbCategories.resumeEvent( 'change' );

		this.showExplicitCategories( explicitCategoryLinks );
		this.showImplicitCategories( implicitCategoryLinks );
	},

	cbCategoriesChange: function() {
		this.btnSave.enable();
		this.btnSave.setHidden( false );
	},

	btnSaveClick:function() {
		var me = this;
		var categories = this.cbCategories.getValue();
		this.setLoading( true );

		bs.api.tasks.exec( 'wikipage', 'setCategories', {
			page_id: this.pageId,
			categories: categories
		} )
		.done( function( result ) {
			me.store.load();
			me.switchToView();
			me.setLoading( false );
		})
		.fail( function() {
			me.setLoading( false );
		});
	},

	btnClearAllClick: function() {
		this.cbCategories.reset();
		this.btnSave.enable();
	},

	btnCancelClick: function() {
		this.switchToView();
	},

	showImplicitCategories: function( implicitCategories ) {
		if( implicitCategories.length === 0 ) {
			return;
		}

		var html = this.renderCategoryLinklist( implicitCategories );
		this.pnlImplcitCategories.update( html );
		this.pnlImplcitCategories.show();
	},

	showExplicitCategories: function( explicitCategories ) {
		if ( explicitCategories.length === 0 ) {
			return;
		}

		var html = this.renderCategoryLinklist( explicitCategories );
		this.pnlExplcitCategoriesList.update( html );
		this.pnlExplcitCategories.show();
	},

	renderCategoryLinklist: function( categories ) {
		if ( categories.length === 0 ) {
			return;
		}

		var html = '<div class="bs-articleinfo-flyout-linklist pills">' + categories.join( '' ) + '</div>';

		return html;
	},

	setLoading: function( state ) {
		if( this.parentFlyout && this.parentFlyout.setLoading ) {
			this.parentFlyout.setLoading( state );
		}
		else {
			this.callParent( arguments );
		}
	},

	switchToEditor: function() {
		this.pnlExplcitCategoriesList.hide();
		this.pnlExplcitCategoriesEditor.show();
	},

	switchToView: function() {
		this.pnlExplcitCategoriesList.show();
		this.pnlExplcitCategoriesEditor.hide();
	}
});