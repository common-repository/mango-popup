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

use Mango_Popup\Popup\Template;

/**
 * Class Popup
 *
 * @package Mango_Popup
 */
class Popup {
	/**
	 * Popup ID
	 *
	 * @var integer
	 */
	public $id;

	/**
	 * Popup title
	 *
	 * @var string
	 */
	public $post_title;

	/**
	 * Content of Popup
	 *
	 * @var string
	 */
	public $post_content;

	/**
	 * Status of Popup
	 *
	 * @var string
	 */
	public $post_status;

	/**
	 * Meta of popup
	 *
	 * @var Array
	 */
	public $meta;

	/**
	 * Template of popup
	 *
	 * @var Template $template
	 */
	public $template;

	/**
	 * Path file css
	 *
	 * @var string $css_path
	 */
	public $css_path;

	/**
	 * Url file css
	 *
	 * @var string $css_url
	 */
	public $css_url;


	/**
	 * Popup constructor.
	 *
	 * @param integer $id ID of Popup.
	 */
	public function __construct( $id = 0 ) {
		$this->id = $id;

		if ( $id ) {
			$this->init_data();
		}
	}

	/**
	 * Init by page id
	 *
	 * @param string $page_id Page ID.
	 *
	 * @return array
	 */
	public static function init_by_page_id( $page_id ) {
		$meta_query = array(
			'relation' => 'OR',
			array(
				'key'   => 'mango_popup_page_id_display',
				'value' => $page_id,
			),
			array(
				'key'   => 'mango_popup_display_all_page',
				'value' => '1',
			),
		);

		if ( function_exists( 'is_home' ) && is_home() ) {
			array_push(
				$meta_query, array(
					'key'   => 'mango_popup_page_id_display',
					'value' => 'home',
				)
			);
		}

		if ( function_exists( 'is_single' ) && is_single() ) {
			array_push(
				$meta_query, array(
					'key'   => 'mango_popup_page_id_display',
					'value' => 'single',
				)
			);
		}

		if ( function_exists( 'is_category' ) && is_category() ) {
			array_push(
				$meta_query, array(
					'key'   => 'mango_popup_page_id_display',
					'value' => 'category',
				)
			);
		}

		if ( function_exists( 'is_tag' ) && is_tag() ) {
			array_push(
				$meta_query, array(
					'key'   => 'mango_popup_page_id_display',
					'value' => 'tag',
				)
			);
		}

		if ( MANGO_POPUP_HAVE_WC ) {
			if ( function_exists( 'is_product' ) && is_product() ) {
				array_push(
					$meta_query, array(
						'key'   => 'mango_popup_page_id_display',
						'value' => 'woocommerce_product',
					)
				);
			}

			if ( function_exists( 'is_product_category' ) && is_product_category() ) {
				array_push(
					$meta_query, array(
						'key'   => 'mango_popup_page_id_display',
						'value' => 'woocommerce_categories',
					)
				);
			}
		}

		$posts = get_posts(
			array(
				'posts_per_page' => 100,
				'post_type'      => 'mango_popup',
				'meta_query'     => $meta_query,
				'post_status'    => 'publish',
			)
		);

		$popups = array();

		foreach ( $posts as $post ) {
			$popup = new Popup( $post->ID );

			array_push( $popups, $popup );
		}

		return $popups;
	}

	/**
	 * Popup save
	 */
	public function save() {
		global $wp_filesystem;
		$data = array(
			'ID'           => $this->id,
			'post_title'   => $this->post_title,
			'post_content' => $this->post_content,
			'post_status'  => $this->post_status,
			'post_type'    => 'mango_popup',
		);

		// save data.
		if ( ! $this->id ) {
			$saved = $this->create_and_save( $data );
		} else {
			$saved = $this->update( $data );
		}

		$css = $this->style_with_id();

		$wp_upload_dir = wp_upload_dir( null, false );
		$upload_folder = $wp_upload_dir['basedir'] . DS . 'mango-popup' . DS;
		$css_path      = $upload_folder . 'popup-' . $this->id . '.css';

		if ( ! is_dir( $upload_folder ) ) {
			wp_mkdir_p( $upload_folder );
		}

		$wp_filesystem->put_contents(
			$css_path,
			sanitize_text_field( $css ),
			FS_CHMOD_FILE
		);

		if ( ! $saved ) {
			return false;
		}

		$this->update_popup_meta();

		return true;
	}

