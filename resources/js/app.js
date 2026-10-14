document.addEventListener('DOMContentLoaded', () => {
    const sidebar = document.getElementById('admin-sidebar');
    const toggle = document.getElementById('sidebar-toggle');
    const navbarLeft = document.getElementById('navbar-left');
    const sidebarLogo = document.getElementById('sidebar-logo');

    // One state manager for every Admin page, including standalone Account and Notifications.
    if (sidebar && toggle && sidebar.dataset.shopeaseToggleReady !== '1') {
        sidebar.dataset.shopeaseToggleReady = '1';
        const links = Array.from(sidebar.querySelectorAll('.sidebar-link'));
        const originalLinks = new Map(links.map(link => [link, link.className]));
        const logout = sidebar.querySelector('.sidebar-logout');
        const originalLogout = logout ? logout.className : '';
        const logoImage = sidebarLogo?.querySelector('img');
        const labelNodes = Array.from(sidebar.querySelectorAll('.sidebar-label'));
        const iconNodes = Array.from(sidebar.querySelectorAll('.sidebar-icon'));
        const wrapperNodes = Array.from(sidebar.querySelectorAll('.sidebar-icon-wrapper'));
        const originalIcons = new Map(iconNodes.map(el => [el, el.className]));
        const originalWrappers = new Map(wrapperNodes.map(el => [el, el.className]));
        const originalLogo = sidebarLogo?.className || '';
        const originalNavbarLeft = navbarLeft?.className || '';

        let collapsed = document.body.classList.contains('sidebar-collapsed');
        function applySidebarState() {
            document.body.classList.toggle('sidebar-collapsed', collapsed);
            document.body.classList.toggle('sidebar-hidden', collapsed);
            document.body.classList.toggle('sidebar-closed', collapsed);
            sidebar.classList.toggle('w-20', collapsed);
            sidebar.classList.toggle('w-72', !collapsed);
            toggle.setAttribute('aria-expanded', String(!collapsed));
            toggle.setAttribute('aria-label', collapsed ? 'Expand sidebar' : 'Collapse sidebar');

            labelNodes.forEach(label => label.classList.toggle('hidden', collapsed));
            if (sidebarLogo) {
                sidebarLogo.className = originalLogo;
                if (collapsed) {
                    sidebarLogo.classList.remove('px-2', 'px-1', 'mb-7', 'mb-10');
                    sidebarLogo.classList.add('h-20', 'mb-6');
                }
            }
            if (logoImage) logoImage.classList.toggle('hidden', collapsed);

            links.forEach(link => {
                link.className = originalLinks.get(link);
                if (collapsed) {
                    link.classList.remove('gap-2.5', 'gap-3', 'px-3', 'px-4', 'py-2.5', 'py-3', 'w-full', 'hover:translate-x-1');
                    link.classList.add('w-10', 'h-10', 'p-0', 'mx-auto', 'justify-center', 'rounded-full');
                    if (link.classList.contains('bg-maroon-700/60')) {
                        link.classList.remove('bg-maroon-700/60');
                        link.classList.add('bg-maroon-700');
                    }
                }
            });
            wrapperNodes.forEach(el => {
                el.className = originalWrappers.get(el);
                if (collapsed) el.classList.add('shrink-0');
            });
            iconNodes.forEach(el => {
                el.className = originalIcons.get(el);
                if (collapsed) {
                    el.classList.remove('w-6', 'h-6');
                    el.classList.add('w-5', 'h-5');
                }
            });
            if (logout) {
                logout.className = originalLogout;
                if (collapsed) {
                    logout.classList.remove('gap-2', 'py-2.5', 'py-3', 'w-full');
                    logout.classList.add('w-10', 'h-10', 'p-0', 'mx-auto', 'rounded-full');
                }
            }
            // Do NOT directly set margins on pages: the shared sidebar CSS synchronizes all containers.
            if (navbarLeft) {
                navbarLeft.className = originalNavbarLeft;
                if (collapsed) navbarLeft.classList.add('shopease-sidebar-collapsed-navbar');
            }
            window.dispatchEvent(new Event('resize'));
        }
        toggle.addEventListener('click', () => {
            collapsed = !collapsed;
            applySidebarState();
        });
        applySidebarState();
    }

    // ==========================================
    // PROFILE DROPDOWN
    // ==========================================

    const profileButton =
        document.getElementById(
            'profile-button'
        );


    const profileDropdown =
        document.getElementById(
            'profile-dropdown'
        );


    if (
        profileButton &&
        profileDropdown
    ) {

        profileButton.addEventListener(
            'click',
            (event) => {

                event.stopPropagation();


                profileDropdown.classList.toggle(
                    'hidden'
                );


                const notificationDropdown =
                    document.getElementById(
                        'notification-dropdown'
                    );


                if (notificationDropdown) {

                    notificationDropdown.classList.add(
                        'hidden'
                    );

                }

            }
        );

    }


    // ==========================================
    // NOTIFICATION DROPDOWN
    // ==========================================

    const notificationButton =
        document.getElementById(
            'notification-button'
        );


    const notificationDropdown =
        document.getElementById(
            'notification-dropdown'
        );


    if (
        notificationButton &&
        notificationDropdown
    ) {

        notificationButton.addEventListener(
            'click',
            (event) => {

                event.stopPropagation();


                notificationDropdown.classList.toggle(
                    'hidden'
                );


                if (profileDropdown) {

                    profileDropdown.classList.add(
                        'hidden'
                    );

                }

            }
        );

    }


    // ==========================================
    // CLICK OUTSIDE DROPDOWNS
    // ==========================================

    document.addEventListener(
        'click',
        () => {

            if (profileDropdown) {

                profileDropdown.classList.add(
                    'hidden'
                );

            }


            if (notificationDropdown) {

                notificationDropdown.classList.add(
                    'hidden'
                );

            }

        }
    );


    // ==========================================
    // PREVENT DROPDOWN CLOSE
    // ==========================================

    if (profileDropdown) {

        profileDropdown.addEventListener(
            'click',
            (event) => {

                event.stopPropagation();

            }
        );

    }


    if (notificationDropdown) {

        notificationDropdown.addEventListener(
            'click',
            (event) => {

                event.stopPropagation();

            }
        );

    }


    // ==========================================
    // BUTTON PRESS ANIMATION
    // ==========================================

    document
        .querySelectorAll('button')
        .forEach(button => {

            button.addEventListener(
                'mousedown',
                () => {

                    button.classList.add(
                        'scale-[0.97]'
                    );

                }
            );


            button.addEventListener(
                'mouseup',
                () => {

                    button.classList.remove(
                        'scale-[0.97]'
                    );

                }
            );


            button.addEventListener(
                'mouseleave',
                () => {

                    button.classList.remove(
                        'scale-[0.97]'
                    );

                }
            );

        });

});


