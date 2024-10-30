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

$import_result      = isset( $_GET['mango_popup_import_result'] ) ? sanitize_text_field( wp_unslash( $_GET['mango_popup_import_result'] ) ) : '';
$template_installed = isset( $_GET['mango_popup_template_installed'] ) ? intval( sanitize_text_field( wp_unslash( $_GET['mango_popup_template_installed'] ) ) ) : 0;
?>
<form
		method="post" enctype="multipart/form-data"
		class="wp-upload-form mango-popup-upload-form" action="
		<?php
		echo esc_url( admin_url( 'update.php?action=mango-popup-import-template' ) );
?>
">
	<div class="form-group">
		<h2 class="setting-border form-group-package"><?php esc_html_e( 'Install Template Package', 'mango_popup' ); ?></h2>
		<div class="form-package-wrap">
			<input type="file" name="templates_file" class="form-packgage-select" required>
			<button type="submit" class="mango-button mango-button--style14">Install Templates</button>
		</div>
	</div>
	<?php
	if ( 'fail' === $import_result ) {
		?>
		<div class="mango-notice mango-notice-error">
			<p><?php esc_html_e( 'The package could not be installed. The Package format is not valid', 'mango_popup' ); ?></p>
		</div>
		<?php
	} elseif ( 'success' === $import_result ) {
		?>
		<div class="mango-notice mango-notice-success">
			<p>
				<?php
					echo esc_html( $template_installed . __( ' template(s) has sucessfully been installed.', 'mango_popup' ) );
				?>
				Do you want to create a popup now?
				<a href="<?php echo esc_url( admin_url( 'admin.php?page=mango_popup_create' ) ); ?>">New popup</a>
			</p>
		</div>
		<?php
	}
	?>
	<div class="mango-setting-text">Please visit <a href="http://extensions.mango-wp.com/" target="_blank">Mango Popup home page</a> to get more template package and install <br/> them to your site by installer above.</div>
</form>
