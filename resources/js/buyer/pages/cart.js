document.addEventListener('DOMContentLoaded', function () {
            /* ─── Config ─────────────────────────────────────────── */
            const configEl = document.getElementById('buyerCartConfig');
            const config   = configEl ? JSON.parse(configEl.textContent) : {};
            const cartUrl  = config.cartUrl  || '/api/v1/buyer/cart';
            const apiToken = config.apiToken || '';

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

            /* ─── Load cart from API and render ─────────────────── */
            function loadCart() {
                if (!apiToken) {
                    console.warn('[Cart] No apiToken — skipping loadCart');
                    return;
                }

                apiFetch(cartUrl)
                    .then((res) => {
                        if (!res.ok) {
                            return res.text().then(t => { throw new Error(res.status + ': ' + t); });
                        }
                        return res.json();
                    })
                    .then((json) => renderCartFromApi(json.data || []))
                    .catch((err) => console.error('[Cart] loadCart failed:', err));
            }

            function escapeHtml(str) {
                return String(str ?? '')
                    .replace(/&/g, '&amp;').replace(/</g, '&lt;')
                    .replace(/>/g, '&gt;').replace(/"/g, '&quot;');
            }

            function renderCartFromApi(items) {
                const cartGroupsEl = document.getElementById('cartGroups');
                if (!cartGroupsEl) return;

                if (!items.length) {
                    cartGroupsEl.innerHTML = '<p style="padding:24px;color:#888;">Your cart is empty.</p>';
                    updateTotals();
                    return;
                }

                // Group by seller
                const groups = {};
                items.forEach((item) => {
                    const key = item.product.seller.id;
                    if (!groups[key]) groups[key] = { shop: item.product.seller.store_name, items: [] };
                    groups[key].items.push(item);
                });

                cartGroupsEl.innerHTML = Object.values(groups).map((group, gi) => `
                    <section class="cart-shop-card" data-shop-index="${gi}">
                        <div class="cart-shop-header">
                            <label class="cart-check-wrap" aria-label="Select ${escapeHtml(group.shop)}">
                                <input type="checkbox" class="cart-checkbox shop-checkbox" checked>
                                <span class="cart-checkmark"></span>
                            </label>
                            <span class="cart-shop-icon" aria-hidden="true">
                                <svg viewBox="0 0 24 24" fill="none"><path d="M4 9h16l-1-5H5L4 9Z" stroke="currentColor" stroke-width="1.7" stroke-linejoin="round"/><path d="M5 9v10h14V9" stroke="currentColor" stroke-width="1.7" stroke-linejoin="round"/></svg>
                            </span>
                            <span class="cart-shop-name">${escapeHtml(group.shop)}</span>
                        </div>
                        ${group.items.map((item) => {
                            const img = item.product.image_url
                                ? `<img src="${escapeHtml(item.product.image_url)}" alt="${escapeHtml(item.product.name)}" style="width:100%;height:100%;object-fit:cover;border-radius:8px;">`
                                : `<svg viewBox="0 0 180 150"><rect width="180" height="150" rx="14" fill="#E9E8E2"/></svg>`;
                            const optionParts = [item.variation, item.color, item.size].filter(Boolean);
                            return `
                            <article class="cart-item-row" data-cart-item data-item-id="${item.id}" data-api-item-id="${item.id}" data-price="${item.product.price}">
                                <div class="cart-item-product">
                                    <label class="cart-check-wrap">
                                        <input type="checkbox" class="cart-checkbox item-checkbox" checked>
                                        <span class="cart-checkmark"></span>
                                    </label>
                                    <div class="cart-product-image cart-product-link">${img}</div>
                                    <div class="cart-product-copy">
                                        <h3>${escapeHtml(item.product.name)}</h3>
                                        ${optionParts.length ? `<div class="cart-selected-variation"><p><span>${escapeHtml(optionParts.join(' · '))}</span></p></div>` : ''}
                                        <strong>₱${Number(item.product.price).toLocaleString('en-PH', {minimumFractionDigits:2})}</strong>
                                    </div>
                                </div>
                                <div class="cart-item-quantity">
                                    <div class="quantity-control">
                                        <button type="button" class="quantity-button quantity-minus" aria-label="Decrease">−</button>
                                        <input type="number" class="quantity-input" value="${item.quantity}" min="1" max="99" readonly>
                                        <button type="button" class="quantity-button quantity-plus" aria-label="Increase">+</button>
                                    </div>
                                </div>
                                <div class="cart-item-total">₱${(item.product.price * item.quantity).toLocaleString('en-PH', {minimumFractionDigits:2})}</div>
                                <div class="cart-item-action">
                                    <button type="button" class="remove-cart-item" aria-label="Remove ${escapeHtml(item.product.name)}">
                                        <svg viewBox="0 0 24 24" fill="none"><path d="M4 7h16M9 7V4h6v3m-8 0 1 13h8l1-13M10 11v5m4-5v5" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                    </button>
                                </div>
                            </article>`;
                        }).join('')}
                    </section>`).join('');

                // Re-bind shop checkboxes after render
                rebindShopCheckboxes();
                updateTotals();
            }

            /* ─── API: update quantity ───────────────────────────── */
            function apiUpdateQuantity(itemId, quantity) {
                apiFetch(`${cartUrl}/${itemId}`, {
                    method: 'PATCH',
                    body: JSON.stringify({ quantity }),
                }).catch(() => {});
            }

            /* ─── API: remove item ───────────────────────────────── */
            function apiRemoveItem(itemId) {
                apiFetch(`${cartUrl}/${itemId}`, { method: 'DELETE' }).catch(() => {});
            }

            const selectAll = document.getElementById('selectAllCart');

            function rebindShopCheckboxes() {
                document.querySelectorAll('.cart-shop-card').forEach((shopCard) => {
                    const shopCheckbox = shopCard.querySelector('.shop-checkbox');
                    shopCheckbox?.addEventListener('change', function () {
                        shopCard.querySelectorAll('.item-checkbox').forEach((cb) => { cb.checked = shopCheckbox.checked; });
                        shopCheckbox.indeterminate = false;
                        updateTotals();
                    });
                    shopCard.querySelectorAll('.item-checkbox').forEach((checkbox) => {
                        checkbox.addEventListener('change', function () {
                            syncShopCheckbox(shopCard);
                            updateTotals();
                        });
                    });
                });
            }

            function getCartGroups() {
                return Array.from(document.querySelectorAll('.cart-shop-card'));
            }

            const shippingFee =
                80;

            const subtotalLabel =
                document.getElementById('subtotalLabel');

            const subtotalValue =
                document.getElementById('subtotalValue');

            const shippingValue =
                document.getElementById('shippingValue');

            const grandTotalValue =
                document.getElementById('grandTotalValue');

            const selectedProductsSummary =
                document.getElementById('selectedProductsSummary');

            function money(value) {
                return new Intl.NumberFormat('en-PH', {
                    style: 'currency',
                    currency: 'PHP',
                    minimumFractionDigits: 2,
                    maximumFractionDigits: 2
                }).format(Number(value || 0));
            }

            function getAllItemRows() {
                return Array.from(
                    document.querySelectorAll('[data-cart-item]')
                );
            }

            function findCartRowById(itemId) {
                return getAllItemRows().find(
                    row => String(row.dataset.itemId) === String(itemId)
                );
            }

            function removeCartRow(row) {
                if (!row) return;

                const shopCard =
                    row.closest('.cart-shop-card');

                row.remove();

                const remainingRows =
                    shopCard
                        ? shopCard.querySelectorAll('[data-cart-item]').length
                        : 0;

                if (
                    shopCard &&
                    remainingRows === 0
                ) {
                    shopCard.classList.add('is-empty');
                }

                if (shopCard) {
                    syncShopCheckbox(shopCard);
                }

                updateTotals();
            }

            function renderSelectedProductsSummary() {
                if (!selectedProductsSummary) {
                    return;
                }

                const groups = [];

                getCartGroups().forEach(shopCard => {
                    if (
                        shopCard.classList.contains('is-empty')
                    ) {
                        return;
                    }

                    const shopName =
                        shopCard
                            .querySelector('.cart-shop-name')
                            ?.textContent
                            ?.trim() ||
                        'Shop';

                    const selectedRows =
                        Array.from(
                            shopCard.querySelectorAll(
                                '[data-cart-item]'
                            )
                        ).filter(
                            row =>
                                row.querySelector(
                                    '.item-checkbox'
                                )?.checked
                        );

                    if (!selectedRows.length) {
                        return;
                    }

                    const items =
                        selectedRows.map(row => {
                            const name =
                                row
                                    .querySelector(
                                        '.cart-product-copy h3'
                                    )
                                    ?.textContent
                                    ?.trim() ||
                                'Product';

                            const detailLines =
                                Array.from(
                                    row.querySelectorAll(
                                        '.cart-product-copy p span'
                                    )
                                )
                                .map(
                                    element =>
                                        element.textContent.trim()
                                )
                                .filter(Boolean);

                            const quantity =
                                Number(
                                    row.querySelector(
                                        '.quantity-input'
                                    )?.value || 1
                                );

                            const price =
                                Number(
                                    row.dataset.price || 0
                                );

                            const productImage =
                                row
                                    .querySelector('.cart-product-image')
                                    ?.innerHTML || '';

                            return {
                                id: row.dataset.itemId,
                                name,
                                details:
                                    detailLines.join(' · '),
                                quantity,
                                total:
                                    price * quantity,
                                productImage
                            };
                        });

                    groups.push({
                        shopName,
                        items,
                        subtotal:
                            items.reduce(
                                (sum, item) =>
                                    sum + item.total,
                                0
                            )
                    });
                });

                if (!groups.length) {
                    selectedProductsSummary.innerHTML = `
                        <div class="selected-products-empty">
                            No products selected yet.
                        </div>
                    `;
                    return;
                }

                selectedProductsSummary.innerHTML =
                    groups.map(group => `
                        <section class="summary-shop-group">
                            <div class="summary-shop-header">
                                <svg
                                    viewBox="0 0 24 24"
                                    fill="none"
                                    aria-hidden="true"
                                >
                                    <path
                                        d="M4 9h16l-1-5H5L4 9Z"
                                        stroke="currentColor"
                                        stroke-width="1.7"
                                        stroke-linejoin="round"
                                    />
                                    <path
                                        d="M5 9v10h14V9"
                                        stroke="currentColor"
                                        stroke-width="1.7"
                                        stroke-linejoin="round"
                                    />
                                </svg>

                                <span>${group.shopName}</span>
                            </div>

                            <div class="summary-shop-items">
                                ${group.items.map(item => `
                                    <div class="summary-selected-item">
                                        <div class="summary-selected-item-main">
                                            <div class="summary-selected-item-image" aria-hidden="true">
                                                ${item.productImage}
                                            </div>

                                            <div class="summary-selected-item-copy">
                                                <strong class="summary-selected-item-name">
                                                    ${item.name}
                                                </strong>

                                                <span class="summary-selected-item-meta">
                                                    ${item.details
                                                        ? `${item.details} · `
                                                        : ''
                                                    }Qty: ${item.quantity}
                                                </span>
                                            </div>
                                        </div>

                                        <div class="summary-selected-item-actions">
                                            <strong class="summary-selected-item-total">
                                                ${money(item.total)}
                                            </strong>

                                            <button
                                                type="button"
                                                class="summary-item-delete"
                                                data-summary-delete-id="${item.id}"
                                                aria-label="Delete ${item.name}"
                                                title="Delete item"
                                            >
                                                <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                                    <path
                                                        d="M4 7h16M9 7V4h6v3m-8 0 1 13h8l1-13M10 11v5m4-5v5"
                                                        stroke="currentColor"
                                                        stroke-width="1.7"
                                                        stroke-linecap="round"
                                                        stroke-linejoin="round"
                                                    />
                                                </svg>
                                            </button>
                                        </div>
                                    </div>
                                `).join('')}
                            </div>

                            <div class="summary-shop-subtotal">
                                <span>Shop Subtotal</span>
                                <strong>${money(group.subtotal)}</strong>
                            </div>
                        </section>
                    `).join('');
            }

            function updateTotals() {
                const rows =
                    getAllItemRows();

                let subtotal =
                    0;

                let selectedCount =
                    0;

                rows.forEach(row => {
                    const checkbox =
                        row.querySelector('.item-checkbox');

                    const quantity =
                        Number(
                            row.querySelector('.quantity-input')?.value || 1
                        );

                    const price =
                        Number(
                            row.dataset.price || 0
                        );

                    const rowTotal =
                        price * quantity;

                    const totalCell =
                        row.querySelector('.cart-item-total');

                    if (totalCell) {
                        totalCell.textContent =
                            money(rowTotal);
                    }

                    row.classList.toggle(
                        'is-unselected',
                        !checkbox?.checked
                    );

                    if (checkbox?.checked) {
                        subtotal += rowTotal;
                        selectedCount++;
                    }
                });

                subtotalLabel.textContent =
                    `Subtotal (${selectedCount} ${selectedCount === 1 ? 'item' : 'items'})`;

                subtotalValue.textContent =
                    money(subtotal);

                shippingValue.textContent =
                    selectedCount > 0
                        ? money(shippingFee)
                        : money(0);

                grandTotalValue.textContent =
                    money(
                        subtotal +
                        (
                            selectedCount > 0
                                ? shippingFee
                                : 0
                        )
                    );

                const allItemCheckboxes =
                    rows.map(
                        row =>
                            row.querySelector('.item-checkbox')
                    ).filter(Boolean);

                selectAll.checked =
                    allItemCheckboxes.length > 0 &&
                    allItemCheckboxes.every(
                        checkbox =>
                            checkbox.checked
                    );

                selectAll.indeterminate =
                    allItemCheckboxes.some(
                        checkbox =>
                            checkbox.checked
                    ) &&
                    !selectAll.checked;

                renderSelectedProductsSummary();
            }

            function syncShopCheckbox(shopCard) {
                const shopCheckbox =
                    shopCard.querySelector('.shop-checkbox');

                const itemCheckboxes =
                    Array.from(
                        shopCard.querySelectorAll('.item-checkbox')
                    );

                if (!shopCheckbox) {
                    return;
                }

                shopCheckbox.checked =
                    itemCheckboxes.length > 0 &&
                    itemCheckboxes.every(
                        checkbox =>
                            checkbox.checked
                    );

                shopCheckbox.indeterminate =
                    itemCheckboxes.some(
                        checkbox =>
                            checkbox.checked
                    ) &&
                    !shopCheckbox.checked;
            }

            selectAll?.addEventListener(
                'change',
                function () {
                    document
                        .querySelectorAll('.shop-checkbox, .item-checkbox')
                        .forEach(
                            checkbox => {
                                checkbox.checked =
                                    selectAll.checked;

                                checkbox.indeterminate =
                                    false;
                            }
                        );

                    updateTotals();
                }
            );

            getCartGroups().forEach(shopCard => {
                const shopCheckbox =
                    shopCard.querySelector('.shop-checkbox');

                shopCheckbox?.addEventListener(
                    'change',
                    function () {
                        shopCard
                            .querySelectorAll('.item-checkbox')
                            .forEach(
                                checkbox => {
                                    checkbox.checked =
                                        shopCheckbox.checked;
                                }
                            );

                        shopCheckbox.indeterminate =
                            false;

                        updateTotals();
                    }
                );

                shopCard
                    .querySelectorAll('.item-checkbox')
                    .forEach(
                        checkbox => {
                            checkbox.addEventListener(
                                'change',
                                function () {
                                    syncShopCheckbox(
                                        shopCard
                                    );

                                    updateTotals();
                                }
                            );
                        }
                    );
            });

            document.addEventListener('click', function (event) {
                const editButton =
                    event.target.closest('.edit-variation-button');

                const cancelButton =
                    event.target.closest('.variation-cancel-button');

                const saveButton =
                    event.target.closest('.variation-save-button');

                const summaryDelete =
                    event.target.closest('.summary-item-delete');

                if (summaryDelete) {
                    event.preventDefault();

                    const row =
                        findCartRowById(
                            summaryDelete.dataset.summaryDeleteId
                        );

                    const checkbox =
                        row?.querySelector('.item-checkbox');

                    if (checkbox) {
                        checkbox.checked = false;
                    }

                    const shopCard =
                        row?.closest('.cart-shop-card');

                    if (shopCard) {
                        syncShopCheckbox(shopCard);
                    }

                    updateTotals();
                    return;
                }

                if (editButton) {
                    event.preventDefault();

                    const row =
                        editButton.closest('[data-cart-item]');

                    const editor =
                        row?.querySelector('.cart-variation-editor');

                    if (!editor) return;

                    editor.hidden = false;
                    editButton.setAttribute('aria-expanded', 'true');

                    editor.dataset.originalColor =
                        row.querySelector('.cart-color-select')?.value || '';

                    editor.dataset.originalVariant =
                        row.querySelector('.cart-variant-select')?.value || '';

                    return;
                }

                if (cancelButton) {
                    event.preventDefault();

                    const editor =
                        cancelButton.closest('.cart-variation-editor');

                    const row =
                        cancelButton.closest('[data-cart-item]');

                    if (!editor || !row) return;

                    const colorSelect =
                        row.querySelector('.cart-color-select');

                    const variantSelect =
                        row.querySelector('.cart-variant-select');

                    if (colorSelect) {
                        colorSelect.value =
                            editor.dataset.originalColor || colorSelect.value;
                    }

                    if (variantSelect) {
                        variantSelect.value =
                            editor.dataset.originalVariant || variantSelect.value;
                    }

                    editor.hidden = true;

                    row
                        .querySelector('.edit-variation-button')
                        ?.setAttribute('aria-expanded', 'false');

                    return;
                }

                if (saveButton) {
                    event.preventDefault();

                    const row =
                        saveButton.closest('[data-cart-item]');

                    const editor =
                        saveButton.closest('.cart-variation-editor');

                    if (!row || !editor) return;

                    const color =
                        row.querySelector('.cart-color-select')?.value || '';

                    const variant =
                        row.querySelector('.cart-variant-select')?.value || '';

                    const colorText =
                        row.querySelector('.cart-selected-color');

                    const variantText =
                        row.querySelector('.cart-selected-variant');

                    if (colorText) {
                        colorText.textContent = `Color: ${color}`;
                    }

                    if (variantText) {
                        variantText.textContent = `Variant: ${variant}`;
                    }

                    editor.hidden = true;

                    row
                        .querySelector('.edit-variation-button')
                        ?.setAttribute('aria-expanded', 'false');

                    updateTotals();
                    return;
                }
            });

            document.addEventListener('click', function (event) {
                const row =
                    event.target.closest('[data-cart-item]');

                if (!row) {
                    return;
                }

                const interactiveTarget =
                    event.target.closest(
                        'button, input, select, label, a, .quantity-control, .cart-variation-editor'
                    );

                if (interactiveTarget) {
                    return;
                }

                const productUrl =
                    row.dataset.productUrl;

                if (productUrl) {
                    window.location.href = productUrl;
                }
            });

            document.addEventListener(
                'click',
                function (event) {
                    const minus =
                        event.target.closest('.quantity-minus');

                    const plus =
                        event.target.closest('.quantity-plus');

                    const remove =
                        event.target.closest('.remove-cart-item');

                    if (minus || plus) {
                        const row =
                            event.target.closest('[data-cart-item]');

                        const input =
                            row?.querySelector('.quantity-input');

                        if (!input) {
                            return;
                        }

                        let quantity =
                            Number(input.value || 1);

                        if (minus) {
                            quantity =
                                Math.max(
                                    1,
                                    quantity - 1
                                );
                        }

                        if (plus) {
                            quantity =
                                Math.min(
                                    99,
                                    quantity + 1
                                );
                        }

                        input.value =
                            quantity;

                        updateTotals();

                        const apiItemId = row?.dataset.apiItemId;
                        if (apiItemId) apiUpdateQuantity(apiItemId, quantity);

                        return;
                    }

                    if (remove) {
                        const row =
                            remove.closest('[data-cart-item]');

                        const apiItemId = row?.dataset.apiItemId;
                        if (apiItemId) apiRemoveItem(apiItemId);

                        removeCartRow(row);
                    }
                }
            );

            document
                .getElementById('proceedCheckoutButton')
                ?.addEventListener(
                    'click',
                    function () {
                        const selected =
                            getAllItemRows().filter(
                                row =>
                                    row.querySelector('.item-checkbox')?.checked
                            );

                        if (!selected.length) {
                            alert('Please select at least one item to checkout.');
                            return;
                        }

                        window.location.href =
                            document.body.dataset.checkoutUrl;
                    }
                );

            updateTotals();
            loadCart();
        });