/**
 * Surneli phone-number login/registration.
 * One flow: phone number -> 4-digit SMS code -> logged in.
 * No password step exists in this UI at all.
 */
(function () {
	function byId(id) {
		return document.getElementById(id);
	}

	document.addEventListener('DOMContentLoaded', function () {
		var root = byId('surneli-phone-auth');
		if (!root || typeof surneliPhoneAuth === 'undefined') {
			return;
		}

		var stepPhone = root.querySelector('[data-step="phone"]');
		var stepCode = root.querySelector('[data-step="code"]');
		var stepLoading = root.querySelector('[data-step="loading"]');

		var phoneInput = byId('spa-phone');
		var codeInput = byId('spa-code');
		var sendBtn = byId('spa-send-btn');
		var phoneError = byId('spa-phone-error');
		var codeError = byId('spa-code-error');
		var phoneDisplay = byId('spa-phone-display');
		var changePhoneLink = byId('spa-change-phone');
		var resendBtn = byId('spa-resend-btn');
		var resendTimer = byId('spa-resend-timer');

		var currentPhone = '';
		var cooldownInterval = null;

		function showStep(step) {
			[stepPhone, stepCode, stepLoading].forEach(function (el) {
				if (el) el.style.display = 'none';
			});
			if (step) step.style.display = '';
		}

		function post(action, extra) {
			var body = new URLSearchParams();
			body.set('action', action);
			body.set('nonce', surneliPhoneAuth.nonce);
			Object.keys(extra || {}).forEach(function (key) {
				body.set(key, extra[key]);
			});

			return fetch(surneliPhoneAuth.ajaxUrl, {
				method: 'POST',
				credentials: 'same-origin',
				headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
				body: body.toString(),
			}).then(function (r) {
				return r.json();
			});
		}

		function sendCode() {
			var phone = phoneInput.value.trim();
			phoneError.textContent = '';

			if (!/^5\d{8}$/.test(phone)) {
				phoneError.textContent = 'გთხოვთ შეიყვანოთ სწორი ნომერი (მაგ: 555123456)';
				return;
			}

			currentPhone = phone;
			sendBtn.disabled = true;
			showStep(stepLoading);

			post('surneli_send_code', { phone: phone })
				.then(function (res) {
					sendBtn.disabled = false;
					if (res.success) {
						phoneDisplay.textContent = '+995 ' + phone;
						codeInput.value = '';
						codeError.textContent = '';
						showStep(stepCode);
						startCooldown((res.data && res.data.cooldown) || 45);
						codeInput.focus();
						requestWebOTP();
					} else {
						showStep(stepPhone);
						phoneError.textContent = (res.data && res.data.message) || 'შეცდომა, სცადეთ ხელახლა';
					}
				})
				.catch(function () {
					sendBtn.disabled = false;
					showStep(stepPhone);
					phoneError.textContent = 'შეცდომა, სცადეთ ხელახლა';
				});
		}

		function verifyCode(code) {
			codeError.textContent = '';
			showStep(stepLoading);

			post('surneli_verify_code', { phone: currentPhone, code: code })
				.then(function (res) {
					if (res.success) {
						window.location.href = res.data.redirect;
					} else {
						showStep(stepCode);
						codeInput.value = '';
						codeInput.focus();
						codeError.textContent = (res.data && res.data.message) || 'არასწორი კოდი';
					}
				})
				.catch(function () {
					showStep(stepCode);
					codeError.textContent = 'შეცდომა, სცადეთ ხელახლა';
				});
		}

		function startCooldown(seconds) {
			var remaining = seconds;
			resendBtn.classList.add('spa-disabled');
			resendTimer.textContent = ' (' + remaining + ')';
			clearInterval(cooldownInterval);
			cooldownInterval = setInterval(function () {
				remaining--;
				if (remaining <= 0) {
					clearInterval(cooldownInterval);
					resendBtn.classList.remove('spa-disabled');
					resendTimer.textContent = '';
				} else {
					resendTimer.textContent = ' (' + remaining + ')';
				}
			}, 1000);
		}

		// WebOTP: on supporting Android/Chrome, the browser reads the SMS
		// itself (matching the "@surneli.ge #code" footer) and offers a
		// one-tap fill - the customer may never need to type the code.
		function requestWebOTP() {
			if (!('OTPCredential' in window)) {
				return;
			}
			var ac = new AbortController();
			navigator.credentials
				.get({ otp: { transport: ['sms'] }, signal: ac.signal })
				.then(function (otp) {
					if (otp && otp.code) {
						var digits = otp.code.replace(/\D/g, '').slice(0, 4);
						codeInput.value = digits;
						if (digits.length === 4) {
							verifyCode(digits);
						}
					}
				})
				.catch(function () {
					/* ignored - user just types the code instead */
				});
		}

		phoneInput.addEventListener('input', function () {
			phoneInput.value = phoneInput.value.replace(/\D/g, '').slice(0, 9);
		});

		phoneInput.addEventListener('keydown', function (e) {
			if (e.key === 'Enter') sendCode();
		});

		sendBtn.addEventListener('click', sendCode);

		codeInput.addEventListener('input', function () {
			codeInput.value = codeInput.value.replace(/\D/g, '').slice(0, 4);
			if (codeInput.value.length === 4) {
				verifyCode(codeInput.value);
			}
		});

		changePhoneLink.addEventListener('click', function (e) {
			e.preventDefault();
			clearInterval(cooldownInterval);
			showStep(stepPhone);
		});

		resendBtn.addEventListener('click', function (e) {
			e.preventDefault();
			if (resendBtn.classList.contains('spa-disabled')) {
				return;
			}
			sendCode();
		});
	});
})();
