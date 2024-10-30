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

use Mango_Popup\Popup\Position;
use Mango_Popup\Popup\Template;
use WP_List_Table;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
/**
 * Class PopupListTable
 *
 * @package Mango_Popup\Settings
 */
class Popup_List_Table extends WP_List_Table {
	/**
	 * Define Columns of Table
	 *
	 * @return array
	 */
	public function get_columns() {
		$columns = array(
			'title'        => __( 'Title', 'mango_popup' ),
			'shortcode'    => __( 'Shortcode', 'mango_popup' ),
			'position'     => __( 'Position', 'mango_popup' ),
			'views'        => __( 'Views', 'mango_popup' ),
			'clicks'       => __( 'Clicks', 'mango_popup' ),
			'conversions'  => __( 'Conversions', 'mango_popup' ),
			'state'        => __( 'State', 'mango_popup' ),
			'created_date' => __( 'Created Date', 'mango_popup' ),
		);

		return $columns;
	}

	/**
	 * Prepare Item
	 *
	 * @return void
	 */
	public function prepare_items() {
		$columns               = $this->get_columns();
		$hidden                = array();
		$sortable              = array();
		$this->_column_headers = array( $columns, $hidden, $sortable );

		$search_params = isset( $_GET['s'] ) ? sanitize_text_field( wp_unslash( $_GET['s'] ) ) : '';
		$count_post    = wp_count_posts( 'mango_popup' );
		$total_items   = (int) $count_post->publish + (int) $count_post->draft + (int) $count_post->private;
		$item_per_page = 10;
		$page          = isset( $_GET['paged'] ) ? (int) $_GET['paged'] : 1;
		$offset        = $item_per_page * ( $page - 1 );

		$this->set_pagination_args(
			array(
				'total_items' => $total_items,
				'per_page'    => $item_per_page,
			)
		);

		$get_popups_args = array(
			'posts_per_page'   => $item_per_page,
			'offset'           => $offset,
			'post_status'      => array( 'publish', 'private', 'draft' ),
			'post_type'        => 'mango_popup',
			's'                => $search_params,
			'order_by'         => 'date',
			'suppress_filters' => false,
		);

		// get popups list.
		$this->items = get_posts( $get_popups_args );

		$this->setup_post_meta();
	}

	/**
	 * Setup post meta for popup
	 *
	 * @return void
	 */
	public function setup_post_meta() {
		foreach ( $this->items as $item ) {
			$meta                = array();
			$meta['views']       = (int) get_post_meta( $item->ID, 'mango_popup_views', true );
			$meta['clicks']      = (int) get_post_meta( $item->ID, 'mango_popup_clicks', true );
			$meta['conversions'] = ( ( $meta['views'] > 0 ) ? number_format(
				$meta['clicks'] / $meta['views'],
				4
			) * 100 : 0 ) . '%';
			$meta['position']    = implode( ',', get_post_meta( $item->ID, 'mango_popup_position', false ) );
			$meta['template']    = get_post_meta( $item->ID, 'mango_popup_template', true );

			$item->meta     = $meta;
			$item->template = new Template( $meta['template'] );
		}
	}

	/**
	 * Column title
	 *
	 * @param mixed $item popup item.
	 */
	public function column_title( $item ) {
		echo '<span class="mango-popup-title">';
		echo ( $item->post_title ) ? esc_html( $item->post_title ) : '<span class="mango-popup-title-no-title">(no title)</span>';
		echo '</span>';
		echo '<span class="mango-popup-title-tools-wrapper">';
		echo '<a href="';
		echo esc_url( admin_url( 'admin.php?page=mango_popup&template=' . $item->meta['template'] . '&position=' . $item->meta['position'] . '&action=customize&id=' . $item->ID ) );
		echo '" class="mango-button mango-button--style2">Edit</a>';

		$preview_url = home_url( '?mango_popup_id=' . $item->ID . '&mango_popup_action=preview' );
		echo '<a class="mango-button mango-button--style3" target="_blank" href="' . esc_url( $preview_url ) . '">';

		esc_html_e( 'Preview', 'mango_popup' );

		echo '</a>';
		echo '<a href="javascript:void(0);" data-title="' . esc_attr( $item->post_title ) . '" data-id="' . esc_attr( $item->ID ) . '" data-nonce="' . esc_attr( wp_create_nonce( 'mango_popup_delete_nonce' ) ) . '" class="mango-button mango-button--style10 mango-popup-delete-button" ng-click="delete($event)">';
		esc_html_e( 'Delete', 'mango_popup' );
		echo '</a></span>';
	}

	/**
	 * Column Shortcode
	 *
	 * @param mixed $item popup item.
	 */
	public function column_shortcode( $item ) {
		$tags = isset( $item->template->options['tags'] ) ? $item->template->options['tags'] : array();
		if ( in_array( 'Shortcode Supported', $tags ) && ( 'modal_fullscreen' !== $item->meta['position'] ) ) {
			echo '[mangopopup id="' . esc_html( $item->ID ) . '"]';
		} else {
			esc_html_e( 'Not Supported', 'mango_popup' );
		}
	}

	/**
	 * Column position
	 *
	 * @param mixed $item popup item.
	 */
	public function column_position( $item ) {
		$position = new Position( $item->meta['position'] );

		echo esc_html( $position->title );
	}

	/**
	 * Column views
	 *
	 * @param mixed $item popup item.
	 */
	public function column_views( $item ) {
		echo esc_html( $item->meta['views'] );
	}

	/**
	 * Column clicks
	 *
	 * @param mixed $item popup item.
	 */
	public function column_clicks( $item ) {
		echo esc_html( $item->meta['clicks'] );
	}

	/**
	 * Column conversions
	 *
	 * @param mixed $item popup item.
	 */
	public function column_conversions( $item ) {
		echo esc_html( $item->meta['conversions'] );
	}

	/**
	 * Column state
	 *
	 * @param mixed $item popup item.
	 */
	public function column_state( $item ) {
		echo '<i data-nonce="' . esc_attr( wp_create_nonce( 'mango_popup_toggle_state_nonce' ) ) . '" 
				ng-click="toggleState($event)"
				data-id="' . esc_attr( $item->ID ) . '"
				data-status="' . esc_attr( $item->post_status ) . '"
				class="icon-toggle-state fa fa-fw ' . ( ( 'publish' === $item->post_status ) ? esc_attr( 'icon-publish fa-circle-o' ) : esc_attr( 'icon-draft fa-times' ) ) . '"></i>';
	}

	/**
	 * Column created date
	 *
	 * @param mixed $item item id.
	 */
	public function column_created_date( $item ) {
		echo get_the_date( '', $item->ID );
	}

	/**
	 * Render search box views
	 *
	 * @param string $text text of search button.
	 * @param string $input_id input id.
	 */
	public function search_box( $text = '', $input_id = '' ) {
		require MANGO_POPUP_VIEWS_PATH . 'search-box.php';
	}
}
