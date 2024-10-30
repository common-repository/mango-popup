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

use Mango_Popup\Popup\Animation;
use Mango_Popup\Popup\Position;
use Mango_Popup\Popup\Template;
use Mango_Popup\Popup\Tip;

$title          = __( 'Customize design', 'mango_popup' );
$position       = isset( $_GET['position'] ) ? sanitize_text_field( wp_unslash( $_GET['position'] ) ) : '';
$list_animation = Animation::get_list_animation();
$pages          = get_pages();
$template       = isset( $_GET['template'] ) ? sanitize_text_field( wp_unslash( $_GET['template'] ) ) : '';
$id             = isset( $_GET['id'] ) ? intval( $_GET['id'] ) : 0;
$page_slug      = isset( $_GET['page'] ) ? sanitize_text_field( wp_unslash( $_GET['page'] ) ) : '';
$is_edit_page   = ( 'mango_popup' == $page_slug ) ? true : false;
$tip            = new Tip();

$template_class = new Template( $template );
$positions      = $template_class->position;
?>
<div ng-app="MangoPopup.Admin">
<div ng-controller="MangoPopupCustomize">
	<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" ng-submit="formSubmit($event)">
		<?php wp_nonce_field( 'mango_popup_save', 'mango_popup_save_nonce' ); ?>
		<div class="wrap mango-wrapper mango-customize">
			<input type="hidden" name="action" value="mango_popup_save">
			<input type="hidden" name="mango_new_popup[template]" value="<?php echo esc_attr( $template ); ?>">
			<input type="hidden" name="mango_new_popup[id]" value="<?php echo esc_attr( $id ); ?>">
			<input type="hidden" name="mango_new_popup[font_families_used]" ng-value="fontsUsed|json">
			<input type="hidden" name="mango_new_popup[post_content]" ng-value="getBase64EncodedHtml">
			<input type="hidden" name="mango_new_popup[max_width]" ng-value="popup.meta.maxWidth">
			<input type="hidden" name="mango_new_popup[custom_css]" ng-value="popup.meta.customCSS|json">
			<div class="mango-customize__header">
				<div class="row">
					<div class="col-lg-4">
						<div style="margin-top: 10px; vertical-align: middle;">
							<?php if ( ! $is_edit_page ) : ?>
								<a class="mango-button mango-button--style9" href="
					<?php
					if ( $position ) {
						echo esc_url( admin_url( 'admin.php?page=mango_popup_create&action=template&position=' . $position ) );
					} else {
						echo esc_url( admin_url( 'admin.php?page=mango_popup_create' ) );
					}
								?>
					">
									<?php esc_html_e( 'Back', 'mango_popup' ); ?>
								</a>
							<?php endif; ?>

							<h2 class="mango-customize__name">
								<?php echo esc_html( $title ); ?>
							</h2>
						</div>
					</div>

					<div class="col-lg-4">
						<div class="mango-customize__title-popup">
							<input type="text"
								   placeholder="<?php esc_attr_e( 'Enter Popup Title here...', 'mango_popup' ); ?>"
								   name="mango_new_popup[popup_title]"
								   ng-value="popup.title">
						</div>
					</div>

					<div class="col-lg-4" style="overflow: hidden">
						<div class="mango-customize__btn">
							<a href="<?php echo esc_url( admin_url( 'admin.php?page=mango_popup' ) ); ?>" class="mango-button mango-button--style6" tabindex="-1">
								<?php esc_html_e( 'Cancel', 'mango_popup' ); ?>
							</a>
							<button type="submit" class="mango-button mango-button--style7">
								<?php
								esc_html_e( 'Save', 'mango_popup' );
								?>
								<i class="fa fa-check"></i></button>
						</div>

						<div ng-cloak class="mango-popup-customize-errors-wrapper" ng-if="!isEmpty(formErrors)">
							<div class="mango-popup-customize-errors">
								<i class="fa fa-fw fa-times-circle"></i> <?php esc_html_e( 'Please enter valid values.', 'mango_popup' ); ?>
							</div>
						</div>

					</div>
				</div>
			</div>
			<div class="mango-customize__body">
				<div class="mango-customize__option">
					<div class="design">
						<h3 class="customize-border mango-customize__title">
							<?php
							esc_html_e( 'Design Options', 'mango_popup' );
							?>
						</h3>
						<div class="content">
							<div class="row">
								<div class="col-md-6">
									<span><?php esc_html_e( 'Background Image', 'mango_popup' ); ?></span>
									<div class="image-wrap">
										<h4>No image</h4>
										<div class="image" ng-style="{'background-image': backgroundImageUrlCSS}">
											<img ng-src="{{ popup.meta.backgroundImageUrl }}" alt="">
										</div>
									</div>
									<a href="javascript:void(0);"
									   class="mango-button mango-button--style8"
									   ng-click="selectMedia()">
										<?php esc_html_e( 'Select Image', 'mango_popup' ); ?>
									</a>
									<a href="javascript:void(0)" ng-click="deleteBackgroundImage()" class="delete"><i
												class="fa fa-trash-o"></i></a>
									<input class="hidden" type="text" ng-model="popup.meta.backgroundImageUrl"
										   name="mango_new_popup[background_image_url]"
										   ng-change="popupDom.setContainerBackgroundImage(popup.meta.backgroundImageUrl);">
								</div>
								<div class="col-md-6">
									<div class="background ">
									<span>
									<?php esc_html_e( 'Background Color', 'mango_popup' ); ?>
									</span>
										<div class="no-padding">
											<input type="text"
												   name="mango_new_popup[background_color]"
												   ng-model="popup.meta.backgroundColor"
												   ng-click="toggleColorPicker($event)"
												   ng-change="changeColor()"
												   id="mango-popup-color-picker-show">
											<input
													ng-model="popup.meta.backgroundColor"
													id="mango-popup-color-picker-hide">
										</div>
									</div>
									<div class="animation">
										<span><?php esc_html_e( 'Animation', 'mango_popup' ); ?></span>
										<div class="no-padding">
											<select name="mango_new_popup[animation_start]"
													ng-model="popup.meta.animationStart"
											>
												<option value="">None</option>
												<?php foreach ( $list_animation as $animation ) : ?>
													<option value="<?php echo esc_attr( $animation ); ?>">
														<?php echo esc_html( $animation ); ?>
													</option>
												<?php endforeach; ?>
											</select>
										</div>
									</div>
								</div>
							</div>
						</div>

					</div>

					<div class="interactive">
						<h3 class="customize-border mango-customize__title">
							<?php
							esc_html_e( 'Interactive Options', 'mango_popup' );
							?>
						</h3>
						<div class="content">
							<div class="display">
								<div style="margin-bottom: 5px">
									<span><?php esc_html_e( 'Pages display', 'mango_popup' ); ?></span>
									<div>? <a href="#"> This is the page(s) that will show popup.</a></div>
								</div>
								<div>
									<div class="position-display">
										<label>
											<input ng-checked="popup.meta.pageIdDisplay.indexOf('all') > -1"
												   ng-click="pageIdDisplaytoggle('all')"
												   type="checkbox" name="mango_new_popup[page_id_display][all]">
											<?php esc_html_e( 'Display on all pages', 'mango_popup' ); ?>
											<br>
										</label>
									</div>

									<div class="position-display">
										<label>
											<input ng-checked="popup.meta.pageIdDisplay.indexOf('home') > -1"
												   ng-click="pageIdDisplaytoggle('home')"
												   type="checkbox" name="mango_new_popup[page_id_display][home]">
											<?php esc_html_e( 'Homepage', 'mango_popup' ); ?>
											<br>
										</label>
									</div>

									<div class="position-display">
										<label>
											<input ng-checked="popup.meta.pageIdDisplay.indexOf('single') > -1"
												   ng-click="pageIdDisplaytoggle('single')"
												   type="checkbox" name="mango_new_popup[page_id_display][single]">
											<?php esc_html_e( 'Single Post', 'mango_popup' ); ?>
											<br>
										</label>
									</div>

									<div class="position-display">
										<label>
											<input ng-checked="popup.meta.pageIdDisplay.indexOf('category') > -1"
												   ng-click="pageIdDisplaytoggle('category')"
												   type="checkbox" name="mango_new_popup[page_id_display][category]">
											<?php esc_html_e( 'Post Categories', 'mango_popup' ); ?>
											<br>
										</label>
									</div>

									<div class="position-display">
										<label>
											<input ng-checked="popup.meta.pageIdDisplay.indexOf('tag') > -1"
												   ng-click="pageIdDisplaytoggle('tag')"
												   type="checkbox" name="mango_new_popup[page_id_display][tag]">
											<?php esc_html_e( 'Post Tags', 'mango_popup' ); ?>
											<br>
										</label>
									</div>

									<?php if ( MANGO_POPUP_HAVE_WC ) : ?>
										<div class="position-display">
											<label>
												<input ng-checked="popup.meta.pageIdDisplay.indexOf('woocommerce_product') > -1"
													   ng-click="pageIdDisplaytoggle('woocommerce_product')"
													   type="checkbox" name="mango_new_popup[page_id_display][woocommerce_product]">
												<?php esc_html_e( 'WooCommerce Product', 'mango_popup' ); ?>
												<br>
											</label>
										</div>

										<div class="position-display">
											<label>
												<input ng-checked="popup.meta.pageIdDisplay.indexOf('woocommerce_categories') > -1"
													   ng-click="pageIdDisplaytoggle('woocommerce_categories')"
													   type="checkbox" name="mango_new_popup[page_id_display][woocommerce_categories]">
												<?php esc_html_e( 'WooCommerce Categories', 'mango_popup' ); ?>
												<br>
											</label>
										</div>
									<?php endif; ?>

									<?php foreach ( $pages as $page ) : ?>
										<div class="position-display">
											<label>
												<input ng-checked="popup.meta.pageIdDisplay.indexOf('<?php echo esc_attr( wp_unslash( $page->ID ) ); ?>') > -1"
													   ng-click="pageIdDisplayToggle('<?php echo esc_attr( wp_unslash( $page->ID ) ); ?>')"
													   type="checkbox"
													   name="mango_new_popup[page_id_display][<?php echo esc_attr( wp_unslash( $page->ID ) ); ?>]">
												<?php echo $page->post_title ? '"' . esc_html( wp_unslash( $page->post_title ) ) . '"' : '(No title)'; ?>
											</label>
											<br>
										</div>
									<?php endforeach; ?>

								</div>

							</div>

							<div class="link">
								<div style="margin-bottom: 5px">
									<span><?php esc_html_e( 'Target Link', 'mango_popup' ); ?></span>
									<div>?<a href="#"> When users have finished submission, automatically redirect to Target Link.</a></div>
								</div>
								<div>
									<input ng-model="popup.meta.targetLink"
										   ng-blur="targetLinkValidateUrl()"
										   ng-class="{'mango-popup-input-has-error': targetLinkHasError == true}"
										   ng-disabled="popup.template.tags.indexOf('Product') > -1"
										   type="text"
										   name="mango_new_popup[target_link]"
										   placeholder="">
								</div>
							</div>

							<div class="mailchimp">
								<div style="margin-bottom: 5px">
									<span><?php esc_html_e( 'Mailchimp target', 'mango_popup' ); ?></span>
									<div>?<a href="#"> Users' contact will be stored in here.</a></div>
								</div>
								<div class="mango-popup-mailchimp-lists-select-box">
									<div class="mango-popup-icon-wrap"
										 ng-show="mailchimpLists===null">
										<i class="fa fa-spinner mango-popup-mailchimp-lists-loading"></i>
									</div>
									<div class="mango-popup-icon-wrap ng-hide"
										 ng-show="mailchimpLists===false">
										<i class="fa fa-exclamation-triangle mango-popup-mailchimp-lists-warning"></i>
										<a href=""><?php esc_html_e( 'Cannot connect to Mailchimp server', 'mango_popup' ); ?></a>
									</div>
									<select name="mango_new_popup[mailchimp_target_id]"
											ng-model="popup.meta.mailchimpTargetId"
											ng-options="
										mailchimpList.id as mailchimpList.name
										 for mailchimpList in mailchimpLists
