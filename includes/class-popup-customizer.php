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
class Popup_Customizer {
	/**
	 * Display edit page with type
	 *
	 * @param string $type type of page create or edit.
	 */
	public function display_customizer_page( $type ) {
		$id            = isset( $_GET['id'] ) ? intval( $_GET['id'] ) : 0;
		$template_slug = isset( $_GET['template'] ) ? sanitize_text_field( wp_unslash( $_GET['template'] ) ) : '';
		$popup         = null;
		$template      = null;

		if ( 'create' == $type && $template_slug ) {
			$template = new Template( $template_slug );
			$popup    = new Popup();
			$popup->init_by_template( $template->template );
		} elseif ( 'edit' == $type && $id > 0 && get_post_type( $id ) == 'mango_popup' ) {
			$popup    = new Popup( $id );
			$template = $popup->template;
		}

		if ( MANGO_POPUP_HAVE_WC ) {
			$products_deal_by_day   = WooCommerce::get_deal_by_day();
			$products_deal_by_value = WooCommerce::get_deal_by_value();
		}

		wp_localize_script(
			'mango-popup-admin-js', 'popupCustomizeOptions', array(
				'popup'                => $popup->to_array(),
				// this is customize for create page popup data = template data.
				'renderCSS'            => MANGO_POPUP_ASSETS_URL . 'frontend/css/main.css',
				'adminAssetsDirectory' => MANGO_POPUP_ASSETS_URL . 'admin',
				'template'             => $template->to_array(),
				'createPageUrl'        => admin_url( 'admin.php?page=mango_popup_create' ),
				'adminStylesheetUrl'   => MANGO_POPUP_ASSETS_URL . 'admin/css/main.css',
				'animateCSS'           => MANGO_POPUP_VENDOR_URL . 'animate.css/animate.min.css',
				'cssUrl'               => $popup->css_url,
				'position'             => isset( $_GET['position'] ) ? sanitize_text_field( wp_unslash( $_GET['position'] ) ) : '',
				'styleWithId'          => $popup->style_with_id(),
				'woocommerce'          => ( MANGO_POPUP_HAVE_WC ) ? array(
					'products' => array(
						'deal_by_day'   => $products_deal_by_day,
						'deal_by_value' => $products_deal_by_value,
					),
				) : array(),
			)
		);
		require_once MANGO_POPUP_VIEWS_PATH . 'customize.php';
	}
}
