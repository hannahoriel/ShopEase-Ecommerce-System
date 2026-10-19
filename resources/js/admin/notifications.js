/** ShopEase Admin Notifications: Date filter submission. */
document.addEventListener('DOMContentLoaded', () => {
    const dateFilter = document.getElementById('snDateFilter');
    if (!dateFilter) return;

    dateFilter.addEventListener('change', () => {
        const form = dateFilter.form;
        if (!form) return;
        if (typeof form.requestSubmit === 'function') {
            form.requestSubmit();
        } else {
            form.submit();
        }
    });
});