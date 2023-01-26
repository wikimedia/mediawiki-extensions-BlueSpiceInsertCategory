<?php

namespace BlueSpice\InsertCategory\HookHandler;

use BlueSpice\InsertCategory\InsertCategoryTool;
use MWStake\MediaWiki\Component\CommonUserInterface\Hook\MWStakeCommonUIRegisterSkinSlotComponents;

class AddToolAfterContent implements MWStakeCommonUIRegisterSkinSlotComponents {

	/**
	 * @inheritDoc
	 */
	public function onMWStakeCommonUIRegisterSkinSlotComponents( $registry ): void {
		/**
		 * NOTE
		 * With 4.2.5 SkinSlot ToolsAfterContent is not used anymore
		 * Should be deleted with next minor
		 */
		$registry->register(
			'ToolsAfterContent',
			[
				'insert-category' => [
					'factory' => static function () {
						return new InsertCategoryTool();
					}
				]
			]
		);

		$registry->register(
			'PageItems',
			[
				'categories' => [
					'factory' => static function () {
						return new InsertCategoryTool();
					}
				]
			]
		);
	}
}
