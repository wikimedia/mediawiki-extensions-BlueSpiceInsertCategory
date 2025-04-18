<?php

namespace BlueSpice\InsertCategory\ConfigDefinition;

class UploadPanelIntegration extends \BlueSpice\ConfigDefinition\BooleanSetting {

	/**
	 *
	 * @return string[]
	 */
	public function getPaths() {
		$ext = 'BlueSpiceInsertCategory';
		return [
			static::MAIN_PATH_FEATURE . '/' . static::FEATURE_EDITOR . "/$ext",
			static::MAIN_PATH_EXTENSION . "/$ext/" . static::FEATURE_EDITOR,
			static::MAIN_PATH_PACKAGE . '/' . static::PACKAGE_FREE . "/$ext",
		];
	}

	/**
	 *
	 * @return string
	 */
	public function getLabelMessageKey() {
		return 'bs-insertcategory-pref-uploadintegration';
	}

	/**
	 *
	 * @return bool
	 */
	public function isRLConfigVar() {
		return true;
	}

	/**
	 *
	 * @return string
	 */
	public function getHelpMessageKey() {
		return 'bs-insertcategory-pref-uploadintegration-help';
	}

}
