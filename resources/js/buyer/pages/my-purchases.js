document.addEventListener('DOMContentLoaded', () => {
            const sideTabs =
                Array.from(document.querySelectorAll('[data-side-panel]'));

            const sidePanels =
                Array.from(document.querySelectorAll('[data-panel-content]'));

            const showSidePanel = (panelName) => {
                sideTabs.forEach(tab => {
                    tab.classList.toggle(
                        'is-active',
                        tab.dataset.sidePanel === panelName
                    );
                });

                sidePanels.forEach(panel => {
                    const active =
                        panel.dataset.panelContent === panelName;

                    panel.hidden = !active;
                    panel.classList.toggle('is-active', active);
                });
            };

            sideTabs.forEach(tab => {
                tab.addEventListener('click', () => {
                    showSidePanel(tab.dataset.sidePanel);
                });
            });

            const notificationItems =
                Array.from(document.querySelectorAll('.buyer-notification-item'));

            notificationItems.forEach(item => {
                item.addEventListener('click', () => {
                    item.classList.remove('is-unread');
                    item.querySelector('.buyer-notification-unread-dot')?.remove();
                });
            });

            document
                .getElementById('buyerMarkAllRead')
                ?.addEventListener('click', () => {
                    notificationItems.forEach(item => {
                        item.classList.remove('is-unread');
                        item.querySelector('.buyer-notification-unread-dot')?.remove();
                    });
                });

            const tabs =
                Array.from(
                    document.querySelectorAll('[data-purchase-tab]')
                );

            const rows =
                Array.from(
                    document.querySelectorAll('[data-purchase-row]')
                );

            const search =
                document.getElementById('purchaseSearch');

            const emptyState =
                document.getElementById('purchaseEmptyState');

            const purchaseRows =
                document.getElementById('purchaseRows');

            const initialTab =
                document.body?.dataset?.activePurchaseTab || 'all';

            const validTabs =
                tabs.map(tab => tab.dataset.purchaseTab);

            let activeTab =
                validTabs.includes(initialTab)
                    ? initialTab
                    : 'all';

            const applyPurchaseFilters = () => {
                const query =
                    (search?.value || '')
                        .trim()
                        .toLowerCase();

                let visibleCount = 0;

                rows.forEach(row => {
                    const rowStatus =
                        row.dataset.status || '';

                    const rowSearch =
                        row.dataset.search || '';

                    const tabMatch =
                        activeTab === 'all' ||
                        rowStatus === activeTab;

                    const searchMatch =
                        !query ||
                        rowSearch.includes(query);

                    const show =
                        tabMatch &&
                        searchMatch;

                    row.hidden = !show;

                    if (show) {
                        visibleCount++;
                    }
                });

                if (purchaseRows) {
                    purchaseRows.hidden =
                        visibleCount === 0;
                }

                if (emptyState) {
                    emptyState.hidden =
                        visibleCount > 0;
                }
            };

            tabs.forEach(tab => {
                tab.addEventListener('click', () => {
                    activeTab =
                        tab.dataset.purchaseTab || 'all';

                    tabs.forEach(item => {
                        const active =
                            item === tab;

                        item.classList.toggle(
                            'is-active',
                            active
                        );

                        item.setAttribute(
                            'aria-selected',
                            active ? 'true' : 'false'
                        );
                    });

                    applyPurchaseFilters();
                });
            });

            search?.addEventListener(
                'input',
                applyPurchaseFilters
            );

            tabs.forEach(tab => {
                const active =
                    tab.dataset.purchaseTab === activeTab;

                tab.classList.toggle('is-active', active);
                tab.setAttribute(
                    'aria-selected',
                    active ? 'true' : 'false'
                );
            });

            applyPurchaseFilters();
        });