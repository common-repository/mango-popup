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
use Mango_Popup\Popup\Font;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Frontend
 *
 * @package Mango_Popup
 */
class Frontend {
	/**
	 * List popup can render to frontend
	 *
	 * @var array List popup render.
	 */
	protected $list_popup_render;

	/**
	 * Enqueue scripts
	 */
	public function enqueue_scripts() {
		wp_register_script(
			'mango-popup-render-js',
			MANGO_POPUP_ASSETS_URL . 'frontend/js/main.js',
			array( 'jquery', 'jquery-form' ),
			MANGO_POPUP_VERSION,
			true
		);

		wp_enqueue_script( 'mango-popup-render-js' );

		wp_localize_script(
			'mango-popup-render-js', 'MangoPopupFrontendL10N', array(
				'ajaxUrl' => admin_url( 'admin-ajax.php' ),
			)
		);
	}

	/**
	 * Enqueue style
	 */
	public function enqueue_styles() {
		wp_register_style(
			'mango-popup-animate-css',
			MANGO_POPUP_VENDOR_URL . 'animate.css/animate.min.css',
			array(),
			MANGO_POPUP_VERSION
		);

		wp_register_style(
			'mango-popup-font-awesome',
			MANGO_POPUP_VENDOR_URL . 'font-awesome/css/font-awesome.min.css',
			array(),
			MANGO_POPUP_VERSION
		);

		wp_register_style(
			'mango-popup-render-css',
			MANGO_POPUP_ASSETS_URL . 'frontend/css/main.css',
			array( 'mango-popup-animate-css', 'mango-popup-font-awesome' ),
			MANGO_POPUP_VERSION
		);

		wp_enqueue_style( 'mango-popup-render-css' );
	}

	/**
	 * Admin constructor.
	 */
	public function __construct() {
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_styles' ) );

		add_action( 'wp_enqueue_scripts', array( $this, 'prepare_render_popup' ) );
		add_action( 'wp_footer', array( $this, 'render_popup' ) );

		add_action( 'wp_ajax_mango_popup_after_open', array( $this, 'mango_popup_after_open' ) );
		add_action( 'wp_ajax_nopriv_mango_popup_after_open', array( $this, 'mango_popup_after_open' ) );

		add_action( 'wp_ajax_mango_popup_form_submit', array( $this, 'mango_popup_form_submit' ) );
		add_action( 'wp_ajax_nopriv_mango_popup_form_submit', array( $this, 'mango_popup_form_submit' ) );

		add_shortcode( 'mangopopup', array( $this, 'mango_popup_shortcode' ) );

		add_action( 'init', array( $this, 'check_woocommerce' ) );

		$this->auto_fill_woocommerce_detals_data();
	}

