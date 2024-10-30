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
 * Class Template
 *
 * @package MangoPopup\Popup
 */
class Template {
	const ERR_ZIP_INVALID = 78406;
	/**
	 * This is array contain all templates
	 *
	 * @var array
	 */
	protected static $templates;

	/**
	 * This is template slug
	 *
	 * @var string
	 */
	public $template;

	/**
	 * This array contain position
	 *
	 * @var array
	 */
	public $position;

	/**
	 * This array contain options (content of options.json)
	 *
	 * @var array
	 */
	public $options;

	/**
	 * HTML of template
	 *
	 * @var string
	 */
	public $html;

	/**
	 * CSS of Template
	 *
	 * @var string
	 */
	public $css;

	/**
	 * CSS of PreviewBox
	 *
	 * @var object
	 */
	public $preview_box_styles;

	/**
	 * Thumbnail url
	 *
	 * @var string
	 */
	public $thumbnail;

	/**
	 * Background image url
	 *
	 * @var string
	 */
	public $background_image;

	/**
	 * Package instance
	 *
	 * @var string
	 */
	public $package;

	/**
	 * Check is Locked
	 *
	 * @var bool is_locked
	 */
	public $is_locked;

	/**
	 * Background color of popup
	 *
	 * @var string
	 */
	public $background_color;

	/**
	 * Animation start of popup
	 *
	 * @var string
	 */
	public $animation_start;

	/**
	 * Template product html
	 *
	 * @var string
	 */
	public $product_template;

	/**
	 * Template constructor.
	 *
	 * @param string $template this is slug of template want to create instance.
	 */
	public function __construct( $template ) {
		$this->template = $template;

		$this->init_data_template();
	}

	/**
	 * Extract template zip
	 *
	 * @param string $path_to_zip Path to zip file.
	 *
	 * @return array
	 */
	public static function extract_templates_zip( $path_to_zip ) {
		global $wp_filesystem;

		$zip = new \ZipArchive();
		$zip->open( $path_to_zip );
		$n1_folder = array();
		$names     = array();
		for ( $increment = 0; $increment < $zip->numFiles; $increment++ ) {
			$name_index    = $zip->getNameIndex( $increment );
			$names[]       = $name_index;
			$name_segments = explode( '/', $name_index );
			$n1_folder[]   = $name_segments[0];
		}
		$templates = array_unique( $n1_folder );
		foreach ( $templates as $template ) {
			$style_css             = $zip->getFromName( $template . '/assets/style.css' );
			$template_html         = $zip->getFromName( $template . '/template.html' );
			$options_json          = $zip->getFromName( $template . '/options.json' );
			$product_template_html = $zip->getFromName( $template . '/product-template.html' );

			if ( ! $wp_filesystem->is_dir( MANGO_POPUP_UPLOAD_PATH ) ) {
				$wp_filesystem->mkdir( MANGO_POPUP_UPLOAD_PATH );
			}

			if ( ! $wp_filesystem->is_dir( MANGO_POPUP_UPLOAD_TEMPLATE_PATH ) ) {
				$wp_filesystem->mkdir( MANGO_POPUP_UPLOAD_TEMPLATE_PATH );
			}

			if ( ! $wp_filesystem->is_dir( MANGO_POPUP_UPLOAD_TEMPLATE_PATH . $template ) ) {
				$wp_filesystem->mkdir( MANGO_POPUP_UPLOAD_TEMPLATE_PATH . $template );
			}

			if ( ! $wp_filesystem->is_dir( MANGO_POPUP_UPLOAD_TEMPLATE_PATH . $template . DS . 'assets' ) ) {
				$wp_filesystem->mkdir( MANGO_POPUP_UPLOAD_TEMPLATE_PATH . $template . DS . 'assets' );
			}

			if ( ! $wp_filesystem->is_dir( MANGO_POPUP_UPLOAD_TEMPLATE_PATH . $template . DS . 'assets' . DS . 'img' ) ) {
				$wp_filesystem->mkdir( MANGO_POPUP_UPLOAD_TEMPLATE_PATH . $template . DS . 'assets' . DS . 'img' );
			}

			$wp_filesystem->put_contents( MANGO_POPUP_UPLOAD_TEMPLATE_PATH . $template . DS . 'assets' . DS . 'style.css', $style_css, FS_CHMOD_FILE );

			$wp_filesystem->put_contents( MANGO_POPUP_UPLOAD_TEMPLATE_PATH . $template . DS . 'template.html', $template_html, FS_CHMOD_FILE );

			$wp_filesystem->put_contents( MANGO_POPUP_UPLOAD_TEMPLATE_PATH . $template . DS . 'options.json', $options_json, FS_CHMOD_FILE );

			if ( $product_template_html ) {
				$wp_filesystem->put_contents( MANGO_POPUP_UPLOAD_TEMPLATE_PATH . $template . DS . 'product-template.html', $product_template_html, FS_CHMOD_FILE );
			}

			foreach ( $names as $name ) {
				if ( strpos( $name, $template . '/assets/img/' ) === 0 && $name !== $template . '/assets/img/' ) {
					$wp_filesystem->put_contents( MANGO_POPUP_UPLOAD_TEMPLATE_PATH . $name, $zip->getFromName( $name ), FS_CHMOD_FILE );
				}
			}
		}

		return $templates;
	}

