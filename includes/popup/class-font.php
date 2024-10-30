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
 * Class Font
 *
 * @package Mango_Popup\Popup
 */
class Font {
	/**
	 * Load fonts from list
	 *
	 * @param array $fonts Array fonts name.
	 */
	public static function get_url( $fonts ) {
		if ( ! $fonts ) {
			return false;
		}

		$fonts_supported = self::get_google_fonts();

		$url    = '//fonts.googleapis.com/css?';
		$family = '';
		foreach ( $fonts as $font ) {
			if ( ! in_array( $font, $fonts_supported ) ) {
				continue;
			}
			$family .= urlencode( $font . ':300,regular,700|' );
		}

		if ( ! $family ) {
			return false;
		}

		$family = 'family=' . trim( $family, '%7C' );

		$subset = '&subset=' . urlencode( 'latin,all' );

		return $url . $family . $subset;
	}

	/**
	 * Get all Fonts available.
	 *
	 * @return array
	 */
	public static function get_google_fonts() {
		return array(
			'Arial',
			'Alegreya Sans',
			'Alegreya',
			'Anonymous Pro',
			'Archivo Narrow',
			'Arvo',
			'BioRhyme',
			'Bitter',
			'Cabin',
			'Cardo',
			'Chivo',
			'Cormorant',
			'Crimson Text',
			'Domine',
			'Eczar',
			'Fira Sans',
			'Gentium Basic',
			'Inconsolata',
			'Karla',
			'Lato',
			'Libre Baskerville',
			'Libre Franklin',
			'Lora',
			'Merriweather',
			'Montserrat',
			'Neuton',
			'Old Standard TT',
			'Open Sans',
			'PT Sans',
			'PT Serif',
			'Pacifico',
			'Playfair Display',
			'Lobster',
			'Poppins',
			'Raleway',
			'Roboto Slab',
			'Roboto',
			'Rubik',
			'Source Sans Pro',
			'Source Serif Pro',
			'Space Mono',
			'Spectral',
			'Work Sans',
		);
	}
}
