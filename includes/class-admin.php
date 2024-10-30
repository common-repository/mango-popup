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
use Mango_Popup\Popup\Template;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Admin
 *
 * @package Mango_Popup
 */
class Admin {

	/**
	 * Admin constructor.
	 */
	public function __construct() {
		add_action( 'admin_enqueue_scripts', array( $this, 'register_scripts' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'register_styles' ) );

		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_styles' ) );

		add_filter( 'admin_body_class', array( $this, 'add_body_class' ) );

		add_action( 'admin_post_mango_popup_save', array( $this, 'mango_popup_save' ) );
		add_action( 'wp_ajax_mango_popup_delete', array( $this, 'mango_popup_delete' ) );
		add_action( 'wp_ajax_mango_popup_toggle_state', array( $this, 'toggle_state' ) );

		add_action( 'wp_ajax_nopriv_mango_popup_mailchimp_get_lists', array( $this, 'ajax_mailchimp_get_lists' ) );
		add_action( 'wp_ajax_mango_popup_mailchimp_get_lists', array( $this, 'ajax_mailchimp_get_lists' ) );

		add_action( 'current_screen', array( $this, 'check_redirect_wrong_page' ) );

		add_action( 'admin_init', array( $this, 'mango_popup_redirect' ) );
		add_action( 'admin_init', array( $this, 'init_hooks' ) );

		add_action( 'admin_init', array( $this, 'check_woocommerce' ) );

		$this->list_page_hook = array(
			'list'   => 'toplevel_page_mango_popup',
			'edit'   => 'toplevel_page_mango_popup',
			'create' => 'mango-popup_page_mango_popup_create',
		);

		$this->list_page_action = array(
			'customize',
			'template',
			'position',
		);
	}

	/**
	 * Init hooks
	 */
	public function init_hooks() {
		add_action( 'update-custom_mango-popup-import-template', array( $this, 'do_import_templates' ) );
	}

	/**
	 * Do import templates
	 */
	public function do_import_templates() {
		$num_extracted_templates = 0;
		if ( isset( $_FILES['templates_file'] ) && isset( $_FILES['templates_file']['tmp_name'] ) ) {

			$url   = wp_nonce_url( admin_url( 'update.php' ), 'mango-popup-import-template' );
			$creds = request_filesystem_credentials( $url, '', false, false, null );
			if ( ! WP_Filesystem( $creds ) ) {
				request_filesystem_credentials( $url, '', true, false, null );
			}

			$template_file = isset( $_FILES['templates_file'] ) ? $_FILES['templates_file'] : array();

			if ( ! isset( $template_file['tmp_name'] ) || ! is_readable( $template_file['tmp_name'] ) ) {
				wp_safe_redirect( admin_url( 'admin.php?page=mango_popup_settings&mango_popup_template_installed=0&mango_popup_import_result=fail&reason_code=403' ) );
				return;
			}
			$path_to_zip = $template_file['tmp_name'];
			if ( Template::validate_templates_zip( $path_to_zip ) ) {
				$extracted_templates     = Template::extract_templates_zip( $path_to_zip );
				$num_extracted_templates = count( $extracted_templates );
			}
		} else {
			wp_safe_redirect( admin_url( 'admin.php?page=mango_popup_settings&mango_popup_template_installed=0&mango_popup_import_result=fail&reason_code=upload_fail' ) );
			return;
		}

		$settings_page_query = array(
			'page'                           => 'mango_popup_settings',
			'mango_popup_template_installed' => $num_extracted_templates,
		);

		if ( 0 === $num_extracted_templates ) {
			$settings_page_query['mango_popup_import_result'] = 'fail';
		} else {
			$settings_page_query['mango_popup_import_result'] = 'success';
		}
		$http_build_query = http_build_query( $settings_page_query );
		$error_last       = error_get_last();
		if ( empty( $error_last ) || 8 !== $error_last[0]['type'] ) {
			wp_safe_redirect( admin_url( 'admin.php?' . $http_build_query ) );
		}
	}


	/**
	 * Check Current screen is valid if not valid => Redirect
	 *
	 * @param \WP_Screen $screen WordPress screen object.
	 */
	public function check_redirect_wrong_page( $screen ) {
		if ( ! in_array( $screen->base, $this->list_page_hook, true ) ) {
			return;
		}

		// is plugin mango popup screen.
		$popup_list_url  = admin_url( 'admin.php?page=mango_popup' );
		$create_page_url = admin_url( 'admin.php?page=mango_popup_create' );
		$action          = isset( $_GET['action'] ) ? sanitize_text_field( wp_unslash( $_GET['action'] ) ) : '';

		if ( $screen->base == $this->list_page_hook['edit'] && $action ) {
			// Edit page.
			if ( 'customize' !== $action ) {
				wp_safe_redirect( $popup_list_url );
				exit;
			}
		}

		if ( $screen->base == $this->list_page_hook['create'] ) {
			// create page.
			if ( $action && ! in_array( $action, $this->list_page_action, true ) ) {
				wp_safe_redirect( $create_page_url );
				exit;
			}
		}
	}