	/**
	 * Group template by package
	 *
	 * @return Package[]
	 */
	public static function group_by_package() {
		$templates = static::get_all();
		/**
		 * List Package instance
		 *
		 * @var Package[] $packages
		 */
		$packages = array();

		/**
		 * List Template instance
		 *
		 * @var Template[] $templates
		 */
		foreach ( $templates as $template ) {
			if ( ! isset( $packages[ $template->package ] ) || ! $packages[ $template->package ] instanceof  Package ) {
				$packages[ $template->package ] = new Package();
				$packages[ $template->package ]->set_title( $template->package );
			}

			$templates   = $packages[ $template->package ]->get_templates();
			$templates[] = $template;
			$packages[ $template->package ]->set_templates( $templates );
		}

		return $packages;
	}
	/**
	 * Group Template b Package and Template Postiion
	 *
	 * @param string $position position slug.
	 * @return Package[]
	 */
	public static function group_by_package_where_position( $position ) {
		$package_groups = self::group_by_package();
		$package_groups = array_filter(
			$package_groups, function( Package $package_group ) use ( $position ) {
				$templates = $package_group->get_templates();
				$templates = array_filter(
					$templates, function( Template $template ) use ( $position ) {
						return $template->has_position( $position );
					}
				);
				$package_group->set_templates( $templates );

				return ! empty( $templates );
			}
		);

		return $package_groups;
	}

	/**
	 * Validate zip file
	 *
	 * @param string $path_to_zip Path to zip file.
	 *
	 * @throws \Exception Exception when failed.
	 * @return bool
	 */
	public static function validate_templates_zip( $path_to_zip ) {
		try {
			$zip        = new \ZipArchive();
			$zip_opened = $zip->open( $path_to_zip );
			if ( ! $zip_opened ) {
				throw new \Exception( 'Zip can not open', static::ERR_ZIP_INVALID );
			}
			$names     = array();
			$n1_folder = array();
			for ( $increment = 0; $increment < $zip->numFiles; $increment++ ) {
				$name_index    = $zip->getNameIndex( $increment );
				$names[]       = $name_index;
				$name_segments = explode( '/', $name_index );
				$n1_folder[]   = $name_segments[0];
			}
			$templates = array_unique( $n1_folder );
			foreach ( $templates as $template ) {
				$options_string = $zip->getFromName( $template . '/options.json' );
				if ( empty( $options_string ) ) {
					throw new \Exception( '', static::ERR_ZIP_INVALID );
				}
				if (
					$zip->getFromName( $template . '/options.json' ) === false ||
					$zip->getFromName( $template . '/assets/style.css' ) === false ||
					$zip->getFromName( $template . '/template.html' ) === false
				) {
					throw new \Exception( '', static::ERR_ZIP_INVALID );
				}

				$template_options = json_decode( $options_string );
				if ( ! is_object( $template_options ) ) {
					throw new \Exception( '', static::ERR_ZIP_INVALID );
				}

				if ( ! isset(
					$template_options->background_color,
					$template_options->animation,
					$template_options->max_width,
					$template_options->height,
					$template_options->positions,
					$template_options->background_image,
					$template_options->tags
				) ) {
					throw new \Exception( 'Template ' . $template . ' invalid', static::ERR_ZIP_INVALID );
				}

				if (
					! is_array( $template_options->positions ) ||
					! is_array( $template_options->tags )
				) {
					throw new \Exception( $template, static::ERR_ZIP_INVALID );
				}
			}
		} catch ( \Exception $exception ) {
			if ( WP_DEBUG ) {
				throw $exception;
			}
			return false;
		}
		return true;
	}

