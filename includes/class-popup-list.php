<?php
/**
 * MANGO POPUP PLUGIN
 *
 * @package     Mango Popup
 * @copyright   Copyright (c) 2017, Mango
 * @license     https://opensource.org/licenses/gpl-license GNU Public License
 * @since       1.0
 */

namespace Mango_Popup;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
/**
 * Class Popup
 *
 * @package Mango_Popup\Settings
 */
class Popup_List {
	/**
	 * Register admin menu
	 */
	public function register_admin_menu() {
		$page_title = __( 'Mango Popup', 'mango_popup' );
		$menu_title = __( 'Mango Popup', 'mango_popup' );
		$capability = 'manage_options';
		$menu_slug  = 'mango_popup';

		$popup_page_title = __( 'Popups', 'mango_popup' );
		$popup_menu_title = __( 'Popups', 'mango_popup' );

		add_menu_page( $page_title, $menu_title, $capability, $menu_slug, array( $this, 'display_settings_page' ) );
		add_submenu_page(
			$menu_slug,
			$popup_page_title,
			$popup_menu_title,
			$capability,
			$menu_slug,
			array( $this, 'display_settings_page' )
		);
	}

	/**
	 * Display views
	 */
	public function display_settings_page() {
		$action = isset( $_GET['action'] ) ? sanitize_text_field( wp_unslash( $_GET['action'] ) ) : '';

		switch ( $action ) {
			case 'customize':
				$popup_edit = new Popup_Customizer();
				$popup_edit->display_customizer_page( 'edit' );

				break;
			default:
				require_once MANGO_POPUP_VIEWS_PATH . 'list.php';
		}
	}

	/**
	 * Tools constructor.
	 */
	public function __construct() {
		add_action( 'admin_menu', array( $this, 'register_admin_menu' ) );
	}
}