	/**
	 * Create and save post data
	 *
	 * @param array $data Post array.
	 */
	public function create_and_save( $data ) {
		$id = wp_insert_post( $data );

		if ( ! $id ) {
			return false;
		}

		$this->id = (int) $id;

		return true;
	}

	/**
	 * Update post with new data
	 *
	 * @param array $data Post array.
	 */
	public function update( $data ) {
		$updated = wp_update_post( $data );

		return (bool) $updated;
	}

	/**
	 * Update popup meta
	 */
	public function update_popup_meta() {

		delete_post_meta( $this->id, 'mango_popup_page_id_display' );

		foreach ( $this->meta as $key => $value ) {
			if ( ! is_array( $value ) || 'hide_on_devices' === $key || 'font_families_used' === $key || 'custom_css' === $key || 'woocommerce' === $key ) {
				update_post_meta( $this->id, 'mango_popup_' . $key, $value );
			} else {
				foreach ( $value as $child_value ) {

					if ( is_array( $child_value ) ) {
						continue;
					}

					if ( 'page_id_display' === $key ) {
						add_post_meta( $this->id, 'mango_popup_' . $key, $child_value );
					} else {
						update_post_meta( $this->id, 'mango_popup_' . $key, $child_value );
					}
				}
			}
		}

	}

	/**
	 * Init data popup by database
	 */
	public function init_data() {
		$popup = get_post( $this->id );

		if ( ! $popup ) {
			return false;
		}

		$this->post_title                      = $popup->post_title;
		$this->post_content                    = $popup->post_content;
		$this->post_status                     = $popup->post_status;
		$this->meta                            = array();
		$this->meta['background_image_url']    = get_post_meta( $this->id, 'mango_popup_background_image_url', true );
		$this->meta['background_color']        = get_post_meta( $this->id, 'mango_popup_background_color', true );
		$this->meta['animation_start']         = get_post_meta( $this->id, 'mango_popup_animation_start', true );
		$this->meta['page_id_display']         = get_post_meta( $this->id, 'mango_popup_page_id_display', false );
		$this->meta['when_popup_display']      = get_post_meta( $this->id, 'mango_popup_when_popup_display', true );
		$this->meta['how_often_popup_display'] = get_post_meta(
			$this->id, 'mango_popup_how_often_popup_display',
			true
		);
		$this->meta['hide_on_devices']         = get_post_meta(
			$this->id, 'mango_popup_hide_on_devices',
			true
		);
		$this->meta['mailchimp_target_id']     = get_post_meta( $this->id, 'mango_popup_mailchimp_target_id', true );
		$this->meta['target_link']             = get_post_meta( $this->id, 'mango_popup_target_link', true );
		$this->meta['position']                = get_post_meta( $this->id, 'mango_popup_position', true );
		$this->meta['max_width']               = get_post_meta( $this->id, 'mango_popup_max_width', true );
		$this->meta['font_families_used']      = get_post_meta( $this->id, 'mango_popup_font_families_used', true );

		$this->meta['woocommerce'] = get_post_meta( $this->id, 'mango_popup_woocommerce', true );

		$this->meta['custom_css'] = get_post_meta( $this->id, 'mango_popup_custom_css', true );

		$wp_upload_dir  = wp_upload_dir( null, false );
		$upload_url     = $wp_upload_dir['baseurl'] . '/mango-popup/';
		$upload_path    = $wp_upload_dir['basedir'] . DS . 'mango-popup' . DS;
		$filename       = 'popup-' . $this->id . '.css';
		$this->css_url  = $upload_url . $filename;
		$this->css_path = $upload_path . $filename;

		$template = get_post_meta( $this->id, 'mango_popup_template', true );

		$this->template = new Template( $template );
	}

	/**
	 * Init new Popup data from template
	 *
	 * @param string $slug Template slug.
	 */
	public function init_by_template( $slug ) {
		$this->template = new Template( $slug );

		$this->meta                            = array();
		$this->meta['background_image_url']    = $this->template->background_image;
		$this->meta['background_color']        = $this->template->background_color;
		$this->meta['animation_start']         = $this->template->animation_start;
		$this->meta['page_id_display']         = array( 'all' );
		$this->meta['when_popup_display']      = 'page-loaded';
		$this->meta['how_often_popup_display'] = 'always';
		$this->meta['target_link']             = '';
		$this->meta['max_width']               = isset( $this->template->options['max_width'] ) ? $this->template->options['max_width'] : '';
		$this->meta['custom_css']              = '';

		$this->meta['woocommerce'] = array(
			'product_detail_label' => isset( $this->template->options['product_detail_label'] ) ? $this->template->options['product_detail_label'] : '',
		);

		if ( isset( $this->template->css_url ) ) {
			if ( ! empty( $this->template->css_url ) ) {
				$this->css_url = $this->template->css_url;
			}
		}
	}

