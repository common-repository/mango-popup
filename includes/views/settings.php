<?php
/**
 * MANGO POPUP PLUGIN
 *
 * @package     Mango Popup
 * @copyright   Copyright (c) 2017, Mango
 * @license     https://opensource.org/licenses/gpl-license GNU Public License
 * @since       1.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use Mango_Popup\Settings\Install_Template;
use Mango_Popup\Settings\Mailchimp_Settings;

$title = __( 'Mango Popup Settings', 'mango_popup' );

?>

<div class="wrap mango-wrapper mango-popup-settings mango-settings">

	<?php
	/** Mailchimp setting views **/
	$mailchimp_settings = new Mailchimp_Settings();
	$mailchimp_settings->display_settings_page();

	/** Install temlpate views */
	$install_template = new Install_Template();
	$install_template->display_settings_page();
	?>
</div>
