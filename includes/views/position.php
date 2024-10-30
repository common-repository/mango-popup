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

use Mango_Popup\Popup\Position;

$title     = __( 'Select a Position', 'mango_popup' );
$list_url  = admin_url( 'admin.php?page=mango_popup' );
$positions = Position::get_all();
?>

<div class="wrap mango-wrapper mango-popup-position mango-position">
	<div class="mango-position__header">
		<a class="mango-button mango-button--style9" href="<?php echo esc_url( $list_url ); ?>">
			<?php esc_html_e( 'Back', 'mango_popup' ); ?>
		</a>
		<h1 class="mango-position__name"><?php echo esc_html( $title ); ?></h1>
	</div>

	<div class="mango-position__body">
		<div class="container">
			<div class="row">
				<h3 class="mango-position__title">
					<?php esc_html_e( 'Select a position for your CTA Popup', 'mango_popup' ); ?>
				</h3>
				<?php foreach ( $positions as $position ) : ?>
					<div class="col-md-6 col-lg-3">
						<div class="mango-position__location">
							<div class="mango-position__image">
								<a href="
								<?php
								echo esc_url( admin_url( 'admin.php?page=mango_popup_create&action=template&position=' ) . $position->slug )
								?>
								">
									<img class="mango-position__thumbnail"
										 src="<?php echo esc_url( $position->thumbnail ); ?>"/>
									<img class="mango-position__thumbnail-hover"
										 src="<?php echo esc_url( $position->thumbnail_hover ); ?>"/>
								</a>
							</div>
							<div class="mango-position__infoLocation">
								<a href="
								<?php
								echo esc_url( admin_url( 'admin.php?page=mango_popup_create&action=template&position=' ) . $position->slug );
								?>
													">
									<?php echo esc_html( $position->title ); ?>
								</a>
							</div>
						</div>
					</div>
				<?php endforeach; ?>
			</div>

		</div>
	</div>

</div><!-- /.wrap -->

