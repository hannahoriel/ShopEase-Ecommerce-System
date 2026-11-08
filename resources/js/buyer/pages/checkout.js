import QRCode from 'qrcode';

const checkoutConfig = {
    api: (() => {
        const element = document.getElementById('buyerCheckoutConfig');
        return element ? JSON.parse(element.textContent) : {};
    })(),
    registrationAddress: {
        name: document.body?.dataset?.registrationAddressName || 'Buyer',
        phone: document.body?.dataset?.registrationAddressPhone || '',
        address: document.body?.dataset?.registrationAddressText || '',
        province: document.body?.dataset?.registrationAddressProvince || '',
        municipality: document.body?.dataset?.registrationAddressMunicipality || ''
    },
    locationsUrl: '/api/v1/locations',
    myPurchasesUrl: document.body?.dataset?.myPurchasesUrl || ''
};

document.addEventListener('DOMContentLoaded', () => {
            const checkoutApiFetch = async (url, options = {}) => {
                const response = await fetch(url, {
                    ...options,
                    headers: {
                        Accept: 'application/json',
                        'Content-Type': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
                        ...(checkoutConfig.api.apiToken
                            ? { Authorization: `Bearer ${checkoutConfig.api.apiToken}` }
                            : {}),
                        ...(options.headers || {}),
                    },
                    credentials: 'same-origin',
                });
                const payload = await response.json().catch(() => ({}));
                if (!response.ok) {
                    const validationMessage = Object.values(payload.errors || {}).flat()[0];
                    throw new Error(validationMessage || payload.message || `Request failed (${response.status}).`);
                }
                return payload;
            };

            const checkoutGroupsElement = document.getElementById('checkoutGroups');
            const checkoutMessage = document.getElementById('checkoutMessage');
            const placeOrderButton = document.getElementById('placeOrderButton');
            let cartItems = [];
            // The cart page saves checked row IDs before navigation. If checkout is opened
            // directly (or from a cached older cart script), use the cart's default checked
            // state and include all current cart rows.
            const checkoutSelectionKey = 'shopease_checkout_cart_item_ids';
            const selectedCartIds = (() => {
                try {
                    const stored = sessionStorage.getItem(checkoutSelectionKey);
                    if (stored === null) return null;
                    const ids = JSON.parse(stored);
                    return Array.isArray(ids) ? [...new Set(ids.map(Number).filter(id => Number.isSafeInteger(id) && id > 0))] : null;
                } catch { return null; }
            })();
            let appliedVoucherCode = '';
            let appliedVoucherDiscount = 0;
            let appliedVoucherSellerId = null;
            let appliedVoucherType = 'merchandise';
            let shippingOptionsBySeller = {};
            let selectedLogisticsBySeller = {};
            let shippingOptionsLoaded = false;
            let walletBalance = 0;
            let walletLoaded = false;
            let walletError = '';
            const walletOption = document.getElementById('checkoutWalletOption');
            const walletAvailability = document.getElementById('checkoutWalletAvailability');
            // Shipping is charged only for an approved, actually selected provider.
            const selectedShippingFee = sellerId => {
                const options = shippingOptionsBySeller[String(sellerId)] || [];
                const selected = selectedLogisticsBySeller[String(sellerId)];
                return shippingOptionsLoaded && selected && options.some(option => String(option.id) === String(selected)) ? 50 : 0;
            };
            const walletTotal = () => cartItems.reduce((sum, item) => sum + Number(item.product.price) * Number(item.quantity), 0)
                + [...new Set(cartItems.map(item => String(item.product.seller.id)))].reduce((sum, id) => sum + selectedShippingFee(id), 0)
                - appliedVoucherDiscount;
            const refreshWalletOption = () => {
                const total = Math.max(0, walletTotal());
                const available = walletLoaded && walletBalance > 0 && total > 0 && cartItems.length > 0 && !appliedVoucherCode
                    && walletBalance + 0.001 >= total;
                if (walletOption) { walletOption.disabled = !available; walletOption.setAttribute('aria-disabled', String(!available)); }
                if (walletAvailability) walletAvailability.textContent = walletLoaded
                    ? `Available ShopEase Wallet balance: ${money(walletBalance)} · Order total: ${money(Math.max(0,walletTotal()))}`
                        + (available ? ' · Sufficient balance' : walletBalance <= 0
                            ? ' · Wallet balance is zero — Cash on Delivery only'
                            : ' · Insufficient balance — Cash on Delivery only')
                    : walletError
                        ? `Wallet balance unavailable · Unable to verify the balance. Wallet payment is temporarily disabled.`
                        : `Wallet balance: ${money(0)} · Checking wallet…`;
                if (!available && document.querySelector('.payment-option.is-selected') === walletOption) {
                    walletOption?.classList.remove('is-selected');
                    const cod = document.querySelector('.payment-option[data-payment-method="Cash on Delivery"]');
                    cod?.classList.add('is-selected');
                    const label = document.getElementById('paymentMethodLabel');
                    if (label) label.textContent = 'Cash on Delivery';
                }
            };
            async function loadWalletBalance() {
                const controller = new AbortController();
                const timeout = window.setTimeout(() => controller.abort(), 8000);
                try {
                    const result = await checkoutApiFetch('/api/v1/buyer/wallet', { signal: controller.signal });
                    const parsed = Number(result.balance);
                    if (!Number.isFinite(parsed) || parsed < 0) throw new Error('Invalid wallet balance response');
                    walletBalance = parsed;
                    walletLoaded = true;
                    walletError = '';
                } catch (error) {
                    walletBalance = 0;
                    walletLoaded = false;
                    walletError = error.name === 'AbortError' ? 'Wallet request timed out' : (error.message || 'Wallet API unavailable');
                } finally {
                    window.clearTimeout(timeout);
                }
                refreshWalletOption();
            }
            loadWalletBalance();

            const escapeCheckoutHtml = value => String(value ?? '').replace(/[&<>"']/g, character => ({
                '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;',
            })[character]);

            const money = value => `₱${Number(value || 0).toLocaleString('en-PH', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2,
            })}`;

            function renderCheckoutCart(items) {
                cartItems = items;
                if (!checkoutGroupsElement) return;
                if (!items.length) {
                    checkoutGroupsElement.innerHTML = '<p class="checkout-empty-cart">No products selected for checkout. Return to Cart and check the items you want to purchase.</p>';
                    placeOrderButton.disabled = true;
                    return;
                }

                const groups = new Map();
                items.forEach(item => {
                    const sellerId = item.product.seller.id;
                    if (!groups.has(sellerId)) {
                        groups.set(sellerId, { id: sellerId, shop: item.product.seller.store_name || 'Shop', items: [] });
                    }
                    groups.get(sellerId).items.push(item);
                });

                checkoutGroupsElement.innerHTML = [...groups.values()].map(group => {
                    const subtotal = group.items.reduce((total, item) => total + Number(item.product.price) * Number(item.quantity), 0);
                    const quantity = group.items.reduce((total, item) => total + Number(item.quantity), 0);
                    return `
                        <section class="checkout-shop-group">
                            <div class="checkout-shop-header">
                                <div class="checkout-shop-name-wrap">
                                    <span class="checkout-shop-icon" aria-hidden="true">▣</span>
                                    <strong>${escapeCheckoutHtml(group.shop)}</strong>
                                </div>
                            </div>
                            ${group.items.map(item => {
                                const options = [item.variation, item.color, item.size].filter(Boolean).join(' · ');
                                const image = item.product.image_url
                                    ? `<img src="${escapeCheckoutHtml(item.product.image_url)}" alt="${escapeCheckoutHtml(item.product.name)}">`
                                    : '<span class="checkout-product-placeholder" aria-hidden="true">No image</span>';
                                return `
                                    <article class="checkout-product-row">
                                        <div class="checkout-product-cell">
                                            <div class="checkout-product-image">${image}</div>
                                            <div class="checkout-product-copy">
                                                <strong>${escapeCheckoutHtml(item.product.name)}</strong>
                                                <span>${options ? `Variation: ${escapeCheckoutHtml(options)}` : 'Standard'}</span>
                                            </div>
                                        </div>
                                        <div class="checkout-unit-price"><strong>${money(item.product.price)}</strong>${item.product.sale_percentage ? `<del>${money(item.product.original_price)}</del><em>-${item.product.sale_percentage}%</em>` : ''}</div>
                                        <div class="checkout-quantity">${item.quantity}</div>
                                        <div class="checkout-item-subtotal">${money(item.product.price * item.quantity)}</div>
                                    </article>`;
                            }).join('')}
                            <div class="shop-checkout-options">
                                <div class="shipping-option">
                                    <div class="checkout-shipping-choice">
                                        <div class="checkout-logistics-heading">
                                            <strong>Logistics Company</strong>
                                            <span class="checkout-logistics-status ${!shippingOptionsLoaded ? 'is-loading' : (shippingOptionsBySeller[group.id] || []).length ? 'is-available' : 'is-unavailable'}">${!shippingOptionsLoaded ? 'Checking availability' : (shippingOptionsBySeller[group.id] || []).length ? 'Available' : 'Unavailable'}</span>
                                        </div>
                                        ${shippingOptionsLoaded && !(shippingOptionsBySeller[group.id] || []).length
                                            ? `<div class="checkout-logistics-notice" role="status"><span class="checkout-logistics-notice-icon" aria-hidden="true">!</span><div><strong>No logistics service available</strong><p>No approved logistics company currently covers both the seller and delivery locations. Try another delivery address or check again later.</p></div></div>`
                                            : `<label class="checkout-logistics-label" for="checkout-logistics-${escapeCheckoutHtml(group.id)}">${shippingOptionsLoaded ? 'Select a logistics company serving both locations' : 'Looking for logistics companies…'}</label>
                                               <select id="checkout-logistics-${escapeCheckoutHtml(group.id)}" class="checkout-logistics-select" data-logistics-seller="${escapeCheckoutHtml(group.id)}" aria-label="Logistics company for ${escapeCheckoutHtml(group.shop)}" ${!shippingOptionsLoaded ? 'disabled' : ''}><option value="">${shippingOptionsLoaded ? 'Select logistics company' : 'Loading available companies…'}</option>${(shippingOptionsBySeller[group.id] || []).map(option => `<option value="${escapeCheckoutHtml(option.id)}" ${String(selectedLogisticsBySeller[group.id] || '') === String(option.id) ? 'selected' : ''}>${escapeCheckoutHtml(option.name)}</option>`).join('')}</select>`}
                                    </div>
                                    <strong class="shipping-fee">${money(selectedShippingFee(group.id))}</strong>
                                </div>
                            </div>
                            <div class="shop-total-row">
                                <span>Order Total (${quantity} ${quantity === 1 ? 'item' : 'items'}):</span>
                                <strong>${money(subtotal + selectedShippingFee(group.id))}</strong>
                            </div>
                        </section>`;
                }).join('');

                const merchandiseTotal = items.reduce((sum, item) => sum + Number(item.product.price) * Number(item.quantity), 0);
                const shippingTotal = [...groups.keys()].reduce((sum, sellerId) => sum + selectedShippingFee(sellerId), 0);
                document.getElementById('checkoutMerchandiseSubtotal').textContent = money(merchandiseTotal);
                document.getElementById('checkoutShippingSubtotal').textContent = money(shippingTotal);
                document.getElementById('checkoutVoucherDiscount').textContent = `−${money(appliedVoucherDiscount)}`;
                document.getElementById('checkoutTotalPayment').textContent = money(Math.max(0, merchandiseTotal + shippingTotal - appliedVoucherDiscount));
                updatePlaceOrderState(groups);
                refreshWalletOption();
            }

            function updatePlaceOrderState(groups = null) {
                if (!placeOrderButton) return;
                if (!cartItems.length) {
                    placeOrderButton.disabled = true;
                    return;
                }
                const sellerIds = groups
                    ? [...groups.keys()]
                    : [...new Set(cartItems.map(item => String(item.product.seller.id)))];
                placeOrderButton.disabled = !shippingOptionsLoaded || sellerIds.some(sellerId => {
                    const options = shippingOptionsBySeller[sellerId] || [];
                    return !options.length || !options.some(option => String(option.id) === String(selectedLogisticsBySeller[sellerId] || ''));
                });
            }

            async function refreshShippingOptions(address = getSavedAddress() || registrationAddress) {
                shippingOptionsLoaded = false;
                shippingOptionsBySeller = {};
                selectedLogisticsBySeller = {};
                renderCheckoutCart(cartItems);
                if (!cartItems.length || !address?.province || !address?.municipality) {
                    shippingOptionsLoaded = true;
                    renderCheckoutCart(cartItems);
                    if (checkoutMessage) checkoutMessage.textContent = 'Add a delivery address with province and municipality to check shipping availability.';
                    return;
                }
                try {
                    const url = new URL(checkoutConfig.api.shippingOptionsUrl || '/api/v1/buyer/shipping-options', window.location.origin);
                    url.searchParams.set('province', address.province);
                    url.searchParams.set('municipality', address.municipality);
                    const payload = await checkoutApiFetch(url.toString());
                    shippingOptionsBySeller = Object.fromEntries((payload.data || []).map(group => [String(group.seller_id), group.options || []]));
                } catch (error) {
                    if (checkoutMessage) checkoutMessage.textContent = error.message;
                } finally {
                    shippingOptionsLoaded = true;
                    renderCheckoutCart(cartItems);
                }
            }

            checkoutGroupsElement?.addEventListener('change', event => {
                const select = event.target.closest('[data-logistics-seller]');
                if (!select) return;
                selectedLogisticsBySeller[String(select.dataset.logisticsSeller)] = select.value;
                renderCheckoutCart(cartItems); // Refresh shipping, order and wallet totals immediately.
            });

            async function loadCheckoutCart() {
                if (!checkoutConfig.api.apiToken) {
                    renderCheckoutCart([]);
                    if (checkoutMessage) checkoutMessage.textContent = 'Your session expired. Sign in again before placing your order.';
                    return;
                }
                try {
                    if (Array.isArray(selectedCartIds) && !selectedCartIds.length) {
                        renderCheckoutCart([]);
                        if (checkoutMessage) checkoutMessage.textContent = 'No cart items were selected. Return to your cart and select the products you want to checkout.';
                        return;
                    }
                    const payload = await checkoutApiFetch(checkoutConfig.api.cartUrl || '/api/v1/buyer/cart');
                    const actual = payload.data || [];
                    const selection = Array.isArray(selectedCartIds) ? new Set(selectedCartIds) : null;
                    const chosen = selection ? actual.filter(item => selection.has(Number(item.id))) : actual;
                    renderCheckoutCart(chosen);
                    if (selection && chosen.length !== selectedCartIds.length && checkoutMessage) {
                        checkoutMessage.textContent = 'Some selected products are no longer in your cart. Review the items before placing the order.';
                    }
                } catch (error) {
                    if (checkoutGroupsElement) checkoutGroupsElement.textContent = error.message;
                    placeOrderButton.disabled = true;
                }
            }

            function renderPlacedOrders(orders) {
                const results = document.getElementById('checkoutOrderResults');
                if (!results) return;
                results.replaceChildren();
                orders.forEach(order => {
                    const card = document.createElement('article');
                    card.className = 'checkout-placed-order';
                    const canvas = document.createElement('canvas');
                    canvas.width = 176;
                    canvas.height = 176;
                    const copy = document.createElement('div');
                    copy.className = 'checkout-placed-order-copy';
                    const heading = document.createElement('strong');
                    heading.textContent = order.order_number;
                    const shop = document.createElement('span');
                    shop.textContent = order.shop;
                    const total = document.createElement('span');
                    total.textContent = `Total: ${money(order.total)}`;
                    const logistics = document.createElement('span');
                    logistics.textContent = `Logistics: ${order.logistics_company || 'Selected logistics provider'}`;
                    const note = document.createElement('small');
                    note.textContent = 'Scan this QR to verify your order number.';
                    copy.append(heading, shop, logistics, total, note);
                    card.append(canvas, copy);
                    results.append(card);
                    QRCode.toCanvas(canvas, order.qr_payload, {
                        width: 176,
                        margin: 1,
                        errorCorrectionLevel: 'M',
                    }).catch(error => {
                        const failure = document.createElement('small');
                        failure.textContent = `QR could not be generated: ${error.message}`;
                        copy.append(failure);
                    });
                });
            }

            async function placeBuyerOrder() {
                if (!cartItems.length || placeOrderButton.disabled) return;
                const address = { ...registrationAddress, ...(getSavedAddress() || {}) };
                if (!address?.name || !address?.phone || !address?.address) {
                    if (checkoutMessage) checkoutMessage.textContent = 'Please choose a complete delivery address before placing your order.';
                    return;
                }
                placeOrderButton.disabled = true;
                placeOrderButton.textContent = 'Placing order…';
                if (checkoutMessage) checkoutMessage.textContent = '';
                try {
                    const response = await checkoutApiFetch(checkoutConfig.api.ordersUrl || '/api/v1/buyer/orders', {
                        method: 'POST',
                        body: JSON.stringify({
                            delivery_name: address.name,
                            delivery_phone: address.phone,
                            delivery_address: address.address,
                            delivery_province: address.province,
                            delivery_municipality: address.municipality,
                            logistics_selections: [...new Set(cartItems.map(item => String(item.product.seller.id)))].map(sellerId => ({
                                seller_id: Number(sellerId),
                                logistics_id: Number(selectedLogisticsBySeller[sellerId]),
                            })),
                            payment_method: paymentMethodLabel?.textContent || 'Cash on Delivery',
                            cart_item_ids: cartItems.map(item => Number(item.id)),
                            voucher_code: appliedVoucherCode || null,
                            voucher_seller_id: appliedVoucherSellerId,
                        }),
                    });
                    renderPlacedOrders(response.orders || []);
                    if (!response.orders?.length) throw new Error('The order was not created. Please try again.');
                    sessionStorage.removeItem(checkoutSelectionKey);
                    loadWalletBalance();
                    if (!response.orders?.length) throw new Error('The order was not created. Please try again.');
                    sessionStorage.removeItem(checkoutSelectionKey);
                    if (checkoutSuccessModal) checkoutSuccessModal.hidden = false;
                    setPageBlurred(true);
                } catch (error) {
                    if (checkoutMessage) checkoutMessage.textContent = error.message;
                    placeOrderButton.disabled = false;
                    placeOrderButton.textContent = 'Place Order';
                }
            }

            document.getElementById('checkoutApplyVoucher')?.addEventListener('click', async () => {
                const input = document.getElementById('checkoutVoucherCode');
                const message = document.getElementById('checkoutVoucherMessage');
                const code = input?.value.trim().toUpperCase();
                appliedVoucherCode = '';
                appliedVoucherDiscount = 0;
                appliedVoucherSellerId = null;
                appliedVoucherType = 'merchandise';
                if (!code) {
                    if (message) message.textContent = 'Enter a voucher code first.';
                    renderCheckoutCart(cartItems);
                    return;
                }
                try {
                    const result = await checkoutApiFetch(checkoutConfig.api.voucherPreviewUrl || '/api/v1/buyer/vouchers/preview', {
                        method: 'POST', body: JSON.stringify({ code }),
                    });
                    appliedVoucherCode = result.data.code;
                    appliedVoucherDiscount = Number(result.data.discount || 0);
                    appliedVoucherSellerId = Number(result.data.seller_id);
                    appliedVoucherType = result.data.voucher_type || 'merchandise';
                    if (message) message.textContent = `${result.data.code} applied to ${result.data.seller_name}${appliedVoucherType === 'shipping' ? ' shipping fee' : ''}.`;
                } catch (error) {
                    if (message) message.textContent = error.message;
                }
                renderCheckoutCart(cartItems);
            });

            document.getElementById('checkoutVoucherCode')?.addEventListener('input', () => {
                appliedVoucherCode = '';
                appliedVoucherDiscount = 0;
                appliedVoucherSellerId = null;
                appliedVoucherType = 'merchandise';
                const message = document.getElementById('checkoutVoucherMessage');
                if (message) message.textContent = '';
                renderCheckoutCart(cartItems);
            });

            const requestedVoucher = new URLSearchParams(window.location.search).get('voucher');
            loadCheckoutCart().then(() => {
                refreshShippingOptions({ ...registrationAddress, ...(getSavedAddress() || {}) });
                if (!requestedVoucher || !cartItems.length) return;
                const voucherInput = document.getElementById('checkoutVoucherCode');
                if (!voucherInput) return;
                voucherInput.value = requestedVoucher;
                document.getElementById('checkoutApplyVoucher')?.click();
            });

            const pageContent =
                document.getElementById('checkoutPageContent');

            const registrationAddressModal =
                document.getElementById('registrationAddressModal');

            const newAddressModal =
                document.getElementById('newAddressModal');

            const useRegistrationAddressYes =
                document.getElementById('useRegistrationAddressYes');

            const useRegistrationAddressNo =
                document.getElementById('useRegistrationAddressNo');

            const backToRegistrationAddress =
                document.getElementById('backToRegistrationAddress');

            const newAddressForm =
                document.getElementById('newAddressForm');

            const checkoutLocationsApiBase =
                '/api/v1/locations';

            const checkoutProvinceWrapper =
                document.getElementById('checkoutProvinceWrapper');

            const checkoutProvinceSearch =
                document.getElementById('checkoutProvinceSearch');

            const checkoutProvinceOptions =
                document.getElementById('checkoutProvinceOptions');

            const checkoutProvinceHidden =
                document.getElementById('checkoutProvince');

            const checkoutMunicipalityWrapper =
                document.getElementById('checkoutMunicipalityWrapper');

            const checkoutMunicipalitySearch =
                document.getElementById('checkoutMunicipalitySearch');

            const checkoutMunicipalityOptions =
                document.getElementById('checkoutMunicipalityOptions');

            const checkoutMunicipalityHidden =
                document.getElementById('checkoutMunicipality');

            const checkoutBarangayWrapper =
                document.getElementById('checkoutBarangayWrapper');

            const checkoutBarangaySearch =
                document.getElementById('checkoutBarangaySearch');

            const checkoutBarangayOptions =
                document.getElementById('checkoutBarangayOptions');

            const checkoutBarangayHidden =
                document.getElementById('checkoutBarangay');

            function getCheckoutLocationName(location) {
                return (
                    location.name ||
                    location.prov_name ||
                    location.city_name ||
                    location.mun_name ||
                    location.brgy_name ||
                    ''
                );
            }

            function getCheckoutLocationCode(location) {
                return (
                    location.code ||
                    location.psgc_code ||
                    location.prov_code ||
                    location.mun_code ||
                    location.city_code ||
                    location.brgy_code ||
                    ''
                );
            }

            async function loadCheckoutLocations(endpoint) {
                const response =
                    await fetch(
                        `${checkoutLocationsApiBase}/${endpoint}`,
                        {
                            headers: {
                                Accept: 'application/json'
                            }
                        }
                    );

                if (!response.ok) {
                    throw new Error(
                        'Unable to load location options.'
                    );
                }

                return response.json();
            }

            function createCheckoutSearchSelect(
                wrapper,
                input,
                optionsContainer,
                hiddenInput
            ) {
                let locations = [];
                let filteredLocations = [];
                let highlightedIndex = -1;
                let loading = false;

                const close = () => {
                    wrapper?.classList.remove('open');
                    input?.setAttribute('aria-expanded', 'false');
                    highlightedIndex = -1;
                };

                const render = (searchText = '') => {
                    if (loading || !optionsContainer) {
                        return;
                    }

                    const query =
                        searchText.trim().toLowerCase();

                    filteredLocations =
                        locations.filter(location =>
                            getCheckoutLocationName(location)
                                .toLowerCase()
                                .includes(query)
                        );

                    highlightedIndex = -1;
                    optionsContainer.innerHTML = '';

                    if (!filteredLocations.length) {
                        optionsContainer.innerHTML =
                            '<div class="checkout-search-select-no-results">No matching result found.</div>';
                        return;
                    }

                    filteredLocations.forEach((location, index) => {
                        const option =
                            document.createElement('button');

                        option.type = 'button';
                        option.className =
                            'checkout-search-select-option';

                        option.textContent =
                            getCheckoutLocationName(location);

                        if (
                            getCheckoutLocationName(location) ===
                            hiddenInput.value
                        ) {
                            option.classList.add('selected');
                        }

                        option.addEventListener(
                            'mousedown',
                            event => {
                                event.preventDefault();

                                const name =
                                    getCheckoutLocationName(location);

                                input.value = name;
                                hiddenInput.value = name;
                                close();

                                input.dispatchEvent(
                                    new CustomEvent(
                                        'location:selected',
                                        { detail: location }
                                    )
                                );
                            }
                        );

                        optionsContainer.appendChild(option);
                    });
                };

                const open = () => {
                    if (!input || input.disabled) {
                        return;
                    }

                    wrapper?.classList.add('open');
                    input.setAttribute('aria-expanded', 'true');
                    render(input.value);
                };

                const setLoading = message => {
                    loading = true;

                    if (optionsContainer) {
                        optionsContainer.innerHTML =
                            `<div class="checkout-search-select-loading">${message}</div>`;
                    }
                };

                const setLocations = newLocations => {
                    locations =
                        Array.isArray(newLocations)
                            ? newLocations
                            : [];

                    filteredLocations = locations.slice();
                    loading = false;
                    render(input?.value || '');
                };

                const clear = () => {
                    locations = [];
                    filteredLocations = [];
                    highlightedIndex = -1;

                    if (input) {
                        input.value = '';
                    }

                    if (hiddenInput) {
                        hiddenInput.value = '';
                    }

                    if (optionsContainer) {
                        optionsContainer.innerHTML = '';
                    }

                    close();
                };

                input?.addEventListener('focus', open);
                input?.addEventListener('click', open);

                input?.addEventListener('input', function () {
                    hiddenInput.value = '';
                    render(this.value);
                    open();
                });

                input?.addEventListener('keydown', event => {
                    if (input.disabled) {
                        return;
                    }

                    if (event.key === 'ArrowDown') {
                        event.preventDefault();
                        open();

                        highlightedIndex =
                            Math.min(
                                highlightedIndex + 1,
                                filteredLocations.length - 1
                            );
                    }

                    if (event.key === 'ArrowUp') {
                        event.preventDefault();

                        highlightedIndex =
                            Math.max(
                                highlightedIndex - 1,
                                0
                            );
                    }

                    if (
                        event.key === 'Enter' &&
                        highlightedIndex >= 0 &&
                        filteredLocations[highlightedIndex]
                    ) {
                        event.preventDefault();

                        const location =
                            filteredLocations[highlightedIndex];

                        const name =
                            getCheckoutLocationName(location);

                        input.value = name;
                        hiddenInput.value = name;
                        close();

                        input.dispatchEvent(
                            new CustomEvent(
                                'location:selected',
                                { detail: location }
                            )
                        );
                    }

                    if (event.key === 'Escape') {
                        close();
                    }

                    optionsContainer
                        ?.querySelectorAll(
                            '.checkout-search-select-option'
                        )
                        .forEach((option, index) => {
                            option.classList.toggle(
                                'highlighted',
                                index === highlightedIndex
                            );
                        });
                });

                input?.addEventListener('blur', () => {
                    window.setTimeout(() => {
                        if (
                            hiddenInput.value !==
                            input.value
                        ) {
                            input.value =
                                hiddenInput.value;
                        }

                        close();
                    }, 180);
                });

                return {
                    setLoading,
                    setLocations,
                    clear,
                    close
                };
            }

            const checkoutProvinceControl =
                createCheckoutSearchSelect(
                    checkoutProvinceWrapper,
                    checkoutProvinceSearch,
                    checkoutProvinceOptions,
                    checkoutProvinceHidden
                );

            const checkoutMunicipalityControl =
                createCheckoutSearchSelect(
                    checkoutMunicipalityWrapper,
                    checkoutMunicipalitySearch,
                    checkoutMunicipalityOptions,
                    checkoutMunicipalityHidden
                );

            const checkoutBarangayControl =
                createCheckoutSearchSelect(
                    checkoutBarangayWrapper,
                    checkoutBarangaySearch,
                    checkoutBarangayOptions,
                    checkoutBarangayHidden
                );

            const disableCheckoutMunicipality = () => {
                checkoutMunicipalityControl.clear();
                checkoutMunicipalitySearch.disabled = true;
                checkoutMunicipalitySearch.placeholder =
                    'Select province first';

                checkoutMunicipalityWrapper.classList.add(
                    'disabled'
                );
            };

            const disableCheckoutBarangay = () => {
                checkoutBarangayControl.clear();
                checkoutBarangaySearch.disabled = true;
                checkoutBarangaySearch.placeholder =
                    'Select municipality first';

                checkoutBarangayWrapper.classList.add(
                    'disabled'
                );
            };

            const enableCheckoutMunicipality = () => {
                checkoutMunicipalitySearch.disabled = false;
                checkoutMunicipalitySearch.placeholder =
                    'Type to search municipality';

                checkoutMunicipalityWrapper.classList.remove(
                    'disabled'
                );
            };

            const enableCheckoutBarangay = () => {
                checkoutBarangaySearch.disabled = false;
                checkoutBarangaySearch.placeholder =
                    'Type to search barangay';

                checkoutBarangayWrapper.classList.remove(
                    'disabled'
                );
            };

            async function loadCheckoutMunicipalities(
                provinceLocation
            ) {
                const provinceCode =
                    getCheckoutLocationCode(provinceLocation);

                const query =
                    provinceLocation.isRegion === true
                        ? '?is_region=1'
                        : '';

                checkoutMunicipalityControl.clear();
                checkoutMunicipalityControl.setLoading(
                    'Loading municipalities...'
                );

                enableCheckoutMunicipality();

                try {
                    const municipalities =
                        await loadCheckoutLocations(
                            `provinces/${encodeURIComponent(
                                provinceCode
                            )}/cities${query}`
                        );

                    checkoutMunicipalityControl.setLocations(
                        municipalities
                    );

                    disableCheckoutBarangay();
                } catch (error) {
                    console.error(
                        'Municipality loading error:',
                        error
                    );

                    disableCheckoutBarangay();
                }
            }

            async function loadCheckoutBarangays(
                municipalityLocation
            ) {
                const municipalityCode =
                    getCheckoutLocationCode(
                        municipalityLocation
                    );

                checkoutBarangayControl.clear();
                checkoutBarangayControl.setLoading(
                    'Loading barangays...'
                );

                enableCheckoutBarangay();

                try {
                    const barangays =
                        await loadCheckoutLocations(
                            `cities/${encodeURIComponent(
                                municipalityCode
                            )}/barangays`
                        );

                    checkoutBarangayControl.setLocations(
                        barangays
                    );
                } catch (error) {
                    console.error(
                        'Barangay loading error:',
                        error
                    );
                }
            }

            checkoutProvinceSearch?.addEventListener(
                'location:selected',
                event => {
                    loadCheckoutMunicipalities(
                        event.detail
                    );
                }
            );

            checkoutMunicipalitySearch?.addEventListener(
                'location:selected',
                event => {
                    loadCheckoutBarangays(
                        event.detail
                    );
                }
            );

            async function initializeCheckoutLocations() {
                disableCheckoutMunicipality();
                disableCheckoutBarangay();

                try {
                    checkoutProvinceControl.setLoading(
                        'Loading provinces...'
                    );

                    const provinces =
                        await loadCheckoutLocations(
                            'provinces'
                        );

                    checkoutProvinceControl.setLocations(
                        provinces
                    );
                } catch (error) {
                    console.error(
                        'Province loading error:',
                        error
                    );
                }
            }

            initializeCheckoutLocations();

            document.addEventListener('click', event => {
                [
                    checkoutProvinceWrapper,
                    checkoutMunicipalityWrapper,
                    checkoutBarangayWrapper
                ].forEach(wrapper => {
                    if (
                        wrapper &&
                        !wrapper.contains(event.target)
                    ) {
                        wrapper.classList.remove('open');
                    }
                });
            });

            const changeAddressButton =
                document.getElementById('changeAddressButton');

            const addressName =
                document.getElementById('checkoutAddressName');

            const addressPhone =
                document.getElementById('checkoutAddressPhone');

            const addressText =
                document.getElementById('checkoutAddressText');

            const paymentMethodLabel =
                document.getElementById('paymentMethodLabel');

            const checkoutSuccessModal =
                document.getElementById('checkoutSuccessModal');

            const checkoutSuccessOkay =
                document.getElementById('checkoutSuccessOkay');

            const addressStorageKey =
                `shopease_checkout_address:${encodeURIComponent(document.body.dataset.buyerId || 'guest')}`;

            const registrationAddress = {
                name: checkoutConfig.registrationAddress.name,
                phone: checkoutConfig.registrationAddress.phone,
                address: checkoutConfig.registrationAddress.address,
                province: checkoutConfig.registrationAddress.province,
                municipality: checkoutConfig.registrationAddress.municipality
            };

            const setPageBlurred = (blurred) => {
                pageContent?.classList.toggle('is-blurred', blurred);
                document.body.classList.toggle(
                    'checkout-modal-open',
                    blurred
                );
            };

            const hideAllAddressModals = () => {
                if (registrationAddressModal) {
                    registrationAddressModal.hidden = true;
                }

                if (newAddressModal) {
                    newAddressModal.hidden = true;
                }
            };

            const showRegistrationAddressModal = () => {
                hideAllAddressModals();

                if (registrationAddressModal) {
                    registrationAddressModal.hidden = false;
                }

                setPageBlurred(true);
            };

            const showNewAddressModal = () => {
                hideAllAddressModals();

                if (newAddressModal) {
                    newAddressModal.hidden = false;
                }

                setPageBlurred(true);

                window.setTimeout(() => {
                    document
                        .getElementById('newAddressName')
                        ?.focus();
                }, 80);
            };

            const renderAddress = (address) => {
                if (!address) {
                    return;
                }

                if (addressName) {
                    addressName.textContent =
                        address.name || 'Buyer';
                }

                if (addressPhone) {
                    addressPhone.textContent =
                        address.phone || '';
                }

                if (addressText) {
                    addressText.textContent =
                        address.address || '';
                }
            };

            const saveAddress = (address) => {
                try {
                    localStorage.setItem(
                        addressStorageKey,
                        JSON.stringify(address)
                    );
                } catch (error) {
                    console.warn(
                        'Checkout address could not be saved locally.',
                        error
                    );
                }

                renderAddress(address);
                hideAllAddressModals();
                setPageBlurred(false);
                refreshShippingOptions({ ...registrationAddress, ...address });
            };

            const getSavedAddress = () => {
                try {
                    const saved =
                        localStorage.getItem(addressStorageKey);

                    return saved
                        ? JSON.parse(saved)
                        : null;
                } catch (error) {
                    return null;
                }
            };

            /*
             * FIRST ENTRY:
             * If no checkout address has been selected yet,
             * blur the page and ask whether to use the registration address.
             */
            const savedAddress =
                getSavedAddress();

            if (savedAddress) {
                renderAddress(savedAddress);
                setPageBlurred(false);
            } else {
                showRegistrationAddressModal();
            }

            useRegistrationAddressYes?.addEventListener(
                'click',
                () => {
                    saveAddress(registrationAddress);
                }
            );

            useRegistrationAddressNo?.addEventListener(
                'click',
                () => {
                    showNewAddressModal();
                }
            );

            backToRegistrationAddress?.addEventListener(
                'click',
                () => {
                    showRegistrationAddressModal();
                }
            );

            newAddressForm?.addEventListener(
                'submit',
                event => {
                    event.preventDefault();

                    const name =
                        document
                            .getElementById('newAddressName')
                            ?.value
                            ?.trim();

                    const phone =
                        document
                            .getElementById('newAddressPhone')
                            ?.value
                            ?.trim();

                    const province =
                        checkoutProvinceHidden?.value?.trim();

                    const municipality =
                        checkoutMunicipalityHidden?.value?.trim();

                    const barangay =
                        checkoutBarangayHidden?.value?.trim();

                    const zip =
                        document
                            .getElementById('newAddressZip')
                            ?.value
                            ?.trim();

                    const street =
                        document
                            .getElementById('newAddressStreet')
                            ?.value
                            ?.trim();

                    const house =
                        document
                            .getElementById('newAddressHouse')
                            ?.value
                            ?.trim();

                    const building =
                        document
                            .getElementById('newAddressBuilding')
                            ?.value
                            ?.trim();

                    const subdivision =
                        document
                            .getElementById('newAddressSubdivision')
                            ?.value
                            ?.trim();

                    if (
                        !name ||
                        !phone ||
                        !province ||
                        !municipality ||
                        !barangay ||
                        !zip
                    ) {
                        alert(
                            'Please complete the required address fields.'
                        );
                        return;
                    }

                    const detailedAddress = [
                        house,
                        building,
                        street,
                        subdivision,
                        barangay,
                        municipality,
                        province,
                        zip
                    ]
                        .filter(Boolean)
                        .join(', ');

                    saveAddress({
                        name,
                        phone,
                        address: detailedAddress,
                        province,
                        municipality,
                        barangay,
                        zip,
                        street,
                        house,
                        building,
                        subdivision
                    });
                }
            );

            changeAddressButton?.addEventListener(
                'click',
                () => {
                    showNewAddressModal();
                }
            );

            document
                .querySelectorAll('.payment-option')
                .forEach(button => {
                    button.addEventListener(
                        'click',
                        () => {
                            document
                                .querySelectorAll('.payment-option')
                                .forEach(option => {
                                    option.classList.remove(
                                        'is-selected'
                                    );
                                });

                            if (button.disabled) { refreshWalletOption(); return; }
                            button.classList.add('is-selected');

                            if (paymentMethodLabel) {
                                paymentMethodLabel.textContent =
                                    button.dataset.paymentMethod;
                            }
                        }
                    );
                });

            document
                .getElementById('placeOrderButton')
                ?.addEventListener(
                    'click',
                    placeBuyerOrder
                );

            checkoutSuccessOkay?.addEventListener(
                'click',
                () => {
                    window.location.href =
                        checkoutConfig.myPurchasesUrl;
                }
            );
        });
