import QRCode from 'qrcode';

const checkoutConfig = {
    api: (() => {
        const element = document.getElementById('buyerCheckoutConfig');
        return element ? JSON.parse(element.textContent) : {};
    })(),
    registrationAddress: {
        name: document.body?.dataset?.registrationAddressName || 'Buyer',
        phone: document.body?.dataset?.registrationAddressPhone || '',
        address: document.body?.dataset?.registrationAddressText || ''
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
                    checkoutGroupsElement.innerHTML = '<p class="checkout-empty-cart">Your cart is empty. Add products before placing an order.</p>';
                    placeOrderButton.disabled = true;
                    return;
                }

                const groups = new Map();
                items.forEach(item => {
                    const sellerId = item.product.seller.id;
                    if (!groups.has(sellerId)) {
                        groups.set(sellerId, { shop: item.product.seller.store_name || 'Shop', items: [] });
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
                                        <div class="checkout-unit-price">${money(item.product.price)}</div>
                                        <div class="checkout-quantity">${item.quantity}</div>
                                        <div class="checkout-item-subtotal">${money(item.product.price * item.quantity)}</div>
                                    </article>`;
                            }).join('')}
                            <div class="shop-checkout-options">
                                <div class="shipping-option">
                                    <div><strong>Shipping Option</strong><span>Shipping fee will be confirmed by the seller.</span><small>Standard Local</small></div>
                                    <strong class="shipping-fee">${money(0)}</strong>
                                </div>
                            </div>
                            <div class="shop-total-row">
                                <span>Order Total (${quantity} ${quantity === 1 ? 'item' : 'items'}):</span>
                                <strong>${money(subtotal)}</strong>
                            </div>
                        </section>`;
                }).join('');

                const merchandiseTotal = items.reduce((sum, item) => sum + Number(item.product.price) * Number(item.quantity), 0);
                document.getElementById('checkoutMerchandiseSubtotal').textContent = money(merchandiseTotal);
                document.getElementById('checkoutShippingSubtotal').textContent = money(0);
                document.getElementById('checkoutTotalPayment').textContent = money(merchandiseTotal);
                placeOrderButton.disabled = false;
            }

            async function loadCheckoutCart() {
                if (!checkoutConfig.api.apiToken) {
                    renderCheckoutCart([]);
                    if (checkoutMessage) checkoutMessage.textContent = 'Your session expired. Sign in again before placing your order.';
                    return;
                }
                try {
                    const payload = await checkoutApiFetch(checkoutConfig.api.cartUrl || '/api/v1/buyer/cart');
                    renderCheckoutCart(payload.data || []);
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
                    const note = document.createElement('small');
                    note.textContent = 'Scan this QR to verify your order number.';
                    copy.append(heading, shop, total, note);
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
                const address = getSavedAddress() || registrationAddress;
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
                            payment_method: paymentMethodLabel?.textContent || 'Cash on Delivery',
                        }),
                    });
                    renderPlacedOrders(response.orders || []);
                    if (!response.orders?.length) throw new Error('The order was not created. Please try again.');
                    if (checkoutSuccessModal) checkoutSuccessModal.hidden = false;
                    setPageBlurred(true);
                } catch (error) {
                    if (checkoutMessage) checkoutMessage.textContent = error.message;
                    placeOrderButton.disabled = false;
                    placeOrderButton.textContent = 'Place Order';
                }
            }

            loadCheckoutCart();

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
                'shopease_checkout_address';

            const registrationAddress = {
                name: checkoutConfig.registrationAddress.name,
                phone: checkoutConfig.registrationAddress.phone,
                address: checkoutConfig.registrationAddress.address
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