" ng-style="{opacity:((mailchimpLists===null || mailchimpLists===false)?'.5':'1')}"
											ng-disabled="mailchimpLists===null || mailchimpLists===false || popup.template.tags.indexOf('Product') > -1"
											id="">
										<option value="">None</option>
									</select>
								</div>

							</div>

							<div class="time">
								<div style="margin-bottom: 5px">
									<span><?php esc_html_e( 'When popup display', 'mango_popup' ); ?></span>
									<div>?<a href="#"> The event triggers the popup display.</a></div>
								</div>
								<div style="overflow: hidden">
									<select name="mango_new_popup[when_popup_display]"
											ng-model="popup.meta.whenPopupDisplay" id="">
										<option value="page-loaded">Page loaded</option>
										<option value="before-user-exit">Before user exit</option>
										<option value="scroll-bottom">User scroll to bottom of page</option>
										<option value="scroll-center">User scroll to center of page</option>
										<option value="in-active-one-minute">User inactive for one minute</option>
									</select>
								</div>

							</div>

							<div class="method">
								<div style="margin-bottom: 5px">
									<span><?php esc_html_e( 'How popup display', 'mango_popup' ); ?></span>
									<div>?<a href="#"> The frequency that popup displays.</a></div>
								</div>
								<div>
									<select ng-model="popup.meta.howOftenPopupDisplay"
											name="mango_new_popup[how_often_popup_display]" id="">
										<option value="always">Always display</option>
										<option value="once-a-day">Once a day</option>
										<option value="once-a-session">Once a session</option>
										<option value="once-a-week">Once a week</option>
										<option value="only-one-time">Only one time</option>
									</select>
								</div>

							</div>
						</div>
					</div>

					<div class="interactive">
						<h3 class="customize-border mango-customize__title">
							<?php
							esc_html_e( 'Available Positions', 'mango_popup' );
							?>
						</h3>

						<div class="content">
							<div class="position">
								<div style="margin-bottom: 5px">
									<span><?php esc_html_e( 'Position', 'mango_popup' ); ?></span>
								</div>
								<div style="overflow: hidden">
									<select name="mango_new_popup[position]"
											ng-model="position">
										<?php foreach ( $positions as $position ) : ?>
											<?php $position_class = new Position( $position ); ?>
											<option value="<?php echo esc_attr( $position_class->slug ); ?>">
												<?php echo esc_html( $position_class->title ); ?>
											</option>
										<?php endforeach; ?>
									</select>
								</div>

							</div>
						</div>
					</div>

					<?php if ( MANGO_POPUP_HAVE_WC ) : ?>
					<div class="interactive ng-hide" ng-show="popup.template.tags.indexOf('WooCommerce') > -1">
						<h3 class="customize-border mango-customize__title">
							<?php
							esc_html_e( 'WooCommerce', 'mango_popup' );
							?>
						</h3>

						<div class="content">
							<div class="position" style="margin-bottom: 15px;">
								<div style="margin-bottom: 5px">
									<span><?php esc_html_e( 'Hot Deal', 'mango_popup' ); ?></span>
								</div>
								<div style="overflow: hidden">
									<select name="mango_new_popup[woocommerce][hotdeal]" ng-model="woocommerce.hotdeal" ng-change="onChangeWooCommerceDeal('{{woocommerce.hotdeal}}')">
										<option value=""><?php esc_html_e( 'Do not use WooCommerce data', 'mango_popup' ); ?></option>
										<option value="day"><?php esc_html_e( 'Flash deals', 'mango_popup' ); ?></option>
										<option value="best"><?php esc_html_e( 'Best deals', 'mango_popup' ); ?></option>
									</select>
								</div>
							</div>

							<div class="link" ng-if="woocommerce.hotdeal">
								<div style="margin-bottom: 5px;">
									<span><?php esc_html_e( 'Product Detail Label', 'mango_popup' ); ?></span>
								</div>
								<div style="overflow: hidden">
									<input type="text" name="mango_new_popup[woocommerce][product_detail_label]" ng-model="woocommerce.product_detail_label" ng-change="changeProductDetailLabel()">
								</div>
							</div>
						</div>
					</div>
					<?php endif; ?>
					<div class="interactive">
						<h3 class="customize-border mango-customize__title">
							<?php
							esc_html_e( 'Display Options', 'mango_popup' );
							?>
						</h3>
						<div class="content">
							<div class="position" style="margin-bottom: 15px;">
								<div class="popup-devices-mobile">
									<label>
										<input type="checkbox" ng-checked="popup.meta.hide_on_devices.indexOf('mobile') > -1"
											   name="mango_new_popup[hide_on_devices][mobile]" title="" id="">
										Hide on Mobile
									</label>
								</div>
								<div class="popup-hide_on_devices-tablet">
									<label>
										<input type="checkbox" ng-checked="popup.meta.hide_on_devices.indexOf('tablet') > -1"
											   name="mango_new_popup[hide_on_devices][tablet]" title="" id="">
										Hide on Tablet
									</label>
								</div>
								<div class="popup-hide_on_devices-desktop">
									<label>
										<input type="checkbox" ng-checked="popup.meta.hide_on_devices.indexOf('desktop') > -1"
											   name="mango_new_popup[hide_on_devices][desktop]" title="" id="">
										Hide on Desktop
									</label>
								</div>
							</div>
						</div>
					</div>
				</div>



				<div class="mango-customize__preview">
					<div class="mango-customize__text">
						<div class="secret mango-popup__tip">"<?php echo esc_html( $tip->random() ); ?>"</div>


						<div class="" style="display: inline-block;
width: 25%; vertical-align: middle; overflow: hidden;">
							<a href="javascript:void()" class="mango-button mango-button--style8"  ng-click="resetDefaultPopup($event)">
								<?php
								esc_html_e( 'Reset Popup Default', 'mango_popup' );
								?>
							</a>
						</div>

					</div>


					<div id="mango-popup-preview-box">
						<div class="mango-popup-guide">
							<?php
							esc_html_e(
								'Just click on text, textbox or button to start customize your popup!',
								'mango_popup'
							);
							?>
							<a class="mango-close-guide" href="javascript:void(0);"></a>
						</div>
					</div>

				</div>

			</div>

		</div><!-- /.wrap -->

	</form>

</div>
</div><!-- /ng-app -->
