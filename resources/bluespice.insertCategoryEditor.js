$( document ).on( 'click', '#ca-insert_category, #bs-insert-category, #bs-category-link-edit', function( e ) {
	e.preventDefault();

	mw.loader.using( 'ext.bluespice.insertcategory.editor.dialog.scripts' ).done( function() {
		var windowManager = OO.ui.getWindowManager();
		var dialog = new ext.InsertCategory.ui.dialog.CategoryEditor();

		windowManager.addWindows( [ dialog ] );
		windowManager.openWindow( dialog );
	} );

	return false;
} );