	/**
	 * Prepare render popup
	 */
	public function prepare_render_popup() {
		global $post;

		if ( is_customize_preview() ) {
			return false;
		}

		$all_fonts               = array();
		$mango_popup_action      = isset( $_GET['mango_popup_action'] ) ? sanitize_text_field( wp_unslash( $_GET['mango_popup_action'] ) ) : '';
		$mango_popup_id          = isset( $_GET['mango_popup_id'] ) ? intval( wp_unslash( $_GET['mango_popup_id'] ) ) : '';
		$current_page_id         = ( $post ) ? $post->ID : 0;
		$this->list_popup_render = array();

		if ( 'preview' === $mango_popup_action && $mango_popup_id > 0 && 'mango_popup' === get_post_type( $mango_popup_id ) && current_user_can( 'manage_options' ) ) {
			$popup                     = new Popup( $mango_popup_id );
			$this->list_popup_render[] = $popup;
			$all_fonts                 = array_merge( $all_fonts, $popup->meta['font_families_used'] );
			wp_enqueue_style(
				'mango-popup-id-' . $popup->id,
				$popup->css_url,
				array(),
				get_post_modified_time( 'U', false, $popup->id )
			);
		} elseif ( $current_page_id ) {
			$popups = Popup::init_by_page_id( $current_page_id );
			/**
			 * Popup instance
			 *
			 * @var Popup $popup
			 */
			foreach ( $popups as $popup ) {
				if ( $popup->can_render() ) {
					$this->list_popup_render[] = $popup;
					$font_families_used        = array();
					if ( isset( $popup->meta['font_families_used'] ) ) {
						if ( ! empty( $popup->meta['font_families_used'] ) ) {
							$font_families_used = $popup->meta['font_families_used'];
						}
					}
					$all_fonts = array_merge( $all_fonts, $font_families_used );

					wp_enqueue_style(
						'mango-popup-id-' . $popup->id,
						$popup->css_url,
						array(),
						get_post_modified_time( 'U', false, $popup->id )
					);
				}
			}
		}
		$load_url = Font::get_url( array_unique( $all_fonts ) );

		if ( $load_url ) {
			wp_enqueue_style(
				'mango-popup-font-used',
				$load_url,
				array(),
				MANGO_POPUP_VERSION
			);
		}

		do_action( 'after_mango_popup_render_popups', array( $this->list_popup_render ) );

		add_action( 'wp_footer', array( $this, 'render_popup' ) );
	}

	/**
	 * Render All Popup
	 */
	public function render_popup() {
		if ( is_customize_preview() ) {
			return false;
		}

		echo '<div class="mango-popup-all-popup-wrapper">';
		/**
		 * Popup instance
		 *
		 * @var Popup $popup
		 */
		$list_popup_render = $this->list_popup_render;
		$list_popup_render = apply_filters( 'mango_popup_post_render_popups', $list_popup_render );
		foreach ( $list_popup_render as $popup ) {
			$popup->render();
		}
		echo '</div>';
	}

	/**
	 * Process Data after open popup
	 */
	public function mango_popup_after_open() {
		$popup_id = isset( $_REQUEST['id'] ) ? intval( wp_unslash( $_REQUEST['id'] ) ) : 0;

		if ( ! $popup_id ) {
			exit;
		}

		$permission = check_ajax_referer( 'mango_popup_after_open', 'nonce', false );

		if ( ! $permission ) {
			exit;
		}

		// Increase views.
		$this->increase_views( $popup_id );

		wp_send_json(
			array(
				'success' => true,
			), 200
		);
		exit;
	}

	/**
	 * Increase views of popup
	 *
	 * @param integer $popup_id Popup id.
	 *
	 * @return bool
	 */
	private function increase_views( $popup_id ) {
		if ( current_user_can( 'manage_options' ) ) { // Admin not increase views.
			return true;
		}

		$views = intval( get_post_meta( $popup_id, 'mango_popup_views', true ) );

		$views = $views ? ( $views + 1 ) : 1;

		update_post_meta( $popup_id, 'mango_popup_views', $views );
	}

