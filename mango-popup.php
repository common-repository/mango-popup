<?php
/**
 * Plugin Name: Mango Popup
 * Description: Mango Popup is awesome tool to create effectively popups in order to quickly grow your email list. With many responsive and well-designed template provided, you are no longer to worry about technical issues but only focus on your content marketing ideas.
 * Version: 1.2.2
 * Author: Mango Popup
 * Author URI: http://extensions.mango-wp.com
 * Text Domain: mango_popup
 * @package     Mango Popup
 * @copyright   Copyright (c) 2017, Mango
 * @license     https://opensource.org/licenses/gpl-license GNU Public License
 * @since       1.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'MANGO_POPUP__FILE__', __FILE__ );
if ( ! defined( 'DS' ) ) {
	define( 'DS', DIRECTORY_SEPARATOR );
}

if ( ! defined( 'MANGO_POPUP_VERSION' ) ) {
	define( 'MANGO_POPUP_VERSION', '1.2.2' );
}

if ( ! defined( 'MANGO_POPUP_PLUGIN_BASE' ) ) {
	define( 'MANGO_POPUP_PLUGIN_BASE', plugin_basename( MANGO_POPUP__FILE__ ) );
}

if ( ! defined( 'MANGO_POPUP_PATH' ) ) {
	define( 'MANGO_POPUP_PATH', plugin_dir_path( MANGO_POPUP__FILE__ ) );
}

if ( ! defined( 'MANGO_POPUP_URL' ) ) {
	define( 'MANGO_POPUP_URL', plugins_url( '/', MANGO_POPUP__FILE__ ) );
}

if ( ! defined( 'MANGO_POPUP_MODULES_PATH' ) ) {
	define( 'MANGO_POPUP_MODULES_PATH', plugin_dir_path( MANGO_POPUP__FILE__ ) . '/modules' );
}

if ( ! defined( 'MANGO_POPUP_ASSETS_URL' ) ) {
	define( 'MANGO_POPUP_ASSETS_URL', MANGO_POPUP_URL . 'assets/' );
}

if ( ! defined( 'MANGO_POPUP_VENDOR_URL' ) ) {
	define( 'MANGO_POPUP_VENDOR_URL', MANGO_POPUP_URL . 'vendor/' );
}

if ( ! defined( 'MANGO_POPUP_TEMPLATE_PATH' ) ) {
	define( 'MANGO_POPUP_TEMPLATE_PATH', MANGO_POPUP_PATH . 'template' . DS );
}

if ( ! defined( 'MANGO_POPUP_TEMPLATE_URL' ) ) {
	define( 'MANGO_POPUP_TEMPLATE_URL', MANGO_POPUP_URL . 'template/' );
}

if ( ! defined ( 'MANGO_POPUP_UPLOAD_PATH' ) ) {
    $wp_upload_dir = wp_upload_dir( null, false );
    define( 'MANGO_POPUP_UPLOAD_PATH' , $wp_upload_dir['basedir'] . DS . 'mango-popup' . DS );
}

if ( ! defined ( 'MANGO_POPUP_UPLOAD_URL' ) ) {
    $wp_upload_dir = wp_upload_dir( null, false );
    define( 'MANGO_POPUP_UPLOAD_URL' , $wp_upload_dir['baseurl'] . '/mango-popup/' );
}

if ( ! defined ( 'MANGO_POPUP_UPLOAD_TEMPLATE_PATH' ) ) {
    define( 'MANGO_POPUP_UPLOAD_TEMPLATE_PATH', MANGO_POPUP_UPLOAD_PATH . 'template' . DS );
}

if ( ! defined ( 'MANGO_POPUP_UPLOAD_TEMPLATE_URL' ) ) {
    define( 'MANGO_POPUP_UPLOAD_TEMPLATE_URL' , MANGO_POPUP_UPLOAD_URL . 'template/' );
}

if ( ! defined( 'MANGO_POPUP_INCLUDES_PATH' ) ) {
	define( 'MANGO_POPUP_INCLUDES_PATH', MANGO_POPUP_PATH . 'includes' . DS );
}

if ( ! defined( 'MANGO_POPUP_VIEWS_PATH' ) ) {
	define( 'MANGO_POPUP_VIEWS_PATH', MANGO_POPUP_INCLUDES_PATH . 'views' . DS );
}


register_activation_hook( __FILE__, 'mango_popup_active' );

function mango_popup_active() {
	add_option( 'mango_popup_plugin_redirect', true );
}

require_once MANGO_POPUP_PATH . 'includes' . DS . 'functions.php';
require_once MANGO_POPUP_PATH . 'includes' . DS . 'class-plugin.php';
