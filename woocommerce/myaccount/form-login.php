<?php
/**
 * Login / Register - phone number version.
 *
 * Overrides WooCommerce's default email+password login/register forms
 * with a single "phone number + SMS code" flow (see
 * inc/phone-auth.php for the logic, js/phone-auth.js for the UI).
 * Whether a customer is new or returning is detected automatically -
 * there is no separate "Register" form.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

do_action( 'woocommerce_before_customer_login_form' );
?>

<div class="surneli-phone-auth" id="surneli-phone-auth">

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
