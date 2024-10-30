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
 * Class Tip
 *
 * @package Mango_Popup\Popup
 */
class Tip {
	/**
	 * Tip constructor.
	 */
	public function __construct() {
		$this->tips = self::get_all_tips();
	}

	/**
	 * Get random tip
	 *
	 * @return mixed
	 */
	public function random() {
		return $this->tips[ rand( 0, count( $this->tips ) - 1 ) ];
	}

	/**
	 * Get all tips
	 *
	 * @return array
	 */
	public static function get_all_tips() {
		return array(
			'Do not use “Submit” on your form buttons, use valuable and actionable copy, such as “Download Now,” “Get Your Free Trial,” “Speak to an Expert,” or “Buy Now.”',
			'Do not hide your CTAs where no one can see them.',
			'Don\'t use the same or similar colors in your CTAs as the page\'s background color.',
			'Don\'t use teeny, tiny CTAs that no one will notice. Make them big and bold.',
			'Use content-related CTAs that  on the existing page, or align with the buyer’s interests and needs.',
			'Don\'t be too wordy, make sure CTAs are clear, simple, and uncluttered.',
			'Use compelling and actionable copy that conveys the value being offered.',
			'Don\'t use too many CTAs on one page, use only one primary and one secondary CTA only.',
			'Don\'t use the same CTAs for too long, experiment with and A/B test your CTAs to know what design, copy, and placement work best.',
			'Don\'t oversell and under-deliver, set the right expectations about what visitors will receive.',
			'Don\'t use branding as the only objective of your CTA, use CTAs to offer something of value.',
			'Don\'t use the same CTA for everyone because everyone is different! Create different CTAs for different personas and audiences. Personalization is one of the best ways to improve the effectiveness of your CTAs.',
			'The best calls to action use action words that let viewers know the specific action they should take next.',
			'Use urgency and scarcity to trigger FOMO (fear of missing out), making it more likely that the reader will take action.',
			'Make your customers feel safe by using some sentences like "you can unsubscribe anytime" or "No risk, no credit card required".',
			'Offer the chance to get more good stuff by becoming an insider.',
			'A good call to action relies on psychology, combining curiosity with anticipation.',
			'Avoid friction words that might make people hesitate (like buy, order, submit and so on).',
			'Keep Your Message Consistent. Consistency reduces confusion, creating a better use experience and making your conversion path seem much more streamlined.',
			'Make your CTA stand out From the Crowd!',
			'Always Be Testing, change Button Color, Messaging, Size or anything and compare results to find the best.',
		);
	}
}