	/**
	 * Submit mango popup action
	 */
	public function mango_popup_form_submit() {
		// process form submit here.
		if ( ! check_ajax_referer( 'mango_popup_submit', 'nonce', false ) ) {
			Plugin::response( false );
			exit;
		}

		// sanitize all.
		$data = array_map( 'sanitize_text_field', wp_unslash( $_REQUEST ) );

		$id = isset( $data['id'] ) ? intval( $data['id'] ) : 0;

		if ( ! $id || 'mango_popup' !== get_post_type( $id ) ) {
			Plugin::response( false );
			exit;
		}

		// Incrase click.
		if ( ! current_user_can( 'manage_options' ) ) { // Admin not increase click.
			$clicks = intval( get_post_meta( $id, 'mango_popup_clicks', true ) );
			$clicks = $clicks ? ( $clicks + 1 ) : 1;
			update_post_meta( $id, 'mango_popup_clicks', $clicks );
		}

		$email = isset( $data['EMAIL'] ) ? sanitize_email( $data['EMAIL'] ) : '';

		if ( ! $email ) {
			Plugin::response( false );
			exit;
		}

		// Mailchimp.
		$list_id = get_post_meta( $id, 'mango_popup_mailchimp_target_id', true );
		if ( $list_id ) {
			$plugin            = Plugin::instance();
			$mailchimp_service = $plugin->mailchimp;
			$merge_fields      = array(
				'FNAME' => isset( $data['FNAME'] ) ? $data['FNAME'] : '',
				'LNAME' => isset( $data['LNAME'] ) ? $data['LNAME'] : '',
				'PHONE' => isset( $data['PHONE'] ) ? $data['PHONE'] : '',
			);

			if ( $mailchimp_service->member( $list_id, $email ) ) {
				$mailchimp_service->update( $list_id, $email, true, $merge_fields );
			} else {
				$mailchimp_service->subscribe( $list_id, $email, true, $merge_fields );
			}
		}

		Plugin::response( true );
		exit;
	}

	/**
	 * Mango popup shortcode
	 *
	 * @param array $args Arguments of shortcode.
	 *
	 * @return string|void
	 */
	public function mango_popup_shortcode( $args ) {
		$id = isset( $args['id'] ) ? $args['id'] : 0;

		if ( ! $id ) {
			return;
		}

		$popup = new Popup( $id );

		if ( 'publish' === $popup->post_status ) {
			wp_enqueue_style(
				'mango-popup-id-' . $popup->id,
				$popup->css_url,
				array(),
				get_post_modified_time( 'U', false, $popup->id )
			);

			return $popup->render_shortcode();
		}
	}

	/**
	 * Auto fill woocommerce detals data
	 */
	private function auto_fill_woocommerce_detals_data() {
		add_action(
			'after_mango_popup_render_popups', function ( $array_popups ) {

				$popups = isset( $array_popups[0] ) ? $array_popups[0] : array();

				/* @var Popup[] $popups */
				foreach ( $popups as $popup ) {
					if ( $popup->is_auto_fill_deals_data_sorted_by_best_price() ) {
						$best_sorted = WooCommerce::get_deal_by_value();
						wp_localize_script( 'mango-popup-render-js', 'best_price_deals_data', $best_sorted );
						return;
					}
				}
			}
		);

		add_action(
			'after_mango_popup_render_popups', function ( $array_popups ) {

				$popups = isset( $array_popups[0] ) ? $array_popups[0] : array();

				/* @var Popup[] $popups */
				foreach ( $popups as $popup ) {
					if ( $popup->is_auto_fill_deals_data_sorted_latest() ) {
						$latest_deals = WooCommerce::get_deal_by_day();
						wp_localize_script( 'mango-popup-render-js', 'latest_deals_data', $latest_deals );
						return;
					}
				}
			}
		);

		add_action(
			'after_mango_popup_render_popups', function ( $array_popups ) {
				$popups = isset( $array_popups[0] ) ? $array_popups[0] : array();

				$templates = array();

				/* @var Popup[] $popups */
				foreach ( $popups as $popup ) {
					if ( ! isset( $templates[ $popup->template->template ] ) ) {
						$templates[ $popup->template->template ] = $popup->template->product_template;
					}
				}

				wp_localize_script( 'mango-popup-render-js', 'mango_popup_product_template', $templates );
			}
		);
	}

	/**
	 * Check wooCommerce
	 *
	 * @return bool
	 */
	public function check_woocommerce() {
		if ( defined( 'MANGO_POPUP_HAVE_WC' ) ) {
			return MANGO_POPUP_HAVE_WC;
		}

		if ( ! defined( 'WC_VERSION' ) ) {
			define( 'MANGO_POPUP_HAVE_WC', false );
		} else {
			define( 'MANGO_POPUP_HAVE_WC', true );
		}
	}
}
