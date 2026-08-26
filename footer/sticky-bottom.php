<?php
/**
 * Mobile bottom sticky nav bar - child theme override of
 * porto/footer/sticky-bottom.php (WordPress/Porto's get_template_part()
 * looks in the child theme first, so this file replaces the parent's
 * automatically - no filter/hook needed).
 *
 * Trimmed to 4 icons (Home / Shop / Wishlist / Cart - Blog and Account
 * dropped per request) with hardcoded Georgian labels, matching the bar
 * on aromati.ge. The parent version is driven by the Redux 'porto_settings'
 * option (Theme Options > Mobile Bottom Sticky Bar), but toggling icons
 * there didn't take effect here - this file is now the single source of
 * truth for this bar instead, independent of that admin screen. To change
 * which icons show, their order, or labels, edit this file directly.
 */

if ( ! defined( 'ABSPATH' ) ) {
	return;
}

$woo      = defined( 'WOOCOMMERCE_VERSION' );
$wishlist = defined( 'YITH_WCWL' );
?>
<div class="porto-sticky-navbar has-ccols ccols-4 d-sm-none">
	<div class="sticky-icon link-home">
		<a href="<?php echo esc_url( get_home_url() ); ?>">
			<i class="porto-icon-category-home"></i>
			<span class="label">მთავარი</span>
		</a>
	</div>
	<?php if ( $woo ) : ?>
	<div class="sticky-icon link-shop">
		<a href="<?php echo esc_url( get_permalink( wc_get_page_id( 'shop' ) ) ); ?>">
			<i class="porto-icon-bars"></i>
			<span class="label">მაღაზია</span>
		</a>
	</div>
	<?php endif; ?>
	<?php if ( $wishlist && $woo ) : ?>
	<div class="sticky-icon link-wishlist">
		<a href="<?php echo esc_url( YITH_WCWL()->get_wishlist_url() ); ?>">
			<i class="porto-icon-wishlist-2"></i>
			<span class="label">სია</span>
		</a>
	</div>
	<?php endif; ?>
	<?php if ( $woo ) : ?>
	<div class="sticky-icon link-cart">
		<a href="<?php echo esc_url( wc_get_cart_url() ); ?>">
			<span class="cart-icon">
				<i class="porto-icon-shopping-cart"></i>
				<span class="cart-items"><?php echo intval( wc()->cart->get_cart_contents_count() ); ?></span>
			</span>
			<span class="label">კალათა</span>
		</a>
	</div>
	<?php endif; ?>
</div>
