document.addEventListener('DOMContentLoaded', function () {

    /* ── Config ─────────────────────────────────────────────── */
    const configEl   = document.getElementById('buyerProductConfig');
    const config     = configEl ? JSON.parse(configEl.textContent) : {};
    const cartUrl    = config.cartUrl    || '/api/v1/buyer/cart';
    const productUrl = config.productUrl || '/api/v1/buyer/products';
    const productId  = config.productId  || null;
    const apiToken   = config.apiToken   || '';

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

    function escapeHtml(str) {
        return String(str ?? '').replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
    }

    function money(v) {
        return '₱' + Number(v).toLocaleString('en-PH', { minimumFractionDigits: 0, maximumFractionDigits: 0 });
    }

    function compactCount(value) {
        return Number(value || 0).toLocaleString('en', {
            notation: 'compact',
            maximumFractionDigits: 1,
        });
    }

    /* ── Gallery state ──────────────────────────────────────── */
    let galleryPhotos = [];
    let activeGalleryIndex = 0;

    function renderGallery(photos) {
        galleryPhotos = photos.length ? photos : [];
        const mainEl   = document.getElementById('mainProductVisual');
        const thumbsEl = document.getElementById('galleryThumbnails');
        if (!mainEl || !thumbsEl) return;

        if (!galleryPhotos.length) {
            mainEl.innerHTML = `<div style="width:100%;height:100%;display:flex;align-items:center;justify-content:center;background:#F3EFED;color:#B0A8A5;font-size:13px;">No image</div>`;
            thumbsEl.innerHTML = '';
            return;
        }

        setMainPhoto(0);

        thumbsEl.innerHTML = galleryPhotos.map((src, i) => `
            <button type="button" class="gallery-thumb ${i === 0 ? 'active' : ''}" data-gallery-index="${i}" aria-label="View image ${i + 1}">
                <img src="${escapeHtml(src)}" alt="" style="width:100%;height:100%;object-fit:cover;border-radius:6px;">
            </button>`).join('');

        thumbsEl.querySelectorAll('.gallery-thumb').forEach((btn) => {
            btn.addEventListener('click', () => setMainPhoto(Number(btn.dataset.galleryIndex)));
        });
    }

    function setMainPhoto(index) {
        activeGalleryIndex = (index + galleryPhotos.length) % galleryPhotos.length;
        const mainEl = document.getElementById('mainProductVisual');
        if (mainEl) {
            mainEl.innerHTML = `<img src="${escapeHtml(galleryPhotos[activeGalleryIndex])}" alt="" style="width:100%;height:100%;object-fit:contain;">`;
            mainEl.classList.add('is-switching');
            setTimeout(() => mainEl.classList.remove('is-switching'), 160);
        }
        document.querySelectorAll('.gallery-thumb').forEach((btn, i) => {
            btn.classList.toggle('active', i === activeGalleryIndex);
        });
    }

    document.getElementById('galleryPrev')?.addEventListener('click', () => setMainPhoto(activeGalleryIndex - 1));
    document.getElementById('galleryNext')?.addEventListener('click', () => setMainPhoto(activeGalleryIndex + 1));

    /* ── Render product data into DOM ───────────────────────── */
    function renderProduct(p) {
        // Breadcrumb
        const bc = document.getElementById('breadcrumbProduct');
        if (bc) bc.textContent = p.name.length > 58 ? p.name.slice(0, 58) + '…' : p.name;

        // Gallery
        renderGallery(p.photos || (p.image_url ? [p.image_url] : []));

        // Name
        const nameEl = document.getElementById('productName');
        if (nameEl) nameEl.textContent = p.name;

        // Meta row
        const metaEl = document.getElementById('productMeta');
        if (metaEl) {
            metaEl.innerHTML = `
                <div class="product-rating-value">
                    <strong>4.5</strong>
                    <span class="stars">★★★★★</span>
                </div>
                <span class="meta-divider"></span>
                <span>No ratings yet</span>
                <span class="meta-divider"></span>
                <span>0 Sold</span>
                <span class="meta-divider"></span>
                <button type="button" class="report-button">Report</button>`;
        }

        // Price
        const priceEl = document.getElementById('pricePanel');
        if (priceEl) {
            priceEl.innerHTML = `<strong>${escapeHtml(money(p.price))}</strong>`;
        }

        // Variations / colors / sizes
        const variationsRow  = document.getElementById('variationsRow');
        const variationsWrap = document.getElementById('variationsWrap');
        const variationsLabel = document.getElementById('variationsLabel');
        const allOptions = [];

        if (p.variations?.length) {
            p.variations.forEach(v => allOptions.push({ label: v.name, type: 'variation' }));
            if (variationsLabel) variationsLabel.textContent = 'Variations';
        }
        if (p.colors?.length) {
            p.colors.forEach(c => allOptions.push({ label: c.name, type: 'color' }));
            if (variationsLabel && !p.variations?.length) variationsLabel.textContent = 'Colors';
        }
        if (p.sizes?.length) {
            p.sizes.forEach(s => allOptions.push({ label: s.name, type: 'size' }));
            if (variationsLabel && !p.variations?.length && !p.colors?.length) variationsLabel.textContent = 'Sizes';
        }

        if (allOptions.length && variationsRow && variationsWrap) {
            variationsRow.style.display = '';
            variationsWrap.innerHTML = allOptions.map((opt, i) =>
                `<button type="button" class="variation-button ${i === 0 ? 'selected' : ''}" data-option-type="${escapeHtml(opt.type)}">${escapeHtml(opt.label)}</button>`
            ).join('');
            variationsWrap.querySelectorAll('.variation-button').forEach((btn) => {
                btn.addEventListener('click', function () {
                    variationsWrap.querySelectorAll('.variation-button').forEach(b => b.classList.remove('selected'));
                    this.classList.add('selected');
                });
            });
        }

        // Stock
        const stockLabel = document.getElementById('stockLabel');
        const qtyInput   = document.getElementById('productQuantity');
        if (stockLabel) stockLabel.textContent = `${p.stock ?? 0} In Stock`;
        if (qtyInput)   qtyInput.max = String(p.stock ?? 99);

        // Add to Cart button product id
        const atcBtn = document.getElementById('addToCartButton');
        if (atcBtn) atcBtn.dataset.productId = String(p.id);

        // Seller
        const sellerName  = document.getElementById('sellerName');
        const sellerLogo  = document.getElementById('sellerLogo');
        const sellerRatings = document.getElementById('sellerRatings');
        const sellerResponseRate = document.getElementById('sellerResponseRate');
        const sellerJoined = document.getElementById('sellerJoined');
        const sellerProducts = document.getElementById('sellerProducts');
        const sellerResponseTime = document.getElementById('sellerResponseTime');
        const sellerFollowers = document.getElementById('sellerFollowers');
        const seller = p.seller || {};
        const joinedYears = Number(seller.joined_years || 0);
        if (sellerName) sellerName.textContent = p.seller?.store_name || '—';
        if (sellerLogo) sellerLogo.innerHTML   = escapeHtml((p.seller?.store_name || 'S').slice(0, 2).toUpperCase()) + '<small>STORE</small>';
        if (sellerRatings) sellerRatings.textContent = Number(seller.ratings || 0) === 0 ? '0%' : compactCount(seller.ratings);
        if (sellerResponseRate) sellerResponseRate.textContent = seller.response_rate || '—';
        if (sellerJoined) sellerJoined.textContent = `${joinedYears} ${joinedYears === 1 ? 'year' : 'years'} ago`;
        if (sellerProducts) sellerProducts.textContent = compactCount(seller.product_count);
        if (sellerResponseTime) sellerResponseTime.textContent = seller.response_time || '—';
        if (sellerFollowers) sellerFollowers.textContent = compactCount(seller.followers);

        // Specifications
        const specsCard = document.getElementById('specsCard');
        const specGrid  = document.getElementById('specGrid');
        const specs = p.specifications || {};
        const specEntries = Object.entries(specs);
        if (specEntries.length && specGrid && specsCard) {
            specsCard.style.display = '';
            const half = Math.ceil(specEntries.length / 2);
            const col1 = specEntries.slice(0, half);
            const col2 = specEntries.slice(half);
            const renderCol = (entries) => entries.map(([k, v]) =>
                `<div><span>${escapeHtml(k)}</span><strong>${escapeHtml(v)}</strong></div>`).join('');
            specGrid.innerHTML = `<div class="spec-column">${renderCol(col1)}</div><div class="spec-column">${renderCol(col2)}</div>`;
        }

        // Description
        const descCard = document.getElementById('descCard');
        const descEl   = document.getElementById('productDescription');
        if (p.description && descEl && descCard) {
            descCard.style.display = '';
            descEl.innerHTML = escapeHtml(p.description).replace(/\n/g, '<br>');
        }
    }

    /* ── Render recommended products ────────────────────────── */
    function renderRecommended(products) {
        const grid = document.getElementById('recommendedProducts');
        if (!grid) return;
        if (!products.length) {
            grid.innerHTML = '<p style="grid-column:1/-1;color:#A09896;font-size:13px;">No recommendations yet.</p>';
            return;
        }
        grid.innerHTML = products.map((p) => {
            const img = p.image_url
                ? `<img src="${escapeHtml(p.image_url)}" alt="${escapeHtml(p.name)}" style="width:100%;height:100%;object-fit:cover;border-radius:7px;">`
                : `<div style="width:100%;height:100%;background:#F0EBE8;border-radius:7px;"></div>`;
            return `
                <a href="/buyer/product?id=${encodeURIComponent(p.id)}" class="product-card" style="display:block;text-decoration:none;color:inherit;">
                    <div class="product-image-box">${img}</div>
                    <h3>${escapeHtml(p.name)}</h3>
                    <p class="product-rating"><span class="rating-star">★</span> <span class="sold-count">${escapeHtml(p.seller?.store_name || '')}</span></p>
                    <div class="product-bottom-row">
                        <strong>${escapeHtml(money(p.price))}</strong>
                        <button type="button" class="product-cart-button" data-product-id="${escapeHtml(String(p.id))}" aria-label="View ${escapeHtml(p.name)}">
                            <img src="/icons/buyer/product-cart.png" alt="">
                        </button>
                    </div>
                </a>`;
        }).join('');

        grid.querySelectorAll('.product-cart-button').forEach((btn) => {
            btn.addEventListener('click', (e) => {
                e.preventDefault();
                e.stopPropagation();
                const pid = btn.dataset.productId;
                if (pid) window.location.href = '/buyer/product?id=' + encodeURIComponent(pid);
            });
        });
    }

    /* ── Load product from API ──────────────────────────────── */
    function loadProduct() {
        if (!productId || !apiToken) {
            document.getElementById('productName').textContent = 'Product not found.';
            return;
        }

        apiFetch(`${productUrl}/${productId}`)
            .then((res) => {
                if (!res.ok) throw new Error(res.status);
                return res.json();
            })
            .then((data) => {
                renderProduct(data);
                loadRecommended(data.category);
            })
            .catch(() => {
                const nameEl = document.getElementById('productName');
                if (nameEl) nameEl.textContent = 'Product not found.';
            });
    }

    function loadRecommended(category) {
        const url = new URL('/api/v1/buyer/dashboard/products', window.location.origin);
        if (category) url.searchParams.set('category', category);
        url.searchParams.set('per_page', '6');

        apiFetch(url.toString())
            .then((res) => res.ok ? res.json() : Promise.reject())
            .then((json) => renderRecommended((json.data || []).filter(p => String(p.id) !== String(productId))))
            .catch(() => {
                const grid = document.getElementById('recommendedProducts');
                if (grid) grid.innerHTML = '';
            });
    }

    /* ── Quantity controls ──────────────────────────────────── */
    const quantityInput = document.getElementById('productQuantity');

    document.getElementById('quantityMinus')?.addEventListener('click', () => {
        quantityInput.value = Math.max(1, Number(quantityInput.value || 1) - 1);
    });
    document.getElementById('quantityPlus')?.addEventListener('click', () => {
        quantityInput.value = Math.min(Number(quantityInput.max || 99), Number(quantityInput.value || 1) + 1);
    });

    /* ── Add to Cart ────────────────────────────────────────── */
    document.getElementById('addToCartButton')?.addEventListener('click', function () {
        const pid = this.dataset.productId;
        if (!pid || !apiToken) return;

        const qty       = Number(quantityInput?.value || 1);
        const variation = document.querySelector('.variation-button.selected[data-option-type="variation"]')?.textContent.trim() || null;
        const color     = document.querySelector('.variation-button.selected[data-option-type="color"]')?.textContent.trim() || null;
        const size      = document.querySelector('.variation-button.selected[data-option-type="size"]')?.textContent.trim() || null;

        this.disabled = true;

        apiFetch(cartUrl, {
            method: 'POST',
            body: JSON.stringify({ product_id: Number(pid), quantity: qty, variation, color, size }),
        })
            .then((res) => { if (!res.ok) throw new Error(); return res.json(); })
            .then(() => { window.location.href = '/buyer/cart'; })
            .catch(() => { this.disabled = false; });
    });

    document.getElementById('buyNowButton')?.addEventListener('click', function () {
        const pid = document.getElementById('addToCartButton')?.dataset.productId;
        if (!pid || !apiToken) return;

        const qty       = Number(quantityInput?.value || 1);
        const variation = document.querySelector('.variation-button.selected[data-option-type="variation"]')?.textContent.trim() || null;
        const color     = document.querySelector('.variation-button.selected[data-option-type="color"]')?.textContent.trim() || null;
        const size      = document.querySelector('.variation-button.selected[data-option-type="size"]')?.textContent.trim() || null;

        this.disabled = true;

        apiFetch(cartUrl, {
            method: 'POST',
            body: JSON.stringify({ product_id: Number(pid), quantity: qty, variation, color, size }),
        })
            .then((res) => { if (!res.ok) throw new Error(); return res.json(); })
            .then(() => { window.location.href = '/buyer/cart'; })
            .catch(() => { this.disabled = false; });
    });

    /* ── Rating filters (static UI) ─────────────────────────── */
    document.querySelectorAll('.rating-filter').forEach((btn) => {
        btn.addEventListener('click', function () {
            document.querySelectorAll('.rating-filter').forEach(b => b.classList.remove('active'));
            this.classList.add('active');
        });
    });

    /* ── Boot ───────────────────────────────────────────────── */
    loadProduct();
});
