<?php

namespace BlueSpice\InsertCategory\Renderer\PageHeader;

use Html;
use Title;
use Config;
use IContextSource;
use MediaWiki\Linker\LinkRenderer;
use BlueSpice\Renderer\Params;
use BlueSpice\Calumma\Renderer\PageHeader\Category as CategoryBase;

class Category extends CategoryBase {

	/**
	 * Constructor
	 * @param Config $config
	 * @param Params $params
	 * @param LinkRenderer|null $linkRenderer
	 * @param IContextSource|null $context
	 * @param string $name | ''
	 */
	protected function __construct( Config $config, Params $params,
		LinkRenderer $linkRenderer = null, IContextSource $context = null,
		$name = '' ) {
		parent::__construct( $config, $params, $linkRenderer, $context, $name );
	}

	/**
	 *
	 * @return string
	 */
	public function render() {
		$html = parent::render();
		$html .= $this->makeChangeLink( $this->getContext()->getTitle() );
		return $html;
	}

	/**
	 *
	 * @param Title $title
	 * @return string
	 */
	private function makeChangeLink( Title $title ) {
		$html = Html::openElement(
				'div',
				[
					'class' => 'bs-insertcategory-category-container-editlink visible-xs visible-sm visible-md'
				]
			);

		if ( $title->userCan( 'edit' ) ) {
			$html .= Html::element(
					'a',
					[
						'class' => 'bs-category-add-category',
						'href' => '#'
					],
					'(' . $this->msg( 'bs-insertcategory-category-editor-explicit-categories-edit-label' )->plain() . ')'
				);
		}

		$html .= Html::closeElement( 'div' );

		return $html;
	}

	/**
	 *
	 * @param Title $title
	 * @return string
	 */
	protected function makeIcon( $title ) {
		if ( $title->userCan( 'edit' ) ) {
			$html = $this->makeLinkedIcon();
		} else {
			$html = parent::makeIcon( $title );
		}
		return $html;
	}

	/**
	 *
	 * @return string
	 */
	protected function makeLinkedIcon() {
		$html = Html::openElement(
				'a',
				[
					'class' => 'bs-category-add-category',
					'href' => '#',
					'title' => $this->msg( 'bs-insertcategory-category-editor-explicit-categories-edit-label' )->plain()
				]
			);

		$html .= Html::element( 'i' );

		$html .= Html::closeElement( 'a' );

		return $html;
	}
}
