
    window.openRealSellerModal = function (row) {
        window.__activeSellerComplianceRow = row;
        const sellerId = String(row?.dataset?.id || '');
        const sellers = @json($initialComplianceData['data'] ?? []);
        const seller = sellers.find(function (item) {
            return String(item.id) === sellerId;
        });
        const modal = document.getElementById('sellerDetailsModal');

        if (!seller || !modal) {
            return;
        }

        const setText = function (id, value) {
            const element = document.getElementById(id);
            if (element) {
                element.textContent = value || '-';
            }
        };

        setText('sellerModalStoreName', seller.store_name);
        setText('sellerModalSince', seller.seller_since);
        setText('sellerModalOwner', seller.owner_name);
        setText('sellerModalEmail', seller.email);
        setText('sellerModalPhone', seller.phone);
        setText('sellerModalLocation', seller.location);
        setText('sellerModalProducts', `${seller.products_count || 0} Product${seller.products_count === 1 ? '' : 's'}`);
        setText('sellerModalScore', `${seller.compliance_score || 0}%`);
        setText('sellerModalCompliancePill', seller.compliance_label);

        const scoreFill = document.getElementById('sellerModalScoreFill');
        if (scoreFill) {
            scoreFill.style.width = `${Math.max(0, Math.min(100, Number(seller.compliance_score || 0)))}%`;
        }

        const categories = document.getElementById('sellerModalCategories');
        if (categories) {
            const categoryLabels = {
                'pet-supplies': 'Pet Supplies',
                'electronics-and-gadgets': 'Electronics & Gadgets',
                'womens-apparel': 'Women\'s Apparel',
                'mens-apparel': 'Men\'s Apparel',
                'kids-and-baby': 'Kids & Baby',
                'home-and-garden': 'Home & Garden',
                'sports-and-outdoors': 'Sports & Outdoors',
                'health-and-beauty': 'Health & Beauty',
                'books-and-media': 'Books & Media',
                'food-and-gourmet': 'Food & Gourmet',
                'automotive-motorcycle': 'Automotive & Motorcycle',
                'furniture-and-office-equipment': 'Furniture & Office Equipment',
                'jewelry-and-watches': 'Jewelry & Watches',
                'office-and-school-supplies': 'Office & School Supplies'
            };

            categories.innerHTML = (seller.categories || []).map(function (category) {
                const key = String(category || '').toLowerCase();
                const label = categoryLabels[key] || String(category || '').replace(/-/g, ' ');
                const className = key || 'default';
                return `<span class="seller-modal-category category-${className}">${label}</span>`;
            }).join('') || '<span class="seller-modal-category category-default">-</span>';
        }

        const documents = [
            {
                selector: '[data-document="business_permit.png"]',
                url: seller.business_permit_url,
                name: seller.business_permit_name || 'Business permit'
            },
            {
                selector: '[data-document="valid_ID.png"]',
                url: seller.valid_id_url,
                name: seller.valid_id_name || 'Valid ID'
            }
        ];

        documents.forEach(function (documentItem) {
            const button = document.querySelector(documentItem.selector);
            if (!button) {
                return;
            }

            button.dataset.url = documentItem.url || '';
            button.querySelector('span')?.replaceChildren(document.createTextNode(documentItem.name));
            button.disabled = !documentItem.url;
            button.classList.toggle('opacity-50', !documentItem.url);
        });

        const productsGrid = document.getElementById('sellerProductsGrid');
        if (productsGrid) {
            productsGrid.innerHTML = (seller.products || []).map(function (product) {
                window.__realSellerProducts[String(product.id)] = product;
                const status = String(product.status || 'pending').toLowerCase();
                const isArchived = Boolean(product.is_archived);
                const isApproved = status === 'active' && !isArchived;
                const statusClass = isArchived
                    ? 'status-removed'
                    : isApproved
                        ? 'status-approved'
                        : 'status-under-review';
                const statusLabel = isArchived ? 'Removed' : isApproved ? 'Approved' : 'Under Review';
                const image = product.image_url
                    ? `<img src="${product.image_url}" alt="${String(product.name || 'Product').replace(/[&<>"']/g, '')}">`
                    : '<div class="seller-product-image-placeholder">No image</div>';
                const price = Number(product.price || 0).toLocaleString('en-PH', {
                    style: 'currency',
                    currency: 'PHP'
                });
                const sold = Number(product.sold_count || 0);

                return `<article class="seller-product-card ${statusClass}" tabindex="0" role="button" data-product-id="${product.id}" onclick="openRealProductModal('${product.id}')">
                    <div class="seller-product-image">${image}</div>
                    <div class="seller-product-info">
                        <div class="seller-product-name">${String(product.name || 'Product').replace(/[&<>"']/g, '')}</div>
                        <div class="seller-product-bottom">
                            <span class="seller-product-price">${price}</span>
                            <span class="seller-product-sold">${sold} sold</span>
                        </div>
                        <span class="seller-product-status-label">${statusLabel}</span>
                    </div>
                </article>`;
            }).join('') || '<p>No products found for this seller.</p>';
        }

        modal.classList.remove('hidden');
        modal.setAttribute('aria-hidden', 'false');
        document.body.classList.add('seller-modal-open');
    };

    window.openRealSellerDocument = function (button) {
        if (button?.dataset?.url) {
            window.open(button.dataset.url, '_blank', 'noopener');
        }
    };

    window.__realSellerProducts = {};
    window.normalizeProductStatus = function (value) {
        return String(value || '').toLowerCase().replace(/[\s_-]+/g, '_');
    };

    window.openRealProductModal = function (productId) {
        const product = window.__realSellerProducts[String(productId)];
        const modal = document.getElementById('sellerProductDetailsModal');
        if (!product || !modal) {
            return;
        }

        window.__activeRealProduct = product;

        const normalizeDisplayValue = function (value) {
            if (value === undefined || value === null) {
                return '-';
            }

            const text = String(value).trim();
            if (text === '' || text.toLowerCase() === 'null' || text.toLowerCase() === 'undefined') {
                return '-';
            }

            return text;
        };

        const setText = function (id, value) {
            const element = document.getElementById(id);
            if (element) {
                element.textContent = normalizeDisplayValue(value);
            }
        };

        const status = window.normalizeProductStatus(product.status || 'pending');
        const isRemoved = Boolean(product.is_archived);
        const statusLabel = isRemoved ? 'Removed' : status === 'active' ? 'Approved' : 'Under Review';
        const price = Number(product.price || 0).toLocaleString('en-PH', {
            style: 'currency',
            currency: 'PHP'
        });

        setText('sellerProductModalTitle', product.name);
        setText('sellerProductModalPrice', price);
        setText('sellerProductModalSold', `${Number(product.sold_count || 0)} sold`);
        setText('sellerProductModalCategory', product.category);
        setText('sellerProductModalStock', `${Number(product.stock_quantity || 0)} pieces`);
        setText('sellerProductModalUploaded', product.created_at ? new Date(product.created_at).toLocaleDateString('en-US', { month: 'long', day: 'numeric', year: 'numeric' }) : '-');
        setText('sellerProductModalDescription', product.description);

        const specifications = product.specifications || {};
        const specificationValue = function (keys) {
            const key = keys.find(function (name) {
                const value = specifications[name];
                if (value === undefined || value === null) {
                    return false;
                }

                const text = String(value).trim();
                return text !== '' && text.toLowerCase() !== 'null' && text.toLowerCase() !== 'undefined';
            });
            return key ? normalizeDisplayValue(specifications[key]) : '-';
        };
        const optionNames = function (items) {
            return Array.isArray(items)
                ? items.map(function (item) { return item.name || ''; }).filter(Boolean).join(', ')
                : '';
        };
        const formatLabel = function (key) {
            return String(key || '')
                .replace(/^category_specifications\[|\]$/g, '')
                .replace(/_/g, ' ')
                .replace(/\b\w/g, function (letter) { return letter.toUpperCase(); });
        };
        const renderOptionBlock = function (sectionId, title, values) {
            const section = document.getElementById(sectionId);
            const container = document.getElementById(sectionId.replace('Section', 'List'));
            if (!section || !container) {
                return;
            }
            if (!values || !values.length) {
                section.classList.add('hidden');
                return;
            }
            container.innerHTML = values.map(function (value) {
                return `<div class="seller-product-option-row">${value}</div>`;
            }).join('');
            section.classList.remove('hidden');
        };
        const renderSpecificationBlock = function () {
            const section = document.getElementById('sellerProductSpecificationsSection');
            const container = document.getElementById('sellerProductSpecificationsList');
            if (!section || !container) {
                return;
            }
            const entries = Object.entries(specifications || {}).filter(function ([key, value]) {
                if (!key || value === undefined || value === null) {
                    return false;
                }

                const text = String(value).trim();
                return text !== '' && text.toLowerCase() !== 'null' && text.toLowerCase() !== 'undefined' && !['brand', 'material', 'sizes', 'size', 'colors', 'color', 'quantity_per_pack', 'weight', 'item_weight', 'country_of_origin', 'origin', 'country', 'subcategory'].includes(String(key));
            });
            if (!entries.length) {
                section.classList.add('hidden');
                return;
            }
            container.innerHTML = entries.map(function ([key, value]) {
                return `<div class="seller-product-spec-row"><span>${formatLabel(key)}</span><strong>${normalizeDisplayValue(value)}</strong></div>`;
            }).join('');
            section.classList.remove('hidden');
        };

        setText('sellerProductDetailBrand', specificationValue(['brand']));
        setText('sellerProductDetailMaterial', specificationValue(['material']));
        setText('sellerProductDetailSizes', optionNames(product.sizes) || specificationValue(['sizes', 'size']));
        setText('sellerProductDetailColors', optionNames(product.colors) || specificationValue(['colors', 'color']));
        setText('sellerProductDetailQuantity', specificationValue(['quantity_per_pack', 'weight', 'item_weight']));
        setText('sellerProductDetailOrigin', specificationValue(['country_of_origin', 'origin', 'country']));

        const buyerOptions = [];
        const variationNames = Array.isArray(product.variations) ? product.variations : [];
        const colorNames = Array.isArray(product.colors) ? product.colors : [];
        const sizeNames = Array.isArray(product.sizes) ? product.sizes : [];
        if (variationNames.length) {
            buyerOptions.push(...variationNames.map(function (item) {
                const priceText = Number(item.price || 0) > 0 ? ` â€” â‚±${Number(item.price || 0).toLocaleString('en-PH', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}` : '';
                return `${item.name || 'Variation'}${priceText} â€” Stock: ${Number(item.stock || 0)}`;
            }));
        }
        if (colorNames.length) {
            buyerOptions.push(...colorNames.map(function (item) {
                const priceText = Number(item.price || 0) > 0 ? ` â€” â‚±${Number(item.price || 0).toLocaleString('en-PH', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}` : '';
                return `${item.name || 'Color'}${priceText} â€” Stock: ${Number(item.stock || 0)}`;
            }));
        }
        if (sizeNames.length) {
            buyerOptions.push(...sizeNames.map(function (item) {
                const priceText = Number(item.price || 0) > 0 ? ` â€” â‚±${Number(item.price || 0).toLocaleString('en-PH', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}` : '';
                return `${item.name || 'Size'}${priceText} â€” Stock: ${Number(item.stock || 0)}`;
            }));
        }
        renderOptionBlock('sellerProductBuyerOptionsSection', 'Buyer Options', buyerOptions);
        renderSpecificationBlock();

        const image = product.image_url
            ? `<img src="${product.image_url}" alt="${String(product.name || 'Product').replace(/[&<>"']/g, '')}">`
            : '<div class="seller-product-image-placeholder">No image</div>';
        const mainImage = document.getElementById('sellerProductMainImage');
        if (mainImage) {
            mainImage.innerHTML = image;
        }

        const notice = document.getElementById('sellerProductComplianceNotice');
        if (notice) {
            notice.className = `seller-product-compliance-notice ${isRemoved ? 'removed' : status === 'active' ? 'approved' : 'under-review'}`;
            notice.innerHTML = `<strong>${statusLabel}</strong><span>${isRemoved ? 'This product is archived and removed from the seller listing.' : status === 'active' ? 'This product is approved and available.' : 'This product is awaiting compliance review.'}</span>`;
        }

        modal.classList.remove('hidden');
        modal.setAttribute('aria-hidden', 'false');
        document.body.classList.add('seller-modal-open');
    };

    window.closeRealProductModal = function () {
        const modal = document.getElementById('sellerProductDetailsModal');
        modal?.classList.add('hidden');
        modal?.setAttribute('aria-hidden', 'true');
        document.body.classList.remove('seller-modal-open');
    };

    window.backToRealSellerProducts = function () {
        window.closeRealProductModal();
        window.showRealSellerProducts();
    };

    window.openRealProductDecisionModal = function (modalId) {
        const productModal = document.getElementById('sellerProductDetailsModal');
        const decisionModal = document.getElementById(modalId);
        if (!decisionModal) {
            return;
        }
        productModal?.classList.add('hidden');
        decisionModal.classList.remove('hidden');
        decisionModal.setAttribute('aria-hidden', 'false');
        document.body.classList.remove('seller-modal-open');
        document.body.classList.add(modalId === 'sellerRemoveProductModal' ? 'seller-remove-modal-open' : 'seller-warning-modal-open');

        if (modalId === 'sellerRemoveProductModal') {
            const removeReasonInputs = decisionModal.querySelectorAll('input[name="remove_reason"]');
            removeReasonInputs.forEach(function (input) { input.checked = false; });
            const details = document.getElementById('sellerRemoveProductDetails');
            if (details) { details.value = ''; }
            const count = document.getElementById('sellerRemoveProductDetailsCount');
            if (count) { count.textContent = '0/300'; }
            const error = decisionModal.querySelector('.seller-remove-reasons');
            error?.classList.remove('has-error');
            const cancel = document.getElementById('sellerRemoveProductCancel');
            cancel?.focus();
        }

        if (modalId === 'sellerIssueWarningModal') {
            const warningReasonInputs = decisionModal.querySelectorAll('input[name="warning_reason"]');
            warningReasonInputs.forEach(function (input) { input.checked = false; });
            const details = document.getElementById('sellerWarningDetails');
            if (details) { details.value = ''; }
            const count = document.getElementById('sellerWarningDetailsCount');
            if (count) { count.textContent = '0/300'; }
            const error = decisionModal.querySelector('.seller-warning-reasons');
            error?.classList.remove('has-error');
            const cancel = document.getElementById('sellerWarningCancel');
            cancel?.focus();
        }
    };

    window.closeRealProductDecisionModal = function (modalId) {
        const decisionModal = document.getElementById(modalId);
        decisionModal?.classList.add('hidden');
        decisionModal?.setAttribute('aria-hidden', 'true');
        document.body.classList.remove('seller-remove-modal-open', 'seller-warning-modal-open');

        const productModal = document.getElementById('sellerProductDetailsModal');
        if (productModal) {
            productModal.classList.remove('hidden');
            productModal.setAttribute('aria-hidden', 'false');
            document.body.classList.add('seller-modal-open');
        }
    };

    window.nextRealProductImage = function () {
        const product = window.__activeRealProduct;
        const images = Array.isArray(product?.photos) ? product.photos : [];
        if (images.length < 2) {
            return;
        }

        const current = Number(product.__imageIndex || 0);
        const next = (current + 1) % images.length;
        product.__imageIndex = next;
        const image = document.getElementById('sellerProductMainImage');
        if (image) {
            image.innerHTML = `<img src="${images[next]}" alt="${String(product.name || 'Product').replace(/[&<>"']/g, '')}">`;
        }
    };

    window.approveRealProduct = async function () {
        const product = window.__activeRealProduct;
        if (!product?.id) {
            return;
        }

        const normalizedStatus = window.normalizeProductStatus(product.status);
        const isArchived = Boolean(product.is_archived);
        const canApprove = ['pending', 'under_review', 'review'].includes(normalizedStatus);

        if (isArchived) {
            window.alert('This product is archived and cannot be approved.');
            return;
        }

        if (!canApprove) {
            window.alert(normalizedStatus === 'active' ? 'This product is already approved.' : 'This product is not eligible for approval yet.');
            return;
        }

        const response = await fetch(`/api/v1/admin/seller-compliance/products/${product.id}/approve`, {
            method: 'POST',
            headers: {
                Accept: 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
            }
        });

        if (!response.ok) {
            const message = response.status === 422
                ? 'This product is no longer eligible for approval.'
                : 'Unable to approve this product.';
            window.alert(message);
            return;
        }

        product.status = 'active';
        product.is_archived = false;
        window.openRealProductModal(product.id);
    };

    window.showRealSellerProducts = function () {
        document.getElementById('sellerOverviewPanel')?.classList.add('hidden');
        document.getElementById('sellerProductsPanel')?.classList.remove('hidden');
        document.querySelectorAll('.seller-details-tab').forEach(function (tab) {
            const active = tab.dataset.tab === 'products';
            tab.classList.toggle('active', active);
            tab.setAttribute('aria-selected', active ? 'true' : 'false');
        });
    };

    window.showRealSellerOverview = function () {
        document.getElementById('sellerOverviewPanel')?.classList.remove('hidden');
        document.getElementById('sellerProductsPanel')?.classList.add('hidden');
        document.querySelectorAll('.seller-details-tab').forEach(function (tab) {
            const active = tab.dataset.tab === 'overview';
            tab.classList.toggle('active', active);
            tab.setAttribute('aria-selected', active ? 'true' : 'false');
        });
    };

    window.closeRealSellerModal = function () {
        const modal = document.getElementById('sellerDetailsModal');
        modal?.classList.add('hidden');
        modal?.setAttribute('aria-hidden', 'true');
        document.body.classList.remove('seller-modal-open');
    };

    window.suspendRealSellerModal = function () {
        const row = window.__activeSellerComplianceRow;
        if (!row) {
            return;
        }

        const storeName = row.dataset.name || 'this seller';
        if (!window.confirm(`Suspend ${storeName}?`)) {
            return;
        }

        row.dataset.status = 'suspended';
        const statusPill = row.querySelector('.status-pill');
        if (statusPill) {
            statusPill.className = 'status-pill status-suspended';
            statusPill.textContent = 'Suspended';
        }

        window.closeRealSellerModal();
    };
