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
 * Class Package
 *
 * @package MangoPopup\Popup
 */
class Package {
	/**
	 * List template instance
	 *
	 * @var Template[]
	 */
	private $templates;
	/**
	 * Title of package
	 *
	 * @var string
	 */
	private $title;
	/**
	 * Package instance
	 *
	 * @var static
	 */
	public static $instance;

	/**
	 * Purchase url
	 *
	 * @var string
	 */
	private $purchase_url = null;
	/**
	 * Package Configs
	 *
	 * @var array
	 */
	public static $packages_config;

	/**
	 * Get package config array
	 *
	 * @return array
	 */
	public function get_packages_config() {
		return json_decode( file_get_contents( MANGO_POPUP_INCLUDES_PATH . 'packages-config.json' ) );
	}

	/**
	 * Package is locked
	 *
	 * @return boolean
	 */
	public function is_locked() {
		$templates = $this->get_templates();
		if ( empty( $templates ) ) {
			return false;
		}
		foreach ( $templates as $template ) {
			if ( ! $template->is_locked() ) {
				return false;
			}
		}

		return true;
	}

	/**
	 * Get list templates
	 *
	 * @return Template[]
	 */
	public function get_templates() {
		return $this->templates;
	}

	/**
	 * Set templates
	 *
	 * @param Template[] $templates List template url.
	 *
	 * @return Package
	 */
	public function set_templates( $templates ) {
		$this->templates = $templates;

		return $this;
	}

	/**
	 * Set purchase url
	 *
	 * @param string $purchase_url Purchase url.
	 *
	 * @return Package
	 */
	public function set_purchase_url( $purchase_url ) {
		$this->purchase_url = $purchase_url;

		return $this;
	}

	/**
	 * Get purchase url
	 *
	 * @return string|null
	 */
	public function get_purchase_url() {
		$title = $this->get_title();

		$packages_config = $this->get_packages_config();

		if ( isset( $packages_config->{$title} ) ) {
			if ( ! empty( $packages_config->{$title} ) ) {
				return $packages_config->{$title};
			}
		}

		return $this->purchase_url;
	}

	/**
	 * Get Package instance
	 *
	 * @return static
	 */
	public static function get_instance() {
		if ( Package::$instance instanceof Package ) {
			return Package::$instance;
		} else {
			Package::$instance = new Package();
			return Package::$instance;
		}
	}

	/**
	 * Get package title
	 *
	 * @return string
	 */
	public function get_title() {
		return $this->title;
	}

	/**
	 * Set Package Title
	 *
	 * @param string $title Title.
	 */
	public function set_title( $title ) {
		$this->title = $title;
	}
}
Package::$packages_config = Package::get_instance()->get_packages_config();
