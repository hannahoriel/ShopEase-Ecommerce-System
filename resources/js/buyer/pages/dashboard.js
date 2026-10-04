document.addEventListener('DOMContentLoaded', () => {
            /* My Purchases card navigation */
            const buyerPurchasesCard =
                document.getElementById('buyerPurchasesCard');

            const openMyPurchases = () => {
                const url =
                    buyerPurchasesCard?.dataset.purchasesUrl;

                if (url) {
                    window.location.href = url;
                }
            };

            buyerPurchasesCard?.addEventListener(
                'click',
                openMyPurchases
            );

            buyerPurchasesCard?.addEventListener(
                'keydown',
                event => {
                    if (
                        event.key === 'Enter' ||
                        event.key === ' '
                    ) {
                        event.preventDefault();
                        openMyPurchases();
                    }
                }
            );

            const recommendedProducts = document.getElementById('recommendedProducts');

            /* Announcement carousel */
            const announcementCarousel =
                document.getElementById('buyerAnnouncementCarousel');

            const announcementSlides =
                Array.from(
                    document.querySelectorAll('[data-announcement-slide]')
                );

            const announcementDots =
                Array.from(
                    document.querySelectorAll('[data-announcement-dot]')
                );

            const announcementPrev =
                document.getElementById('announcementPrev');

            const announcementNext =
                document.getElementById('announcementNext');

            let announcementIndex = 0;
            let announcementTimer = null;

            const showAnnouncement = (index) => {
                if (!announcementSlides.length) return;

                announcementIndex =
                    (index + announcementSlides.length) %
                    announcementSlides.length;

                announcementSlides.forEach((slide, slideIndex) => {
                    const active = slideIndex === announcementIndex;

                    slide.classList.toggle('is-active', active);
                    slide.setAttribute(
                        'aria-hidden',
                        active ? 'false' : 'true'
                    );
                });

                announcementDots.forEach((dot, dotIndex) => {
                    const active = dotIndex === announcementIndex;

                    dot.classList.toggle('active', active);
                    dot.setAttribute(
                        'aria-selected',
                        active ? 'true' : 'false'
                    );
                });
            };

            const stopAnnouncementAutoPlay = () => {
                if (announcementTimer) {
                    window.clearInterval(announcementTimer);
                    announcementTimer = null;
                }
            };

            const startAnnouncementAutoPlay = () => {
                stopAnnouncementAutoPlay();

                announcementTimer = window.setInterval(() => {
                    showAnnouncement(announcementIndex + 1);
                }, 1900);
            };

            announcementPrev?.addEventListener('click', () => {
                showAnnouncement(announcementIndex - 1);
                startAnnouncementAutoPlay();
            });

            announcementNext?.addEventListener('click', () => {
                showAnnouncement(announcementIndex + 1);
                startAnnouncementAutoPlay();
            });

            announcementDots.forEach((dot, index) => {
                dot.addEventListener('click', () => {
                    showAnnouncement(index);
                    startAnnouncementAutoPlay();
                });
            });

            announcementCarousel?.addEventListener(
                'mouseenter',
                stopAnnouncementAutoPlay
            );

            announcementCarousel?.addEventListener(
                'mouseleave',
                startAnnouncementAutoPlay
            );

            announcementCarousel?.addEventListener(
                'focusin',
                stopAnnouncementAutoPlay
            );

            announcementCarousel?.addEventListener(
                'focusout',
                startAnnouncementAutoPlay
            );

            document
                .querySelectorAll('[data-scroll-target="recommendedProducts"]')
                .forEach((button) => {
                    button.addEventListener('click', () => {
                        recommendedProducts?.scrollIntoView({
                            behavior: 'smooth',
                            block: 'start'
                        });
                    });
                });

            showAnnouncement(0);
            startAnnouncementAutoPlay();

            /* Category selection */
            const categoryCards = document.querySelectorAll('.category-card');

            categoryCards.forEach((card) => {
                card.addEventListener('click', () => {
                    categoryCards.forEach((item) => item.classList.remove('selected'));
                    card.classList.add('selected');
                    console.log('Selected category:', card.dataset.category || '');
                });
            });

            /* View all categories */
            document.getElementById('viewAllCategories')?.addEventListener('click', () => {
                document.getElementById('buyerCategories')?.scrollIntoView({
                    behavior: 'smooth',
                    block: 'center'
                });
            });

            /* Favorites */
            document.querySelectorAll('.favorite-button').forEach((button) => {
                button.addEventListener('click', (event) => {
                    event.stopPropagation();
                    const active = button.classList.toggle('is-favorite');
                    button.textContent = active ? '♥' : '♡';
                    button.setAttribute('aria-pressed', active ? 'true' : 'false');
                });
            });

            /* Add to cart */
            document.querySelectorAll('.product-cart-button').forEach((button) => {
                button.addEventListener('click', (event) => {
                    event.stopPropagation();

                    const image = button.querySelector('img');
                    const originalSrc = image?.getAttribute('src');

                    button.classList.add('added');
                    button.innerHTML = '<span style="font-size:12px;font-weight:700;color:#477B4E;">✓</span>';

                    window.setTimeout(() => {
                        button.classList.remove('added');
                        button.innerHTML = '';

                        if (originalSrc) {
                            const newImage = document.createElement('img');
                            newImage.src = originalSrc;
                            newImage.alt = '';
                            newImage.width = 18;
                            newImage.height = 18;
                            newImage.className = 'clean-icon';
                            newImage.dataset.cleanBg = 'true';
                            button.appendChild(newImage);
                            removePngBackground(newImage);
                        }
                    }, 900);
                });
            });

            /* Latest notifications */
            document.querySelectorAll('.notification-row').forEach((notification) => {
                notification.addEventListener('click', () => {
                    notification.classList.remove('is-unread');

                    const unreadDot =
                        notification.querySelector('.notification-dot');

                    unreadDot?.remove();
                });
            });

            /* Purchase status navigation */
            document.querySelectorAll('.purchase-card').forEach((card) => {
                card.addEventListener('click', (event) => {
                    event.stopPropagation();

                    const url =
                        card.dataset.purchaseStatusUrl;

                    if (url) {
                        window.location.href = url;
                    }
                });
            });

            /*
             * Remove a baked square/pastel background from supplied PNG icons.
             * It samples the edge/background color and flood-fills only connected
             * pixels close to that color, preserving the central icon artwork.
             */
            function removePngBackground(img) {
                if (!img || img.dataset.cleaned === 'true') return;

                const clean = () => {
                    if (!img.naturalWidth || !img.naturalHeight) return;

                    const canvas = document.createElement('canvas');
                    canvas.width = img.naturalWidth;
                    canvas.height = img.naturalHeight;

                    const ctx = canvas.getContext('2d', { willReadFrequently: true });
                    if (!ctx) return;

                    ctx.drawImage(img, 0, 0);

                    let imageData;
                    try {
                        imageData = ctx.getImageData(0, 0, canvas.width, canvas.height);
                    } catch (error) {
                        return;
                    }

                    const { data, width, height } = imageData;

                    const sample = (x, y) => {
                        const i = (y * width + x) * 4;
                        return [data[i], data[i + 1], data[i + 2], data[i + 3]];
                    };

                    const edgeSamples = [
                        sample(0, 0),
                        sample(width - 1, 0),
                        sample(0, height - 1),
                        sample(width - 1, height - 1),
                        sample(Math.floor(width / 2), 0),
                        sample(Math.floor(width / 2), height - 1)
                    ].filter((p) => p[3] > 0);

                    if (!edgeSamples.length) return;

                    const background = edgeSamples.reduce(
                        (acc, p) => [acc[0] + p[0], acc[1] + p[1], acc[2] + p[2]],
                        [0, 0, 0]
                    ).map((v) => v / edgeSamples.length);

                    const visited = new Uint8Array(width * height);
                    const queue = [];
                    const tolerance = 48;

                    const colorDistance = (r, g, b) => {
                        return Math.sqrt(
                            Math.pow(r - background[0], 2) +
                            Math.pow(g - background[1], 2) +
                            Math.pow(b - background[2], 2)
                        );
                    };

                    const enqueue = (x, y) => {
                        if (x < 0 || y < 0 || x >= width || y >= height) return;

                        const idx = y * width + x;
                        if (visited[idx]) return;

                        const i = idx * 4;
                        if (data[i + 3] === 0) {
                            visited[idx] = 1;
                            return;
                        }

                        if (colorDistance(data[i], data[i + 1], data[i + 2]) <= tolerance) {
                            visited[idx] = 1;
                            queue.push(idx);
                        }
                    };

                    for (let x = 0; x < width; x++) {
                        enqueue(x, 0);
                        enqueue(x, height - 1);
                    }

                    for (let y = 0; y < height; y++) {
                        enqueue(0, y);
                        enqueue(width - 1, y);
                    }

                    while (queue.length) {
                        const idx = queue.shift();
                        const x = idx % width;
                        const y = Math.floor(idx / width);
                        const i = idx * 4;

                        data[i + 3] = 0;

                        enqueue(x + 1, y);
                        enqueue(x - 1, y);
                        enqueue(x, y + 1);
                        enqueue(x, y - 1);
                    }

                    ctx.putImageData(imageData, 0, 0);

                    img.src = canvas.toDataURL('image/png');
                    img.dataset.cleaned = 'true';
                };

                if (img.complete && img.naturalWidth) {
                    clean();
                } else {
                    img.addEventListener('load', clean, { once: true });
                }
            }

            document.querySelectorAll('.clean-icon[data-clean-bg="true"]').forEach(removePngBackground);
        });