	/**
	 * Object to array for js use
	 *
	 * @return array
	 */
	public function to_array() {
		return array(
			'title'    => $this->post_title,
			'content'  => $this->post_content,
			'meta'     => array(
				'backgroundImageUrl'   => $this->meta['background_image_url'],
				'backgroundColor'      => $this->meta['background_color'],
				'animationStart'       => $this->meta['animation_start'],
				'pageIdDisplay'        => $this->meta['page_id_display'],
				'whenPopupDisplay'     => $this->meta['when_popup_display'],
				'howOftenPopupDisplay' => $this->meta['how_often_popup_display'],
				'targetLink'           => $this->meta['target_link'],
				'mailchimpTargetId'    => ( isset( $this->meta['mailchimp_target_id'] ) && $this->meta['mailchimp_target_id'] ) ? $this->meta['mailchimp_target_id'] : null,
				'maxWidth'             => $this->meta['max_width'],
				'hide_on_devices'      => isset( $this->meta['hide_on_devices'] ) ? $this->meta['hide_on_devices'] : array(),
				'customCSS'            => $this->meta['custom_css'],
				'woocommerce'          => isset( $this->meta['woocommerce'] ) ? $this->meta['woocommerce'] : array(),
			),
			'template' => $this->template->to_array(),
			'style'    => array(
				'styleWithId'         => $this->style_with_id(),
				'cssSelectorPrefixId' => $this->get_css_selector_prefix_id(),
				'defaultStyleWithId'  => $this->style_with_id( true ),
			),
		);
	}

	/**
	 * Render Popup to HTML
	 *
	 * @param bool $preview Is this for preview.
	 */
	public function render( $preview = false ) {
		$style = '';

		$animation_class = $this->meta['animation_start'] ? $this->meta['animation_start'] : '';

		$position       = $this->meta['position'];
		$position_class = $position ? 'mango-popup--' . str_replace( '_', '-', $position ) : '';

		$content = ( $this->post_content ) ? $this->post_content : '';

		$max_width = $this->meta['max_width'];
		$style    .= ( $max_width && 'modal_fullscreen' !== $this->meta['position'] ) ? 'max-width:' . (int) $max_width . 'px;' : '';

		$background_image = $this->meta['background_image_url'];
		$style           .= ( $background_image ) ? 'background-image: url(\'' . $background_image . '\');' : '';

		$background_color = isset( $this->meta['background_color'] ) ? $this->meta['background_color'] : '';

		$when_popup_display = $this->meta['when_popup_display'] ? $this->meta['when_popup_display'] : '';
		$how_often          = $this->meta['how_often_popup_display'] ? $this->meta['how_often_popup_display'] : '';

		$target_link = $this->meta['target_link'] ? $this->meta['target_link'] : '';

		$nonce_open   = wp_create_nonce( 'mango_popup_after_open' );
		$nonce_submit = wp_create_nonce( 'mango_popup_submit' );

		$woocommerce = ! empty( $this->meta['woocommerce'] ) ? $this->meta['woocommerce'] : array();

		$template = $this->template;

		$args = array(
			'id'                 => $this->id,
			'animation'          => $animation_class,
			'position'           => $position_class,
			'content'            => $content,
			'style'              => $style,
			'when_popup_display' => $when_popup_display,
			'how_often'          => $how_often,
			'nonce_open'         => $nonce_open,
			'nonce_submit'       => $nonce_submit,
			'preview'            => $preview,
			'target_link'        => $target_link,
			'background_color'   => $background_color,
			'woocommerce'        => $woocommerce,
			'template'           => $template,
		);
		$this->render_html( $args );
	}

