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

$page         = isset( $_GET['page'] ) ? sanitize_text_field( wp_unslash( $_GET['page'] ) ) : 'mango_popup';
$search_param = isset( $_REQUEST['s'] ) ? sanitize_text_field( wp_unslash( $_REQUEST['s'] ) ) : '';
?>

<div class="search-box">
	<form method="get" action="admin.php?page=mango_popup">
		<div class="form-group">
			<input type="hidden" name="page" value="<?php echo esc_attr( $page ); ?>"/>
			<input type="text" name="s" class="mango-search-input" placeholder="Search"
				   value="<?php echo esc_attr( $search_param ); ?>"/>
			<button class="mango-icon" type="submit"><i class="fa fa-search"></i></button>
		</div>
	</form>
</div>
