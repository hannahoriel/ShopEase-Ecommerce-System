document.addEventListener('DOMContentLoaded', () => {

    // ==========================================
    // ELEMENTS
    // ==========================================

    const sidebar =
        document.getElementById('admin-sidebar');

    const toggle =
        document.getElementById('sidebar-toggle');

    const content =
        document.getElementById('admin-content');

    const navbarLeft =
        document.getElementById('navbar-left');

    const sidebarLogo =
        document.getElementById('sidebar-logo');


    if (!sidebar || !toggle) {
        return;
    }


    // ==========================================
    // STORE ORIGINAL SIDEBAR CLASSES
    // ==========================================
    //
    // This is important because the sidebar has
    // specific hover, radius, spacing and active
    // classes that must return exactly after expand.
    //

    const originalSidebarLinkClasses = new Map();

    sidebar
        .querySelectorAll('.sidebar-link')
        .forEach(link => {

            originalSidebarLinkClasses.set(
                link,
                link.className
            );

        });


    const logout =
        sidebar.querySelector('.sidebar-logout');


    const originalLogoutClasses =
        logout
            ? logout.className
            : '';


    // ==========================================
    // COLLAPSED STATE
    // ==========================================

    let collapsed = false;


    // ==========================================
    // SIDEBAR TOGGLE
    // ==========================================

    toggle.addEventListener('click', () => {

        collapsed = !collapsed;


        // ==================================================
        // COLLAPSED
        // ==================================================

        if (collapsed) {


            // ==========================================
            // SIDEBAR WIDTH
            // ==========================================

            sidebar.classList.remove(
                'w-72'
            );

            sidebar.classList.add(
                'w-20'
            );


            // ==========================================
            // LOGO
            // ==========================================

            if (sidebarLogo) {

                sidebarLogo.classList.remove(
                    'px-2',
                    'mb-10'
                );

                sidebarLogo.classList.add(
                    'h-20',
                    'mb-6'
                );


                const logoImage =
                    sidebarLogo.querySelector('img');


                if (logoImage) {

                    logoImage.classList.add(
                        'hidden'
                    );

                }

            }


            // ==========================================
            // HIDE LABELS
            // ==========================================

            sidebar
                .querySelectorAll('.sidebar-label')
                .forEach(label => {

                    label.classList.add(
                        'hidden'
                    );

                });


            // ==========================================
            // SIDEBAR LINKS
            // ==========================================
            //
            // IMPORTANT:
            // We only change the dimensions/position.
            //
            // We DO NOT remove the original hover
            // background classes or rounded-full.
            // This keeps the sidebar beautiful when
            // collapsed.
            //

            sidebar
                .querySelectorAll('.sidebar-link')
                .forEach(link => {

                    link.classList.remove(
                        'gap-3',
                        'px-4',
                        'py-3',
                        'w-full',
                        'hover:translate-x-1'
                    );


                    link.classList.add(
                        'w-10',
                        'h-10',
                        'p-0',
                        'mx-auto',
                        'justify-center',
                        'rounded-full'
                    );


                    // ==================================
                    // ACTIVE ITEM
                    // ==================================

                    if (
                        link.classList.contains(
                            'bg-maroon-700/60'
                        )
                    ) {

                        link.classList.remove(
                            'bg-maroon-700/60'
                        );

                        link.classList.add(
                            'bg-maroon-700'
                        );

                    }

                });


            // ==========================================
            // ICON WRAPPERS
            // ==========================================

            sidebar
                .querySelectorAll(
                    '.sidebar-icon-wrapper'
                )
                .forEach(wrapper => {

                    wrapper.classList.remove(
                        'w-5',
                        'h-5'
                    );

                    wrapper.classList.add(
                        'w-5',
                        'h-5',
                        'shrink-0'
                    );

                });


            // ==========================================
            // ICONS
            // ==========================================

            sidebar
                .querySelectorAll('.sidebar-icon')
                .forEach(icon => {

                    icon.classList.remove(
                        'w-6',
                        'h-6'
                    );

                    icon.classList.add(
                        'w-5',
                        'h-5',
                        'object-contain'
                    );

                });


            // ==========================================
            // LOGOUT
            // ==========================================

            if (logout) {

                logout.classList.remove(
                    'gap-2',
                    'py-3',
                    'w-full'
                );

                logout.classList.add(
                    'w-10',
                    'h-10',
                    'p-0',
                    'mx-auto',
                    'rounded-full'
                );

            }


            // ==========================================
            // CONTENT
            // ==========================================

            if (content) {

                content.classList.remove(
                    'ml-72'
                );

                content.classList.add(
                    'ml-20'
                );

            }


            // ==========================================
            // NAVBAR LEFT
            // ==========================================

            if (navbarLeft) {

                navbarLeft.classList.remove(
                    'ml-[338px]'
                );

                navbarLeft.classList.add(
                    'ml-[108px]'
                );

            }

        }


        // ==================================================
        // EXPANDED
        // ==================================================

        else {


            // ==========================================
            // SIDEBAR WIDTH
            // ==========================================

            sidebar.classList.remove(
                'w-20'
            );

            sidebar.classList.add(
                'w-72'
            );


            // ==========================================
            // LOGO
            // ==========================================

            if (sidebarLogo) {

                sidebarLogo.classList.remove(
                    'h-20',
                    'mb-6'
                );

                sidebarLogo.classList.add(
                    'px-2',
                    'mb-10'
                );


                const logoImage =
                    sidebarLogo.querySelector('img');


                if (logoImage) {

                    logoImage.classList.remove(
                        'hidden'
                    );

                }

            }


            // ==========================================
            // SHOW LABELS
            // ==========================================

            sidebar
                .querySelectorAll('.sidebar-label')
                .forEach(label => {

                    label.classList.remove(
                        'hidden'
                    );

                });


            // ==========================================
            // RESTORE ORIGINAL SIDEBAR LINKS
            // ==========================================
            //
            // Instead of manually rebuilding classes,
            // restore the EXACT class list each item
            // originally had.
            //

            sidebar
                .querySelectorAll('.sidebar-link')
                .forEach(link => {

                    const originalClasses =
                        originalSidebarLinkClasses.get(
                            link
                        );


                    if (originalClasses) {

                        link.className =
                            originalClasses;

                    }

                });


            // ==========================================
            // RESTORE ORIGINAL LOGOUT
            // ==========================================

            if (
                logout &&
                originalLogoutClasses
            ) {

                logout.className =
                    originalLogoutClasses;

            }


            // ==========================================
            // CONTENT
            // ==========================================

            if (content) {

                content.classList.remove(
                    'ml-20'
                );

                content.classList.add(
                    'ml-72'
                );

            }


            // ==========================================
            // NAVBAR LEFT
            // ==========================================

            if (navbarLeft) {

                navbarLeft.classList.remove(
                    'ml-[108px]'
                );

                navbarLeft.classList.add(
                    'ml-[338px]'
                );

            }

        }

    });


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