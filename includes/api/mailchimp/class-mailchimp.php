<?php
/**
 * MANGO POPUP PLUGIN
 *
 * @package     Mango Popup
 * @copyright   Copyright (c) 2017, Mango
 * @license     https://opensource.org/licenses/gpl-license GNU Public License
 * @since       1.0
 */

namespace Mango_Popup\Api\Mailchimp;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Mailchimp
 *
 * @package Mango_Popup\Api\Mailchimp
 */
class Mailchimp {
	/**
	 * Version of mailchimp
	 *
	 * @var string
	 */
	protected $version = '3.0';

	/**
	 * Data center
	 *
	 * @var string
	 */
	protected $data_center = 'us17';

	/**
	 * API KEY
	 *
	 * @var null
	 */
	protected $api_key = null;

	/**
	 * AUTH TYPE
	 *
	 * @var string
	 */
	protected $auth_type = 'key';

	/**
	 * MailChimpService constructor.
	 *
	 * @param null $api_key API KEY.
	 */
	public function __construct( $api_key = null ) {
		if ( ! empty( $api_key ) ) {
			$this->api_key = $api_key;
		}
		$api_key_segments = explode( '-', $api_key );
		if ( isset( $api_key_segments[1] ) ) {
			$this->data_center = $api_key_segments[1];
		}
	}

	/**
	 * Get lists
	 *
	 * @return array
	 */
	public function get_lists() {
		$url  = $this->url( 'lists' );
		$args = $this->get_http_args();

		$response = wp_remote_get( $url, $args );
		$result   = wp_remote_retrieve_body( $response );
		return json_decode( $result );
	}

	/**
	 * Check this email subscribed or not
	 *
	 * @param string $list_id list id.
	 * @param string $email email address.
	 * @return array|bool
	 */
	public function member( $list_id, $email ) {
		$hash     = md5( strtolower( trim( $email ) ) );
		$url      = $this->url( "lists/$list_id/members/$hash" );
		$args     = $this->get_http_args();
		$response = wp_remote_get( $url, $args );

		if ( 200 === wp_remote_retrieve_response_code( $response ) ) {
			$result = wp_remote_retrieve_body( $response );
			return json_decode( $result );
		} else {
			return false;
		}
	}

	/**
	 * Subscribe
	 *
	 * @param string $list_id list id.
	 * @param string $email email address.
	 * @param bool   $subscribed subscribed or not.
	 * @param array  $merge_fields merged fields.
	 *
	 * @return array|bool
	 */
	public function subscribe( $list_id, $email, $subscribed = true, $merge_fields = array() ) {
		$data = array(
			'email_type'    => 'html',
			'email_address' => $email,
			'status'        => ( true === $subscribed ? 'subscribed' : 'pending' ),
			'merge_fields'  => $merge_fields,
		);

		if ( empty( $data['merge_fields'] ) ) {
			unset( $data['merge_fields'] );
		}
		$url            = $this->url( "lists/$list_id/members" );
		$args           = $this->get_http_args();
		$args['body']   = json_encode( $data );
		$args['method'] = 'POST';
		$response       = wp_remote_post( $url, $args );
		$result         = wp_remote_retrieve_body( $response );
		return json_decode( $result );
	}

	/**
	 * Update subscriber
	 *
	 * @param string $list_id list id.
	 * @param string $email email address.
	 * @param bool   $subscribed subscribed or not.
	 * @param array  $merge_fields merged fields.
	 *
	 * @return array|bool
	 */
	public function update( $list_id, $email, $subscribed = true, $merge_fields = array() ) {
		$hash = md5( strtolower( trim( $email ) ) );

		$data = array(
			'email_address' => $email,
			'status'        => true === $subscribed ? 'subscribed' : 'unsubscribed',
			'merge_fields'  => $merge_fields,
		);

		if ( empty( $data['merge_fields'] ) ) {
			unset( $data['merge_fields'] );
		}

		$url            = $this->url( "lists/$list_id/members/$hash" );
		$args           = $this->get_http_args();
		$args['method'] = 'PATCH';
		$args['body']   = json_encode( $data );

		$response = wp_remote_post( $url, $args );
		$result   = wp_remote_retrieve_body( $response );
		return json_decode( $result );
	}

	/**
	 * Get url
	 *
	 * @param string $extra extra.
	 * @return string
	 */
	protected function url( $extra = '' ) {
		$url = "https://{$this->data_center}.api.mailchimp.com/{$this->version}/";

		if ( ! empty( $extra ) ) {
			$url .= $extra;
		}

		return $url;
	}

	/**
	 * Get http args for query
	 *
	 * @return array
	 */
	private function get_http_args() {
		$username  = 'mailchimp';
		$password  = $this->api_key;
		$auth      = base64_encode( $username . ':' . $password );
		$http_args = array(
			'headers' => array(
				'Authorization' => "Basic $auth",
			),
		);

		return $http_args;
	}
}
