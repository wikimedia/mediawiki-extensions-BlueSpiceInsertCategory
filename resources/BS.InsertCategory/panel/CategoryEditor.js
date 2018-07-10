Ext.define( 'BS.InsertCategory.panel.CategoryEditor', {
	extend: 'Ext.panel.Panel',
	requires: [ 'BS.form.field.CategoryTag' ],
	title: mw.message(
		'bs-insertcategory-category-editor-title'
	).plain(),

	pageId: -1,
	allCategories: [],

	initComponent: function() {
		this.cbCategories = new BS.form.field.CategoryTag({
			showTreeTrigger: true
		});
		this.cbCategories.on( 'change', this.cbCategoriesChange, this );

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
			this.cbCategories,
			this.pnlImplcitCategories
		];

		this.btnSave = new Ext.button.Button( {
			text: mw.message( 'bs-extjs-save' ).plain(),
			disabled: true,
			hidden:true
		} );

		this.btnSave.on( 'click', this.btnSaveClick, this );

		this.buttons = [
			this.btnSave
		];

		this.loadCategories();

		this.callParent( arguments );
	},

	loadCategories: function() {
		var me = this;
		bs.api.tasks.exec( 'wikipage', 'getExplicitCategories', {
			page_id: this.pageId
		} )
		.done( function( result ) {
			me.cbCategories.suspendEvent( 'change' );
			me.cbCategories.setValue( result.payload );
			me.cbCategories.resumeEvent( 'change' );

			me.showImplicitCategories( result.payload );
		});
	},

	cbCategoriesChange: function() {
		this.btnSave.enable();
		this.btnSave.setHidden( false );
	},

	btnSaveClick:function() {
		var me = this;
		var categories = this.cbCategories.getValue();

		bs.api.tasks.exec( 'wikipage', 'setCategories', {
			page_id: this.pageId,
			categories: categories
		} )
		.done( function( result ) {
			me.btnSave.disable();
			me.btnSave.setHidden( true );
		});
	},

	showImplicitCategories: function( explicitCategories ) {
		var implicitCategories = [];
		for( var i = 0; i < this.allCategories.length; i++ ) {
			var currentCategory = this.allCategories[i];
			if( explicitCategories.indexOf( currentCategory ) === -1 ) {
				implicitCategories.push( currentCategory );
			}
		}

		if( implicitCategories.length === 0 ) {
			return;
		}

		var html = this.renderImplicitCategoryLinklist( implicitCategories );

		this.pnlImplcitCategories.update( html );
		this.pnlImplcitCategories.show();
	},

	renderImplicitCategoryLinklist: function( categories ) {
		var links = [];
		for( var i = 0; i < categories.length; i++ ) {
			var categoryName = categories[i];
			var title = mw.Title.newFromText( categoryName );
			var link = mw.html.element(
				'a',
				{
					href: title.getUrl(),
					title: categoryName,
					'bs-data-title': title.getPrefixedDb()
				},
				categoryName
			);

			links.push( link );
		}

		return links.join( ', ' );
	}
});