	/**
	 * Init template data from template slug
	 */
	public function init_data_template() {
		$template_path = $this->get_template_path();
		$template_url  = $this->get_template_url();

		$options_json = file_exists( $template_path . 'options.json' ) ?
			file_get_contents( $template_path . 'options.json' ) :
			'';

		$options = json_decode( $options_json, true );

		$html = file_exists( $template_path . 'template.html' ) ?
			file_get_contents( $template_path . 'template.html' ) :
			'';

		$css = file_exists( $template_path . 'assets' . DS . 'style.css' ) ?
			file_get_contents( $template_path . 'assets' . DS . 'style.css' ) :
			'';

		$thumbnail = file_exists( $template_path . 'assets' . DS . 'img' . DS . 'thumbnail.jpg' ) ?
			$template_url . 'assets/img/thumbnail.jpg' : '';

		$background_image = ( isset( $options['background_image'] )
							  && file_exists( $template_path . $options['background_image'] )
							  && ! is_dir( $template_path . $options['background_image'] ) ) ?
			( $template_url . $options['background_image'] ) : '';

		$background_color = ( isset( $options['background_color'] ) && 'transparent' !== $options['background_color'] ) ?
			$options['background_color'] : '';

		$animation_start = ( isset( $options['animation'] ) ) ? $options['animation'] : '';

		$is_locked       = ( isset( $options['isLocked'] ) ) ? $options['isLocked'] : false;
		$this->is_locked = $is_locked;

		$package       = ( isset( $options['package'] ) ) ? $options['package'] : 'Basic Templates';
		$this->package = $package;

		$product_template = file_exists( $template_path . 'product-template.html' ) ?
			file_get_contents( $template_path . 'product-template.html' ) :
			'';

		$this->slug             = $this->template;
		$this->options          = $options;
		$this->html             = $html;
		$this->css              = $css;
		$this->css              = str_replace( '{$MANGO_POPUP_TEMPLATE_URL}', MANGO_POPUP_TEMPLATE_URL . '/' . $this->template . '/', $this->css );
		$this->thumbnail        = $thumbnail;
		$this->position         = isset( $options['positions'] ) ? $options['positions'] : array();
		$this->background_image = $background_image;
		$this->background_color = $background_color;
		$this->animation_start  = $animation_start;
		$this->tags             = isset( $options['tags'] ) ? $options['tags'] : array();
		$this->product_template = $product_template;

		$preview_box_styles = new \stdClass();
		if ( isset( $options['preview_box_styles'] ) ) {
			if ( ! empty( $options['preview_box_styles'] ) ) {
				$this->set_preview_box_styles( $options['preview_box_styles'] );
			}
		}
		$this->set_preview_box_styles( $preview_box_styles );
	}

	/**
	 * Check position is locked
	 *
	 * @return string
	 */
	public function is_locked() {
		return $this->is_locked;
	}

	/**
	 * Check this template has position
	 *
	 * @param string $position Slug of position.
	 *
	 * @return bool
	 */
	public function has_position( $position ) {
		if ( ! $this->position || ! is_array( $this->position ) ) {
			return false;
		}

		return in_array( $position, $this->position, true );
	}

