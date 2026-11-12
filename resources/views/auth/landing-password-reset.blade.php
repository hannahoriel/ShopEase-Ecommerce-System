<div class="view active recovery-panel" data-recovery hidden>
    <section data-recovery-step="email">
        <h1>Forgot Password?</h1>
        <p class="subhead">Enter your registered email to get a verification code.</p>
        <form action="{{ route('landing.recovery.send') }}" method="POST" data-recovery-form data-next="code">
            @csrf
            <input type="hidden" name="portal" value="{{ $recoveryPortal }}">
            <div class="field-group">
                <label for="recovery-email">Email Address</label>
                <div class="first-password-input">
                    <input id="recovery-email" name="email" type="email" placeholder="Enter your email address" autocomplete="email" required>
                </div>
            </div>
            <button type="submit" class="btn-primary">Send Code</button>
        </form>
    </section>
    <section data-recovery-step="code" hidden>
        <h1>Verify Your Email</h1>
        <p class="subhead">If your email matches an account, we sent a 6-digit code.</p>
        <form action="{{ route('landing.recovery.verify') }}" method="POST" data-recovery-form data-next="password">
            @csrf
            <input type="hidden" name="code">
            <div class="sa-otp-digits" role="group" aria-label="Verification code">
                @for ($digit = 1; $digit <= 6; $digit++)
                    <input type="text" class="sa-otp-digit" maxlength="1" inputmode="numeric" pattern="[0-9]" autocomplete="{{ $digit === 1 ? 'one-time-code' : 'off' }}" aria-label="Digit {{ $digit }}" required>
                @endfor
            </div>
            <button type="submit" class="btn-primary">Verify Code</button>
        </form>
        <button type="button" class="link recovery-link recovery-back" data-recovery-email>Change email</button>
        <button type="button" class="link recovery-link recovery-back" data-recovery-resend disabled>Resend Code</button>
    </section>
    <section data-recovery-step="password" hidden>
        @include('auth.first-password-form', ['passwordRecovery' => true, 'logisticsSetup' => true])
    </section>
    <section data-recovery-step="success" hidden role="status">
        <span class="recovery-success-check" aria-hidden="true">&#10003;</span>
        <h1>Password Changed</h1>
        <p class="subhead">You can now log in with your new password.</p>
        <button type="button" class="btn-primary" data-recovery-login>Back to Log In</button>
    </section>
    <p class="first-password-error recovery-error" data-recovery-error role="alert" hidden></p>
    <button type="button" class="link recovery-link recovery-back" data-recovery-login data-recovery-cancel>Back to Log In</button>
</div>

