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
 * Class WooCommerce
 *
 * @package Mango_Popup
 */
class WooCommerce {
	/**
	 * Get flash deals
	 *
	 * @param int $limit limit product.
	 * @return array
	 */
	public static function get_deal_by_day( $limit = 3 ) {
		$query_args = array(
			'posts_per_page' => $limit,
			'no_found_rows'  => 1,
			'post_status'    => 'publish',
			'post_type'      => 'product',
			'meta_query'     => WC()->query->get_meta_query(),
			'post__in'       => array_merge( array( 0 ), wc_get_product_ids_on_sale() ),
			'meta_key'       => '_sale_price_dates_to',
		);

		add_filter( 'posts_orderby', '\Mango_Popup\WooCommerce::get_deal_by_day_orderby' );

		$posts = new \WP_Query( $query_args );

		remove_filter( 'posts_orderby', '\Mango_Popup\WooCommerce::get_deal_by_day_orderby' );

		$products = array();

		foreach ( $posts->posts as $post ) {
			$product = new WooCommerce_Product( $post->ID );

			$products[] = $product->to_array();
		}

		return $products;
	}

	/**
	 * Get best deals
	 *
	 * @param int $limit product limit.
	 * @return array
	 */
	public static function get_deal_by_value( $limit = 3 ) {
		$query_args = array(
			'posts_per_page' => $limit,
			'no_found_rows'  => 1,
			'post_status'    => 'publish',
			'post_type'      => 'product',
			'meta_query'     => array(
				'relation'              => 'AND',
				'_sale_price_clause'    => array(
					'key'     => '_sale_price',
					'compare' => 'EXISTS',
				),
				'_regular_price_clause' => array(
					'key'     => '_regular_price',
					'value'   => 0,
					'type'    => 'decimal',
					'compare' => '>',
				),
			),
			'post__in'       => array_merge( array( 0 ), wc_get_product_ids_on_sale() ),
		);

		add_filter( 'posts_orderby', '\Mango_Popup\WooCommerce::get_deal_by_value_orderby' );

		$posts = new \WP_Query( $query_args );

		remove_filter( 'posts_orderby', '\Mango_Popup\WooCommerce::get_deal_by_value_orderby' );

		$products = array();

		foreach ( $posts->posts as $post ) {
			$product = new WooCommerce_Product( $post->ID );

			$products[] = $product->to_array();
		}

		return $products;
	}

	/**
	 * Order by filter
	 *
	 * @param string $order_by source order by.
	 * @return string
	 */
	public static function get_deal_by_day_orderby( $order_by ) {
		global $wpdb;

		$order_by = '(MAX(' . $wpdb->postmeta . '.meta_value)=\'\' OR ISNULL(MAX(' . $wpdb->postmeta . '.meta_value))), MAX(' . $wpdb->postmeta . '.meta_value) ASC, ' . $order_by;

		return $order_by;
	}

	/**
	 * Order by filter
	 *
	 * @param string $order_by source order by.
	 * @return string
	 */
	public static function get_deal_by_value_orderby( $order_by ) {
		global $wpdb;

		$order_by = '(MAX(' . $wpdb->postmeta . '.meta_value)/MAX(mt1.meta_value)) ASC, ' . $order_by;

		return $order_by;
	}
}
