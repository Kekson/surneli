<?php
/**
 * Surneli Phone Auth
 * -------------------
 * Replaces email+password registration/login with a single
 * "phone number + 4-digit SMS code" flow for customers, on both
 * the My Account page and checkout account creation.
 *
 * wp-admin login is completely untouched - this only affects the
 * customer-facing account system.
 *
 * Identity model: WordPress still requires a unique user_email
 * internally, so each account gets an auto-generated placeholder
 * (<phone>@phone.surneli.ge) the customer never sees or uses. The
 * real identifier customers interact with is just their phone
 * number; wp_insert_user's user_login is set to the local 9-digit
 * number, and a random password is generated once and never used -
 * login always happens through the SMS code, never a password.
 */

if (!defined('ABSPATH')) {
	exit;
}

class Surneli_Phone_Auth {

	const CODE_LENGTH      = 4;
	const CODE_TTL         = 300; // seconds a code stays valid (5 min)
	const RESEND_COOLDOWN  = 45;  // seconds before a customer can request another code
	const MAX_ATTEMPTS     = 5;   // wrong guesses allowed before a code is burned
	const REMEMBER_DAYS    = 60;  // how long a verified customer stays logged in
	const EMAIL_SUFFIX     = '@phone.surneli.ge'; // placeholder email domain, never shown to customers

	public static function init() {
		add_action('wp_ajax_nopriv_surneli_send_code', [__CLASS__, 'ajax_send_code']);
		add_action('wp_ajax_surneli_send_code', [__CLASS__, 'ajax_send_code']);
		add_action('wp_ajax_nopriv_surneli_verify_code', [__CLASS__, 'ajax_verify_code']);
		add_action('wp_ajax_surneli_verify_code', [__CLASS__, 'ajax_verify_code']);

		add_action('wp_enqueue_scripts', [__CLASS__, 'enqueue_assets']);

		// Keep a verified customer logged in for weeks, not the WP default.
		add_filter('auth_cookie_expiration', [__CLASS__, 'extend_remembered_session'], 10, 3);

		// A guest checkout silently gets an account attached to their phone
		// number, so next time they come back they can just verify their
		// phone and see their past orders - no separate "register" step.
		add_action('woocommerce_checkout_order_processed', [__CLASS__, 'maybe_create_account_from_order'], 10, 1);
		add_filter('pre_wp_mail', [__CLASS__, 'block_placeholder_email'], 10, 2);

		// Some orders may carry the placeholder in their own stored
		// billing_email (e.g. left over from before this field was
		// removed from checkout, on a resumed pending order) - strip it
		// wherever an order's billing email is read for display, not
		// just at checkout time.
		add_filter('woocommerce_order_get_billing_email', function ($value) {
			return self::is_placeholder_email($value) ? '' : $value;
		});

		// "Confirm your email address to check for past orders and link
		// them to your account" - a WooCommerce prompt asking customers to
		// verify an email address they never see or use. Stripped by
		// content match on the Orders tab, since it isn't tied to a single
		// filterable hook.
		add_filter('the_content', [__CLASS__, 'strip_email_verification_notice'], 20);
	

		// The checkout's own "create an account?" checkbox + password
		// field become redundant once every order silently gets an
		// account attached via the phone number above - hide it so
		// checkout stays exactly as simple as it is today.
		add_filter('pre_option_woocommerce_enable_signup_and_login_from_checkout', function () {
			return 'no';
		});
	

		// Never let the auto-generated placeholder email
		// (<phone>@phone.surneli.ge) show up anywhere a customer can
		// see it - it exists purely to satisfy WordPress's internal
		// requirement that every account have an email on file.
		add_filter('default_checkout_billing_email', function ($value) {
			return self::is_placeholder_email($value) ? '' : $value;
		});
	}

	/* ---------------------------------------------------------------
	   Helpers
	--------------------------------------------------------------- */

	private static function normalize_phone($raw) {
		$digits = preg_replace('/\D+/', '', (string) $raw);
		$digits = preg_replace('/^995/', '', $digits);
		if (!preg_match('/^5\d{8}$/', $digits)) {
			return false;
		}
		return $digits; // local 9-digit form, e.g. 555123456
	}

	private static function is_placeholder_email($email) {
		return is_string($email) && str_ends_with($email, self::EMAIL_SUFFIX);
	}

	private static function otp_key($phone) {
		return 'surneli_otp_' . $phone;
	}

	private static function cooldown_key($phone) {
		return 'surneli_otp_cd_' . $phone;
	}

	/* ---------------------------------------------------------------
	   AJAX: send code
	--------------------------------------------------------------- */

