<?php
/**
 * MANGO POPUP PLUGIN
 *
 * @package     Mango Popup
 * @copyright   Copyright (c) 2017, Mango
 * @license     https://opensource.org/licenses/gpl-license GNU Public License
 * @since       1.0
 */

namespace Mango_Popup\Popup;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
/**
 * Class Position
 *
 * @package Mango_Popup\Popup
 */
class Position {
	/**
	 * Singleton Array store Positions config
	 *
	 * @var array
	 */
	public static $positions_config;

	/**
	 * Singleton Array Store Position
	 *
	 * @var array
	 */
	public static $positions;

	/**
	 * Config of position (Slug, Title, Thumbnail)
	 *
	 * @var array
	 */
	public $config;

	/**
	 * Title of position
	 *
	 * @var string
	 */
	public $title;

	/**
	 * Slug of position
	 *
	 * @var string
	 */
	public $slug;

	/**
	 * Thumbnail url of position
	 *
	 * @var string
	 */
	public $thumbnail;

	/**
	 * Position constructor.
	 *
	 * @param string $slug Config default of position.
	 */
	public function __construct( $slug ) {
		$positions_config = self::get_positions_config();

		if ( isset( $positions_config[ $slug ] ) ) {
			$this->config = $positions_config[ $slug ];
		}

		$this->init();
	}

	/**
	 * Init function
	 *
	 * @return void
	 */
	public function init() {
		$this->slug            = isset( $this->config['slug'] ) ? $this->config['slug'] : '';
		$this->title           = isset( $this->config['title'] ) ? $this->config['title'] : '';
		$this->thumbnail       = $this->slug ?
			MANGO_POPUP_ASSETS_URL . 'admin/img/position/' . $this->slug . '.png' :
			'';
		$this->thumbnail_hover = $this->slug ?
			MANGO_POPUP_ASSETS_URL . 'admin/img/position/' . $this->slug . '_hover.png' :
			'';
	}

	/**
	 * Get all postiions
	 *
	 * @return array
	 */
	public static function get_all() {
		if ( self::$positions ) {
			return self::$positions;
		}

		$positions = array();

		$positions_config = self::get_positions_config();

		foreach ( $positions_config as $config ) {
			$positions[] = new Position( $config['slug'] );
		}

		self::$positions = $positions;

		return self::$positions;
	}

	/**
	 * Get Positions config
	 */
	public static function get_positions_config() {
		if ( self::$positions_config ) {
			return self::$positions_config;
		}

		self::$positions_config = array(
			'modal_popup'      => array(
				'slug'  => 'modal_popup',
				'title' => __( 'Modal Popup', 'mango_popup' ),
			),

			'modal_fullscreen' => array(
				'slug'  => 'modal_fullscreen',
				'title' => __( 'Modal Fullscreen', 'mango_popup' ),
			),

			/*
			'info_bar_top' => array(
				'slug'  => 'info_bar_top',
				'title' => __( 'Top Bar', 'mango_popup' ),
			),

			'info_bar_bottom' => array(
				'slug'  => 'info_bar_bottom',
				'title' => __( 'Bottom Bar', 'mango_popup' ),
			),
			*/

			'slide_in_left'    => array(
				'slug'  => 'slide_in_left',
				'title' => __( 'Left Corner Box', 'mango_popup' ),
			),

			'slide_in_right'   => array(
				'slug'  => 'slide_in_right',
				'title' => __( 'Right Corner Box', 'mango_popup' ),
			),

			/*
			'sticky_box_left' => array(
				'slug'  => 'sticky_box_left',
				'title' => __( 'Left Sticky Box', 'mango_popup' ),
			),

			'sticky_box_right' => array(
				'slug'  => 'sticky_box_right',
				'title' => __( 'Right Sticky Box', 'mango_popup' ),
			),
			*/
		);

		return self::$positions_config;
	}
}
