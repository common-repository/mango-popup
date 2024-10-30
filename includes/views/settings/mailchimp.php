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
$name  = 'mango_popup_mailchimp_api_key';
$value = get_option( $name );
$title = __( 'Mango Popup Settings', 'mango_popup' );
?>
<form name="form" action="options.php" method="post">

	<div class="mango-group-header">
		<h1 class="mango-setting-title"><?php echo esc_html( $title ); ?></h1>
		<?php wp_nonce_field( 'options-options' ); ?>
		<script>
			setTimeout(function(){
				jQuery('.mango-popup-settings-saved-wrapper').hide();
			}, 3e3);
		</script>
			<div class="mango-popup-button-save-changes submit">
				<div class="mango-popup-settings-saved-wrapper">
					<?php
					$settings_errors = get_settings_errors();
					if ( ! empty( $settings_errors ) ) {
					?>
					<div class="mango-popup-settings-saved">
						<span class="mango-popup-settings-icon"></span>
						<?php
						foreach ( $settings_errors as $setting_error ) {
							echo esc_html( $setting_error['message'] );
						}
						?>
					</div>
						<?php } ?>
				</div>
				<?php submit_button( __( 'Save Changes' ), 'primary', 'Update' ); ?>
			</div>
	</div>

	<div class="form-group">
	<h2 class="setting-border form-group-mailchimp"><?php esc_html_e( 'Mailchimp API Settings', 'mango_popup' ); ?></h2>
	<input type="hidden" name="action" value="update"/>
	<input type="hidden" name="option_page" value="options"/>
	<table class="form-table form-table-setting">
		<tr>
			<td>
				<label for="<?php echo esc_attr( $name ); ?>">
					<?php echo 'API Key'; ?>
				</label>
			</td>

		</tr>
		<tr>
			<td>
				<input class="regular-text " type="text" name="<?php echo esc_attr( $name ); ?>"
					   id="<?php echo esc_attr( $name ); ?>"
					   value="<?php echo esc_attr( $value ); ?>"/>
				<p class="help mango-setting-text">
					The API key for connecting with your MailChimp account.
					<a class="mango-setting-guide" target="_blank" href="https://admin.mailchimp.com/account/api-key-popup/">
						Get your API key here.
					</a>
				</p>
			</td>
		</tr>
	</table>
	<input type="hidden" name="page_options" value="mango_popup_mailchimp_api_key"/>
</div>
</form>