	/**
	 * Render HTML
	 *
	 * @param array $data Data to fill html.
	 */
	public function render_html( $data ) {
		echo '<div class="mango-popup mango-popup--default ' . esc_attr( $data['position'] );
		echo '" id="mango-popup-' . esc_attr( $data['id'] );
		echo '" data-id="' . esc_attr( $data['id'] );
		echo '" data-when-popup-display="' . esc_attr( $data['when_popup_display'] );
		echo '" data-animation="' . esc_attr( $data['animation'] );
		echo '" data-nonce-open="' . esc_attr( $data['nonce_open'] );
		echo '" data-nonce-submit="' . esc_attr( $data['nonce_submit'] );
		echo '" data-how-often="' . esc_attr( $data['how_often'] );
		echo '" data-preview="' . esc_attr( $data['preview'] );
		echo '" data-target-link="' . esc_attr( $data['target_link'] );
		echo '" data-background-color="' . esc_attr( $data['background_color'] );
		echo '" data-template="' . esc_attr( $data['template']->template );

		/** WOOCOMMERCE */
		if ( ! empty( $data['woocommerce']['hotdeal'] ) ) {
			echo '" data-woocommerce-hotdeal="' . esc_attr( $data['woocommerce']['hotdeal'] );
		}

		if ( ! empty( $data['woocommerce']['product_detail_label'] ) ) {
			echo '" data-woocommerce-label="' . esc_attr( $data['woocommerce']['product_detail_label'] );
		}

		if ( ! empty( $data['template']->options['max_product'] ) ) {
			echo '" data-woocommerce-max-product="' . esc_attr( $data['template']->options['max_product'] );
		}
		/** END WOOCOMMERCE */

		echo '">
			<div class="mango-popup__tb">
				<div class="mango-popup__tb-cell">	
					<div class="mango-popup__content mango-popup-container" style="' . esc_attr( $data['style'] ) . '">
						<div class="mango-popup__header"> 
							<span class="mango-popup__close mango-duration">&times;</span> 
						</div>
						<div class="mango-popup__body mango-popup-reset-css">';
		$allowed_html = self::list_allowed_html();
		add_filter(
			'safe_style_css', function ( $safe_style_css ) {
				$safe_style_css[] = 'text-decoration-line';

				return $safe_style_css;
			}
		);
		echo wp_kses( $data['content'], $allowed_html );
		echo '</div>
					</div>
					<div class="mango-popup__overlay"></div>
				</div>
			</div>
		</div>';
	}

	/**
	 * Render shortcode
	 *
	 * @return string
	 */
	public function render_shortcode() {
		$html  = '';
		$style = '';

		$background_image = $this->meta['background_image_url'];
		$style           .= ( $background_image ) ? 'background-image: url(\'' . $background_image . '\');' : '';
		$background_color = isset( $this->meta['background_color'] ) ? $this->meta['background_color'] : '';

		$max_width = $this->meta['max_width'];
		$style    .= ( $max_width && 'modal_fullscreen' !== $this->meta['position'] ) ? 'max-width:' . (int) $max_width . 'px;' : '';

		$nonce_submit = wp_create_nonce( 'mango_popup_submit' );

		$html        .= '<div class="mango-popup mango-popup--default mango-popup--shortcode active" 
					data-id="' . esc_attr( $this->id ) . '" id="mango-popup-' . esc_attr( $this->id ) . '" data-background-color="' . $background_color . '" 
					data-target-link="' . esc_attr( $this->meta['target_link'] ) . '" data-nonce-submit="' . esc_attr( $nonce_submit ) . '">';
		$html        .= '<div class="mango-popup__tb">
				<div class="mango-popup__tb-cell">	
					<div class="mango-popup__content mango-popup-container" style="' . esc_attr( $style ) . '">
						<div class="mango-popup__body mango-popup-reset-css">';
		$allowed_html = self::list_allowed_html();
		add_filter(
			'safe_style_css', function ( $safe_style_css ) {
				$safe_style_css[] = 'text-decoration-line';

				return $safe_style_css;
			}
		);
		$html .= wp_kses( $this->post_content, $allowed_html );
		$html .= '</div></div></div></div>';
		$html .= '</div>';

		return $html;
	}

	/**
	 * Auto add prefix with id to css
	 *
	 * @param boolean $default Is default style.
	 *
	 * @return mixed|string
	 */
	public function style_with_id( $default = false ) {
		$css = $this->template->css;

		$custom_css = ! empty( $this->meta['custom_css'] ) ? $this->meta['custom_css'] : array();

		if ( ! $default ) {
			$css .= $this->get_css_content_from_array( $custom_css );
		}

		// auto add prefix.
		$selector_prefix = $this->get_css_selector_prefix();
		$css             = preg_replace( '/^(?!@media)(.*?)({|,)(\s+|)$/m', $selector_prefix . ' $1$2', $css );
		$css             = preg_replace( '/\h+/', ' ', $css );
		$template_url    = MANGO_POPUP_TEMPLATE_URL . '/' . $this->template->template . '/';
		$css             = str_replace( '{$MANGO_POPUP_TEMPLATE_URL}', $template_url, $css );
		$css             = str_replace( $selector_prefix . ' .mango-popup__content{', '.mango-popup__content{', $css );
		$css             = str_replace( ' .mango-popup--slide-in-right', '.mango-popup--slide-in-right', $css );
		$css             = str_replace(
			$selector_prefix . ' .mango-popup-customizer-body',
			'.mango-popup-customizer-body ' . $selector_prefix, $css
		);

		return $css;
	}