	public static function ajax_send_code() {
		check_ajax_referer('surneli_phone_auth', 'nonce');

		$phone = self::normalize_phone($_POST['phone'] ?? '');
		if (!$phone) {
			wp_send_json_error(['message' => 'გთხოვთ შეიყვანოთ სწორი ნომერი (მაგ: 555123456)']);
		}

		if (get_transient(self::cooldown_key($phone))) {
			wp_send_json_error(['message' => 'კოდი უკვე გაიგზავნა, სცადეთ რამდენიმე წამში']);
		}

		$code = str_pad((string) wp_rand(0, 9999), self::CODE_LENGTH, '0', STR_PAD_LEFT);

		// Keep every code sent within the current window valid, not just
		// the newest one - a resend shouldn't invalidate a first message
		// that's simply running late but still on its way.
		$existing = get_transient(self::otp_key($phone));
		$hashes   = is_array($existing) && !empty($existing['hashes']) ? $existing['hashes'] : [];
		$hashes[] = wp_hash($code);

		set_transient(self::otp_key($phone), [
			'hashes'   => $hashes,
			'attempts' => is_array($existing) && isset($existing['attempts']) ? $existing['attempts'] : 0,
		], self::CODE_TTL);

		set_transient(self::cooldown_key($phone), 1, self::RESEND_COOLDOWN);

		// The "@domain #code" footer lets the WebOTP API auto-read the
		// code on supporting Android/Chrome without the customer typing
		// or even opening the Messages app - pure bonus, harmless on
		// phones/browsers that ignore it (iOS reads the plain number).
		$message = "თქვენი კოდია: {$code}\n\n@surneli.ge #{$code}";
		$sent = Surneli_SMS_Gateway::send_sms_blocking($phone, $message);

		if (!$sent) {
			// Roll back to whatever was valid before this attempt, rather
			// than wiping out a code from an earlier successful send just
			// because this particular resend failed to go out.
			if ($existing) {
				set_transient(self::otp_key($phone), $existing, self::CODE_TTL);
			} else {
				delete_transient(self::otp_key($phone));
			}
			delete_transient(self::cooldown_key($phone));
			wp_send_json_error(['message' => 'SMS ვერ გაიგზავნა, სცადეთ ხელახლა']);
		}

		wp_send_json_success([
			'message'  => 'კოდი გამოგზავნილია',
			'cooldown' => self::RESEND_COOLDOWN,
		]);
	}

	/* ---------------------------------------------------------------
	   AJAX: verify code + log in / create account
	--------------------------------------------------------------- */

	public static function ajax_verify_code() {
		check_ajax_referer('surneli_phone_auth', 'nonce');

		$phone = self::normalize_phone($_POST['phone'] ?? '');
		$code  = preg_replace('/\D+/', '', (string) ($_POST['code'] ?? ''));

		if (!$phone || strlen($code) !== self::CODE_LENGTH) {
			wp_send_json_error(['message' => 'არასწორი კოდი']);
		}

		$data = get_transient(self::otp_key($phone));
		if (!$data) {
			wp_send_json_error(['message' => 'კოდის ვადა გავიდა, გამოგზავნეთ ახალი']);
		}

		if ($data['attempts'] >= self::MAX_ATTEMPTS) {
			delete_transient(self::otp_key($phone));
			wp_send_json_error(['message' => 'ცდების ლიმიტი ამოწურულია, გამოგზავნეთ ახალი კოდი']);
		}

		$submitted_hash = wp_hash($code);
		$code_matches   = false;

		foreach ((array) ($data['hashes'] ?? []) as $stored_hash) {
			if (hash_equals($stored_hash, $submitted_hash)) {
				$code_matches = true;
				break;
			}
		}

		if (!$code_matches) {
			$data['attempts']++;
			set_transient(self::otp_key($phone), $data, self::CODE_TTL);
			wp_send_json_error(['message' => 'არასწორი კოდი, სცადეთ ხელახლა']);
		}

		delete_transient(self::otp_key($phone));
		delete_transient(self::cooldown_key($phone));

		$user = self::get_or_create_user($phone);
		if (!$user) {
			wp_send_json_error(['message' => 'დაფიქსირდა შეცდომა, სცადეთ მოგვიანებით']);
		}

		wp_set_current_user($user->ID);
		wp_set_auth_cookie($user->ID, true); // true = "remember me", extended below
		do_action('wp_login', $user->user_login, $user);

		wp_send_json_success([
			'redirect' => wc_get_page_permalink('shop'),
		]);
	}

	/* ---------------------------------------------------------------
	   Find or create the WP user tied to a phone number
	--------------------------------------------------------------- */

