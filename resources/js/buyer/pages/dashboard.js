document.addEventListener('DOMContentLoaded', () => {
            /* ─── Config ─────────────────────────────────────────── */
            const configEl = document.getElementById('buyerDashboardConfig');
            const config = configEl ? JSON.parse(configEl.textContent) : {};
            const productsUrl    = config.productsUrl   || '';
            const announcementsUrl = config.announcementsUrl || '';
            const ordersUrl = config.ordersUrl || '';
            const notificationsUrl = config.notificationsUrl || '';
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

            /* ─── Real purchase counts and notifications ────────── */
            const notificationList = document.getElementById('buyerDashboardNotifications');
            const notificationError = document.getElementById('buyerDashboardNotificationsError');
            let dashboardNotifications = [];

            const setDashboardError = (message) => {
                if (!notificationError) return;
                notificationError.textContent = message;
                notificationError.hidden = !message;
            };

            const loadPurchaseCounts = async () => {
                if (!ordersUrl || !apiToken) return;
                const response = await apiFetch(ordersUrl);
                if (!response.ok) throw new Error(`Purchase counts could not be loaded (${response.status}).`);
                const payload = await response.json();
                const counts = {
                    processing: 0,
                    'to-ship': 0,
                    'in-transit': 0,
                    'out-for-delivery': 0,
                    delivered: 0,
                };
                const statusGroups = {
                    pending: 'processing',
                    new: 'processing',
                    preparing: 'processing',
                    to_ship: 'to-ship',
                    in_transit: 'in-transit',
                    out_for_delivery: 'out-for-delivery',
                    delivered: 'delivered',
                    completed: 'delivered',
                };

                const statusCounts = payload.status_counts || (payload.data || []).reduce((totals, order) => {
                    const status = String(order.status || '').toLowerCase();
                    totals[status] = (totals[status] || 0) + 1;
                    return totals;
                }, {});
                Object.entries(statusCounts).forEach(([status, total]) => {
                    const group = statusGroups[status.toLowerCase()];
                    if (group) counts[group] += Number(total);
                });

                Object.entries(counts).forEach(([group, count]) => {
                    const countElement = document.querySelector(`[data-purchase-count="${group}"]`);
                    if (countElement) countElement.textContent = String(count);
                });
            };

            const relativeNotificationTime = (value) => {
                if (!value) return '';
                const seconds = Math.round((new Date(value).getTime() - Date.now()) / 1000);
                const units = [
                    ['year', 31536000], ['month', 2592000], ['week', 604800],
                    ['day', 86400], ['hour', 3600], ['minute', 60],
                ];
                const [unit, size] = units.find(([, unitSize]) => Math.abs(seconds) >= unitSize) || ['second', 1];
                return new Intl.RelativeTimeFormat('en', { numeric: 'auto' }).format(Math.round(seconds / size), unit);
            };

            const notificationVisual = (type) => {
                if (type === 'shipping' || type === 'delivered' || type === 'completed') {
                    return {
                        className: 'notification-icon-delivery',
                        svg: '<path d="M3 6h11v10H3V6Zm11 3h4l3 3v4h-7V9Z" stroke="currentColor" stroke-width="1.7" stroke-linejoin="round"/><circle cx="7" cy="18" r="1.5" stroke="currentColor" stroke-width="1.7"/><circle cx="18" cy="18" r="1.5" stroke="currentColor" stroke-width="1.7"/>',
                    };
                }
                if (type === 'promotion') {
                    return {
                        className: 'notification-icon-promo',
                        svg: '<path d="M4 12V5h7l9 9-7 7-9-9Z" stroke="currentColor" stroke-width="1.7" stroke-linejoin="round"/><circle cx="8" cy="9" r="1.3" fill="currentColor"/>',
                    };
                }
                return {
                    className: 'notification-icon-order',
                    svg: '<path d="M4 7h16l-1 12H5L4 7Z" stroke="currentColor" stroke-width="1.7" stroke-linejoin="round"/><path d="M8 9V6a4 4 0 0 1 8 0v3" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"/>',
                };
            };

            const renderDashboardNotifications = () => {
                if (!notificationList) return;
                if (!dashboardNotifications.length) {
                    notificationList.innerHTML = '<p class="buyer-dashboard-notifications-state">You have no notifications yet.</p>';
                    return;
                }

                notificationList.innerHTML = dashboardNotifications.slice(0, 3).map((notification) => {
                    const visual = notificationVisual(notification.type);
                    const unread = !notification.read_at;
                    return `<button type="button" class="notification-row ${unread ? 'is-unread' : ''}" data-dashboard-notification="${escapeHtml(notification.id)}">
                        <span class="notification-icon ${visual.className}" aria-hidden="true">
                            <svg viewBox="0 0 24 24" fill="none">${visual.svg}</svg>
                        </span>
                        <span class="notification-copy">
                            <strong>${escapeHtml(notification.title)}</strong>
                            <span>${escapeHtml(notification.message)}</span>
                        </span>
                        <span class="notification-meta">
                            <time datetime="${escapeHtml(notification.created_at)}">${escapeHtml(relativeNotificationTime(notification.created_at))}</time>
                            ${unread ? '<span class="notification-dot" aria-label="Unread"></span>' : ''}
                        </span>
                    </button>`;
                }).join('');
            };

            const loadDashboardNotifications = async () => {
                if (!notificationsUrl || !apiToken || !notificationList) return;
                const response = await apiFetch(notificationsUrl);
                if (!response.ok) throw new Error(`Notifications could not be loaded (${response.status}).`);
                const payload = await response.json();
                dashboardNotifications = payload.data || [];
                renderDashboardNotifications();
            };

            const loadDashboardData = async () => {
                const results = await Promise.allSettled([
                    loadPurchaseCounts(),
                    loadDashboardNotifications(),
                ]);
                const failures = results.filter((result) => result.status === 'rejected');
                if (failures.length) {
                    setDashboardError(failures.map((result) => result.reason.message).join(' '));
                    if (notificationList && failures.some((result) => result.reason.message.startsWith('Notifications'))) {
                        notificationList.innerHTML = '<p class="buyer-dashboard-notifications-state">Notifications could not be loaded. Please refresh to try again.</p>';
                    }
                }
            };

            notificationList?.addEventListener('click', async (event) => {
                const row = event.target.closest('[data-dashboard-notification]');
                if (!row || !row.classList.contains('is-unread')) return;
                const notificationId = row.dataset.dashboardNotification;
                const notification = dashboardNotifications.find((item) => String(item.id) === notificationId);
                if (!notification) return;

                try {
                    const response = await apiFetch(`${notificationsUrl}/${encodeURIComponent(notificationId)}/read`, {
                        method: 'PATCH',
                    });
                    if (!response.ok) throw new Error(`Notification could not be marked as read (${response.status}).`);
                    notification.read_at = new Date().toISOString();
                    setDashboardError('');
                    renderDashboardNotifications();
                } catch (error) {
                    setDashboardError(error.message);
                }
            });

            loadDashboardData();

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
                const imageUrl = product.image_url || product.photos?.[0] || '';
                const hasImage = typeof imageUrl === 'string' && imageUrl.trim() !== '';

                const imageHtml = hasImage
                    ? `<img src="${escapeHtml(imageUrl)}" alt="${escapeHtml(product.name)}" class="product-photo" loading="lazy">`
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
                recommendedProducts?.querySelectorAll('.product-photo').forEach((image) => {
                    image.addEventListener('error', () => {
                        const placeholder = document.createElement('div');
                        placeholder.className = 'product-art-placeholder';
                        placeholder.setAttribute('aria-hidden', 'true');
                        image.replaceWith(placeholder);
                    }, { once: true });
                });

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
