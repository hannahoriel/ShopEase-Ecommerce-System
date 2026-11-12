<div data-first-password-panel>
    <h1>Create a new password</h1>
    <p class="subhead">{{ !empty($passwordRecovery) ? 'Choose a new password for your account.' : 'Replace your emailed password to finish signing in.' }}</p>
    <form method="POST" action="{{ route(!empty($passwordRecovery) ? 'landing.recovery.reset' : 'login.first-password') }}" data-first-password-form>
        @csrf
        @foreach (['password' => 'New Password', 'password_confirmation' => 'Confirm Password'] as $name => $label)
            <div class="field-group">
                <label for="{{ !empty($passwordRecovery) ? 'recovery' : 'first' }}-{{ $name }}">{{ $label }} <span class="first-password-required" aria-hidden="true">*</span></label>
                <div class="first-password-input">
                    <input id="{{ !empty($passwordRecovery) ? 'recovery' : 'first' }}-{{ $name }}" name="{{ $name }}" type="password" placeholder="{{ $name === 'password' ? 'Create a password' : 'Confirm your password' }}" autocomplete="new-password" minlength="8" maxlength="72" @if ($name === 'password') aria-describedby="{{ !empty($passwordRecovery) ? 'recovery' : 'first' }}-password-requirements" @else aria-describedby="{{ !empty($passwordRecovery) ? 'recovery' : 'first' }}-password-mismatch" @endif required>
                    <button type="button" data-loading="off" data-first-password-eye aria-label="Show password" aria-pressed="false">
                        <img src="{{ asset('icons/login/hide-password.png') }}" data-show-icon="{{ asset('icons/login/show-password.png') }}" data-hide-icon="{{ asset('icons/login/hide-password.png') }}" alt="">
                    </button>
                </div>
                @if ($name === 'password')
                    <div class="first-password-rules" id="{{ !empty($passwordRecovery) ? 'recovery' : 'first' }}-password-requirements">
                        <strong>Password requirements</strong>
                        <ul>
                            <li data-rule="length">At least 8 characters</li>
                            <li data-rule="upper">One uppercase letter</li>
                            <li data-rule="lower">One lowercase letter</li>
                            <li data-rule="number">A number</li>
                            <li data-rule="symbol">A special character</li>
                        </ul>
                    </div>
                    <p class="first-password-hint">At least 8 characters with uppercase and lowercase letters, a number, and a special character.</p>
                @else
                    <p class="first-password-error first-password-mismatch" id="{{ !empty($passwordRecovery) ? 'recovery' : 'first' }}-password-mismatch" data-password-mismatch aria-live="polite" hidden>Passwords do not match.</p>
                @endif
            </div>
        @endforeach
        <p class="first-password-error" data-first-password-error role="alert" @if (!empty($logisticsSetup) || !$errors->has('password')) hidden @endif>{{ empty($logisticsSetup) ? $errors->first('password') : '' }}</p>
        <button type="submit" class="btn-primary">{{ !empty($passwordRecovery) ? 'Save New Password' : 'Save Password and Log In' }}</button>
    </form>
</div>
<style>
    [data-logistics-password-setup][hidden], #logisticsLoginView[hidden] { display: none !important; }
    [data-first-password-panel] .field-group > label { font-weight: 500; }
    .first-password-required { color: #a42020; }
    .first-password-input { position: relative; }
    [data-first-password-panel] .first-password-input input {
        box-sizing: border-box;
        width: 100%;
        min-height: 48px;
        padding: 12px 46px 12px 14px;
        border: 1px solid #d9d5d3;
        border-radius: 10px;
        background: #fff;
        color: #201715;
        font: inherit;
        font-size: 14px;
    }
    .first-password-input input::placeholder { color: #a39b97; }
    [data-first-password-eye] {
        position: absolute;
        inset: 0 7px 0 auto;
        width: 34px;
        display: grid;
        place-items: center;
        padding: 0;
        border: 0;
        border-radius: 8px;
        background: transparent;
        cursor: pointer;
    }
    [data-first-password-eye] img { display: block; width: 18px; height: 18px; object-fit: contain; }
    [data-first-password-eye]:focus-visible { outline: 2px solid #8a7b76; outline-offset: -3px; }
    .first-password-rules {
        margin-top: 10px;
        padding: 12px 14px;
        border: 1px solid #eee0dc;
        border-radius: 9px;
        background: #fffbfa;
        font-size: 12px;
        line-height: 1.8;
    }
    .first-password-rules strong { display: block; color: #201715; }
    .first-password-rules ul { margin: 2px 0 0; padding: 0; list-style: none; color: #8a7774; }
    .first-password-rules li::before { content: '○ '; }
    .first-password-rules .is-met { color: #267446; }
    .first-password-rules .is-met::before { content: '✓ '; }
    .first-password-hint { margin: 8px 0 0; color: #736560; font-size: 12px; }
    .first-password-mismatch { margin-top: 6px !important; }
    .first-password-error { margin: 0 0 12px; color: #a42020; font-size: 13px; }
</style>
<script>
    (() => {
        const panel = document.currentScript.previousElementSibling.previousElementSibling;
        const password = panel.querySelector('[name=password]');
        const confirmation = panel.querySelector('[name=password_confirmation]');
        const mismatch = panel.querySelector('[data-password-mismatch]');
        panel.querySelectorAll('[data-first-password-eye]').forEach(button => {
            button.addEventListener('click', () => {
                const input = button.previousElementSibling;
                const visible = input.type === 'password';
                input.type = visible ? 'text' : 'password';
                const icon = button.querySelector('img');
                icon.src = visible ? icon.dataset.showIcon : icon.dataset.hideIcon;
                button.setAttribute('aria-pressed', String(visible));
                button.setAttribute('aria-label', visible ? 'Hide password' : 'Show password');
            });
        });
        const updateRules = () => {
            const value = password.value;
            const rules = { length: value.length >= 8, upper: /\p{Lu}/u.test(value), lower: /\p{Ll}/u.test(value), number: /\p{N}/u.test(value), symbol: /[\p{Z}\p{S}\p{P}]/u.test(value) };
            panel.querySelectorAll('[data-rule]').forEach(item => item.classList.toggle('is-met', rules[item.dataset.rule]));
            const passwordsDiffer = confirmation.value !== '' && confirmation.value !== value;
            mismatch.hidden = !passwordsDiffer;
            confirmation.setAttribute('aria-invalid', String(passwordsDiffer));
            confirmation.setCustomValidity(passwordsDiffer ? mismatch.textContent : '');
        };
        password.addEventListener('input', updateRules);
        confirmation.addEventListener('input', updateRules);
        confirmation.form.addEventListener('reset', () => queueMicrotask(updateRules));
    })();
</script>