<style>
    .recovery-panel[hidden], .recovery-panel [hidden], #loginView[hidden] { display: none !important; }
    .recovery-link { padding: 0; border: 0; background: transparent; font: inherit; font-size: 13px; cursor: pointer; }
    .recovery-back { display: block; margin: 14px auto 0; }
    .recovery-success-check { display: grid; place-items: center; width: 56px; height: 56px; margin: 0 auto 14px; border-radius: 50%; background: #f8e7e5; color: #7b1e2b; font-size: 30px; font-weight: 700; }
    .recovery-error { margin: 12px 0 0; }
    .recovery-panel .first-password-input input { width: 100%; box-sizing: border-box; }
    .recovery-panel .sa-otp-digits { display: grid; grid-template-columns: repeat(6, minmax(0, 45px)); justify-content: center; gap: 8px; margin: 24px 0; }
    .recovery-panel .sa-otp-digits .sa-otp-digit { box-sizing: border-box; min-width: 0; width: 100%; height: 50px; padding: 0; text-align: center; font: 600 20px Poppins, sans-serif; }
</style>

<script>
    (() => {
        const panel = document.currentScript.previousElementSibling.previousElementSibling;
        const right = panel.parentElement;
        const opener = right.querySelector('[data-open-recovery]');
        const login = opener.closest('.view');
        const error = panel.querySelector('[data-recovery-error]');
        const steps = [...panel.querySelectorAll('[data-recovery-step]')];
        const cancel = panel.querySelector('[data-recovery-cancel]');
        const digits = [...panel.querySelectorAll('.sa-otp-digit')];
        const passwordForm = panel.querySelector('[data-first-password-form]');
        passwordForm.dataset.next = 'success';
        const emailForm = panel.querySelector('[data-next=code]');
        const resend = panel.querySelector('[data-recovery-resend]');
        let submitting = false;
        let revision = 0;
        let stopLoading;

        panel.addEventListener('recovery:close', () => {
            revision++;
            panel.hidden = true;
            login.hidden = false;
            stopLoading?.();
            emailForm.reset();
            passwordForm.reset();
            digits.forEach(input => { input.value = ''; });
            show('email');
        });
        let resendAt = 0;
        let countdown;

        function updateResend() {
            const remaining = Math.max(0, Math.ceil((resendAt - Date.now()) / 1000));
            resend.disabled = submitting || remaining > 0;
            resend.textContent = remaining ? 'Resend Code in ' + remaining + 's' : 'Resend Code';
            if (!remaining) clearInterval(countdown);
        }

        function startCountdown(seconds) {
            resendAt = Date.now() + seconds * 1000;
            clearInterval(countdown);
            countdown = setInterval(updateResend, 250);
            updateResend();
        }

        resend.addEventListener('click', () => emailForm.requestSubmit());

        function show(step) {
            error.hidden = true;
            steps.forEach(section => { section.hidden = section.dataset.recoveryStep !== step; });
            cancel.hidden = step === 'success';
            if (!panel.hidden) panel.querySelector('[data-recovery-step="' + step + '"] input:not([type=hidden]), [data-recovery-step="' + step + '"] button')?.focus();
        }

        opener.addEventListener('click', () => {
            login.hidden = true;
            panel.hidden = false;
            show('email');
        });
        panel.querySelectorAll('[data-recovery-login]').forEach(button => button.addEventListener('click', () => {
            if (submitting) return;
            panel.hidden = true;
            login.hidden = false;
            opener.focus();
        }));
        panel.querySelector('[data-recovery-email]').addEventListener('click', () => show('email'));

        digits.forEach((input, index) => {
            input.addEventListener('input', () => {
                input.value = input.value.replace(/\D/g, '');
                if (input.value) digits[index + 1]?.focus();
            });
            input.addEventListener('keydown', event => {
                if (event.key === 'Backspace' && !input.value) digits[index - 1]?.focus();
            });
            input.addEventListener('paste', event => {
                const code = event.clipboardData.getData('text').replace(/\D/g, '').slice(0, 6);
                if (!code) return;
                event.preventDefault();
                const offset = code.length === 6 ? 0 : index;
                [...code].forEach((digit, position) => {
                    if (digits[offset + position]) digits[offset + position].value = digit;
                });
                digits[Math.min(offset + code.length, 5)].focus();
            });
        });

        panel.addEventListener('submit', async event => {
            event.preventDefault();
            if (submitting) return;
            const form = event.target;
            const next = form.dataset.next;
            const button = form.querySelector('[type=submit]');
            if (next === 'password') form.elements.code.value = digits.map(input => input.value).join('');
            error.hidden = true;
            submitting = true;
            const requestRevision = revision;
            button.disabled = true;
            updateResend();
            const saving = next === 'success';
            stopLoading = window.ShopEaseLoading?.startBag(button);
            try {
                const response = await fetch(form.action, {
                    method: 'POST',
                    headers: { Accept: 'application/json' },
                    body: new FormData(form),
                });
                const data = await response.json();
                if (next === 'code' && Number(data.retry_after) > 0) startCountdown(Number(data.retry_after));
                if (requestRevision !== revision) return;
                if (!response.ok) throw new Error(Object.values(data.errors || {}).flat()[0] || data.message);
                if (next === 'code') digits.forEach(input => { input.value = ''; });
                await new Promise(resolve => setTimeout(resolve, 280));
                if (requestRevision !== revision) return;
                if (saving) passwordForm.reset();
                stopLoading?.();
                show(next);
            } catch (failure) {
                if (requestRevision !== revision) return;
                error.textContent = failure.message || 'Please try again.';
                error.hidden = false;
            } finally {
                stopLoading?.();
                button.disabled = false;
                submitting = false;
                updateResend();
            }
        });
    })();
</script>
