<?php

namespace BlueSpice\InsertCategory;

use Html;
use MediaWiki\MediaWikiServices;
use Message;
use MWStake\MediaWiki\Component\CommonUserInterface\Component\Literal;
use RequestContext;
use Title;

class InsertCategoryTool extends Literal {

	/**
	 *
	 * @var MediaWikiServices
	 */
	private $services = null;

	/**
	 *
	 * @var bool
	 */
	private $btnDisabled = false;

	/**
	 *
	 * @var PermissionManger
	 */
	private $permissionManager = null;

	/**
	 *
	 */
	public function __construct() {
		parent::__construct(
			'bs-category-inline-editor',
			$this->getCategoryEditorHtml()
		);
		$this->services = MediaWikiServices::getInstance();
		$this->permissionManager = $this->services->getPermissionManager();
		/** @var RequestContext */
		$context = RequestContext::getMain();
		$user = $context->getUser();

		/** @var Title */
		$title = $context->getTitle();

		$this->btnDisabled = !$this->permissionManager
			->userCan( 'edit', $context->getUser(), $title );
	}

	/**
	 *
	 * @param IContextSource $context
	 * @return bool
	 */
	public function shouldRender( $context ): bool {
		$title = $context->getTitle();
		if ( !$title || $title->isSpecialPage() ) {
			return false;
		}
		return true;
	}

	/**
	 *
	 * @return string
	 */
	private function getCategoryEditorHtml(): string {
		/** @var RequestContext */
		$context = RequestContext::getMain();

		/** @var Title */
		$title = $context->getTitle();
		if ( !$title->exists() ) {
			return '';
		}
		$html = '<div class="bs-insert-category-tool">';
		$html .= $this->makeIconButton( $title, $context );
		$html .= $this->makeList( $title, $context );
		$html .= '</div>';
		return $html;
	}

	/**
	 *
	 * @param Title $title
	 * @param Context $context
	 * @return string
	 */
	private function makeIconButton( $title, $context ): string {
		$class = 'icon-category-tag-outline';
		if ( $this->btnDisabled ) {
			$class .= ' disabled';
		}

		$html = Html::element(
			'a',
			[
				'id' => "bs-insert-category",
				'class' => $class,
				'title' => Message::newFromKey( 'bs-insertcategory-edit-dialog-button-tooltip' )->text(),
				'aria-label' => Message::newFromKey( 'bs-insertcategory-edit-dialog-button-aria-label' )->text(),
				'role' => 'button',
				'href' => ''
			]
		);
		return $html;
	}

	/**
	 *
	 * @param Title $title
	 * @param Context $context
	 * @return string
	 */
	private function makeList( $title, RequestContext $context ): string {
		$categoryNames = $context->getSkin()->getOutput()->getCategories( 'all' );
		krsort( $categoryNames, SORT_NATURAL );

		$html = '<ul>';
		foreach ( $categoryNames as $name ) {
			$title = Title::makeTitle( NS_CATEGORY, $name );
			if ( !$title ) {
				continue;
			}
			$categoryLink = Html::element(
				'a',
				[
					'title' => $title->getPrefixedText(),
					'aria-label' => $title->getPrefixedText(),
					'href' => $title->getLocalURL(),
					'role' => 'link'
				],
				$name
			);

			$html .= '<li>' . $categoryLink . '</li> ';
		}
		$html .= '</ul>';
		return $html;
	}
}