document.addEventListener('DOMContentLoaded', () => {
            /* ─── Config ─────────────────────────────────────────── */
            const configEl = document.getElementById('buyerDashboardConfig');
            const config = configEl ? JSON.parse(configEl.textContent) : {};
            const productsUrl    = config.productsUrl   || '';
            const announcementsUrl = config.announcementsUrl || '';
            const productBaseUrl = config.productBaseUrl || '/buyer/product';
            const cartUrl        = config.cartUrl        || '/api/v1/buyer/cart';
            const apiToken       = config.apiToken       || '';
            const cartIconUrl    = '/icons/buyer/product-cart.png';

            /* ─── API helper ─────────────────────────────────────── */
            function apiFetch(url, options = {}) {
                return fetch(url, {
                    ...options,
                    headers: {
                        'Accept': 'application/json',
                        'Content-Type': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                        ...(apiToken ? { 'Authorization': 'Bearer ' + apiToken } : {}),
                        ...(options.headers || {}),
                    },
                    credentials: 'same-origin',
                });
            }

            /* ─── Add to cart ────────────────────────────────────── */
            function addToCart(productId, button) {
                if (!cartUrl || !apiToken) return;

                button.disabled = true;

                apiFetch(cartUrl, {
                    method: 'POST',
                    body: JSON.stringify({ product_id: productId, quantity: 1 }),
                })
                    .then((res) => {
                        if (!res.ok) throw new Error('Failed');
                        return res.json();
                    })
                    .then(() => {
                        window.location.href = '/buyer/cart';
                    })
                    .catch(() => {
                        button.disabled = false;
                        button.innerHTML = `<img src="${escapeHtml(cartIconUrl)}" alt="" class="clean-icon" data-clean-bg="true">`;
                    });
            }

            /* ─── My Purchases card navigation ───────────────────── */
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

            /* ─── Announcement carousel ──────────────────────────── */
            const announcementCarousel =
                document.getElementById('buyerAnnouncementCarousel');

            let announcementSlides =
                Array.from(
                    document.querySelectorAll('[data-announcement-slide]')
                );

            let announcementDots =
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

                if (announcementSlides.length < 2) return;

                announcementTimer = window.setInterval(() => {
                    showAnnouncement(announcementIndex + 1);
                }, 1900);
            };

            const appendPublishedAnnouncement = (announcement) => {
                const track = announcementCarousel?.querySelector('.buyer-announcement-track');
                const dotContainer = announcementCarousel?.querySelector('.hero-dots');

                if (!track || !dotContainer) return;

                const index = track.querySelectorAll('[data-announcement-slide]').length;
                const slide = document.createElement('article');
                slide.className = 'buyer-announcement-slide buyer-admin-announcement';
                slide.dataset.announcementSlide = String(index);
                slide.setAttribute('aria-hidden', 'true');

                const image = announcement.banner_url
                    ? `<img class="buyer-admin-announcement-image" src="${escapeHtml(announcement.banner_url)}" alt="" loading="lazy">`
                    : '';
                const badge = announcement.type === 'Policy Update'
                    ? 'Policy Update'
                    : 'ShopEase Announcement';

                slide.innerHTML = `
                    <div class="announcement-slide-content announcement-slide-content--soft">
                        <div class="announcement-copy">
                            <span class="hero-pill">${escapeHtml(badge)}</span>
                            <h1>${escapeHtml(announcement.title)}</h1>
                            <p>${escapeHtml(announcement.description)}</p>
                        </div>
                        ${image}
                    </div>
                `;

                track.append(slide);

                const dot = document.createElement('button');
                dot.type = 'button';
                dot.className = 'dot';
                dot.dataset.announcementDot = String(index);
                dot.setAttribute('aria-label', `Show announcement ${index + 1}`);
                dot.setAttribute('aria-selected', 'false');
                dot.addEventListener('click', () => {
                    showAnnouncement(index);
                    startAnnouncementAutoPlay();
                });
                dotContainer.append(dot);

                announcementSlides = Array.from(
                    track.querySelectorAll('[data-announcement-slide]')
                );
                announcementDots = Array.from(
                    dotContainer.querySelectorAll('[data-announcement-dot]')
                );
                startAnnouncementAutoPlay();
            };

            if (announcementsUrl && apiToken) {
                apiFetch(announcementsUrl)
                    .then((response) => {
                        if (!response.ok) {
                            throw new Error('Unable to load announcements.');
                        }

                        return response.json();
                    })
                    .then((payload) => {
                        (payload.data || []).forEach(appendPublishedAnnouncement);
                    })
                    .catch((error) => {
                        console.error('[Buyer dashboard] Announcement load failed:', error);
                    });
            }

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

            /* ─── Category selection ─────────────────────────────── */
            const categoryCards = document.querySelectorAll('.category-card');
            let activeCategory = '';

            categoryCards.forEach((card) => {
                card.addEventListener('click', () => {
                    const isAlreadySelected = card.classList.contains('selected');

                    categoryCards.forEach((item) => item.classList.remove('selected'));

                    if (isAlreadySelected) {
                        activeCategory = '';
                    } else {
                        card.classList.add('selected');
                        activeCategory = card.dataset.category || '';
                    }

                    loadProducts({ category: activeCategory });
                });
            });

            /* ─── View all categories ────────────────────────────── */
            document.getElementById('viewAllCategories')?.addEventListener('click', () => {
                document.getElementById('buyerCategories')?.scrollIntoView({
                    behavior: 'smooth',
                    block: 'center'
                });
            });

            /* ─── Latest notifications ───────────────────────────── */
            document.querySelectorAll('.notification-row').forEach((notification) => {
                notification.addEventListener('click', () => {
                    notification.classList.remove('is-unread');

                    const unreadDot =
                        notification.querySelector('.notification-dot');

                    unreadDot?.remove();
                });
            });

            /* ─── Purchase status navigation ─────────────────────── */
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

            /* ─── Product rendering ──────────────────────────────── */
            function escapeHtml(str) {
                return String(str ?? '')
                    .replace(/&/g, '&amp;')
                    .replace(/</g, '&lt;')
                    .replace(/>/g, '&gt;')
                    .replace(/"/g, '&quot;');
            }

            function formatPrice(price) {
                return '\u20b1' + Number(price).toLocaleString('en-PH', {
                    minimumFractionDigits: 2,
                    maximumFractionDigits: 2,
                });
            }

            function buildProductCard(product) {
                const productUrl = productBaseUrl + '?id=' + encodeURIComponent(product.id);
                const hasImage   = product.image_url && !product.image_url.startsWith('data:');

                const imageHtml = hasImage
                    ? `<img src="${escapeHtml(product.image_url)}" alt="${escapeHtml(product.name)}" class="product-photo" loading="lazy">`
                    : `<div class="product-art-placeholder" aria-hidden="true"></div>`;

                return `
                    <a href="${escapeHtml(productUrl)}" class="product-card">
                        <div class="product-image-box">
                            <button type="button" class="favorite-button" aria-label="Add to favorites" data-product-id="${escapeHtml(product.id)}">♡</button>
                            ${imageHtml}
                        </div>
                        <h3>${escapeHtml(product.name)}</h3>
                        <p class="product-rating">
                            <span class="rating-star">★</span>
                            <span class="sold-count">${escapeHtml(product.seller?.store_name || '')}</span>
                        </p>
                        <div class="product-bottom-row">
                            <strong>${escapeHtml(formatPrice(product.price))}</strong>
                            <button type="button" class="product-cart-button" aria-label="Add ${escapeHtml(product.name)} to cart" data-product-id="${escapeHtml(product.id)}">
                                <img src="${escapeHtml(cartIconUrl)}" alt="" class="clean-icon" data-clean-bg="true">
                            </button>
                        </div>
                    </a>`;
            }

            function renderProducts(products) {
                if (!recommendedProducts) return;

                if (!products.length) {
                    recommendedProducts.innerHTML =
                        '<p class="products-empty-state">No products available yet. Check back soon!</p>';
                    return;
                }

                recommendedProducts.innerHTML = products
                    .map(buildProductCard)
                    .join('');

                attachProductCardListeners();
            }

            function attachProductCardListeners() {
                recommendedProducts?.querySelectorAll('.favorite-button').forEach((button) => {
                    button.addEventListener('click', (event) => {
                        event.preventDefault();
                        event.stopPropagation();
                        const active = button.classList.toggle('is-favorite');
                        button.textContent = active ? '\u2665' : '\u2661';
                        button.setAttribute('aria-pressed', active ? 'true' : 'false');
                    });
                });

                recommendedProducts?.querySelectorAll('.product-cart-button').forEach((button) => {
                    button.addEventListener('click', (event) => {
                        event.preventDefault();
                        event.stopPropagation();
                        const pid = button.dataset.productId;
                        if (pid) window.location.href = productBaseUrl + '?id=' + encodeURIComponent(pid);
                    });
                });
            }

            /* ─── Fetch products from API ────────────────────────── */
            function loadProducts(params = {}) {
                if (!productsUrl || !recommendedProducts) return;

                const loadingEl = document.getElementById('recommendedProductsLoading');

                if (loadingEl) {
                    loadingEl.style.display = 'block';
                }

                recommendedProducts.innerHTML =
                    '<div id="recommendedProductsLoading" class="products-loading-state">Loading products…</div>';

                const url = new URL(productsUrl, window.location.origin);

                if (params.category) {
                    url.searchParams.set('category', params.category);
                }

                if (params.search) {
                    url.searchParams.set('search', params.search);
                }

                url.searchParams.set('per_page', '24');

                fetch(url.toString(), {
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                        ...(apiToken ? { 'Authorization': 'Bearer ' + apiToken } : {}),
                    },
                    credentials: 'same-origin',
                })
                    .then((response) => {
                        if (!response.ok) {
                            throw new Error('Failed to load products.');
                        }
                        return response.json();
                    })
                    .then((json) => {
                        renderProducts(json.data || []);
                    })
                    .catch(() => {
                        if (recommendedProducts) {
                            recommendedProducts.innerHTML =
                                '<p class="products-empty-state">Could not load products. Please refresh the page.</p>';
                        }
                    });
            }

            /* ─── Initial load ───────────────────────────────────── */
            loadProducts();

            /*
             * Remove a baked square/pastel background from supplied PNG icons.
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
