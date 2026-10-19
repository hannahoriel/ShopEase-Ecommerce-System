document.addEventListener('DOMContentLoaded', function () {

            const page = document.getElementById(
                'sellerNotificationsPage'
            );

            const sidebar = document.getElementById(
                'sellerSidebar'
            );

            if (!page) {
                return;
            }

            function updateSidebarOffset() {

                if (window.innerWidth <= 760) {
                    page.style.marginLeft = '0px';
                    return;
                }

                const collapsed = sidebar &&
                    sidebar.classList.contains(
                        'seller-sidebar-collapsed'
                    );

                page.style.marginLeft = collapsed
                    ? '100px'
                    : '270px';
            }

            updateSidebarOffset();

            window.addEventListener(
                'resize',
                updateSidebarOffset
            );

            if (sidebar && typeof MutationObserver !== 'undefined') {

                const observer = new MutationObserver(function () {
                    requestAnimationFrame(updateSidebarOffset);
                });

                observer.observe(sidebar, {
                    attributes: true,
                    attributeFilter: ['class']
                });
            }

            document.body.addEventListener(
                'toggle-seller-sidebar',
                function () {
                    requestAnimationFrame(updateSidebarOffset);
                }
            );

        });