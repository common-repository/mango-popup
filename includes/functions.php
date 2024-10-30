<?php
/**
 * MANGO POPUP PLUGIN
 *
 * @package     Mango Popup
 * @copyright   Copyright (c) 2017, Mango
 * @license     https://opensource.org/licenses/gpl-license GNU Public License
 * @since       1.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Sanitize all array
 *
 * @param array $array Array to sanitize.
 *
 * @return mixed
 */
function mango_popup_sanitize_array( &$array ) {
	foreach ( $array as &$value ) {
		if ( ! is_array( $value ) ) {
			// sanitize if value is not an array.
			$value = sanitize_text_field( $value );
		} else {
			// go inside this function again.
			mango_popup_sanitize_array( $value );
		}
	}

	return $array;
}
