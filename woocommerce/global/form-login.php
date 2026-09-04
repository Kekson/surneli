<?php
/**
 * Login form - phone number version.
 *
 * This overrides WooCommerce's default email+password login form used
 * wherever core code calls woocommerce_login_form() directly (which
 * renders woocommerce/global/form-login.php) - most notably the
 * "Please log in to your account to view this order" gate that
 * class-wc-shortcode-checkout.php shows on the order-received page for
 * non-guest orders when the visitor isn't currently logged in.
 *
 * This is a separate template from myaccount/form-login.php (used on
 * the My Account page itself) because WooCommerce core references the
 * two paths independently - both need to point at the same phone
 * flow. See inc/phone-auth.php for the logic, js/phone-auth.js for the
 * UI. Whether a customer is new or returning is detected automatically
 * - there is no separate "Register" form.
 *
 * @var array $args {
 *     @type string $message  Optional notice WooCommerce core passes in - already
 *                            printed by the caller (e.g. wc_print_notice()) before
 *                            this template runs, so it isn't repeated here.
 *     @type string $redirect Where to send the customer once they've verified their
 *                            phone - e.g. back to the order-received page they were
 *                            blocked from. Falls back to the shop page if absent.
 *     @type bool   $hidden   Unused by this template.
 * }
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$redirect = ! empty( $args['redirect'] ) ? $args['redirect'] : '';

do_action( 'woocommerce_before_customer_login_form' );
?>

<div class="surneli-phone-auth" id="surneli-phone-auth" data-redirect="<?php echo esc_url( $redirect ); ?>">

	<div class="spa-step" data-step="phone">
		<h2><?php esc_html_e( 'შესვლა', 'surneli' ); ?></h2>
		<p class="spa-sub"><?php esc_html_e( 'შეიყვანეთ თქვენი მობილურის ნომერი და მიიღეთ ერთჯერადი კოდი', 'surneli' ); ?></p>

		<p class="spa-phone-row">
			<span class="spa-prefix">+995</span>
			<input
				type="tel"
				inputmode="numeric"
				autocomplete="tel-national"
				maxlength="9"
				id="spa-phone"
				class="woocommerce-Input woocommerce-Input--text input-text"
				placeholder="5XX XXX XXX"
			/>
		</p>

		<button type="button" class="button spa-btn" id="spa-send-btn"><?php esc_html_e( 'კოდის მიღება', 'surneli' ); ?></button>
		<p class="spa-error" id="spa-phone-error"></p>
	</div>

	<div class="spa-step" data-step="code" style="display:none">
		<h2><?php esc_html_e( 'შეიყვანეთ კოდი', 'surneli' ); ?></h2>
		<p class="spa-sub">
			<?php esc_html_e( 'გამოგზავნილია ნომერზე', 'surneli' ); ?>
			<strong id="spa-phone-display"></strong>
			&middot;
			<a href="#" id="spa-change-phone"><?php esc_html_e( 'შეცვლა', 'surneli' ); ?></a>
		</p>

		<input
			type="text"
			inputmode="numeric"
			autocomplete="one-time-code"
			pattern="[0-9]*"
			maxlength="4"
			id="spa-code"
			class="spa-code-input"
			placeholder="----"
		/>

		<p class="spa-error" id="spa-code-error"></p>

		<p class="spa-resend">
			<a href="#" id="spa-resend-btn"><?php esc_html_e( 'კოდის ხელახლა გაგზავნა', 'surneli' ); ?></a><span id="spa-resend-timer"></span>
		</p>
	</div>

	<div class="spa-step spa-step-loading" data-step="loading" style="display:none">
		<span class="spa-spinner" aria-hidden="true"></span>
	</div>

</div>

<?php
do_action( 'woocommerce_after_customer_login_form' );
