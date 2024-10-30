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

use Mango_Popup\Popup\Template;

/**
 * Class Popup_Create
 *
 * @package Mango_Popup
 */
class Popup_Create {
	/**
	 * Register admin menu
	 */
	public function register_admin_menu() {
		// Create menu.
		$page_title       = __( 'Create Popup', 'mango_popup' );
		$menu_title       = __( 'Create Popup', 'mango_popup' );
		$capability       = 'manage_options';
		$menu_slug        = 'mango_popup';
		$create_menu_slug = 'mango_popup_create';

		add_submenu_page(
			$menu_slug, $page_title, $menu_title, $capability, $create_menu_slug,
			array( $this, 'display_settings_page' )
		);
	}

	/**
	 * Display views
	 */
	public function display_settings_page() {
		$action = isset( $_GET['action'] ) ? sanitize_text_field( wp_unslash( $_GET['action'] ) ) : '';

		switch ( $action ) {
			case 'position':
			case '':
				$this->display_position_page();
				break;
			case 'template':
				$this->display_template_page();
				break;
			case 'customize':
				$popup_create = new Popup_Customizer();
				$popup_create->display_customizer_page( 'create' );
				break;
			default:
				wp_safe_redirect( admin_url( 'admin.php?page=mango_popup' ) );
		}
	}

	/**
	 * Display view position
	 */
	public function display_position_page() {
		require MANGO_POPUP_VIEWS_PATH . 'position.php';
	}

	/**
	 * Display view template
	 */
	public function display_template_page() {
		require MANGO_POPUP_VIEWS_PATH . 'template.php';
	}

	/**
	 * Tools constructor.
	 */
	public function __construct() {
		add_action( 'admin_menu', array( $this, 'register_admin_menu' ) );
		$this->init();
	}

	/**
	 * Get page title
	 *
	 * @return string
	 */
	public function get_page_title() {
		return __( 'Create Popup', 'mango_popup' );
	}

	/**
	 * Display customize
	 */
	public function display_customize_page() {
		require MANGO_POPUP_VIEWS_PATH . 'customize.php';
	}

	/**
	 * Init
	 */
	private function init() {
		$this->init_hooks();
	}

	/**
	 * Ajax get list mail chimp
	 */
	public function ajax_mailchimp_get_lists() {
		try {
			$lists = Plugin::instance()->mailchimp->get_lists();
		} catch ( \Exception $e ) {
			return wp_send_json_error( 'Cannot get Mailchimp lists' );
		}
		$results = array();
		foreach ( $lists as $list ) {
			$results[] = array(
				'name' => $list->name,
				'id'   => $list->id,
			);
		}
		return wp_send_json_success( $results );
	}

	/**
	 * Init hook
	 */
	private function init_hooks() {
		add_action( 'wp_ajax_nopriv_mango_popup_mailchimp_get_lists', array( $this, 'ajax_mailchimp_get_lists' ) );
		add_action( 'wp_ajax_mango_popup_mailchimp_get_lists', array( $this, 'ajax_mailchimp_get_lists' ) );
	}
}
