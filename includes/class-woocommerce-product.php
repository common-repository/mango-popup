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

namespace Mango_Popup;

/**
 * Class WooCommerce_Product
 *
 * @package Mango_Popup
 */
class WooCommerce_Product {
	/**
	 * WooCommerce_Product constructor.
	 *
	 * @param int $id ID of product.
	 */
	public function __construct( $id ) {
		if ( ! $id ) {
			return;
		}

		$this->woocommerce_product = wc_get_product( $id );
	}

	/**
	 * Convert object to array
	 *
	 * @return array
	 */
	public function to_array() {
		return array(
			'id'                    => $this->woocommerce_product->get_id(),
			'title'                 => $this->woocommerce_product->get_title(),
			'price'                 => $this->woocommerce_product->get_price(),
			'regularPrice'          => $this->woocommerce_product->get_regular_price(),
			'salePrice'             => $this->woocommerce_product->get_sale_price(),
			'regularPriceFormatted' => wc_price( $this->woocommerce_product->get_regular_price() ),
			'salePriceFormatted'    => wc_price( $this->woocommerce_product->get_sale_price() ),
			'ratioSale'             => ( $this->woocommerce_product->get_regular_price() > 0 ) ? ( $this->woocommerce_product->get_sale_price() / $this->woocommerce_product->get_regular_price() ) : '',
			'ratioSaleView'         => ( $this->woocommerce_product->get_regular_price() > 0 ) ? number_format( 1 - $this->woocommerce_product->get_sale_price() / $this->woocommerce_product->get_regular_price(), 2 ) * 100 . '%' : '',
			'saleFrom'              => $this->woocommerce_product->get_date_on_sale_from(),
			'saleTo'                => $this->woocommerce_product->get_date_on_sale_to(),
			'image'                 => $this->woocommerce_product->get_image_id() ? wp_get_attachment_url( $this->woocommerce_product->get_image_id() ) : wc_placeholder_img_src(),
			'permalink'             => $this->woocommerce_product->get_permalink(),
		);
	}
}
