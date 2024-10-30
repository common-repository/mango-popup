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
 * Class AutoLoader
 *
 * @package Mango_Popup
 */
class AutoLoader {
	/**
	 * Classes map
	 *
	 * @var array
	 */
	private static $classes_map = array();

	/**
	 * Run autoload
	 */
	public static function run() {
		spl_autoload_register( array( __CLASS__, 'autoload' ) );
	}

	/**
	 * Class name
	 *
	 * @param string $class class path.
	 */
	public static function autoload( $class ) {
		if ( 0 !== strpos( $class, __NAMESPACE__ . '\\' ) ) {
			return;
		}

		$rel_class_name = preg_replace( '/^' . __NAMESPACE__ . '\\\/', '', $class );

		$final_class_name = __NAMESPACE__ . '\\' . $rel_class_name;

		if ( ! class_exists( $final_class_name ) ) {
			self::load_class( $rel_class_name );
		}
	}

	/**
	 * Load class
	 *
	 * @param string $relative_class_name class name.
	 *
	 * @throws \Exception Autoload not working.
	 */
	private static function load_class( $relative_class_name ) {
		if ( isset( self::$classes_map[ $relative_class_name ] ) ) {
			$filename = MANGO_POPUP_PATH . '/' . self::$classes_map[ $relative_class_name ];
		} else {
			$filename = preg_replace(
				array( '/_/', '/\\\/' ),
				array( '-', DIRECTORY_SEPARATOR ),
				$relative_class_name
			);

			/** For autoload with class- prefix */
			$filename                           = explode( DS, $filename );
			$filename[ count( $filename ) - 1 ] = 'class-' . $filename[ count( $filename ) - 1 ];
			$filename                           = implode( DS, $filename );

			$filename = strtolower( $filename );

			$filename = MANGO_POPUP_PATH . DS . 'includes' . DS . $filename . '.php';
		}

		if ( is_readable( $filename ) ) {
			require_once $filename;
		} else {
			throw new \Exception( 'Autoload not working with file not found ' . $filename );
		}
	}
}
