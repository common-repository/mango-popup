<?php
/**
 * MANGO POPUP PLUGIN
 *
 * @package     Mango Popup
 * @copyright   Copyright (c) 2017, Mango
 * @license     https://opensource.org/licenses/gpl-license GNU Public License
 * @since       1.0
 */

namespace Mango_Popup\Settings;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Install_Template
 *
 * @package Mango_Popup\Settings
 */
class Install_Template {
	/**
	 * Display views
	 */
	public function display_settings_page() {
		require_once MANGO_POPUP_VIEWS_PATH . 'settings' . DS . 'install-template.php';
	}
}