	/**
	 * Get all templates
	 *
	 * @return mixed
	 */
	public static function get_all() {
		if ( self::$templates ) {
			return self::$templates;
		}

		$template_folder = scandir( MANGO_POPUP_TEMPLATE_PATH );
		$templates       = array();

		foreach ( $template_folder as $template ) {
			if ( in_array( $template, array( '.', '..' ), true ) ) {
				continue;
			}

			if ( is_dir( MANGO_POPUP_TEMPLATE_PATH . $template ) ) {
				$templates[ $template ] = new Template( $template );
			}
		}

		self::$templates = $templates;

		return self::$templates;
	}

	/**
	 * Set html for template
	 *
	 * @param string $html content template html.
	 *
	 * @return Template
	 */
	public function set_html( $html ) {
		$this->html = $html;

		return $this;
	}

	/**
	 * Get content html of template
	 *
	 * @return string
	 */
	public function get_html() {
		$html         = $this->html;
		$assets_url   = $this->get_assets_url();
		$template_url = $this->get_template_url();

		$html = str_replace( '{$MANGO_POPUP_TEMPLATE_ASSETS_URL}', $assets_url, $html );
		$html = str_replace( '{$MANGO_POPUP_TEMPLATE_URL}', $template_url, $html );

		return $html;
	}

	/**
	 * Get Assets url of template
	 *
	 * @return string
	 */
	private function get_assets_url() {
		return $this->get_template_url() . '/assets';
	}

	/**
	 * Get template url
	 *
	 * @return string
	 */
	private function get_template_url() {
		if ( $this->is_installed() ) {
			return MANGO_POPUP_UPLOAD_TEMPLATE_URL . $this->template . '/';
		}

		return MANGO_POPUP_TEMPLATE_URL . $this->template . '/';
	}

	/**
	 * Get template path
	 *
	 * @return string
	 */
	private function get_template_path() {
		if ( $this->is_installed() ) {
			return MANGO_POPUP_UPLOAD_TEMPLATE_PATH . $this->template . DS;
		}

		return MANGO_POPUP_TEMPLATE_PATH . $this->template . DS;
	}

	/**
	 * Is Template installed
	 *
	 * @return bool
	 */
	private function is_installed() {
		if (
			file_exists( MANGO_POPUP_UPLOAD_TEMPLATE_PATH . $this->template . DS . 'options.json' ) &&
			file_exists( MANGO_POPUP_UPLOAD_TEMPLATE_PATH . $this->template . DS . 'template.html' ) &&
			file_exists( MANGO_POPUP_UPLOAD_TEMPLATE_PATH . $this->template . DS . 'assets' . DS . 'style.css' )
		) {
			return true;
		}

		return false;
	}

	/**
	 * Convert object template to array
	 *
	 * @return array
	 */
	public function to_array() {
		return array(
			'tags'             => $this->tags,
			'previewBoxStyles' => $this->get_preview_box_styles(),
			'html'             => $this->get_html(),
			'stylesheetsUrls'  => $this->get_stylesheets_urls(),
			'height'           => isset( $this->options['height'] ) ? $this->options['height'] : '',
			'maxWidth'         => isset( $this->options['max_width'] ) ? $this->options['max_width'] : '',
			'name'             => $this->template,
			'backgroundImage'  => $this->background_image,
			'backgroundColor'  => $this->background_color,
			'animationStart'   => $this->animation_start,
			'productTemplate'  => $this->product_template,
			'maxProduct'       => isset( $this->options['max_product'] ) ? $this->options['max_product'] : 3,
		);
	}

	/**
	 * Get stylesheets url
	 *
	 * @return array
	 */
	private function get_stylesheets_urls() {
		return array();
	}

	/**
	 * Get preview box styles
	 *
	 * @return object
	 */
	private function get_preview_box_styles() {
		return $this->preview_box_styles;
	}

	/**
	 * Set preview box style
	 *
	 * @param object $preview_box_styles Template object.
	 *
	 * @return Template
	 */
	public function set_preview_box_styles( $preview_box_styles ) {
		$this->preview_box_styles = $preview_box_styles;

		return $this;
	}
}
