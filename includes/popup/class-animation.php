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
 * Class Animation
 *
 * @package Mango_Popup\Popup
 */
class Animation {
	/**
	 * Animation constructor.
	 *
	 * @param string $slug slug of animation.
	 */
	public function __construct( $slug ) {
		$this->slug = $slug;
	}

	/**
	 * Get list animation
	 *
	 * @return array
	 */
	public static function get_list_animation() {
		$animation_in = array(
			'bounce',
			'bounceIn',
			'bounceInDown',
			'bounceInLeft',
			'bounceInRight',
			'bounceInUp',
			'fadeIn',
			'fadeInDown',
			'fadeInDownBig',
			'fadeInLeft',
			'fadeInLeftBig',
			'fadeInRight',
			'fadeInRightBig',
			'fadeInUp',
			'fadeInUpBig',
			'flash',
			'flipInX',
			'flipInY',
			'headShake',
			'jello',
			'jackInTheBox',
			'lightSpeedIn',
			'pulse',
			'rollIn',
			'rotateIn',
			'rotateInDownLeft',
			'rotateInDownRight',
			'rotateInUpLeft',
			'rotateInUpRight',
			'rubberBand',
			'shake',
			'slideInDown',
			'slideInLeft',
			'slideInRight',
			'slideInUp',
			'swing',
			'tada',
			'wobble',
			'zoomIn',
			'zoomInDown',
			'zoomInLeft',
			'zoomInRight',
			'zoomInUp',
		);

		$animation_out = array(
			'bounceOut',
			'bounceOutDown',
			'bounceOutLeft',
			'bounceOutRight',
			'bounceOutUp',
			'fadeOut',
			'fadeOutDown',
			'fadeOutDownBig',
			'fadeOutLeft',
			'fadeOutLeftBig',
			'fadeOutRight',
			'fadeOutRightBig',
			'fadeOutUp',
			'fadeOutUpBig',
			'flipOutX',
			'flipOutY',
			'hinge',
			'lightSpeedOut',
			'rollOut',
			'rotateOut',
			'rotateOutDownLeft',
			'rotateOutDownRight',
			'rotateOutUpLeft',
			'rotateOutUpRight',
			'slideOutDown',
			'slideOutLeft',
			'slideOutRight',
			'slideOutUp',
			'zoomOut',
			'zoomOutDown',
			'zoomOutLeft',
			'zoomOutRight',
			'zoomOutUp',
		);

		return $animation_in;
	}
}
