document.addEventListener('DOMContentLoaded', function () {

            /* =====================================================
               GALLERY
            ====================================================== */
            const thumbnails =
                Array.from(
                    document.querySelectorAll('.gallery-thumb')
                );

            const mainProductVisual =
                document.getElementById('mainProductVisual');

            let activeGalleryIndex = 0;

            function setGalleryIndex(index) {
                activeGalleryIndex =
                    (
                        index +
                        thumbnails.length
                    ) %
                    thumbnails.length;

                thumbnails.forEach(
                    (thumb, thumbIndex) => {
                        thumb.classList.toggle(
                            'active',
                            thumbIndex === activeGalleryIndex
                        );
                    }
                );

                mainProductVisual.classList.add(
                    'is-switching'
                );

                window.setTimeout(
                    () => {
                        mainProductVisual.classList.remove(
                            'is-switching'
                        );
                    },
                    160
                );
            }

            thumbnails.forEach(
                (thumb, index) => {
                    thumb.addEventListener(
                        'click',
                        () => setGalleryIndex(index)
                    );
                }
            );

            document
                .getElementById('galleryPrev')
                ?.addEventListener(
                    'click',
                    () =>
                        setGalleryIndex(
                            activeGalleryIndex - 1
                        )
                );

            document
                .getElementById('galleryNext')
                ?.addEventListener(
                    'click',
                    () =>
                        setGalleryIndex(
                            activeGalleryIndex + 1
                        )
                );

            /* =====================================================
               VARIATIONS
            ====================================================== */
            document
                .querySelectorAll('.variation-button')
                .forEach(
                    button => {
                        button.addEventListener(
                            'click',
                            function () {
                                document
                                    .querySelectorAll('.variation-button')
                                    .forEach(
                                        item =>
                                            item.classList.remove(
                                                'selected'
                                            )
                                    );

                                this.classList.add(
                                    'selected'
                                );
                            }
                        );
                    }
                );

            /* =====================================================
               QUANTITY
            ====================================================== */
            const quantityInput =
                document.getElementById('productQuantity');

            const minQuantity =
                Number(
                    quantityInput?.min ||
                    1
                );

            const maxQuantity =
                Number(
                    quantityInput?.max ||
                    99
                );

            document
                .getElementById('quantityMinus')
                ?.addEventListener(
                    'click',
                    function () {
                        quantityInput.value =
                            Math.max(
                                minQuantity,
                                Number(quantityInput.value || 1) - 1
                            );
                    }
                );

            document
                .getElementById('quantityPlus')
                ?.addEventListener(
                    'click',
                    function () {
                        quantityInput.value =
                            Math.min(
                                maxQuantity,
                                Number(quantityInput.value || 1) + 1
                            );
                    }
                );

            /* =====================================================
               RATINGS FILTERS
            ====================================================== */
            const ratingFilterButtons =
                Array.from(
                    document.querySelectorAll('.rating-filter')
                );

            const reviewItems =
                Array.from(
                    document.querySelectorAll('.review-item')
                );

            const ratingsFilterEmpty =
                document.getElementById('ratingsFilterEmpty');

            function applyRatingFilter(filter) {
                let visibleCount = 0;

                reviewItems.forEach(review => {
                    const rating =
                        String(review.dataset.rating || '');

                    const hasComments =
                        review.dataset.hasComments === 'true';

                    const hasMedia =
                        review.dataset.hasMedia === 'true';

                    let shouldShow = false;

                    if (filter === 'all') {
                        shouldShow = true;
                    } else if (filter === 'comments') {
                        shouldShow = hasComments;
                    } else if (filter === 'media') {
                        shouldShow = hasMedia;
                    } else {
                        shouldShow = rating === filter;
                    }

                    review.hidden =
                        !shouldShow;

                    if (shouldShow) {
                        visibleCount++;
                    }
                });

                if (ratingsFilterEmpty) {
                    ratingsFilterEmpty.hidden =
                        visibleCount > 0;
                }
            }

            ratingFilterButtons.forEach(button => {
                button.addEventListener(
                    'click',
                    function () {
                        ratingFilterButtons.forEach(
                            item =>
                                item.classList.remove('active')
                        );

                        this.classList.add('active');

                        applyRatingFilter(
                            this.dataset.ratingFilter || 'all'
                        );
                    }
                );
            });

            applyRatingFilter('all');

            /* =====================================================
               ADD TO CART / BUY NOW
            ====================================================== */
            const addToCartButton =
                document.getElementById('addToCartButton');

            addToCartButton?.addEventListener(
                'click',
                function () {
                    const originalText =
                        'Add to Cart';

                    this.classList.add(
                        'is-added'
                    );

                    this.lastChild.textContent =
                        ' Added to Cart';

                    window.setTimeout(
                        () => {
                            this.classList.remove(
                                'is-added'
                            );

                            this.lastChild.textContent =
                                ` ${originalText}`;
                        },
                        1200
                    );
                }
            );

            document
                .getElementById('buyNowButton')
                ?.addEventListener(
                    'click',
                    function () {
                        console.log(
                            'Buy now clicked.',
                            {
                                quantity:
                                    Number(
                                        quantityInput?.value ||
                                        1
                                    ),

                                variation:
                                    document
                                        .querySelector(
                                            '.variation-button.selected'
                                        )
                                        ?.textContent
                                        .trim() ||
                                    ''
                            }
                        );
                    }
                );

            /* =====================================================
               DASHBOARD-STYLE RECOMMENDED CART BUTTONS
            ====================================================== */
            document
                .querySelectorAll('.product-cart-button')
                .forEach(
                    button => {
                        button.addEventListener(
                            'click',
                            function () {
                                this.classList.add(
                                    'added'
                                );

                                window.setTimeout(
                                    () =>
                                        this.classList.remove(
                                            'added'
                                        ),
                                    700
                                );
                            }
                        );
                    }
                );

        });