/** ShopEase Admin Notifications: Date filter submission. */
document.addEventListener('DOMContentLoaded', () => {
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '';
    const apiUrl = new URL('/api/v1/admin/notifications', window.location.origin);
    new URLSearchParams(window.location.search).forEach((value, key) => {
        apiUrl.searchParams.set(key, value);
    });

    const dateFilter = document.getElementById('snDateFilter');
    if (dateFilter) {
        dateFilter.addEventListener('change', () => {
            const form = dateFilter.form;
            if (!form) return;
            if (typeof form.requestSubmit === 'function') {
                form.requestSubmit();
            } else {
                form.submit();
            }
        });
    }

    fetch(apiUrl, {
        credentials: 'same-origin',
        headers: { Accept: 'application/json' }
    })
        .then(async response => {
            const payload = await response.json();
            if (!response.ok) {
                throw new Error(payload.message || 'Unable to load notifications.');
            }
            const summary = payload.counts || {};
            const tabs = document.querySelectorAll('.sn-tab-count');
            [tabs[0], tabs[1], tabs[2]].forEach((element, index) => {
                if (element) element.textContent = [summary.all, summary.unread, summary.read][index] || 0;
            });
        })
        .catch(error => {
            console.error(error);
            const errorElement = document.getElementById('notifications-api-error');
            if (errorElement) {
                errorElement.textContent = error.message;
                errorElement.hidden = false;
            }
        });

    document.querySelectorAll('.sn-toolbar-mark-all, .sn-notification-footer form').forEach(form => {
        form.addEventListener('submit', async event => {
            event.preventDefault();
            const button = form.querySelector('button[type="submit"]');
            if (button) button.disabled = true;
            try {
                const response = await fetch(form.action, {
                    method: 'PATCH',
                    credentials: 'same-origin',
                    headers: {
                        Accept: 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    }
                });
                const payload = await response.json().catch(() => ({}));
                if (!response.ok) {
                    throw new Error(payload.message || 'Unable to update notifications.');
                }
                window.location.reload();
            } catch (error) {
                if (button) button.disabled = false;
                window.alert(error.message);
            }
        });
    });
});