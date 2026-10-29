/* Same eye PNG files and show/hide interaction as the existing login; independent modal scope. */
window.initLogisticsLogin = function(root) {
    const scope = root || document;
    const panels = scope.matches?.('[data-logistics-login]')
        ? [scope] : scope.querySelectorAll('[data-logistics-login]');
    panels.forEach(panel => {
        if (panel.dataset.loginInitialized === 'true') return;
        panel.dataset.loginInitialized = 'true';
        const input = panel.querySelector('#logisticsLoginPass');
        const btn = panel.querySelector('#logisticsEyeBtn');
        const icon = panel.querySelector('#logisticsEyeIcon');
        const feedback = panel.querySelector('[data-login-feedback]');
        const form = panel.querySelector('[data-logistics-form]');
        const toggle = () => {
            const shown = input.type === 'text';
            icon.src = shown ? icon.dataset.showIcon : icon.dataset.hideIcon;
            icon.alt = shown ? 'Password visible' : 'Password hidden';
            btn.setAttribute('aria-label', shown ? 'Hide password' : 'Show password');
            btn.setAttribute('aria-pressed', String(shown));
            btn.classList.add('show'); // Always display the eye icon, including when password is empty.
        };
        input.addEventListener('input', () => {
            if (!input.value) input.type = 'password';
            toggle();
        });
        btn.addEventListener('click', () => {
            input.type = input.type === 'password' ? 'text' : 'password';
            toggle();
            input.focus();
        });
        toggle();
        // Authenticate first. Only a confirmed successful logistics login starts the opening transition.
        form.addEventListener('submit', async event => {
            if (form.dataset.ready !== '1' || !form.reportValidity()) return;
            event.preventDefault();
            if (form.dataset.submitting === 'true') return;
            form.dataset.submitting = 'true';
            const submit = form.querySelector('[type="submit"]');
            if (submit) { submit.disabled = true; submit.textContent = 'Logging in...'; }
            if (feedback) { feedback.hidden = true; feedback.textContent = ''; }
            try {
                const response = await fetch(form.action, {
                    method: 'POST',
                    body: new FormData(form),
                    credentials: 'same-origin',
                    redirect: 'follow',
                    headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
                });
                if (!response.ok) {
                    let message = 'Unable to log in. Please check your email and password.';
                    try {
                        const data = await response.json();
                        message = Object.values(data.errors || {}).flat()[0] || data.message || message;
                    } catch (_) {}
                    throw new Error(message);
                }
                // Laravel's authenticated redirect has been followed by fetch. Verify the final route.
                const destination = new URL(response.url, window.location.href);
                if (destination.origin !== window.location.origin ||
                    !/^\/logistics\/dashboard\/?$/.test(destination.pathname)) {
                    window.location.assign(destination.href);
                    return;
                }
                if (typeof window.startLogisticsDashboardTransition === 'function') {
                    window.startLogisticsDashboardTransition(destination.href);
                } else {
                    window.location.assign(destination.href);
                }
            } catch (error) {
                if (feedback) {
                    feedback.textContent = error.message || 'Unable to log in. Please try again.';
                    feedback.hidden = false;
                }
            } finally {
                form.dataset.submitting = 'false';
                if (submit) { submit.disabled = false; submit.textContent = 'Log In'; }
            }
        });
        form.addEventListener('submit', event => {
            if (form.dataset.ready !== '1') {
                event.preventDefault();
                feedback.textContent = 'Logistics authentication route is not yet configured.';
                feedback.hidden = false;
            }
        });
        panel.querySelector('[data-logistics-forgot]')?.addEventListener('click', event => event.preventDefault());
    });
};
window.initLogisticsLogin(document);