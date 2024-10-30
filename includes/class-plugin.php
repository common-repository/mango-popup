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

use Mango_Popup\Api\Mailchimp\Mailchimp_Service;
use Mango_Popup\Settings\Settings_Page;

/**
 * Class Plugin
 *
 * @package Mango_Popup
 */
class Plugin {
	/**
	 * Singleton Plugin instance
	 *
	 * @var Plugin
	 */
	private static $instance;

	/**
	 * Admin class
	 *
	 * @var Admin
	 */
	private $admin;
	/**
	 * Frontend class
	 *
	 * @var Frontend
	 */
	private $frontend;
	/**
	 * Mailchimp service instance
	 *
	 * @var Mailchimp_Service
	 */
	public $mailchimp;

	/**
	 * Singleton Instance
	 *
	 * @return Plugin
	 */
	public static function instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
			do_action( 'mango_popup_loaded' );
		}

		return self::$instance;
	}

	/**
	 * Init components
	 */
	public function init() {
		$this->init_components();

		$this->register_post_type();
	}

	/**
	 * Init components
	 */
	private function init_components() {
		$this->frontend = new Frontend();
		if ( is_admin() ) {
			$this->mailchimp = new Mailchimp_Service();
			$this->admin     = new Admin();
			$this->list      = new Popup_List();
			$this->create    = new Popup_Create();
			$this->settings  = new Settings_Page();
		}
	}

	/**
	 * Plugin constructor.
	 */
	private function __construct() {
		$this->register_autoloader();

		add_action( 'init', array( $this, 'init' ), 0 );
	}

	/**
	 * Autoloader
	 */
	private function register_autoloader() {
		require MANGO_POPUP_PATH . '/includes/class-autoloader.php';
		AutoLoader::run();
	}

	/**
	 * Register post type mango_popup
	 */
	private function register_post_type() {
		register_post_type( 'mango_popup' );
	}

	/**
	 * Response json
	 *
	 * @param bool  $success success.
	 * @param array $array data.
	 * @param int   $status_code status code.
	 */
	public static function response( $success = true, $array = array(), $status_code = 200 ) {
		if ( ! $array ) {
			$array = array(
				'success' => $success,
			);
		}

		wp_send_json( $array, $status_code );
		exit;
	}
}

Plugin::instance();
