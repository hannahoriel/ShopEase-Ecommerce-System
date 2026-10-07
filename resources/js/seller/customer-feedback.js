document.addEventListener('DOMContentLoaded', () => {
    const configElement = document.getElementById('sellerFeedbackConfig');
    if (!configElement) return;

    const config = JSON.parse(configElement.textContent);
    const modal = document.getElementById('productReviewsModal');
    const feedbackList = document.getElementById('feedbackList');
    const productGrid = document.getElementById('productGrid');
    const searchInput = document.getElementById('feedbackSearch');
    const ratingFilter = document.getElementById('feedbackRatingFilter');
    const perPageSelect = document.getElementById('feedbackPerPage');
    const pagination = document.querySelector('.feedback-pagination');
    const tabs = [...document.querySelectorAll('.feedback-tab')];
    let currentTab = 'feedback';
    let currentPage = 1;
    let totalPages = 1;
    let closeTimer = null;

    const escapeHtml = (value) => String(value ?? '').replace(/[&<>"']/g, (character) => ({
        '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;',
    })[character]);

    const formatDate = (value) => {
        if (!value) return '—';
        return new Intl.DateTimeFormat('en-PH', {
            dateStyle: 'medium',
            timeStyle: 'short',
        }).format(new Date(value));
    };

    async function apiFetch(url) {
        const response = await fetch(url, {
            headers: {
                Accept: 'application/json',
                Authorization: `Bearer ${config.apiToken}`,
                'X-Requested-With': 'XMLHttpRequest',
            },
            credentials: 'same-origin',
        });
        if (!response.ok) throw new Error(`Feedback request failed (${response.status}).`);
        return response.json();
    }

    function renderStars(rating) {
        return Array.from({ length: 5 }, (_, index) =>
            `<span class="${index >= rating ? 'star-empty' : ''}">★</span>`
        ).join('');
    }

    function categoryPillClass(category) {
        const slug = String(category || '')
            .trim()
            .toLowerCase()
            .replace(/&/g, 'and')
            .replace(/[^a-z0-9]+/g, '-')
            .replace(/^-|-$/g, '');
        const aliases = {
            'electronics-gadgets': 'electronics-and-gadgets',
            'automotive-and-motorcycle': 'automotive-motorcycle',
        };
        const normalized = aliases[slug] || slug;
        const knownCategories = [
            'pet-supplies',
            'electronics-and-gadgets',
            'womens-apparel',
            'mens-apparel',
            'kids-and-baby',
            'home-and-garden',
            'sports-and-outdoors',
            'health-and-beauty',
            'books-and-media',
            'food-and-gourmet',
            'automotive-motorcycle',
            'furniture-and-office-equipment',
            'jewelry-and-watches',
            'office-and-school-supplies',
        ];
        return knownCategories.includes(normalized)
            ? `category-${normalized}`
            : 'category-default';
    }

    function categoryDisplayName(category) {
        const slug = String(category || '')
            .trim()
            .toLowerCase()
            .replace(/&/g, 'and')
            .replace(/[^a-z0-9]+/g, '-')
            .replace(/^-|-$/g, '');
        const aliases = {
            'electronics-gadgets': 'electronics-and-gadgets',
            'automotive-and-motorcycle': 'automotive-motorcycle',
        };
        const labels = {
            'pet-supplies': 'Pet Supplies',
            'electronics-and-gadgets': 'Electronics and Gadgets',
            'womens-apparel': "Women's Apparel",
            'mens-apparel': "Men's Apparel",
            'kids-and-baby': 'Kids and Baby',
            'home-and-garden': 'Home and Garden',
            'sports-and-outdoors': 'Sports and Outdoors',
            'health-and-beauty': 'Health and Beauty',
            'books-and-media': 'Books and Media',
            'food-and-gourmet': 'Food and Gourmet',
            'automotive-motorcycle': 'Automotive & Motorcycle',
            'furniture-and-office-equipment': 'Furniture and Office Equipment',
            'jewelry-and-watches': 'Jewelry and Watches',
            'office-and-school-supplies': 'Office and School Supplies',
        };
        const normalized = aliases[slug] || slug;
        return labels[normalized] || String(category || '').trim().replace(/-/g, ' ');
    }

    function renderFeedbackRow(review) {
        const sentiment = review.rating >= 4 ? 'positive' : (review.rating === 3 ? 'neutral' : 'negative');
        const date = formatDate(review.created_at);
        return `
            <div class="feedback-row" data-rating="${review.rating}" data-search="${escapeHtml(`${review.customer} ${review.product} ${review.title} ${review.body}`.toLowerCase())}">
                <div class="order-cell">
                    <div class="truck-box"><img src="/icons/seller/shipping-status/delivered.png" alt="Customer review"></div>
                    <div>
                        <p class="order-id">${escapeHtml(review.product || 'Product')}</p>
                        <div class="order-date">Product feedback</div>
                        <div class="delivered-label"><span class="delivered-dot"></span>Reviewed</div>
                    </div>
                </div>
                <div>
                    <p class="customer-name">${escapeHtml(review.customer)}</p>
                    <div class="customer-phone">${escapeHtml(review.phone || '')}</div>
                </div>
                <div class="rating-cell">
                    <div class="stars">${renderStars(review.rating)}</div>
                    <span class="sentiment ${sentiment}">${sentiment[0].toUpperCase()}${sentiment.slice(1)}</span>
                </div>
                <div>
                    <p class="feedback-text-title">${escapeHtml(review.title)}</p>
                    <div class="feedback-text-body">${escapeHtml(review.body)}</div>
                    <div class="feedback-photo"></div>
                </div>
                <div class="feedback-date"><div>${escapeHtml(date)}</div></div>
            </div>`;
    }

    function renderPagination(feedback) {
        const count = document.getElementById('feedbackCount');
        const first = feedback.total ? ((feedback.current_page - 1) * feedback.per_page) + 1 : 0;
        const last = Math.min(feedback.current_page * feedback.per_page, feedback.total);
        if (count) {
            count.textContent =
                feedback.total
                    ? `Showing ${first}–${last} out of ${feedback.total} entries`
                    : 'Showing 0 out of 0 entries';
        }
        totalPages = feedback.last_page;

        const buttons = [];
        if (totalPages > 1) {
            buttons.push(`<button type="button" class="page-button" data-page="${Math.max(1, currentPage - 1)}" ${currentPage === 1 ? 'disabled' : ''}>‹</button>`);
            for (let page = 1; page <= totalPages; page += 1) {
                buttons.push(`<button type="button" class="page-button ${page === currentPage ? 'active' : ''}" data-page="${page}">${page}</button>`);
            }
            buttons.push(`<button type="button" class="page-button" data-page="${Math.min(totalPages, currentPage + 1)}" ${currentPage === totalPages ? 'disabled' : ''}>›</button>`);
        }
        pagination?.querySelectorAll('.page-button').forEach((button) => button.remove());
        pagination?.insertAdjacentHTML('afterbegin', buttons.join(''));
        pagination?.querySelectorAll('[data-page]').forEach((button) => {
            button.addEventListener('click', () => {
                currentPage = Number(button.dataset.page);
                loadFeedback();
            });
        });
    }

    function renderProducts(products) {
        if (!productGrid) return;
        if (!products.length) {
            productGrid.innerHTML = `
                <div class="feedback-empty-state product-rating-empty-state" role="status">
                    <strong>No product ratings found.</strong>
                    <span>Ratings will appear here once customers review your products.</span>
                </div>
            `;
            return;
        }

        productGrid.innerHTML = products.map((product) => `
            <article class="product-card" data-product-id="${product.id}" data-search="${escapeHtml(product.name.toLowerCase())}">
                <div class="product-card-image">
                    ${product.image_url
                        ? `<img src="${escapeHtml(product.image_url)}" alt="${escapeHtml(product.name)}">`
                        : '<div class="feedback-product-placeholder" aria-hidden="true">No image</div>'}
                </div>
                <div class="product-card-body">
                    <p class="product-card-name">${escapeHtml(product.name)}</p>
                    <div class="product-card-stars">${renderStars(Math.round(product.average_rating))}</div>
                    <small>${Number(product.average_rating).toFixed(1)} · ${product.total_reviews} reviews</small>
                </div>
            </article>`).join('');
    }

    async function loadFeedback() {
        if (!feedbackList) return;
        document.querySelector('.feedback-card')
            ?.classList.remove('is-feedback-empty');

        feedbackList.innerHTML =
            '<p class="feedback-empty feedback-loading">Loading customer feedback…</p>';
        const url = new URL(config.url, window.location.origin);
        url.searchParams.set('page', currentPage);
        url.searchParams.set('per_page', perPageSelect?.value || '7');
        if (searchInput?.value.trim()) url.searchParams.set('search', searchInput.value.trim());
        if (ratingFilter?.value && ratingFilter.value !== 'all') url.searchParams.set('rating', ratingFilter.value);

        try {
            const payload = await apiFetch(url);
            const feedback = payload.feedback;
            feedbackList.innerHTML = feedback.data.length
                ? feedback.data.map(renderFeedbackRow).join('')
                : `
                    <div class="feedback-empty-state" role="status">
                        <strong>No customer feedback found.</strong>
                        <span>Try another search or rating filter.</span>
                    </div>
                `;

            document.querySelector('.feedback-card')
                ?.classList.toggle(
                    'is-feedback-empty',
                    feedback.data.length === 0
                );

            renderPagination(feedback);
            renderProducts(payload.products || []);
            applyProductSearch();
        } catch (error) {
            document.querySelector('.feedback-card')
                ?.classList.remove('is-feedback-empty');

            feedbackList.innerHTML =
                `<p class="feedback-empty">${escapeHtml(error.message)} Please refresh and try again.</p>`;

            const count = document.getElementById('feedbackCount');
            if (count) count.textContent = 'Feedback could not be loaded.';
        }
    }

    function applyProductSearch() {
        const query = (searchInput?.value || '').trim().toLowerCase();
        productGrid?.querySelectorAll('.product-card').forEach((card) => {
            card.style.display = !query || card.dataset.search.includes(query) ? '' : 'none';
        });
    }

    function renderModalReview(review) {
        const date = formatDate(review.created_at);
        return `
            <article class="review-card">
                <div class="review-avatar" aria-hidden="true">👤</div>
                <div>
                    <h4 class="review-name">${escapeHtml(review.customer)}</h4>
                    <div class="review-meta">
                        <span class="review-stars">${renderStars(review.rating)}</span>
                        <span>${escapeHtml(date)}</span>
                    </div>
                    ${review.title ? `<strong>${escapeHtml(review.title)}</strong>` : ''}
                    <p class="review-text">${escapeHtml(review.body)}</p>
                </div>
            </article>`;
    }

    async function openProductReviews(productId) {
        if (!modal) return;
        if (closeTimer) {
            window.clearTimeout(closeTimer);
            closeTimer = null;
        }
        modal.classList.remove('is-closing');
        modal.classList.add('open');
        modal.setAttribute('aria-hidden', 'false');
        document.body.style.overflow = 'hidden';

        const name = document.getElementById('modalProductName');
        const score = document.getElementById('modalRatingScore');
        const count = document.getElementById('modalReviewCount');
        const title = document.getElementById('modalReviewsTitle');
        const breakdown = document.getElementById('modalRatingBreakdown');
        const reviewsSection = document.querySelector('.modal-reviews-section');
        const reviewList = document.getElementById('modalReviewsList');
        if (name) name.textContent = 'Loading reviews…';
        if (score) score.textContent = '—';
        if (count) count.textContent = '(0 reviews)';
        if (title) title.textContent = 'Customer Reviews';
        if (reviewList) reviewList.innerHTML = '';

        try {
            const payload = await apiFetch(`${config.url}/products/${productId}`);
            if (name) name.textContent = payload.product.name;
            const category = document.getElementById('modalProductCategory');
            if (category) {
                category.textContent = categoryDisplayName(payload.product.category) || 'Uncategorized';
                const pill = category.closest('.category-pill');
                if (pill) {
                    pill.className = `category-pill ${categoryPillClass(payload.product.category)}`;
                }
            }
            const summary = payload.summary;
            if (score) score.textContent = `${Number(summary.average_rating).toFixed(1)} out of 5`;
            if (count) count.textContent = `(${summary.total_reviews} reviews)`;
            if (title) title.textContent = `Customer Reviews (${summary.total_reviews})`;
            if (breakdown) {
                breakdown.innerHTML = [5, 4, 3, 2, 1].map((rating) => {
                    const ratingCount = summary.rating_counts[rating] || 0;
                    const width = summary.total_reviews ? (ratingCount / summary.total_reviews) * 100 : 0;
                    return `<div class="rating-breakdown-row"><span>${rating}</span><span class="star">★</span><div class="rating-bar"><div class="rating-bar-fill" style="width:${width}%"></div></div><span>${ratingCount}</span></div>`;
                }).join('');
            }
            if (reviewList) {
                reviewList.innerHTML = payload.data.length
                    ? payload.data.map(renderModalReview).join('')
                    : '<p class="feedback-empty">No reviews yet.</p>';
            } else if (reviewsSection) {
                reviewsSection.insertAdjacentHTML('beforeend', '<div id="modalReviewsList"></div>');
            }
            const image = document.querySelector('.modal-product-image');
            if (image) {
                image.innerHTML = payload.product.image_url
                    ? `<img src="${escapeHtml(payload.product.image_url)}" alt="${escapeHtml(payload.product.name)}">`
                    : '<div class="feedback-product-placeholder">No image</div>';
            }
        } catch (error) {
            if (name) name.textContent = 'Unable to load product reviews';
            if (reviewList) reviewList.textContent = error.message;
        }
    }

    function closeModal() {
        if (!modal?.classList.contains('open')) return;
        modal.classList.add('is-closing');
        closeTimer = window.setTimeout(() => {
            modal.classList.remove('open', 'is-closing');
            modal.setAttribute('aria-hidden', 'true');
            document.body.style.overflow = '';
            closeTimer = null;
        }, 220);
    }

    tabs.forEach((tab) => {
        tab.addEventListener('click', () => {
            tabs.forEach((item) => item.classList.toggle('active', item === tab));
            currentTab = tab.dataset.tab;
            const showFeedback = currentTab === 'feedback';
            document.getElementById('allFeedbackPanel').style.display = showFeedback ? 'block' : 'none';
            document.getElementById('productRatingsPanel').classList.toggle('active', !showFeedback);
            ratingFilter.style.display = showFeedback ? '' : 'none';
            document.getElementById('feedbackRefresh').style.display = showFeedback ? '' : 'none';
            if (pagination) pagination.style.display = showFeedback ? '' : 'none';
            searchInput.placeholder = showFeedback ? 'Search order ID or customer name' : 'Search Product';
            document.getElementById('feedbackSubtitle').textContent = showFeedback
                ? 'View feedbacks from your customers.'
                : 'View ratings and reviews for your products.';
            searchInput.value = '';
            ratingFilter.value = 'all';
            currentPage = 1;
            if (showFeedback) loadFeedback();
            else applyProductSearch();
        });
    });

    let searchTimer;
    searchInput?.addEventListener('input', () => {
        currentPage = 1;
        if (currentTab === 'feedback') {
            window.clearTimeout(searchTimer);
            searchTimer = window.setTimeout(loadFeedback, 250);
        } else {
            applyProductSearch();
        }
    });
    ratingFilter?.addEventListener('change', () => {
        currentPage = 1;
        loadFeedback();
    });
    perPageSelect?.addEventListener('change', () => {
        currentPage = 1;
        loadFeedback();
    });
    document.getElementById('feedbackRefresh')?.addEventListener('click', () => {
        searchInput.value = '';
        ratingFilter.value = 'all';
        currentPage = 1;
        loadFeedback();
    });
    productGrid?.addEventListener('click', (event) => {
        const card = event.target.closest('.product-card');
        if (card) openProductReviews(card.dataset.productId);
    });
    document.getElementById('closeShippingModal')?.addEventListener('click', closeModal);
    modal?.addEventListener('click', (event) => {
        if (event.target === modal) closeModal();
    });
    document.addEventListener('keydown', (event) => {
        if (event.key === 'Escape') closeModal();
    });

    const feedbackPage = document.getElementById('feedbackPage');
    const sellerSidebar = document.getElementById('sellerSidebar');
    const syncSidebar = () => feedbackPage?.classList.toggle(
        'sidebar-collapsed',
        Boolean(sellerSidebar?.classList.contains('seller-sidebar-collapsed') || sellerSidebar?.classList.contains('w-20')),
    );
    document.body.addEventListener('seller-sidebar-state-changed', (event) => {
        feedbackPage?.classList.toggle('sidebar-collapsed', Boolean(event.detail?.collapsed));
    });
    if (sellerSidebar) {
        syncSidebar();
        new MutationObserver(syncSidebar).observe(sellerSidebar, {
            attributes: true,
            attributeFilter: ['class'],
        });
    }

    loadFeedback();
});