	/**
	 * Get css selector prefix
	 *
	 * @return string
	 */
	private function get_css_selector_prefix() {
		$css_selector_prefix_id = $this->get_css_selector_prefix_id();

		return 'body #' . $css_selector_prefix_id;
	}

	/**
	 * Get css selector prefix id
	 *
	 * @return string
	 */
	private function get_css_selector_prefix_id() {
		return 'mango-popup-' . $this->id;
	}

	/**
	 * Check this popup can render to frontend
	 *
	 * @return bool
	 */
	public function can_render() {
		$check_how_often    = $this->check_how_often();
		$is_devices_display = $this->is_devices_display();
		$deals_available    = $this->check_deal();

		$is_can_render = $check_how_often && $is_devices_display && $deals_available;
		return $is_can_render;
	}

	/**
	 * Check how often popup display
	 *
	 * @return bool
	 */
	private function check_how_often() {
		if ( ! $this->meta['how_often_popup_display'] || 'always' === $this->meta['how_often_popup_display'] ) {
			return true; // ok.
		}

		if ( $this->meta['how_often_popup_display'] && 'once-a-session' === $this->meta['how_often_popup_display'] ) {
			return $this->check_how_often_session();
		} else {
			return $this->check_how_often_cookie();
		}
	}

	/**
	 * Check with session
	 *
	 * @return bool
	 */
	private function check_how_often_session() {
		$json_session = isset( $_COOKIE['mango-popup-data-session'] ) ? sanitize_text_field( wp_unslash( $_COOKIE['mango-popup-data-session'] ) ) : '{}';
		$data_session = json_decode( $json_session, true );

		if ( ! isset( $data_session[ $this->id ] ) || ! $data_session[ $this->id ] ) {
			return true; // ok first time to see.
		}

		return false;
	}

	/**
	 * Check with cookie
	 *
	 * @return bool
	 */
	private function check_how_often_cookie() {
		$json_cookie = isset( $_COOKIE['mango-popup-data'] ) ? sanitize_text_field( wp_unslash( $_COOKIE['mango-popup-data'] ) ) : '{}';
		$data_cookie = json_decode( $json_cookie, true );

		if ( ! isset( $data_cookie[ $this->id ] ) ) {
			return true; // ok first time to see.
		}

		$timezone = isset( $data_cookie['time_zone'] ) ? $data_cookie['time_zone'] : 'UTC';

		$now = new \DateTime();
		$now->setTimezone( new \DateTimeZone( $timezone ) );

		$viewed_at = isset( $data_cookie[ $this->id ] ) ? $data_cookie[ $this->id ] : 0;
		$viewed    = new \DateTime();
		$viewed->setTimestamp( $viewed_at );
		$viewed->setTimezone( new \DateTimeZone( $timezone ) );

		$now_year    = $now->format( 'Y' );
		$viewed_year = $viewed->format( 'Y' );

		$is_valid = false;

		switch ( $this->meta['how_often_popup_display'] ) {
			case 'once-a-day':
				if ( $now_year > $viewed_year ) {
					$is_valid = true;
					break;
				}

				$now_day    = $now->format( 'z' );
				$viewed_day = $viewed->format( 'z' );

				if ( $now_day > $viewed_day ) {
					$is_valid = true;
				}
				break;

			case 'once-a-week':
				if ( $now_year > $viewed_year ) {
					$is_valid = true;
					break;
				}
				$now_week    = $now->format( 'W' );
				$viewed_week = $viewed->format( 'W' );

				if ( $now_week > $viewed_week ) {
					$is_valid = true;
				}

				break;

			case 'only-one-time':
			default:
				break;
		}

		return $is_valid;
	}