// ======================================================
// SIDEBAR RELOAD UX
// ======================================================

document.addEventListener(
    'DOMContentLoaded',
    () => {

        const sidebar =
            document.getElementById(
                'admin-sidebar'
            );


        if (!sidebar) {
            return;
        }


        // ==========================================
        // LOGO RELOAD
        // ==========================================

        const logo =
            document.getElementById(
                'sidebar-logo'
            );


        // ==========================================
        // MENU ITEMS
        // ==========================================

        const menuItems =
            sidebar.querySelectorAll(
                '.sidebar-menu-item'
            );


        // ==========================================
        // LOGOUT
        // ==========================================

        const logout =
            sidebar.querySelector(
                '.sidebar-logout-reload'
            );


        // ==========================================
        // RESTART LOGO ANIMATION
        // ==========================================

        if (logo) {

            logo.classList.remove(
                'sidebar-logo-reload'
            );


            void logo.offsetWidth;


            logo.classList.add(
                'sidebar-logo-reload'
            );

        }


        // ==========================================
        // RESTART MENU ANIMATIONS
        // ==========================================

        menuItems.forEach(
            item => {

                item.classList.remove(
                    'sidebar-menu-item'
                );


                void item.offsetWidth;


                item.classList.add(
                    'sidebar-menu-item'
                );

            }
        );


        // ==========================================
        // RESTART LOGOUT ANIMATION
        // ==========================================

        if (logout) {

            logout.classList.remove(
                'sidebar-logout-reload'
            );


            void logout.offsetWidth;


            logout.classList.add(
                'sidebar-logout-reload'
            );

        }

    }
);