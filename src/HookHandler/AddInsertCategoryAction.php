<?php

namespace BlueSpice\InsertCategory\HookHandler;

use MediaWiki\Hook\SkinTemplateNavigation__UniversalHook;
use MediaWiki\MediaWikiServices;
use SkinTemplate;

class AddInsertCategoryAction implements SkinTemplateNavigation__UniversalHook {

	/**
	 * @param SkinTemplate $sktemplate
	 * @return bool
	 */
	protected function skipProcessing( SkinTemplate $sktemplate ) {
		if ( $sktemplate->getRequest()->getVal( 'action', 'view' ) != 'view' ) {
			return true;
		}
		if ( !MediaWikiServices::getInstance()->getPermissionManager()
			->userCan(
				'edit',
				$sktemplate->getUser(),
				$sktemplate->getTitle()
			)
		) {
			return true;
		}
		return false;
	}

	/**
	 * // phpcs:disable MediaWiki.NamingConventions.LowerCamelFunctionsName.FunctionName
	 * @inheritDoc
	 */
	public function onSkinTemplateNavigation__Universal( $sktemplate, &$links ): void {
		if ( $this->skipProcessing( $sktemplate ) ) {
			return;
		}

		$links['actions']['insert_category'] = [
			'text' => $sktemplate->msg( 'bs-insertcategory-insertcat' )->text(),
			'href' => '#',
			'class' => false,
			'id' => 'ca-insertcategory',
			'bs-group' => 'hidden'
		];
	}
}
