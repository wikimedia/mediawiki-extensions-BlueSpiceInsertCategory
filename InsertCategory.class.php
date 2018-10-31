<?php

/**
 * InsertCategory extension for BlueSpice
 *
 * Dialogbox to enter a category link.
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, version 3.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License along
 * with this program; if not, write to the Free Software Foundation, Inc.,
 * 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301, USA.
 *
 * This file is part of BlueSpice MediaWiki
 * For further information visit http://www.bluespice.com
 *
 * @author     Markus Glaser <glaser@hallowelt.com>
 * @author     Sebastian Ulbricht
 * @author     Stefan Widmann <widmann@hallowelt.com>
 * @package    BlueSpiceInsertCategory
 * @copyright  Copyright (C) 2016 Hallo Welt! GmbH, All rights reserved.
 * @license    http://www.gnu.org/copyleft/gpl.html GNU Public License v3
 * @filesource
 */

// Last review RBV (30.06.11 8:40)

/**
 * Class for category management assistent
 * @package BlueSpiceInsertCategory
 */
class InsertCategory extends BsExtensionMW {
	/**
	 * Initialise the InsertCategory extension
	 */
	protected function initExt() {
		$this->setHook( 'SkinTemplateNavigation' );
		$this->setHook( 'BeforePageDisplay' );
	}

	/**
	 *
	 * @param OutputPage $out
	 * @param Skin $skin
	 * @return boolean
	 */
	public static function onBeforePageDisplay( &$out, &$skin ) {
		$out->addModuleStyles('ext.bluespice.insertcategory.styles');
		$out->addModules( 'ext.bluespice.insertcategory' );

		$config = \BlueSpice\Services::getInstance()->getConfigFactory()
			->makeConfig( 'bsg' );
		if( $config->get( 'InsertCategoryUploadPanelIntegration' ) ) {
			$out->addModules( 'ext.bluespice.insertCategory.uploadPanelIntegration' );
		}
		return true;
	}

	/**
	 * Adds the "Insert category" menu entry in view mode
	 * @param SkinTemplate $sktemplate
	 * @param array $links
	 * @return boolean Always true to keep hook running
	 */
	public function onSkinTemplateNavigation( &$sktemplate, &$links ) {
		if ( $this->getRequest()->getVal( 'action', 'view') != 'view' ) {
			return true;
		}
		if ( !$this->getTitle()->userCan( 'edit' ) ) {
			return true;
		}
		$links['actions']['insert_category'] = array(
			'text' => wfMessage( 'bs-insertcategory-insertcat' )->text(),
			'href' => '#',
			'class' => false,
			'id' => 'ca-insertcategory',
			'bs-group' => 'hidden'
		);

		return true;
	}
}
