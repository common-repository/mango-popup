<?php
/**
 *
 * This is view file of select Popup Template
 * at Create new Popup & Edit Popup
 *
 * @package MangoPopup
 */

use Mango_Popup\Popup\Template;

$title = __( 'Select a Template', 'mango_popup' );

$position_url     = admin_url( 'admin.php?page=mango_popup_create&action=position' );
$current_position = isset( $_GET['position'] ) ? sanitize_text_field( wp_unslash( $_GET['position'] ) ) : '';
$total_template   = 0;
$no_template_text = __( "Don't have any Template with this position", 'mango_popup' );

$packages = Template::group_by_package_where_position( $current_position );
?>

<div class="wrap mango-wrapper mango-popup-template mango-template">
	<div class="mango-template__header">
		<a class="mango-button mango-button--style9" href="<?php echo esc_url( $position_url ); ?>">
			<?php
			esc_html_e( 'Back', 'mango_popup' );
			?>
		</a>
		<h1 class="mango-template__name"><?php echo esc_html( $title ); ?></h1>
	</div>

	<div class="mango-template__body">
		<div class="mango-template__wrapper">

			<?php foreach ( $packages as $key => $package ) : ?>
				<div class="mango-template__package-section">
					<h2 class="mango-template__package-name"><?php echo esc_html( $key ); ?></h2>
					<?php if ( $package->is_locked() ) : ?>
						<a
								href="<?php echo esc_url( $package->get_purchase_url() ); ?>"
								class="mango-template__package-button-unlock"
								target="_blank"
						>
							<img src="<?php echo esc_url( MANGO_POPUP_ASSETS_URL . 'admin/img/icons/lock.png' ); ?>" alt="">
							<?php esc_html_e( 'Unlock All', 'mango_popup' ); ?>
						</a>
					<?php endif; ?>
				</div>

				<?php foreach ( $package->get_templates() as $template ) : ?>
					<?php if ( $template->has_position( $current_position ) ) : ?>
						<div class="mango-template__item <?php echo ( $template->is_locked() ? 'mango-popup-template-locked' : '' ); ?>">
							<div class="mango-template__item-select">
								<img src="<?php echo esc_url( $template->thumbnail ); ?>"/>
								<a class="mango-button mango-button--style5 mango-template__btn-select"
								   href="<?php echo esc_url( admin_url( 'admin.php?page=mango_popup_create&action=customize&position=' . $current_position . '&template=' . $template->slug ) ); ?>"> Select
								</a>
								<?php if ( ! empty( $template->tags ) ) : ?>
								<ul class="mango-template__tags">
									<?php foreach ( $template->tags as $tag ) : ?>
										<li class="mango-template__tag"><?php echo esc_html( $tag ); ?></li>
									<?php endforeach; ?>
								</ul>
								<?php endif; ?>
								<div class="mango-popup__overlay"></div>
							</div>
						</div>
						<?php $total_template ++; ?>
					<?php endif; ?>
				<?php endforeach; ?>
			<?php endforeach; ?>

			<?php if ( ! $total_template ) : ?>
				<h3><?php echo esc_html( $no_template_text ); ?></h3>
			<?php endif; ?>
		</div>

	</div><!-- /.wrap -->