	private static function get_or_create_user($phone) {
		// 1. Phone-native accounts (created by this flow) use the phone
		//    number itself as the username.
		$user = get_user_by('login', $phone);
		if ($user) {
			return $user;
		}

		// 2. Older accounts (created pre-phone-login, or via a guest
		//    checkout) may have a different username but the same
		//    billing phone on file - match those too, so people don't
		//    end up split across two accounts.
		$by_meta = get_users([
			'meta_key'   => 'billing_phone',
			'meta_value' => $phone,
			'number'     => 1,
			'fields'     => 'all',
		]);
		if (!empty($by_meta)) {
			update_user_meta($by_meta[0]->ID, 'surneli_phone', $phone);
			return $by_meta[0];
		}

		$user_id = wp_insert_user([
			'user_login' => $phone,
			'user_pass'  => wp_generate_password(32, true, true), // never used - login is always by SMS code
			'user_email' => $phone . self::EMAIL_SUFFIX,
			'role'       => 'customer',
		]);

		if (is_wp_error($user_id)) {
			return false;
		}

		update_user_meta($user_id, 'surneli_phone', $phone);
		update_user_meta($user_id, 'billing_phone', $phone);

		return get_user_by('id', $user_id);
	}

	private static function sync_customer_from_order($user_id, $order) {
		$customer = new WC_Customer($user_id);

		foreach ($order->get_address('billing') as $key => $value) {
			if ('' !== $value && is_callable([$customer, "set_billing_{$key}"])) {
				$customer->{"set_billing_{$key}"}($value);
			}
		}

		foreach ($order->get_address('shipping') as $key => $value) {
			if ('' !== $value && is_callable([$customer, "set_shipping_{$key}"])) {
				$customer->{"set_shipping_{$key}"}($value);
			}
		}

		$customer->save();
	}

	/* ---------------------------------------------------------------
	   Silently attach an account to a guest checkout order
	--------------------------------------------------------------- */

	public static function maybe_create_account_from_order($order_id) {
		if (is_user_logged_in()) {
			return;
		}

		$order = wc_get_order($order_id);
		if (!$order) {
			return;
		}

		$phone = self::normalize_phone($order->get_billing_phone());
		if (!$phone) {
			return;
		}

		$user = self::get_or_create_user($phone);
		if (!$user) {
			return;
		}

		$order->set_customer_id($user->ID);
		$order->save();

		// WooCommerce only copies billing/shipping details into a
		// customer's saved profile (for prefilling next time) when the
		// shopper is already logged in DURING checkout. A first-time
		// order here is placed as a guest - the account only exists
		// after this point - so without this, name/address would never
		// get saved for next time and the customer would have to retype
		// everything on every order. Do it ourselves from the order.
		self::sync_customer_from_order($user->ID, $order);

		// Without this, WooCommerce sees the order now belongs to a real
		// account and gates the order-received page behind a login form -
		// for an account the customer never manually created or knew
		// existed. Log them straight in instead.
		wp_set_current_user($user->ID);
		wp_set_auth_cookie($user->ID, true);
	}

	/* ---------------------------------------------------------------
	   Never actually mail the placeholder address - nothing reads that
	   inbox, it may not even resolve, and customers already get order
	   updates by SMS. Silently "succeeds" instead of erroring/retrying.
	--------------------------------------------------------------- */

	public static function block_placeholder_email($return, $atts) {
		$to = $atts['to'] ?? '';
		$recipients = is_array($to) ? $to : explode(',', (string) $to);

		foreach ($recipients as $address) {
			if (self::is_placeholder_email(trim((string) $address))) {
				return true;
			}
		}

		return $return;
	}

	public static function strip_email_verification_notice($content) {
		if (!is_account_page() || !function_exists('is_wc_endpoint_url') || !is_wc_endpoint_url('orders')) {
			return $content;
		}

		return preg_replace(
			'#<div class="woocommerce-info"[^>]*>.*?wc_send_verification.*?</div>#s',
			'',
			$content
		);
	}

	/* ---------------------------------------------------------------
	   Keep verified customers signed in for weeks, not WordPress's
	   2/14-day default.
	--------------------------------------------------------------- */

	public static function extend_remembered_session($expiration, $user_id, $remember) {
		if ($remember) {
			return self::REMEMBER_DAYS * DAY_IN_SECONDS;
		}
		return $expiration;
	}

	/* ---------------------------------------------------------------
	   Assets
	--------------------------------------------------------------- */

	public static function enqueue_assets() {
		if (!is_account_page() && !is_checkout()) {
			return;
		}

		$js_path = get_stylesheet_directory() . '/js/phone-auth.js';

		wp_enqueue_script(
			'surneli-phone-auth',
			get_stylesheet_directory_uri() . '/js/phone-auth.js',
			[],
			file_exists($js_path) ? filemtime($js_path) : null,
			true
		);

		wp_localize_script('surneli-phone-auth', 'surneliPhoneAuth', [
			'ajaxUrl' => admin_url('admin-ajax.php'),
			'nonce'   => wp_create_nonce('surneli_phone_auth'),
		]);
	}
}

Surneli_Phone_Auth::init();