	/**
	 * Enqueue scripts
	 */
	public function register_scripts() {
		wp_register_script(
			'mango-popup-angular',
			MANGO_POPUP_VENDOR_URL . 'angular/angular.min.js',
			array(),
			MANGO_POPUP_VERSION
		);
		wp_register_script(
			'mango-popup-jquery-editable-select',
			MANGO_POPUP_VENDOR_URL . 'jquery-editable-select/dist/jquery-editable-select.min.js',
			array( 'jquery' ),
			MANGO_POPUP_VERSION
		);

		wp_register_script(
			'mango-popup-admin-js',
			MANGO_POPUP_ASSETS_URL . 'admin/js/main.js',
			array(
				'jquery',
				'wp-color-picker',
				'jquery-ui-dialog',
				'mango-popup-angular',
				'mango-popup-jquery-editable-select',
			),
			MANGO_POPUP_VERSION,
			true
		);
	}

	/**
	 * Enqueue styles
	 */
	public function register_styles() {
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
			'mango-popup-jquery-editable-select',
			MANGO_POPUP_VENDOR_URL . 'jquery-editable-select/dist/jquery-editable-select.min.css',
			array(),
			MANGO_POPUP_VERSION
		);

		wp_register_style(
			'mango-popup-admin-css',
			MANGO_POPUP_ASSETS_URL . 'admin/css/main.css',
			array(
				'wp-jquery-ui-dialog',
				'mango-popup-font-awesome',
				'mango-popup-jquery-editable-select',
				'mango-popup-animate-css',
			),
			MANGO_POPUP_VERSION
		);
	}

	/**
	 * Enqueue Script
	 *
	 * @param string $hook Hook name.
	 */
	public function enqueue_scripts( $hook ) {
		if ( ! in_array( $hook, $this->list_page_hook, true ) ) {
			return;
		}

		wp_enqueue_script( 'mango-popup-admin-js' );
		wp_enqueue_media();

		wp_localize_script(
			'mango-popup-admin-js', 'MangoPopupAdminL10N', array(
				'ajaxUrl'         => admin_url( 'admin-ajax.php' ),
				'deletePopupText' => __( 'Delete Popup', 'mango_popup' ),
			)
		);
	}

	/**
	 * Enqueue Style
	 *
	 * @param string $hook Hook name.
	 */
	public function enqueue_styles( $hook ) {
		wp_enqueue_style( 'mango-popup-admin-css' );

		if ( ! in_array( $hook, $this->list_page_hook, true ) ) {
			return;
		}

		wp_enqueue_style( 'wp-color-picker' );
	}


	/**
	 * Add body class in Mango Popup's page
	 *
	 * @param string $classes body classes.
	 *
	 * @return string
	 */
	public function add_body_class( $classes ) {
		$page = isset( $_GET['page'] ) ? sanitize_text_field( wp_unslash( $_GET['page'] ) ) : '';

		$pages_slug = array(
			'list'     => 'mango_popup',
			'create'   => 'mango_popup_create',
			'settings' => 'mango_popup_settings',
		);

		if ( ! $page || ! in_array( $page, $pages_slug, true ) ) {
			return $classes;
		}

		$classes .= ' mango-popup-plugin ';

		return $classes;
	}

	/**
	 * Process save form create/edit mango popup
	 */
	public function mango_popup_save() {
		if ( ! current_user_can( 'manage_options' ) ) {
			return false;
		}
		// Current user have permission.
		if ( isset( $_POST['mango_new_popup'], $_POST['mango_popup_save_nonce'] )
			 && wp_verify_nonce( sanitize_key( $_POST['mango_popup_save_nonce'] ), 'mango_popup_save' ) ) {

			$url   = wp_nonce_url( admin_url( 'admin-post.php' ), 'mango_popup_save' );
			$creds = request_filesystem_credentials( $url, '', false, false, null );
			if ( ! WP_Filesystem( $creds ) ) {
				request_filesystem_credentials( $url, '', true, false, null );
			}

			// pass nonce verify.
			// sanitize_text_field all.
			@$mango_popup_data = array_map( 'sanitize_text_field', wp_unslash( $_POST['mango_new_popup'] ) );

			$list_allowed = Popup::list_allowed_html();

			$post_content = base64_decode( sanitize_text_field( wp_unslash( $_POST['mango_new_popup']['post_content'] ) ) );
			add_filter(
				'safe_style_css', function ( $safe_style_css ) {
					$safe_style_css[] = 'text-decoration-line';

					return $safe_style_css;
				}
			);
			preg_match_all( '#rgb\(\s*(\d+)\s*,\s*(\d+)\s*,\s*(\d+)\s*\)#is', $post_content, $rgb_colors, PREG_SET_ORDER );
			$post_content = preg_replace( '#none rgb\(\s*(\d+)\s*,\s*(\d+)\s*,\s*(\d+)\s*\)#is', 'none', $post_content );
			$post_content = str_replace( ';;', ';', $post_content );
			$post_content = str_replace( 'style=";', 'style="', $post_content );
			foreach ( $rgb_colors as $rgb_color_set ) {
				$hex          = sprintf( '#%02x%02x%02x', $rgb_color_set[1], $rgb_color_set[2], $rgb_color_set[3] );
				$rgb_value    = $rgb_color_set[0];
				$post_content = str_replace( $rgb_value, $hex, $post_content );
			}
			$popup_content = wp_kses(
				$post_content,
				$list_allowed
			);

			$popup                               = new Popup();
			$popup->id                           = isset( $mango_popup_data['id'] ) ? intval( $mango_popup_data['id'] ) : 0;
			$popup->post_title                   = isset( $mango_popup_data['popup_title'] ) ? $mango_popup_data['popup_title'] : '';
			$popup->post_content                 = $popup_content;
			$popup->meta                         = array();
			$popup->meta['background_image_url'] = isset( $mango_popup_data['background_image_url'] ) ? $mango_popup_data['background_image_url'] : '';
			$popup->meta['background_color']     = isset( $mango_popup_data['background_color'] ) ? $mango_popup_data['background_color'] : '';
			$popup->meta['animation_start']      = isset( $mango_popup_data['animation_start'] ) ? $mango_popup_data['animation_start'] : '';
			if ( 0 === $popup->id ) {
				$popup->meta['views']  = 0;
				$popup->meta['clicks'] = 0;
				$popup->post_status    = 'publish';
			} else {
				$popup->meta['views']  = get_post_meta( $popup->id, 'mango_popup_views', true );
				$popup->meta['clicks'] = get_post_meta( $popup->id, 'mango_popup_clicks', true );
				$popup->post_status    = get_post_status( $popup->id );
			}
			if ( ! isset( $_POST['mango_new_popup']['hide_on_devices'] ) ) {
				$_POST['mango_new_popup']['hide_on_devices'] = array();
			}
			$hide_on_devices                        = array_map(
				'sanitize_text_field',
				wp_unslash( $_POST['mango_new_popup']['hide_on_devices'] )
			);
			$hide_on_devices                        = array_unique( array_keys( $hide_on_devices ) );
			$hide_on_devices                        = array_map(
				'sanitize_text_field',
				wp_unslash( $hide_on_devices )
			);
			$popup->meta['hide_on_devices']         = $hide_on_devices;
			$popup->meta['position']                = isset( $mango_popup_data['position'] ) ? $mango_popup_data['position'] : '';
			$popup->meta['how_often_popup_display'] = isset( $mango_popup_data['how_often_popup_display'] ) ? $mango_popup_data['how_often_popup_display'] : '';
			$popup->meta['when_popup_display']      = isset( $mango_popup_data['when_popup_display'] ) ? $mango_popup_data['when_popup_display'] : '';
			$popup->meta['target_link']             = isset( $mango_popup_data['target_link'] ) ? $mango_popup_data['target_link'] : '';
			$popup->meta['template']                = isset( $mango_popup_data['template'] ) ? $mango_popup_data['template'] : '';
			$popup->meta['max_width']               = isset( $mango_popup_data['max_width'] ) ? intval( $mango_popup_data['max_width'] ) : '';
			$popup->meta['mailchimp_target_id']     = isset( $mango_popup_data['mailchimp_target_id'] ) ? $mango_popup_data['mailchimp_target_id'] : '';
			$popup->meta['font_families_used']      = isset( $mango_popup_data['font_families_used'] ) ? json_decode(
				$mango_popup_data['font_families_used'],
				true
			) : '';
			$popup->meta['custom_css']              = isset( $mango_popup_data['custom_css'] ) ? json_decode( $mango_popup_data['custom_css'], true ) : array();
			$popup->meta['mailchimp_target_id']     = preg_replace(
				'#^(string|number):#is', '',
				$popup->meta['mailchimp_target_id']
			);

			if ( isset( $_POST['mango_new_popup']['page_id_display'] ) ) {
				$list_page_id = array_map(
					'sanitize_text_field',
					wp_unslash( $_POST['mango_new_popup']['page_id_display'] )
				);

				$list_page_id = array_unique( array_keys( $list_page_id ) );

				$mango_popup_data['page_id_display'] = array_map(
					'sanitize_text_field',
					wp_unslash( $list_page_id )
				);
			}

			$popup->meta['page_id_display'] = isset( $mango_popup_data['page_id_display'] ) ? $mango_popup_data['page_id_display'] : array();

			$popup->meta['display_all_page'] = in_array(
				'all',
				$mango_popup_data['page_id_display']
			) ? '1' : '';

			/** FOR WOOCOMMERCE */
			if ( isset( $_POST['mango_new_popup']['woocommerce'] ) ) {
				$woocommerce = array_map(
					'sanitize_text_field',
					wp_unslash( $_POST['mango_new_popup']['woocommerce'] )
				);

				$popup->meta['woocommerce'] = $woocommerce;
			}
			/** WOOCOMMERCE */

			$popup->template = new Template( $popup->meta['template'] );

			$saved = $popup->save();

			if ( $saved ) {
				$id       = $popup->id;
				$template = $popup->meta['template'];
				$position = $popup->meta['position'];

				wp_safe_redirect(
					admin_url(
						'admin.php?page=mango_popup&action=customize&id=' . $popup->id
						. '&template=' . $template . '&position=' . $position
					)
				);
				exit;
			}

			wp_safe_redirect( esc_url( admin_url( 'admin.php?page=mango_popup' ) ) );
			exit;
		}
	}

	/**
	 * Delete popup
	 */
	public function mango_popup_delete() {
		if ( ! check_ajax_referer( 'mango_popup_delete_nonce', 'nonce', false ) ) {
			wp_send_json(
				array(
					'success' => false,
				), 403
			);
			exit;
		}

		$id = isset( $_POST['id'] ) ? intval( wp_unslash( $_POST['id'] ) ) : 0;

		$this->check_permission( $id );

		$popup = new Popup( $id );

		// Permission & Capability & ID & Post is mango popup. Delete post.
		$deleted = wp_delete_post( $id );

		if ( $deleted ) {
			if ( file_exists( $popup->css_path ) ) {
				wp_delete_file( $popup->css_path );
			}

			$response    = array(
				'success' => true,
			);
			$status_code = 200;
		} else {
			$response    = array(
				'success' => false,
			);
			$status_code = 500;
		}

		wp_send_json( $response, $status_code );
		exit;
	}

	/**
	 * Toggle State of Popup
	 */
	public function toggle_state() {
		if ( ! check_ajax_referer( 'mango_popup_toggle_state_nonce', 'nonce', false ) ) {
			wp_send_json(
				array(
					'success' => false,
				), 403
			);
			exit;
		}

		$id = isset( $_POST['id'] ) ? intval( wp_unslash( $_POST['id'] ) ) : 0;

		$this->check_permission( $id );

		$current_status = isset( $_POST['status'] ) ? sanitize_text_field( wp_unslash( $_POST['status'] ) ) : '';

		$popup_args = array(
			'ID'          => $id,
			'post_status' => ( 'publish' === $current_status ) ? 'draft' : 'publish',
		);

		$updated = wp_update_post( $popup_args );

		if ( $updated ) {
			$response    = array(
				'success' => true,
			);
			$status_code = 200;
		} else {
			$response    = array(
				'success' => false,
			);
			$status_code = 500;
		}

		wp_send_json( $response, $status_code );
		exit;
	}

	/**
	 * Check Permission (Check admin, id and post type)
	 *
	 * @param integer $id Id of Popup.
	 */
	private function check_permission( $id ) {
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_send_json(
				array(
					'success' => false,
				), 403
			);
			exit;
		}

		if ( ! $id ) {
			wp_send_json(
				array(
					'success' => false,
				), 403
			);
			exit;
		}

		if ( get_post_type( $id ) !== 'mango_popup' ) {
			wp_send_json(
				array(
					'success' => false,
				), 403
			);
			exit;
		}
	}

	/**
	 * Ajax mailchimp get list
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
	 * Redirect to settings page
	 */
	public function mango_popup_redirect() {
		if ( get_option( 'mango_popup_plugin_redirect', false ) ) {
			delete_option( 'mango_popup_plugin_redirect' );

			if ( ! get_option( 'mango_popup_mailchimp_api_key', false ) ) {
				wp_safe_redirect( admin_url( 'admin.php?page=mango_popup_settings' ) );
			} else {
				wp_safe_redirect( admin_url( 'admin.php?page=mango_popup' ) );
			}
			exit;
		}
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
