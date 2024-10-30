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

$list_table = new Mango_Popup\Popup_List_Table();
$list_table->prepare_items();
$title            = __( 'Mango Popup', 'mango_popup' );
$create_popup_url = admin_url( 'admin.php?page=mango_popup_create' );
?>

<div class="wrap mango-list mango-wrapper" ng-app="MangoPopup.Admin.List">
	<div class="row mango-headerList">
		<div class="col-md-8">
			<div class="mango-headerList__left">
				<h1 class="mango-headerList__title"><?php echo esc_html( $title ); ?></h1>
				<a class="mango-button mango-button--style1"
				   href="<?php echo esc_url( $create_popup_url ); ?>">
					<?php esc_html_e( 'Create New', 'mango_popup' ); ?>
				</a>
			</div>
		</div>
		<div class="col-md-4">
			<div class="mango-headerList__right">
				<a class="mango-link" href="<?php echo esc_url( admin_url( 'admin.php?page=mango_popup_settings' ) ); ?>">
					<i class="fa fa-cog"></i>
					<?php esc_html_e( 'Settings', 'mango_popup' ); ?>
				</a>
				<?php $list_table->search_box(); ?>
			</div>
		</div>
	</div>

	<div class="table-list-popup" ng-controller="MangoPopupList">
		<?php $list_table->display(); ?>
	</div>

	<div id="mango-popup-delete-confirm">
		<?php esc_html_e( 'Do you want to delete this popup? This popup will be permanently deleted.', 'mango_popup' ); ?>
	</div>
</div><!-- /.wrap -->

