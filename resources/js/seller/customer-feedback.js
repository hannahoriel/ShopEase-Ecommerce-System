document.addEventListener(
            'DOMContentLoaded',
            function () {

                const tabs =
                    document.querySelectorAll(
                        '.feedback-tab'
                    );


                const allFeedbackPanel =
                    document.getElementById(
                        'allFeedbackPanel'
                    );


                const productRatingsPanel =
                    document.getElementById(
                        'productRatingsPanel'
                    );


                const searchInput =
                    document.getElementById(
                        'feedbackSearch'
                    );


                const ratingFilter =
                    document.getElementById(
                        'feedbackRatingFilter'
                    );


                const refreshButton =
                    document.getElementById(
                        'feedbackRefresh'
                    );


                const subtitle =
                    document.getElementById(
                        'feedbackSubtitle'
                    );


                const modal =
                    document.getElementById(
                        'productReviewsModal'
                    );


                const closeModalButton =
                    document.getElementById(
                        'closeShippingModal'
                    );


                let currentTab =
                    'feedback';


                /* =================================================
                   TABS
                ================================================== */

                tabs.forEach(
                    function (tab) {

                        tab.addEventListener(
                            'click',
                            function () {

                                tabs.forEach(
                                    function (item) {

                                        item.classList.remove(
                                            'active'
                                        );

                                    }
                                );


                                tab.classList.add(
                                    'active'
                                );


                                currentTab =
                                    tab.dataset.tab;


                                const showFeedback =
                                    currentTab ===
                                    'feedback';


                                allFeedbackPanel.style.display =
                                    showFeedback
                                        ? 'block'
                                        : 'none';


                                productRatingsPanel.classList.toggle(
                                    'active',
                                    !showFeedback
                                );


                                ratingFilter.style.display =
                                    showFeedback
                                        ? ''
                                        : 'none';


                                refreshButton.style.display =
                                    showFeedback
                                        ? ''
                                        : 'none';


                                searchInput.placeholder =
                                    showFeedback
                                        ? 'Search order ID or customer name'
                                        : 'Search Product';


                                subtitle.textContent =
                                    showFeedback
                                        ? 'View feedbacks from your customers.'
                                        : 'View and manage new orders from your customers.';


                                searchInput.value =
                                    '';


                                ratingFilter.value =
                                    'all';


                                applyFilters();

                            }
                        );

                    }
                );


                /* =================================================
                   FILTERING
                ================================================== */

                function applyFilters() {

                    const query =
                        searchInput.value
                            .trim()
                            .toLowerCase();


                    if (
                        currentTab ===
                        'feedback'
                    ) {

                        const rows =
                            document.querySelectorAll(
                                '.feedback-row'
                            );


                        const selectedRating =
                            ratingFilter.value;


                        rows.forEach(
                            function (row) {

                                const matchSearch =
                                    !query ||
                                    (
                                        row.dataset.search ||
                                        ''
                                    ).includes(
                                        query
                                    );


                                const matchRating =
                                    selectedRating ===
                                    'all' ||
                                    row.dataset.rating ===
                                    selectedRating;


                                row.style.display =
                                    matchSearch &&
                                    matchRating
                                        ? ''
                                        : 'none';

                            }
                        );

                    } else {

                        const cards =
                            document.querySelectorAll(
                                '.product-card'
                            );


                        cards.forEach(
                            function (card) {

                                const match =
                                    !query ||
                                    (
                                        card.dataset.search ||
                                        ''
                                    ).includes(
                                        query
                                    );


                                card.style.display =
                                    match
                                        ? ''
                                        : 'none';

                            }
                        );

                    }

                }


                searchInput.addEventListener(
                    'input',
                    applyFilters
                );


                ratingFilter.addEventListener(
                    'change',
                    applyFilters
                );


                refreshButton.addEventListener(
                    'click',
                    function () {

                        searchInput.value =
                            '';

                        ratingFilter.value =
                            'all';

                        applyFilters();


                        refreshButton.animate(
                            [
                                {
                                    transform:
                                        'rotate(0deg)'
                                },

                                {
                                    transform:
                                        'rotate(360deg)'
                                }
                            ],
                            {
                                duration:
                                    400,

                                easing:
                                    'ease'
                            }
                        );

                    }
                );


                /* =================================================
                   PRODUCT MODAL
                ================================================== */

                document
                    .querySelectorAll(
                        '.product-card'
                    )
                    .forEach(
                        function (card) {

                            card.addEventListener(
                                'click',
                                function () {

                                    if (modalCloseTimer) {

                                        window.clearTimeout(
                                            modalCloseTimer
                                        );

                                        modalCloseTimer =
                                            null;

                                    }


                                    modal.classList.remove(
                                        'is-closing'
                                    );


                                    modal.classList.add(
                                        'open'
                                    );


                                    modal.setAttribute(
                                        'aria-hidden',
                                        'false'
                                    );


                                    document.body.style.overflow =
                                        'hidden';

                                }
                            );

                        }
                    );


                let modalCloseTimer =
                    null;


                function closeModal() {

                    if (
                        !modal ||
                        !modal.classList.contains(
                            'open'
                        )
                    ) {
                        return;
                    }


                    if (modalCloseTimer) {

                        window.clearTimeout(
                            modalCloseTimer
                        );

                    }


                    /*
                     * Shipping Status-style closing:
                     * fade only, no scale/slide.
                     */
                    modal.classList.add(
                        'is-closing'
                    );


                    modalCloseTimer =
                        window.setTimeout(
                            function () {

                                modal.classList.remove(
                                    'open',
                                    'is-closing'
                                );


                                modal.setAttribute(
                                    'aria-hidden',
                                    'true'
                                );


                                document.body.style.overflow =
                                    '';


                                modalCloseTimer =
                                    null;

                            },
                            220
                        );

                }


                closeModalButton.addEventListener(
                    'click',
                    closeModal
                );


                modal.addEventListener(
                    'click',
                    function (event) {

                        if (
                            event.target ===
                            modal
                        ) {

                            closeModal();

                        }

                    }
                );


                document.addEventListener(
                    'keydown',
                    function (event) {

                        if (
                            event.key ===
                            'Escape' &&
                            modal.classList.contains(
                                'open'
                            )
                        ) {

                            closeModal();

                        }

                    }
                );


                /* =================================================
                   SIDEBAR COLLAPSE SYNC
                   Same expanded/collapsed content spacing as Shipping.
                ================================================== */

                const feedbackPage =
                    document.getElementById(
                        'feedbackPage'
                    );


                const sellerSidebar =
                    document.getElementById(
                        'sellerSidebar'
                    );


                function syncFeedbackSidebarState(
                    collapsed
                ) {

                    feedbackPage
                        ?.classList.toggle(
                            'sidebar-collapsed',
                            Boolean(
                                collapsed
                            )
                        );

                }


                document.body.addEventListener(
                    'seller-sidebar-state-changed',
                    function (event) {

                        syncFeedbackSidebarState(
                            event.detail
                                ?.collapsed
                        );

                    }
                );


                /*
                 * Also sync on initial load so the content spacing
                 * is correct even when the sidebar was already
                 * collapsed before this page finished loading.
                 */
                if (sellerSidebar) {

                    const getSidebarCollapsedState =
                        function () {

                            return (
                                sellerSidebar.classList.contains(
                                    'seller-sidebar-collapsed'
                                ) ||
                                sellerSidebar.classList.contains(
                                    'w-20'
                                )
                            );

                        };


                    syncFeedbackSidebarState(
                        getSidebarCollapsedState()
                    );


                    const sidebarObserver =
                        new MutationObserver(
                            function () {

                                syncFeedbackSidebarState(
                                    getSidebarCollapsedState()
                                );

                            }
                        );


                    sidebarObserver.observe(
                        sellerSidebar,
                        {
                            attributes:
                                true,

                            attributeFilter:
                                [
                                    'class'
                                ]
                        }
                    );

                }

            }
        );