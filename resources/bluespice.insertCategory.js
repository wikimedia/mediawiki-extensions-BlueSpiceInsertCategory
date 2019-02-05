$(document).on('click', '#ca-insert_category', function(e) {
	e.preventDefault();
	var me = $(this).find('a');
	mw.loader.using( 'ext.bluespice.extjs' ).done(function(){
		Ext.require('BS.InsertCategory.Dialog', function(){
			BS.InsertCategory.Dialog.clearListeners();
			BS.InsertCategory.Dialog.on( 'ok', function ( sender, data ) {
				if ( BS.InsertCategory.Dialog.isDirty ) {
					BsInsertCategoryViewHelper.setCategories( data );
					return false;
				}
			} );
			BS.InsertCategory.Dialog.setData(
				BsInsertCategoryViewHelper.getCategories()
			);
			BS.InsertCategory.Dialog.show( me );
		});
	});
	return false;
});