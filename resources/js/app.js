document.addEventListener('DOMContentLoaded', () => {

    // ==========================================
    // ELEMENTS
    // ==========================================

    const sidebar = document.getElementById('admin-sidebar');
    const toggle = document.getElementById('sidebar-toggle');
    const content = document.getElementById('admin-content');
    const navbarLeft = document.getElementById('navbar-left');
    const sidebarLogo = document.getElementById('sidebar-logo');

    if (!sidebar || !toggle) return;


    // ==========================================
    // SIDEBAR TOGGLE
    // ==========================================

    let collapsed = false;

    toggle.addEventListener('click', () => {

        collapsed = !collapsed;


        // ==========================================
        // COLLAPSED
        // ==========================================

        if (collapsed) {

            // Sidebar width
            sidebar.classList.remove('w-72');
            sidebar.classList.add('w-20');

            // Sidebar horizontal padding
            sidebar.classList.remove('px-5');
            sidebar.classList.add('px-5');


            // ------------------------------------------
            // LOGO
            // ------------------------------------------

            if (sidebarLogo) {

                sidebarLogo.classList.remove(
                    'px-2',
                    'mb-10'
                );

                sidebarLogo.classList.add(
                    'h-20',
                    'mb-6'
                );

                // Hide logo completely so it won't become tiny
                const logoImage =
                    sidebarLogo.querySelector('img');

                if (logoImage) {
                    logoImage.classList.add('hidden');
                }

            }


            // ------------------------------------------
            // SIDEBAR LABELS
            // ------------------------------------------

            document
                .querySelectorAll('.sidebar-label')
                .forEach(label => {

                    label.classList.add('hidden');

                });


            // ------------------------------------------
            // SIDEBAR LINKS
            // ------------------------------------------

            document
                .querySelectorAll('.sidebar-link')
                .forEach(link => {

                    // Remove normal desktop sizing
                    link.classList.remove(
                        'gap-3',
                        'px-4',
                        'py-3',
                        'w-full',
                        'hover:translate-x-1'
                    );

                    // Fixed icon button
                    link.classList.add(
                        'w-10',
                        'h-10',
                        'p-0',
                        'mx-auto',
                        'justify-center',
                        'rounded-full'
                    );

                });


            // ------------------------------------------
            // ACTIVE ICON
            // ------------------------------------------

            document
                .querySelectorAll('.sidebar-link')
                .forEach(link => {

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


            // ------------------------------------------
            // ICON WRAPPERS
            // ------------------------------------------

            document
                .querySelectorAll('.sidebar-icon-wrapper')
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


            // ------------------------------------------
            // ICONS
            // ------------------------------------------

            document
                .querySelectorAll('.sidebar-icon')
                .forEach(icon => {

                    // Keep EXACT same icon size
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


            // ------------------------------------------
            // LOGOUT
            // ------------------------------------------

            const logout =
                document.querySelector('.sidebar-logout');

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


            // ------------------------------------------
            // CONTENT
            // ------------------------------------------

            if (content) {

                content.classList.remove('ml-72');
                content.classList.add('ml-20');

            }


            // ------------------------------------------
            // NAVBAR
            // ------------------------------------------

            if (navbarLeft) {

                navbarLeft.classList.remove(
                    'ml-[338px]'
                );

                navbarLeft.classList.add(
                    'ml-[108px]'
                );

            }

        }


        // ==========================================
        // EXPANDED
        // ==========================================

        else {

            // Sidebar width
            sidebar.classList.remove('w-20');
            sidebar.classList.add('w-72');


            // ------------------------------------------
            // LOGO
            // ------------------------------------------

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
                    logoImage.classList.remove('hidden');
                }

            }


            // ------------------------------------------
            // SIDEBAR LABELS
            // ------------------------------------------

            document
                .querySelectorAll('.sidebar-label')
                .forEach(label => {

                    label.classList.remove('hidden');

                });


            // ------------------------------------------
            // SIDEBAR LINKS
            // ------------------------------------------

            document
                .querySelectorAll('.sidebar-link')
                .forEach(link => {

                    link.classList.remove(
                        'w-10',
                        'h-10',
                        'p-0',
                        'mx-auto',
                        'justify-center',
                        'rounded-full'
                    );

                    link.classList.add(
                        'gap-3',
                        'px-4',
                        'py-3',
                        'w-full'
                    );

                });


            // ------------------------------------------
            // ACTIVE ICON
            // ------------------------------------------

            document
                .querySelectorAll('.sidebar-link')
                .forEach(link => {

                    if (
                        link.classList.contains(
                            'bg-maroon-700'
                        )
                    ) {

                        link.classList.remove(
                            'bg-maroon-700'
                        );

                        link.classList.add(
                            'bg-maroon-700/60'
                        );

                    }

                });


            // ------------------------------------------
            // LOGOUT
            // ------------------------------------------

            const logout =
                document.querySelector('.sidebar-logout');

            if (logout) {

                logout.classList.remove(
                    'w-10',
                    'h-10',
                    'p-0',
                    'mx-auto',
                    'rounded-full'
                );

                logout.classList.add(
                    'gap-2',
                    'py-3',
                    'w-full'
                );

            }


            // ------------------------------------------
            // CONTENT
            // ------------------------------------------

            if (content) {

                content.classList.remove('ml-20');
                content.classList.add('ml-72');

            }


            // ------------------------------------------
            // NAVBAR
            // ------------------------------------------

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
        document.getElementById('profile-button');

    const profileDropdown =
        document.getElementById('profile-dropdown');


    if (profileButton && profileDropdown) {

        profileButton.addEventListener('click', (event) => {

            event.stopPropagation();

            profileDropdown.classList.toggle('hidden');


            const notificationDropdown =
                document.getElementById(
                    'notification-dropdown'
                );

            if (notificationDropdown) {

                notificationDropdown.classList.add(
                    'hidden'
                );

            }

        });

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

    document.addEventListener('click', () => {

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

    });



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

// ==========================================
// SIDEBAR RELOAD UX
// ==========================================

document.addEventListener('DOMContentLoaded', () => {

    const sidebar = document.getElementById('admin-sidebar');

    if (!sidebar) return;

    // Restart sidebar animations on page reload
    const logo = document.getElementById('sidebar-logo');
    const menuItems = document.querySelectorAll('.sidebar-menu-item');
    const logout = document.querySelector('.sidebar-logout-reload');

    if (logo) {
        logo.classList.remove('sidebar-logo-reload');

        void logo.offsetWidth;

        logo.classList.add('sidebar-logo-reload');
    }

    menuItems.forEach(item => {

        item.classList.remove('sidebar-menu-item');

        void item.offsetWidth;

        item.classList.add('sidebar-menu-item');

    });

    if (logout) {

        logout.classList.remove('sidebar-logout-reload');

        void logout.offsetWidth;

        logout.classList.add('sidebar-logout-reload');

    }

});