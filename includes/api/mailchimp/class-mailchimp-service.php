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
 * Class Mailchimp_Service
 *
 * @package Mango_Popup\Api\Mailchimp
 */
class Mailchimp_Service {
	/**
	 * Mailchimp Instance
	 *
	 * @var Mailchimp
	 */
	private $api;

	/**
	 * Mailchimp_Service constructor.
	 */
	public function __construct() {
		$api_key   = get_option( 'mango_popup_mailchimp_api_key' );
		$this->api = new Mailchimp( $api_key );
	}

	/**
	 * Service subscribe
	 *
	 * @param string $list_id list id to add subscriber.
	 * @param string $email email address to subscribe.
	 * @param bool   $subscribed subscribed true or false.
	 * @param array  $merge_vars merged fields.
	 *
	 * @return bool
	 */
	public function subscribe( $list_id, $email, $subscribed = true, array $merge_vars = array() ) {
		return $this->api->subscribe( $list_id, $email, $subscribed, $merge_vars );
	}

	/**
	 * Get list
	 *
	 * @return array
	 */
	public function get_lists() {
		return $this->api->get_lists()->lists;
	}

	/**
	 * Check member
	 *
	 * @param string $list_id list id to check member.
	 * @param string $email email address.
	 *
	 * @return array|bool
	 */
	public function member( $list_id, $email ) {
		return $this->api->member( $list_id, $email );
	}

	/**
	 * Update Contact Subscribed
	 *
	 * @param string $list_id list id.
	 * @param string $email email need to update.
	 * @param bool   $subscribed subscribed true or false.
	 * @param array  $merge_fields merged fields.
	 *
	 * @return array|bool
	 */
	public function update( $list_id, $email, $subscribed = true, $merge_fields = array() ) {
		return $this->api->update( $list_id, $email, $subscribed, $merge_fields );
	}
}