	/**
	 * List allowed html of popup content
	 */
	public static function list_allowed_html() {
		$list_allowed = wp_kses_allowed_html( 'post' );

		if ( ! array_key_exists( 'input', $list_allowed ) ) {
			$list_allowed['input'] = array(
				'class'       => true,
				'id'          => true,
				'name'        => true,
				'required'    => true,
				'autofocus'   => true,
				'value'       => true,
				'placeholder' => true,
				'type'        => true,
				'disabled'    => true,
				'style'       => true,
			);
		}

		foreach ( array( 'span' ) as $tag_allowed_style_attribute ) {
			if ( ! array_key_exists( $tag_allowed_style_attribute, $list_allowed ) ) {
				$list_allowed[ $tag_allowed_style_attribute ] = array(
					'style' => true,
				);
			} else {
				$list_allowed[ $tag_allowed_style_attribute ]['style'] = true;
			}
		}

		if ( ! array_key_exists( 'select', $list_allowed ) ) {
			$list_allowed['select'] = array(
				'class'       => true,
				'id'          => true,
				'name'        => true,
				'required'    => true,
				'multiple'    => true,
				'value'       => true,
				'placeholder' => true,
				'type'        => true,
				'disabled'    => true,
				'style'       => true,
			);
		}

		if ( ! array_key_exists( 'option', $list_allowed ) ) {
			$list_allowed['select'] = array(
				'class' => true,
				'id'    => true,
				'value' => true,
				'style' => true,
			);
		}

		if ( array_key_exists( 'button', $list_allowed ) ) {
			$list_allowed['button']['data-href'] = true;
		}

		return $list_allowed;
	}

	/**
	 * Get css content from array css
	 *
	 * @param array $custom_css Custom css array.
	 * @return string
	 */
	private function get_css_content_from_array( $custom_css ) {
		$css = '';

		foreach ( $custom_css as $id => $css_array ) {
			if ( empty( $css_array ) ) {
				continue;
			}

			$css .= PHP_EOL . '#' . $id . ' { ' . PHP_EOL;

			foreach ( $css_array as $property => $value ) {
				if ( empty( $value ) || empty( $property ) ) {
					continue;
				}

				$css .= $property . ':' . $value . '; ';
			}

			$css .= PHP_EOL . '}' . PHP_EOL;
		}

		return $css;
	}

	/**
	 * Is best deal
	 *
	 * @return bool
	 */
	public function is_auto_fill_deals_data_sorted_by_best_price() {
		return ! empty( $this->meta['woocommerce']['hotdeal'] ) && 'best' === $this->meta['woocommerce']['hotdeal'];
	}

	/**
	 * Is flash deals
	 *
	 * @return bool
	 */
	public function is_auto_fill_deals_data_sorted_latest() {
		return ! empty( $this->meta['woocommerce']['hotdeal'] ) && 'day' === $this->meta['woocommerce']['hotdeal'];
	}

	/**
	 * Is devices display
	 *
	 * @return bool
	 */
	private function is_devices_display() {
		$mobile_detector = new Mobile_Detect();
		if ( ! isset( $this->meta['hide_on_devices'] ) ) {
			return true;
		}
		$hide_on_devices = $this->meta['hide_on_devices'];
		if ( ! is_array( $hide_on_devices ) ) {
			return true;
		}

		$hidden_in_mobile  = in_array( 'mobile', $hide_on_devices );
		$hidden_in_tablet  = in_array( 'tablet', $hide_on_devices );
		$hidden_in_desktop = in_array( 'desktop', $hide_on_devices );

		$is_mobile  = $mobile_detector->isMobile() && ! $mobile_detector->isTablet();
		$is_tablet  = $mobile_detector->isTablet() && ! $is_mobile;// case of tablet, mobile is true.
		$is_desktop = ! $is_mobile && ! $is_tablet;

		if ( $hidden_in_mobile && $is_mobile ) {
			return false;
		}
		if ( $hidden_in_tablet && $is_tablet ) {
			return false;
		}
		if ( $hidden_in_desktop && $is_desktop ) {
			return false;
		}

		return true;
	}

	/**
	 * Check deal
	 *
	 * @return boolean
	 */
	private function check_deal() {
		if ( $this->is_auto_fill_deals_data_sorted_by_best_price() ) {
			$best_sorted = WooCommerce::get_deal_by_value();

			if ( ! $best_sorted ) {
				return false;
			}
		}

		if ( $this->is_auto_fill_deals_data_sorted_latest() ) {
			$latest_deals = WooCommerce::get_deal_by_day();

			if ( ! $latest_deals ) {
				return false;
			}
		}

		return true;
	}
}
