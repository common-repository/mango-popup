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
 * Class Setting_Page
 *
 * @package Mango_Popup\Settings
 */
class Settings_Page {
	/**
	 * Slug
	 *
	 * @var string
	 */
	public $slug = 'mango_popup';

	/**
	 * Mailchimp Setting Page constructor.
	 */
	public function __construct() {
		add_action( 'admin_menu', array( $this, 'register_admin_menu' ) );
	}

	/**
	 * Register admin menu
	 */
	public function register_admin_menu() {
		$capability = $this->get_capability();
		add_submenu_page(
			'mango_popup', $this->get_page_title(), __( 'Settings', 'mango_popup' ), $capability,
			'mango_popup_settings',
			array( $this, 'display_settings_page' )
		);
	}

	/**
	 * Get capability
	 *
	 * @return string
	 */
	protected function get_capability() {
		return 'manage_options';
	}

	/**
	 * Display views
	 */
	public function display_settings_page() {
		require_once MANGO_POPUP_VIEWS_PATH . 'settings.php';
	}


	/**
	 * Get page title
	 *
	 * @return string
	 */
	public function get_page_title() {
		return __( 'Mango Popup Settings', 'mango_popup' );
	}
}
