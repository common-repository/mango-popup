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
 * Class MailChimp_Settings
 *
 * @package Mango_Popup
 */
class Mailchimp_Settings {
	/**
	 * Display views
	 */
	public function display_settings_page() {
		require_once MANGO_POPUP_VIEWS_PATH . 'settings' . DS . 'mailchimp.php';
	}
}
