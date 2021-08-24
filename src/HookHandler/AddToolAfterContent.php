<?php

namespace BlueSpice\InsertCategory\HookHandler;

use BlueSpice\InsertCategory\InsertCategoryTool;
use MWStake\MediaWiki\Component\CommonUserInterface\Hook\MWStakeCommonUIRegisterSkinSlotComponents;

class AddToolAfterContent implements MWStakeCommonUIRegisterSkinSlotComponents {

	/**
	 * @inheritDoc
	 */
	public function onMWStakeCommonUIRegisterSkinSlotComponents( $registry ): void {
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
	}
}
