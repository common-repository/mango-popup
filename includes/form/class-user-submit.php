<?php
/**
 * MANGO POPUP PLUGIN
 *
 * @package     Mango Popup
 * @copyright   Copyright (c) 2017, Mango
 * @license     https://opensource.org/licenses/gpl-license GNU Public License
 * @since       1.0
 */

namespace Mango_Popup\Form;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class User_Submit
 *
 * @package Mango_Popup
 */
class User_Submit {
	/**
	 * Handle user submit
	 *
	 * @return bool
	 */
	public function handle() {
		$options = get_option( 'mailchimp-woocommerce', array() );

		// load up the user.
		$api_key = isset( $options['mailchimp_api_key'] ) ? $options['mailchimp_api_key'] : false;
		$list_id = isset( $options['mailchimp_list'] ) ? $options['mailchimp_list'] : false;

		// we need a valid api key and list id to continue.
		if ( empty( $api_key ) || empty( $list_id ) ) {
			mailchimp_log( 'member.sync', "Invalid Api Key or ListID :: {$user->user_email}" );
			return false;
		}

		$api = new \Mango_Popup\Api\Mailchimp\Mailchimp( $api_key );

		$merge_vars = array();

		$fn = trim( $user->first_name );
		$ln = trim( $user->last_name );

		if ( ! empty( $fn ) ) {
			$merge_vars['FNAME'] = $fn;
		}
		if ( ! empty( $ln ) ) {
			$merge_vars['LNAME'] = $ln;
		}

		try {
			// see if we have a member.
			$api->member( $list_id, $user->user_email );

			// ok let's update this member.
			$api->update( $list_id, $user->user_email, null, $merge_vars );
		} catch ( \Exception $e ) {

			// if we have a 404 not found, we can create the member.
			if ( $e->getCode() == 404 ) {

				try {
					$api->subscribe( $list_id, $user->user_email, null, $merge_vars );
				} catch ( \Exception $e ) {
					// this exception not handled.
					$e->getMessage();
				}

				return false;
			}
		}

		return false;
	}

}
