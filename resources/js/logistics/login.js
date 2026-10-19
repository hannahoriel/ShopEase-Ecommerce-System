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