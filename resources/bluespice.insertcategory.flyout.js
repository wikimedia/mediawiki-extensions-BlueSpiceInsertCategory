(function( mw, $, bs, undefined ) {
	bs.util.registerNamespace( 'bs.insertcategory.flyout' );

	bs.insertcategory.flyout.makeItems = function() {
		var categoryEditor = Ext.create( 'BS.InsertCategory.panel.CategoryEditor', {
			pageId: mw.config.get( 'wgArticleId' ),
			allCategories: mw.config.get( 'wgCategories' )
		} );

		return {
			centerLeft: [
				categoryEditor
			]
		}
	};

})( mediaWiki, jQuery, blueSpice );
