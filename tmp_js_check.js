
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
                const hasWarning = Boolean(product.warning_reason || product.warningReason || status === 'warning');
                const hasRemove = Boolean(product.is_archived || product.remove_reason || product.removeReason || status === 'archived' || status === 'removed' || status === 'rejected');
                const isApproved = (status === 'active' || status === 'approved') && !isArchived && !hasWarning && !hasRemove;
                const statusClass = hasRemove
                    ? 'status-removed'
                    : hasWarning
                        ? 'status-warning'
                        : isApproved
                            ? 'status-approved'
                            : 'status-under-review';
                const statusLabel = hasRemove ? 'Removed' : hasWarning ? 'Warning Issued' : isApproved ? 'Approved' : 'Under Review';
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
        const normalized = String(value || '').trim().toLowerCase().replace(/[\s_-]+/g, '_');
        if (['warning', 'yellow', 'orange'].includes(normalized)) return 'warning';
        if (['removed', 'rejected', 'archived', 'red', 'gray'].includes(normalized)) return 'removed';
        if (['approved', 'green', 'active'].includes(normalized)) return 'approved';
        if (['under_review', 'underreview', 'review'].includes(normalized)) return 'under_review';
        return normalized || 'pending';
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
        const isRemoved = Boolean(product.is_archived || product.remove_reason || product.removeReason || ['removed', 'rejected', 'archived', 'red', 'gray'].includes(status));
        const hasWarning = Boolean(product.warning_reason || product.warningReason || ['warning', 'yellow', 'orange'].includes(status));
        const statusLabel = isRemoved ? 'Removed' : hasWarning || status === 'warning' ? 'Warning Issued' : status === 'active' || status === 'approved' ? 'Approved' : 'Under Review';
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

        const normalizeDetailList = function (items) {
            if (Array.isArray(items)) {
                return items.filter(function (item) {
                    return item !== null && item !== undefined;
                }).map(function (item) {
                    if (typeof item === 'string') {
                        return { name: item };
                    }
                    if (typeof item === 'object') {
                        return {
                            name: item.name || item.label || item.value || 'Option',
                            price: item.price ?? item.amount ?? 0,
                            stock: item.stock ?? item.quantity ?? 0,
                        };
                    }
                    return { name: String(item) };
                });
            }

            if (items && typeof items === 'object') {
                return Object.entries(items).map(function ([key, value]) {
                    if (value && typeof value === 'object') {
                        return {
                            name: value.name || value.label || key,
                            price: value.price ?? 0,
                            stock: value.stock ?? value.quantity ?? 0,
                        };
                    }

                    return { name: String(value || key) };
                });
            }

            return [];
        };

        const normalizeSpecificationMap = function (source) {
            if (!source) {
                return {};
            }

            if (Array.isArray(source)) {
                return source.reduce(function (accumulator, item) {
                    if (!item || typeof item !== 'object') {
                        return accumulator;
                    }

                    const key = item.key || item.name || 'detail';
                    const value = item.value ?? item.name ?? item.label ?? '';
                    if (String(value).trim() !== '') {
                        accumulator[key] = value;
                    }
                    return accumulator;
                }, {});
            }

            if (typeof source === 'object') {
                return Object.entries(source).reduce(function (accumulator, [key, value]) {
                    if (value !== null && value !== undefined && String(value).trim() !== '') {
                        accumulator[key] = value;
                    }
                    return accumulator;
                }, {});
            }

            return {};
        };

        const specifications = normalizeSpecificationMap(product.specifications);
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
            return normalizeDetailList(items).map(function (item) { return item.name || ''; }).filter(Boolean).join(', ');
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
                container.innerHTML = '<div class="seller-product-option-row">No buyer options available</div>';
                section.classList.remove('hidden');
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
                container.innerHTML = '<div class="seller-product-spec-row"><span>Details</span><strong>Not available</strong></div>';
                section.classList.remove('hidden');
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
        const variationNames = normalizeDetailList(product.variations);
        const colorNames = normalizeDetailList(product.colors);
        const sizeNames = normalizeDetailList(product.sizes);
        if (variationNames.length) {
            buyerOptions.push(...variationNames.map(function (item) {
                const priceText = Number(item.price || 0) > 0 ? ` — ₱${Number(item.price || 0).toLocaleString('en-PH', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}` : '';
                return `${item.name || 'Variation'}${priceText} — Stock: ${Number(item.stock || 0)}`;
            }));
        }
        if (colorNames.length) {
            buyerOptions.push(...colorNames.map(function (item) {
                const priceText = Number(item.price || 0) > 0 ? ` — ₱${Number(item.price || 0).toLocaleString('en-PH', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}` : '';
                return `${item.name || 'Color'}${priceText} — Stock: ${Number(item.stock || 0)}`;
            }));
        }
        if (sizeNames.length) {
            buyerOptions.push(...sizeNames.map(function (item) {
                const priceText = Number(item.price || 0) > 0 ? ` — ₱${Number(item.price || 0).toLocaleString('en-PH', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}` : '';
                return `${item.name || 'Size'}${priceText} — Stock: ${Number(item.stock || 0)}`;
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
            const warningReason = product.warning_reason || product.warningReason || '';
            const warningDetails = product.warning_details || product.warningDetails || '';
            const removeReason = product.remove_reason || product.removeReason || '';
            const removeDetails = product.remove_details || product.removeDetails || '';
            const hasWarning = Boolean(warningReason || status === 'warning' || ['warning', 'yellow', 'orange'].includes(status));
            const hasRemove = Boolean(isRemoved || removeReason || status === 'removed' || status === 'archived' || ['removed', 'rejected', 'archived', 'red', 'gray'].includes(status));
            const noticeTitle = hasRemove ? 'Removed' : hasWarning ? 'Issue Warning' : status === 'active' || status === 'approved' ? 'Approved' : 'Under Review';
            const noticeText = hasRemove
                ? (removeReason || 'This product is archived and removed from the seller listing.') + (removeDetails ? ' — ' + removeDetails : '')
                : hasWarning
                    ? (warningReason || 'A compliance issue was identified for this product.') + (warningDetails ? ' — ' + warningDetails : '')
                    : status === 'active' || status === 'approved'
                        ? 'This product is approved and available.'
                        : 'This product is awaiting compliance review.';

            notice.className = `seller-product-compliance-notice ${hasRemove ? 'removed' : hasWarning ? 'warning' : status === 'active' || status === 'approved' ? 'approved' : 'under-review'}`;
            notice.innerHTML = `<strong>${noticeTitle}</strong><span>${noticeText}</span>`;
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
            credentials: 'same-origin',
            headers: {
                Accept: 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
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
        product.warning_reason = '';
        product.warning_details = '';
        product.remove_reason = '';
        product.remove_details = '';

        window.openRealProductModal(product.id);
        if (typeof window.loadSellerComplianceData === 'function') {
            window.loadSellerComplianceData();
        }
        if (typeof window.showProductDecisionFlash === 'function') {
            window.showProductDecisionFlash('approved', product.name);
        }
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

        document.addEventListener('DOMContentLoaded', function () {
            const removeCancelButton = document.getElementById('sellerRemoveProductCancel');
            const removeSubmitButton = document.getElementById('sellerRemoveProductSubmit');
            const warningCancelButton = document.getElementById('sellerWarningCancel');
            const warningSubmitButton = document.getElementById('sellerWarningSubmit');

            removeCancelButton?.addEventListener('click', function () {
                window.closeRealProductDecisionModal('sellerRemoveProductModal');
                const productId = window.__activeRealProduct?.id;
                if (productId) {
                    window.openRealProductModal(productId);
                }
            });

            warningCancelButton?.addEventListener('click', function () {
                window.closeRealProductDecisionModal('sellerIssueWarningModal');
                const productId = window.__activeRealProduct?.id;
                if (productId) {
                    window.openRealProductModal(productId);
                }
            });

            removeSubmitButton?.addEventListener('click', function () {
                const product = window.__activeRealProduct;
                const reasonInput = document.querySelector('input[name="remove_reason"]:checked');
                const removeModal = document.getElementById('sellerRemoveProductModal');
                if (!product || !reasonInput || !removeModal) {
                    return;
                }

                const reason = reasonInput.value || 'Violation of platform policies';
                const details = document.getElementById('sellerRemoveProductDetails')?.value?.trim() || '';
                product.is_archived = true;
                product.status = 'archived';
                product.warning_reason = '';
                product.warning_details = '';
                product.remove_reason = reason;
                product.remove_details = details;
                removeModal.querySelector('.seller-remove-reasons')?.classList.remove('has-error');

                window.closeRealProductDecisionModal('sellerRemoveProductModal');
                window.openRealProductModal(product.id);
                if (typeof window.loadSellerComplianceData === 'function') {
                    window.loadSellerComplianceData();
                }
                if (typeof window.showProductDecisionFlash === 'function') {
                    window.showProductDecisionFlash('removed', product.name);
                }
            });

            warningSubmitButton?.addEventListener('click', function () {
                const product = window.__activeRealProduct;
                const reasonInput = document.querySelector('input[name="warning_reason"]:checked');
                const warningModal = document.getElementById('sellerIssueWarningModal');
                if (!product || !reasonInput || !warningModal) {
                    return;
                }

                const reason = reasonInput.value || 'Product review issue';
                const details = document.getElementById('sellerWarningDetails')?.value?.trim() || '';
                product.status = 'warning';
                product.warning_reason = reason;
                product.warning_details = details;
                product.remove_reason = '';
                product.remove_details = '';
                warningModal.querySelector('.seller-warning-reasons')?.classList.remove('has-error');

                window.closeRealProductDecisionModal('sellerIssueWarningModal');
                window.openRealProductModal(product.id);
                if (typeof window.loadSellerComplianceData === 'function') {
                    window.loadSellerComplianceData();
                }
                if (typeof window.showProductDecisionFlash === 'function') {
                    window.showProductDecisionFlash('warning', product.name);
                }
            });
        });
    

        function initializeSellerCompliance() {

                const sidebar =
                    document.getElementById('admin-sidebar') ||
                    document.getElementById('adminSidebar') ||
                    document.getElementById('sellerSidebar') ||
                    document.querySelector('[data-admin-sidebar]') ||
                    document.querySelector('.admin-sidebar');

                const page =
                    document.getElementById('admin-content');

                /* =================================================
                   SIDEBAR MOVEMENT ONLY
                   -------------------------------------------------
                   Keep ALL existing Seller Compliance spacing as-is.
                   This only synchronizes the page movement with the
                   shared sidebar's w-20 collapsed state.
                ================================================== */
                if (sidebar && page) {

                    const originalPageMarginLeft =
                        parseFloat(
                            window.getComputedStyle(page).marginLeft
                        ) || 0;

                    const originalSidebarWidth =
                        sidebar.getBoundingClientRect().width || 256;

                    const originalSidebarGap =
                        originalPageMarginLeft - originalSidebarWidth;

                    const collapsedSidebarWidth = 80;

                    page.style.transitionProperty =
                        'margin-left';

                    page.style.transitionDuration =
                        '300ms';

                    page.style.transitionTimingFunction =
                        'ease';

                    function syncSellerComplianceWithSidebar() {

                        /*
                         * Leave the page's existing responsive spacing
                         * completely untouched on smaller screens.
                         */
                        if (window.innerWidth <= 900) {
                            page.style.removeProperty('margin-left');
                            return;
                        }

                        const isCollapsed =
                            sidebar.classList.contains('w-20');

                        if (isCollapsed) {
                            page.style.marginLeft =
                                `${collapsedSidebarWidth + originalSidebarGap}px`;
                        } else {
                            /*
                             * Restore the exact margin the page already had.
                             * No desktop spacing is changed.
                             */
                            page.style.marginLeft =
                                `${originalPageMarginLeft}px`;
                        }
                    }

                    const sidebarMovementObserver =
                        new MutationObserver(function (mutations) {

                            const classChanged =
                                mutations.some(function (mutation) {
                                    return (
                                        mutation.type === 'attributes' &&
                                        mutation.attributeName === 'class'
                                    );
                                });

                            if (!classChanged) {
                                return;
                            }

                            /*
                             * Run after the sidebar toggle has updated its
                             * width class, so both animations begin together.
                             */
                            requestAnimationFrame(
                                syncSellerComplianceWithSidebar
                            );
                        });

                    sidebarMovementObserver.observe(
                        sidebar,
                        {
                            attributes: true,
                            attributeFilter: ['class']
                        }
                    );

                    window.addEventListener(
                        'resize',
                        syncSellerComplianceWithSidebar
                    );
                }

                const sellerComplianceApiUrl = '/api/v1/admin/seller-compliance';
                const initialSellerComplianceData = @json($initialComplianceData ?? null);
                const initialSellerRowsData = Array.isArray(initialSellerComplianceData?.data)
                    ? initialSellerComplianceData.data
                    : [];
                let rows = Array.from(
                    document.querySelectorAll('.seller-compliance-row')
                );

                const searchInput =
                    document.getElementById('sellerSearch');

                const categoryFilter =
                    document.getElementById('sellerTypeFilter');

                const complianceFilter =
                    document.getElementById('complianceFilter');

                const refreshButton =
                    document.getElementById('refreshCompliance');

                const refreshIcon =
                    document.getElementById('complianceRefreshIcon');

                const showingCount =
                    document.getElementById('sellerShowingCount');

                const showingText =
                    showingCount?.parentElement;

                const noResults =
                    document.getElementById('sellerComplianceNoResults');

                const previousPage =
                    document.getElementById('sellerPreviousPage');

                const nextPage =
                    document.getElementById('sellerNextPage');

                const pageButtons =
                    document.querySelectorAll(
                        '.seller-compliance-page .pagination-button[data-page]'
                    );

                const itemsPerPage =
                    document.getElementById('sellerItemsPerPage');

                let currentPage = 1;
                let itemsLimit = Number(itemsPerPage?.value) || 7;
                let activeSellerApiData = null;



/* =================================================
                   FILTER HELPERS
                ================================================== */

                function normalize(value) {

                    return String(value ?? '')
                        .toLowerCase()
                        .replace(/[’‘]/g, "'")
                        .replace(/&/g, 'and')
                        .replace(/[^a-z0-9]+/g, ' ')
                        .trim()
                        .replace(/\s+/g, ' ');

                }

                function titleCase(value) {
                    return String(value ?? '')
                        .split(/[-_\s]+/)
                        .filter(Boolean)
                        .map(function (part) {
                            return part.charAt(0).toUpperCase() + part.slice(1);
                        })
                        .join(' ');
                }

                function categoryChipMarkup(category) {
                    const value = String(category || '').trim();
                    if (!value) {
                        return '<span class="category-pill category-default">—</span>';
                    }

                    const normalized = value.toLowerCase();
                    return `<span class="category-pill category-${normalized}">${titleCase(value)}</span>`;
                }

                function updateSummaryCards(summary = {}) {
                    const summaryMap = {
                        compliant: document.getElementById('sellerComplianceCompliantCount'),
                        warnings: document.getElementById('sellerComplianceWarningsCount'),
                        under_review: document.getElementById('sellerComplianceUnderReviewCount'),
                        suspended: document.getElementById('sellerComplianceSuspendedCount'),
                        total_sellers: document.getElementById('sellerComplianceTotalCount'),
                    };

                    Object.entries(summaryMap).forEach(([key, element]) => {
                        if (!element) {
                            return;
                        }

                        const value = summary[key] ?? 0;
                        element.textContent = Number(value || 0);
                    });
                }

                function hydrateSellerRow(row, item) {
                    if (!row || !item) {
                        return;
                    }

                    const categories = Array.isArray(item.categories) ? item.categories : [];
                    const compliance = String(item.compliance || 'compliant').toLowerCase();
                    const statusLabel = String(item.compliance_label || 'Compliant');
                    const score = Number(item.compliance_score ?? 0);
                    const totalProducts = Number(item.products_count ?? 0);
                    const underReviewProducts = Number(item.products_under_review ?? 0);

                    row.dataset.id = item.id || '';
                    row.dataset.name = item.store_name || 'Seller';
                    row.dataset.category = categories.join(' ');
                    row.dataset.compliance = compliance;
                    row.dataset.status = String(item.status || 'active');
                    row.dataset.search = [
                        item.store_name,
                        item.owner_name,
                        item.email,
                        item.phone,
                        categories.join(' '),
                        item.compliance_label,
                    ].filter(Boolean).join(' ');

                    const storeName = row.querySelector('.seller-store-name');
                    const ownerName = row.querySelector('.seller-owner-name');
                    const categoryGroup = row.querySelector('.category-pill-group');
                    const productInfo = row.querySelector('.seller-products');
                    const scoreNumber = row.querySelector('.score-number');
                    const scoreFill = row.querySelector('.score-fill');
                    const statusPill = row.querySelector('.status-pill');

                    if (storeName) {
                        storeName.textContent = item.store_name || 'Seller';
                    }

                    if (ownerName) {
                        ownerName.textContent = item.owner_name || '—';
                    }

                    if (categoryGroup) {
                        categoryGroup.innerHTML = categories.length
                            ? categories.map(categoryChipMarkup).join('')
                            : '<span class="category-pill category-default">—</span>';
                    }

                    if (productInfo) {
                        productInfo.innerHTML = `
                            <span class="seller-products-total">${totalProducts} Product${totalProducts === 1 ? '' : 's'}</span>
                            <span class="seller-products-review" title="Products currently under admin review">${underReviewProducts} Under Review</span>
                        `;
                    }

                    if (scoreNumber) {
                        scoreNumber.textContent = `${Math.max(0, Math.min(100, score))}%`;
                    }

                    if (scoreFill) {
                        scoreFill.style.width = `${Math.max(0, Math.min(100, score))}%`;
                        scoreFill.classList.remove('score-green', 'score-orange', 'score-red');

                        if (compliance === 'suspended') {
                            scoreFill.classList.add('score-red');
                        } else if (compliance === 'warning' || compliance === 'under-review') {
                            scoreFill.classList.add('score-orange');
                        } else {
                            scoreFill.classList.add('score-green');
                        }
                    }

                    if (statusPill) {
                        statusPill.textContent = statusLabel;
                        statusPill.className = 'status-pill ' + ({
                            compliant: 'status-compliant',
                            warning: 'status-warning',
                            'under-review': 'status-under-review',
                            suspended: 'status-suspended',
                        })[compliance] || 'status-compliant';
                    }
                }

                async function loadSellerComplianceData() {
                    const params = new URLSearchParams();

                    if (searchInput && searchInput.value.trim()) {
                        params.set('search', searchInput.value.trim());
                    }

                    if (categoryFilter && categoryFilter.value && categoryFilter.value !== 'all') {
                        params.set('category', categoryFilter.value);
                    }

                    if (complianceFilter && complianceFilter.value && complianceFilter.value !== 'all') {
                        params.set('compliance', complianceFilter.value);
                    }

                    if (itemsPerPage && Number(itemsPerPage.value)) {
                        params.set('per_page', String(Number(itemsPerPage.value)));
                    }

                    try {
                        const response = await fetch(`${sellerComplianceApiUrl}?${params.toString()}`, {
                            credentials: 'same-origin',
                            headers: {
                                Accept: 'application/json',
                                'X-Requested-With': 'XMLHttpRequest',
                            },
                        });

                        if (!response.ok) {
                            throw new Error('Unable to load seller compliance data.');
                        }

                        const payload = await response.json();
                        const items = Array.isArray(payload.data) ? payload.data : [];

                        updateSummaryCards(payload.summary || {});

                        const rowNodes = Array.from(document.querySelectorAll('.seller-compliance-row'));
                        rowNodes.forEach((row, index) => {
                            const item = items[index] || null;
                            row.classList.toggle('hidden', !item);
                            if (item) {
                                hydrateSellerRow(row, item);
                            }
                        });

                        rows = rowNodes;
                        renderRows();
                    } catch (error) {
                        console.error(error);

                        if (initialSellerComplianceData && !params.toString()) {
                            updateSummaryCards(initialSellerComplianceData.summary || {});
                            const initialItems = Array.isArray(initialSellerComplianceData.data)
                                ? initialSellerComplianceData.data
                                : [];
                            const rowNodes = Array.from(document.querySelectorAll('.seller-compliance-row'));
                            rowNodes.forEach((row, index) => {
                                const item = initialItems[index] || null;
                                row.classList.toggle('hidden', !item);
                                if (item) {
                                    hydrateSellerRow(row, item);
                                }
                            });
                            rows = rowNodes;
                            renderRows();
                        }
                    }
                }


                function categoryMatches(row, selectedCategory) {

                    if (selectedCategory === 'all') {
                        return true;
                    }

                    const categories = String(
                        row.dataset.category || ''
                    )
                        .toLowerCase()
                        .split(/\s+/)
                        .filter(Boolean);

                    return categories.includes(
                        String(selectedCategory).toLowerCase()
                    );

                }


                function getFilteredRows() {

                    const query = normalize(
                        searchInput?.value || ''
                    );

                    const selectedCategory =
                        categoryFilter?.value || 'all';

                    const selectedCompliance =
                        complianceFilter?.value || 'all';

                    return rows.filter(function (row) {

                        /* Search across seller name, owner, email, phone,
                           shop/category text, and the visible row content. */
                        const searchableText = normalize(
                            [
                                row.dataset.name || '',
                                row.dataset.search || '',
                                row.dataset.category || '',
                                row.textContent || ''
                            ].join(' ')
                        );

                        const matchesSearch =
                            query === '' ||
                            searchableText.includes(query);

                        const matchesCategory =
                            categoryMatches(
                                row,
                                selectedCategory
                            );

                        const rowCompliance = String(
                            row.dataset.compliance || ''
                        )
                            .toLowerCase()
                            .trim();

                        const matchesCompliance =
                            selectedCompliance === 'all' ||
                            rowCompliance === selectedCompliance;

                        return (
                            matchesSearch &&
                            matchesCategory &&
                            matchesCompliance
                        );

                    });

                }


                /* =================================================
                   RENDER FILTERED ROWS + PAGINATION
                ================================================== */

                function renderRows() {

                    const filteredRows = getFilteredRows();
                    const total = filteredRows.length;

                    const totalPages = Math.max(
                        1,
                        Math.ceil(total / itemsLimit)
                    );

                    if (currentPage > totalPages) {
                        currentPage = totalPages;
                    }

                    const startIndex =
                        (currentPage - 1) * itemsLimit;

                    const endIndex =
                        startIndex + itemsLimit;

                    rows.forEach(function (row) {
                        row.classList.add('hidden');
                    });

                    filteredRows
                        .slice(startIndex, endIndex)
                        .forEach(function (row) {
                            bindSellerComplianceRow(row);
                            row.classList.remove('hidden');
                        });

                    const visibleCount = Math.min(
                        itemsLimit,
                        Math.max(0, total - startIndex)
                    );

                    if (showingCount) {
                        const countStart = total === 0 ? 0 : startIndex + 1;
                        const countEnd = total === 0 ? 0 : startIndex + visibleCount;
                        showingCount.textContent = `${countStart}-${countEnd}`;
                    }

                    if (showingText) {
                        const suffix = total === 1 ? 'entry' : 'entries';
                        showingText.lastChild.textContent = ` out of ${total} ${suffix}`;
                    }

                    if (noResults) {
                        noResults.classList.toggle(
                            'hidden',
                            total !== 0
                        );
                    }

                    pageButtons.forEach(function (button) {

                        const buttonPage = Number(
                            button.dataset.page
                        );

                        button.classList.toggle(
                            'current',
                            buttonPage === currentPage
                        );

                        button.classList.toggle(
                            'hidden',
                            buttonPage > totalPages
                        );

                    });

                    previousPage?.classList.toggle(
                        'disabled',
                        currentPage === 1
                    );

                    nextPage?.classList.toggle(
                        'disabled',
                        currentPage >= totalPages
                    );

                }


                /* =================================================
                   SEARCH + FILTER EVENTS
                ================================================== */

                searchInput?.addEventListener(
                    'input',
                    async function () {
                        currentPage = 1;
                        await loadSellerComplianceData();
                    }
                );

                categoryFilter?.addEventListener(
                    'change',
                    async function () {
                        currentPage = 1;
                        await loadSellerComplianceData();
                    }
                );

                complianceFilter?.addEventListener(
                    'change',
                    async function () {
                        currentPage = 1;
                        await loadSellerComplianceData();
                    }
                );


                /* =================================================
                   PAGINATION
                ================================================== */

                pageButtons.forEach(function (button) {

                    button.addEventListener(
                        'click',
                        function () {

                            currentPage = Number(
                                button.dataset.page
                            );

                            renderRows();

                        }
                    );

                });

                previousPage?.addEventListener(
                    'click',
                    function () {

                        if (currentPage <= 1) {
                            return;
                        }

                        currentPage--;
                        renderRows();

                    }
                );

                nextPage?.addEventListener(
                    'click',
                    function () {

                        const totalPages = Math.max(
                            1,
                            Math.ceil(
                                getFilteredRows().length /
                                itemsLimit
                            )
                        );

                        if (currentPage >= totalPages) {
                            return;
                        }

                        currentPage++;
                        renderRows();

                    }
                );

                itemsPerPage?.addEventListener(
                    'change',
                    async function () {

                        itemsLimit =
                            Number(itemsPerPage.value) || 7;

                        currentPage = 1;
                        await loadSellerComplianceData();

                    }
                );

                loadSellerComplianceData();


                /* =================================================
                   REFRESH
                ================================================== */

                refreshButton?.addEventListener(
                    'click',
                    async function () {

                        refreshIcon?.classList.add(
                            'compliance-spin'
                        );

                        if (searchInput) {
                            searchInput.value = '';
                        }

                        if (categoryFilter) {
                            categoryFilter.value = 'all';
                        }

                        if (complianceFilter) {
                            complianceFilter.value = 'all';
                        }

                        currentPage = 1;

                        try {
                            await loadSellerComplianceData();
                        } finally {
                            setTimeout(function () {
                                refreshIcon?.classList.remove(
                                    'compliance-spin'
                                );
                            }, 350);
                        }

                    }
                );



                /* =================================================
                   SELLER DETAILS MODAL UX
                ================================================== */

                const sellerDetailsModal =
                    document.getElementById('sellerDetailsModal');

                const sellerDetailsDialog =
                    sellerDetailsModal?.querySelector('.seller-details-dialog');

                const sellerDetailsClose =
                    document.getElementById('sellerDetailsClose');

                const sellerDetailsCancel =
                    document.getElementById('sellerModalCancel');

                const sellerDetailsSuspend =
                    document.getElementById('sellerModalSuspend');

                const sellerModalStoreName =
                    document.getElementById('sellerModalStoreName');

                const sellerModalSince =
                    document.getElementById('sellerModalSince');

                const sellerModalOwner =
                    document.getElementById('sellerModalOwner');

                const sellerModalEmail =
                    document.getElementById('sellerModalEmail');

                const sellerModalPhone =
                    document.getElementById('sellerModalPhone');

                const sellerModalLocation =
                    document.getElementById('sellerModalLocation');

                const sellerModalCategories =
                    document.getElementById('sellerModalCategories');

                const sellerModalProducts =
                    document.getElementById('sellerModalProducts');

                const sellerModalScore =
                    document.getElementById('sellerModalScore');

                const sellerModalScoreFill =
                    document.getElementById('sellerModalScoreFill');

                const sellerModalCompliancePill =
                    document.getElementById('sellerModalCompliancePill');

                const sellerModalPolicyText =
                    document.getElementById('sellerModalPolicyText');

                const sellerModalIssues =
                    document.getElementById('sellerModalIssues');

                const sellerOverviewPanel =
                    document.getElementById('sellerOverviewPanel');

                const sellerProductsPanel =
                    document.getElementById('sellerProductsPanel');

                const sellerProductsGrid =
                    document.getElementById('sellerProductsGrid');

                const sellerProductDetailsModal =
                    document.getElementById('sellerProductDetailsModal');

                const sellerProductDetailsDialog =
                    sellerProductDetailsModal?.querySelector('.seller-product-details-dialog');

                const sellerProductBack =
                    document.getElementById('sellerProductBack');

                const sellerProductActions =
                    document.getElementById('sellerProductActions');

                const sellerProductClose =
                    document.getElementById('sellerProductClose');

                const sellerProductMainImage =
                    document.getElementById('sellerProductMainImage');

                const sellerProductThumbnails =
                    document.getElementById('sellerProductThumbnails');

                const sellerProductComplianceNotice =
                    document.getElementById('sellerProductComplianceNotice');

                const sellerProductThumbNext =
                    document.getElementById('sellerProductThumbNext');

                const sellerProductModalTitle =
                    document.getElementById('sellerProductModalTitle');

                const sellerProductModalPrice =
                    document.getElementById('sellerProductModalPrice');

                const sellerProductModalSold =
                    document.getElementById('sellerProductModalSold');

                const sellerProductModalCategory =
                    document.getElementById('sellerProductModalCategory');

                const sellerProductModalStock =
                    document.getElementById('sellerProductModalStock');

                const sellerProductModalUploaded =
                    document.getElementById('sellerProductModalUploaded');

                const sellerProductModalDescription =
                    document.getElementById('sellerProductModalDescription');

                const sellerProductDetailBrand =
                    document.getElementById('sellerProductDetailBrand');

                const sellerProductDetailMaterial =
                    document.getElementById('sellerProductDetailMaterial');

                const sellerProductDetailSizes =
                    document.getElementById('sellerProductDetailSizes');

                const sellerProductDetailColors =
                    document.getElementById('sellerProductDetailColors');

                const sellerProductDetailWeight =
                    document.getElementById('sellerProductDetailWeight');

                const sellerProductDetailOrigin =
                    document.getElementById('sellerProductDetailOrigin');

                let activeSellerRow = null;
                let activeProduct = null;
                let lastProductFocusedElement = null;
                let lastFocusedElement = null;

                const sellerProfiles = {
                    'DelaCruzShop': {
                        since: 'August 2024',
                        email: 'juandelacruz@gmail.com',
                        phone: '0917 123 4567',
                        location: 'Calamba, Laguna',
                        products: '145 Products',
                        issues: [
                            { status: 'Warning', type: 'warning', issue: 'Issue: Pangit ng design nyo hahahaha.', date: 'September 24, 2026' },
                            { status: 'Removed', type: 'removed', issue: 'Reason: Pangit ng design nyo hahahaha.', date: 'September 24, 2026' },
                            { status: 'Warning', type: 'warning', issue: 'Issue: Pangit ng design nyo hahahaha.', date: 'September 24, 2026' }
                        ]
                    },
                    'WellnessHub': {
                        since: 'July 2024', email: 'angela.reyes@gmail.com', phone: '0918 222 1001', location: 'Quezon City', products: '86 Products',
                        issues: [
                            { status: 'Warning', type: 'warning', issue: 'Missing compliance documentation.', date: 'September 20, 2026' },
                            { status: 'Warning', type: 'warning', issue: 'Product listing requires review.', date: 'September 18, 2026' }
                        ]
                    },
                    'TechCore PH': {
                        since: 'June 2024', email: 'marco.santos@gmail.com', phone: '0919 333 2002', location: 'Makati City', products: '142 Products',
                        issues: [
                            { status: 'Removed', type: 'removed', issue: 'Repeated policy violation.', date: 'September 22, 2026' },
                            { status: 'Warning', type: 'warning', issue: 'Restricted product documentation incomplete.', date: 'September 18, 2026' }
                        ]
                    },
                    'ActiveLife Hub': {
                        since: 'May 2024', email: 'carlo.reyes@gmail.com', phone: '0920 444 3003', location: 'Taguig City', products: '74 Products',
                        issues: [
                            { status: 'Warning', type: 'warning', issue: 'One product listing is under review.', date: 'September 19, 2026' }
                        ]
                    },
                    'Home Haven PH': {
                        since: 'April 2024', email: 'maria.santos@gmail.com', phone: '0921 555 4004', location: 'Pasig City', products: '118 Products',
                        issues: [
                            { status: 'Warning', type: 'warning', issue: 'Five product listings require review.', date: 'September 21, 2026' }
                        ]
                    },
                    'Little Sprouts': {
                        since: 'March 2024', email: 'ana.garcia@gmail.com', phone: '0922 666 5005', location: 'Antipolo City', products: '63 Products',
                        issues: [
                            { status: 'Warning', type: 'warning', issue: 'Four product listings are under review.', date: 'September 17, 2026' }
                        ]
                    },
                    'Glow & Care': {
                        since: 'February 2024', email: 'sofia.cruz@gmail.com', phone: '0923 777 6006', location: 'Muntinlupa City', products: '97 Products',
                        issues: [
                            { status: 'Warning', type: 'warning', issue: 'Seven product listings require review.', date: 'September 16, 2026' }
                        ]
                    }
                };

                const categoryLabels = {
                    'pet-supplies': 'Pet Supplies',
                    'electronics-and-gadgets': 'Electronics & Gadgets',
                    'womens-apparel': 'Women’s Apparel',
                    'mens-apparel': 'Men’s Apparel',
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

                const categoryClasses = {
                    'pet-supplies': 'category-pet-supplies',
                    'electronics-and-gadgets': 'category-electronics-and-gadgets',
                    'womens-apparel': 'category-womens-apparel',
                    'mens-apparel': 'category-mens-apparel',
                    'kids-and-baby': 'category-kids-and-baby',
                    'home-and-garden': 'category-home-and-garden',
                    'sports-and-outdoors': 'category-sports-and-outdoors',
                    'health-and-beauty': 'category-health-and-beauty',
                    'books-and-media': 'category-books-and-media',
                    'food-and-gourmet': 'category-food-and-gourmet',
                    'automotive-motorcycle': 'category-automotive-motorcycle',
                    'furniture-and-office-equipment': 'category-furniture-and-office-equipment',
                    'jewelry-and-watches': 'category-jewelry-and-watches',
                    'office-and-school-supplies': 'category-office-and-school-supplies'
                };

                function escapeModalHtml(value) {
                    return String(value ?? '')
                        .replace(/&/g, '&amp;')
                        .replace(/</g, '&lt;')
                        .replace(/>/g, '&gt;')
                        .replace(/"/g, '&quot;')
                        .replace(/'/g, '&#039;');
                }

                function renderModalCategories(rawCategory) {
                    const values = String(rawCategory || '')
                        .split(/\s+/)
                        .filter(Boolean);

                    if (!values.length) {
                        return '<span class="seller-modal-category category-default">—</span>';
                    }

                    return values.map(function (value) {
                        const key = value.toLowerCase();
                        const label = categoryLabels[key] || value;
                        const cls = categoryClasses[key] || 'category-default';
                        return `<span class="seller-modal-category ${cls}">${escapeModalHtml(label)}</span>`;
                    }).join('');
                }

                function shirtThumbSvg() {
                    return `
                        <svg viewBox="0 0 60 70" aria-hidden="true">
                            <path d="M18 8l12 5 12-5 8 6 5 14-9 4-4-8v31H18V24l-4 8-9-4 5-14z" fill="#1A1817" stroke="#4A4542" stroke-width="1.2"/>
                            <path d="M25 13c0 4 2 6 5 6s5-2 5-6" fill="none" stroke="#EEEAE8" stroke-width="2"/>
                        </svg>
                    `;
                }

                function blackShirtProductSvg() {
                    return `
                        <svg viewBox="0 0 240 240" preserveAspectRatio="xMidYMid slice" aria-hidden="true">
                            <rect width="240" height="240" fill="#F8F5F3"/>
                            <ellipse cx="120" cy="225" rx="76" ry="9" fill="#DED7D3" opacity=".6"/>
                            <path d="M77 48 L102 36 C111 48 129 48 138 36 L163 48 198 80 176 114 157 102 151 214 89 214 83 102 64 114 42 80z" fill="#111111" stroke="#2E2B29" stroke-width="2"/>
                            <path d="M102 36 C103 53 137 53 138 36" fill="#EEE7E3"/>
                            <path d="M96 75 C98 105 98 158 96 205" stroke="#222" stroke-width="2" opacity=".35"/>
                            <path d="M144 75 C142 105 142 158 144 205" stroke="#222" stroke-width="2" opacity=".35"/>
                        </svg>
                    `;
                }

                function snailProductSvg() {
                    return `
                        <svg viewBox="0 0 240 240" preserveAspectRatio="xMidYMid slice" aria-hidden="true">
                            <defs>
                                <linearGradient id="snailBg" x1="0" y1="0" x2="1" y2="1">
                                    <stop offset="0" stop-color="#6D4D3D"/>
                                    <stop offset=".55" stop-color="#A97B51"/>
                                    <stop offset="1" stop-color="#D7AE73"/>
                                </linearGradient>
                            </defs>
                            <rect width="240" height="240" fill="url(#snailBg)"/>
                            <text x="12" y="20" fill="#FFF8F0" font-size="7" font-family="Arial" font-weight="700">NATURE REPUBLIC</text>
                            <text x="12" y="30" fill="#FFF8F0" font-size="5" font-family="Arial">OFFICIAL STORE</text>
                            <rect x="17" y="138" width="205" height="73" rx="8" fill="#E8D7C5" opacity=".42"/>
                            <rect x="25" y="94" width="48" height="91" rx="9" fill="#EDE2D5" stroke="#D5C3B2"/>
                            <rect x="80" y="112" width="42" height="76" rx="8" fill="#F2E8DD" stroke="#D5C3B2"/>
                            <rect x="132" y="104" width="57" height="82" rx="9" fill="#EFE1D3" stroke="#D5C3B2"/>
                            <circle cx="48" cy="111" r="11" fill="#B98A63" opacity=".8"/>
                            <circle cx="101" cy="130" r="8" fill="#B98A63" opacity=".8"/>
                            <circle cx="160" cy="123" r="10" fill="#B98A63" opacity=".8"/>
                            <text x="98" y="67" fill="#FFF8F0" font-size="14" font-family="Arial" font-style="italic">Snail Solution</text>
                            <text x="99" y="82" fill="#FFF8F0" font-size="10" font-family="Arial">Skin Care Set</text>
                            <text x="18" y="221" fill="#FFF8F0" font-size="7" font-family="Arial">MOISTURIZING • REPAIR • GLOW</text>
                        </svg>
                    `;
                }

                const sellerProductSets = {
                    'DelaCruzShop': [
                        { name: 'Men’s Graphic T-shirt for everyone', price: '₱59', sold: '0 sold', status: 'green', image: 'shirt', category: 'Women’s Apparel', stock: '154 pieces', uploaded: 'September 9, 2026  2:13 PM', brand: 'HangLoose', material: '100% Cotton', sizes: 'S, M, L, XL, XXL', colors: 'Black, White, Gray', weight: '150g', origin: 'Philippines' },
                        { name: 'Snail Solution Care Set', price: '₱690', sold: '15 sold', status: 'green', image: 'snail', category: 'Health & Beauty', stock: '85 pieces', uploaded: 'September 9, 2026  2:13 PM', brand: 'Nature Republic', material: 'Skincare formula', sizes: 'One Size', colors: 'Natural', weight: '320g', origin: 'Philippines' },
                        { name: 'Classic Cotton Tee', price: '₱79', sold: '8 sold', status: 'orange', image: 'shirt', category: 'Women’s Apparel', stock: '62 pieces', uploaded: 'September 8, 2026  4:20 PM', brand: 'HangLoose', material: '100% Cotton', sizes: 'S, M, L, XL', colors: 'Black, White', weight: '145g', origin: 'Philippines' },
                        { name: 'Everyday Graphic Tee', price: '₱89', sold: '4 sold', status: 'orange', image: 'shirt', category: 'Women’s Apparel', stock: '47 pieces', uploaded: 'September 8, 2026  1:05 PM', brand: 'HangLoose', material: 'Cotton Blend', sizes: 'S, M, L, XL', colors: 'Black, Gray', weight: '150g', origin: 'Philippines' },
                        { name: 'Daily Repair Skincare Set', price: '₱720', sold: '21 sold', status: 'green', image: 'snail', category: 'Health & Beauty', stock: '73 pieces', uploaded: 'September 7, 2026  11:42 AM', brand: 'Nature Republic', material: 'Skincare formula', sizes: 'One Size', colors: 'Natural', weight: '330g', origin: 'Philippines' },
                        { name: 'Snail Solution Care Set', price: '₱690', sold: '15 sold', status: 'red', image: 'snail', category: 'Health & Beauty', stock: '28 pieces', uploaded: 'September 6, 2026  9:30 AM', brand: 'Nature Republic', material: 'Skincare formula', sizes: 'One Size', colors: 'Natural', weight: '320g', origin: 'Philippines' }
                    ],
                    'WellnessHub': [
                        { name: 'Daily Wellness Kit', price: '₱549', sold: '32 sold', status: 'orange', image: 'snail', category: 'Health & Beauty', stock: '44 pieces', uploaded: 'September 9, 2026  10:15 AM', brand: 'WellnessHub', material: 'Personal care formula', sizes: 'One Size', colors: 'Natural', weight: '280g', origin: 'Philippines' },
                        { name: 'Hydrating Care Set', price: '₱629', sold: '18 sold', status: 'green', image: 'snail', category: 'Health & Beauty', stock: '69 pieces', uploaded: 'September 8, 2026  3:25 PM', brand: 'WellnessHub', material: 'Hydrating formula', sizes: 'One Size', colors: 'Natural', weight: '300g', origin: 'Philippines' },
                        { name: 'Gourmet Tea Collection', price: '₱399', sold: '25 sold', status: 'green', image: 'snail', category: 'Food & Gourmet', stock: '88 packs', uploaded: 'September 8, 2026  9:40 AM', brand: 'WellnessHub', material: 'Premium tea blend', sizes: 'One Size', colors: 'Assorted', weight: '250g', origin: 'Philippines' },
                        { name: 'Herbal Glow Set', price: '₱459', sold: '11 sold', status: 'orange', image: 'snail', category: 'Health & Beauty', stock: '31 pieces', uploaded: 'September 7, 2026  2:10 PM', brand: 'WellnessHub', material: 'Herbal formula', sizes: 'One Size', colors: 'Natural', weight: '265g', origin: 'Philippines' },
                        { name: 'Organic Snack Box', price: '₱299', sold: '41 sold', status: 'green', image: 'snail', category: 'Food & Gourmet', stock: '57 boxes', uploaded: 'September 6, 2026  5:35 PM', brand: 'WellnessHub', material: 'Organic ingredients', sizes: 'Medium', colors: 'Assorted', weight: '500g', origin: 'Philippines' },
                        { name: 'Complete Self Care Set', price: '₱799', sold: '9 sold', status: 'red', image: 'snail', category: 'Health & Beauty', stock: '16 pieces', uploaded: 'September 5, 2026  1:55 PM', brand: 'WellnessHub', material: 'Personal care formula', sizes: 'One Size', colors: 'Natural', weight: '420g', origin: 'Philippines' }
                    ],
                    'TechCore PH': [
                        { name: 'Wireless Earbuds Pro', price: '₱1,499.00', sold: '1.2k sold', status: 'red', image: 'snail', category: 'Electronics & Gadgets', stock: '24 pieces', uploaded: 'September 10, 2026  9:20 AM', brand: 'TechCore', material: 'ABS + Silicone', sizes: 'One Size', colors: 'Black', weight: '85g', origin: 'Philippines' },
                        { name: 'Smart Watch Series 5', price: '₱2,899.00', sold: '420 sold', status: 'orange', image: 'snail', category: 'Electronics & Gadgets', stock: '36 pieces', uploaded: 'September 9, 2026  2:45 PM', brand: 'TechCore', material: 'Aluminum + Glass', sizes: '42mm', colors: 'Black', weight: '48g', origin: 'Philippines' },
                        { name: 'Car Phone Mount', price: '₱349.00', sold: '215 sold', status: 'green', image: 'shirt', category: 'Automotive & Motorcycle', stock: '92 pieces', uploaded: 'September 8, 2026  11:15 AM', brand: 'TechCore', material: 'ABS Plastic', sizes: 'Universal', colors: 'Black', weight: '180g', origin: 'Philippines' },
                        { name: 'USB-C Fast Charger', price: '₱599.00', sold: '680 sold', status: 'orange', image: 'snail', category: 'Electronics & Gadgets', stock: '51 pieces', uploaded: 'September 7, 2026  4:05 PM', brand: 'TechCore', material: 'Fireproof PC', sizes: '30W', colors: 'White', weight: '95g', origin: 'Philippines' },
                        { name: 'Wireless Car Charger', price: '₱899.00', sold: '144 sold', status: 'green', image: 'snail', category: 'Automotive & Motorcycle', stock: '63 pieces', uploaded: 'September 6, 2026  8:50 AM', brand: 'TechCore', material: 'ABS + Silicone', sizes: 'Universal', colors: 'Black', weight: '220g', origin: 'Philippines' },
                        { name: 'Bluetooth Speaker Mini', price: '₱1,099.00', sold: '305 sold', status: 'red', image: 'snail', category: 'Electronics & Gadgets', stock: '12 pieces', uploaded: 'September 5, 2026  10:30 AM', brand: 'TechCore', material: 'ABS + Metal Mesh', sizes: 'Mini', colors: 'Black', weight: '310g', origin: 'Philippines' }
                    ],
                    'ActiveLife Hub': [
                        { name: 'Performance Running Shoes', price: '₱1,899', sold: '86 sold', status: 'green', image: 'shirt', category: 'Sports & Outdoors', stock: '34 pairs', uploaded: 'September 10, 2026  7:40 AM', brand: 'ActiveLife', material: 'Mesh + Rubber', sizes: '6-12', colors: 'Black', weight: '650g', origin: 'Philippines' },
                        { name: 'Training Resistance Bands', price: '₱499', sold: '124 sold', status: 'green', image: 'shirt', category: 'Sports & Outdoors', stock: '75 sets', uploaded: 'September 9, 2026  1:10 PM', brand: 'ActiveLife', material: 'Natural Latex', sizes: 'Set of 5', colors: 'Assorted', weight: '420g', origin: 'Philippines' },
                        { name: 'Pet Comfort Bed', price: '₱899', sold: '56 sold', status: 'orange', image: 'shirt', category: 'Pet Supplies', stock: '22 pieces', uploaded: 'September 8, 2026  3:00 PM', brand: 'ActiveLife', material: 'Plush Fabric', sizes: 'Medium', colors: 'Gray', weight: '900g', origin: 'Philippines' },
                        { name: 'Adjustable Dumbbell', price: '₱2,499', sold: '31 sold', status: 'green', image: 'shirt', category: 'Sports & Outdoors', stock: '18 pieces', uploaded: 'September 7, 2026  12:20 PM', brand: 'ActiveLife', material: 'Steel + Rubber', sizes: '20kg', colors: 'Black', weight: '20kg', origin: 'Philippines' },
                        { name: 'Interactive Pet Toy', price: '₱329', sold: '73 sold', status: 'green', image: 'shirt', category: 'Pet Supplies', stock: '48 pieces', uploaded: 'September 6, 2026  9:15 AM', brand: 'ActiveLife', material: 'ABS + Rubber', sizes: 'Medium', colors: 'Blue', weight: '190g', origin: 'Philippines' },
                        { name: 'Outdoor Camping Mat', price: '₱799', sold: '38 sold', status: 'orange', image: 'shirt', category: 'Sports & Outdoors', stock: '27 pieces', uploaded: 'September 5, 2026  5:10 PM', brand: 'ActiveLife', material: 'EVA Foam', sizes: 'Standard', colors: 'Green', weight: '620g', origin: 'Philippines' }
                    ],
                    'Home Haven PH': [
                        { name: 'Modern Storage Cabinet', price: '₱3,499', sold: '15 sold', status: 'green', image: 'shirt', category: 'Home & Garden', stock: '12 pieces', uploaded: 'September 10, 2026  10:05 AM', brand: 'Home Haven', material: 'Engineered Wood', sizes: 'Large', colors: 'Oak', weight: '18kg', origin: 'Philippines' },
                        { name: 'Minimalist Office Desk', price: '₱4,299', sold: '9 sold', status: 'green', image: 'shirt', category: 'Furniture & Office Equipment', stock: '8 pieces', uploaded: 'September 9, 2026  2:15 PM', brand: 'Home Haven', material: 'Wood + Steel', sizes: '120cm', colors: 'Oak', weight: '16kg', origin: 'Philippines' },
                        { name: 'Indoor Plant Stand', price: '₱999', sold: '27 sold', status: 'orange', image: 'shirt', category: 'Home & Garden', stock: '19 pieces', uploaded: 'September 8, 2026  8:45 AM', brand: 'Home Haven', material: 'Solid Wood', sizes: 'Medium', colors: 'Walnut', weight: '2.5kg', origin: 'Philippines' },
                        { name: 'Ergonomic Office Chair', price: '₱5,999', sold: '13 sold', status: 'green', image: 'shirt', category: 'Furniture & Office Equipment', stock: '6 pieces', uploaded: 'September 7, 2026  11:30 AM', brand: 'Home Haven', material: 'Mesh + Steel', sizes: 'Standard', colors: 'Black', weight: '14kg', origin: 'Philippines' },
                        { name: 'Garden Tool Set', price: '₱1,299', sold: '22 sold', status: 'green', image: 'shirt', category: 'Home & Garden', stock: '25 sets', uploaded: 'September 6, 2026  4:30 PM', brand: 'Home Haven', material: 'Steel + Wood', sizes: '5-Piece', colors: 'Natural', weight: '1.8kg', origin: 'Philippines' },
                        { name: 'Compact Bookshelf', price: '₱2,199', sold: '17 sold', status: 'orange', image: 'shirt', category: 'Furniture & Office Equipment', stock: '10 pieces', uploaded: 'September 5, 2026  1:25 PM', brand: 'Home Haven', material: 'Engineered Wood', sizes: 'Small', colors: 'Oak', weight: '11kg', origin: 'Philippines' }
                    ],
                    'Little Sprouts': [
                        { name: 'Soft Baby Romper', price: '₱349', sold: '74 sold', status: 'green', image: 'shirt', category: 'Kids & Baby', stock: '54 pieces', uploaded: 'September 10, 2026  9:05 AM', brand: 'Little Sprouts', material: '100% Cotton', sizes: '0-24M', colors: 'Pastel', weight: '110g', origin: 'Philippines' },
                        { name: 'Kids School Supply Set', price: '₱499', sold: '42 sold', status: 'green', image: 'shirt', category: 'Office & School Supplies', stock: '37 sets', uploaded: 'September 9, 2026  12:35 PM', brand: 'Little Sprouts', material: 'Plastic + Paper', sizes: '20-Piece', colors: 'Assorted', weight: '520g', origin: 'Philippines' },
                        { name: 'Storybook Collection', price: '₱699', sold: '29 sold', status: 'orange', image: 'shirt', category: 'Books & Media', stock: '31 sets', uploaded: 'September 8, 2026  3:45 PM', brand: 'Little Sprouts', material: 'Paper', sizes: 'Set of 5', colors: 'Assorted', weight: '750g', origin: 'Philippines' },
                        { name: 'Toddler Backpack', price: '₱599', sold: '61 sold', status: 'green', image: 'shirt', category: 'Kids & Baby', stock: '45 pieces', uploaded: 'September 7, 2026  10:50 AM', brand: 'Little Sprouts', material: 'Polyester', sizes: 'Small', colors: 'Blue', weight: '280g', origin: 'Philippines' },
                        { name: 'Learning Activity Book', price: '₱259', sold: '88 sold', status: 'green', image: 'shirt', category: 'Books & Media', stock: '63 pieces', uploaded: 'September 6, 2026  1:15 PM', brand: 'Little Sprouts', material: 'Paper', sizes: 'A4', colors: 'Assorted', weight: '180g', origin: 'Philippines' },
                        { name: 'Kids Art Supply Kit', price: '₱429', sold: '35 sold', status: 'red', image: 'shirt', category: 'Office & School Supplies', stock: '14 sets', uploaded: 'September 5, 2026  9:55 AM', brand: 'Little Sprouts', material: 'Plastic + Paper', sizes: '15-Piece', colors: 'Assorted', weight: '410g', origin: 'Philippines' }
                    ],
                    'Glow & Care': [
                        { name: 'Brightening Skin Care Set', price: '₱899', sold: '96 sold', status: 'green', image: 'snail', category: 'Health & Beauty', stock: '39 sets', uploaded: 'September 10, 2026  8:30 AM', brand: 'Glow & Care', material: 'Skincare formula', sizes: 'One Size', colors: 'Natural', weight: '300g', origin: 'Philippines' },
                        { name: 'Everyday Blouse', price: '₱499', sold: '47 sold', status: 'green', image: 'shirt', category: 'Women’s Apparel', stock: '28 pieces', uploaded: 'September 9, 2026  2:20 PM', brand: 'Glow & Care', material: 'Cotton Blend', sizes: 'S, M, L, XL', colors: 'White', weight: '180g', origin: 'Philippines' },
                        { name: 'Gentle Cleansing Set', price: '₱649', sold: '73 sold', status: 'orange', image: 'snail', category: 'Health & Beauty', stock: '25 sets', uploaded: 'September 8, 2026  11:40 AM', brand: 'Glow & Care', material: 'Gentle skincare formula', sizes: 'One Size', colors: 'Natural', weight: '280g', origin: 'Philippines' },
                        { name: 'Classic Women’s Top', price: '₱549', sold: '33 sold', status: 'green', image: 'shirt', category: 'Women’s Apparel', stock: '41 pieces', uploaded: 'September 7, 2026  4:10 PM', brand: 'Glow & Care', material: 'Rayon Blend', sizes: 'S, M, L', colors: 'Pink', weight: '160g', origin: 'Philippines' },
                        { name: 'Repair Moisture Care Set', price: '₱749', sold: '58 sold', status: 'green', image: 'snail', category: 'Health & Beauty', stock: '47 sets', uploaded: 'September 6, 2026  10:05 AM', brand: 'Glow & Care', material: 'Moisturizing formula', sizes: 'One Size', colors: 'Natural', weight: '315g', origin: 'Philippines' },
                        { name: 'Soft Knit Cardigan', price: '₱799', sold: '21 sold', status: 'orange', image: 'shirt', category: 'Women’s Apparel', stock: '18 pieces', uploaded: 'September 5, 2026  12:50 PM', brand: 'Glow & Care', material: 'Knit Fabric', sizes: 'S, M, L', colors: 'Beige', weight: '320g', origin: 'Philippines' }
                    ]
                };

                function normalizeProductModerationStatus(product) {
                    if (!product) return 'under-review';

                    const explicitStatus = String(product.moderationStatus || '').trim().toLowerCase().replace(/[\s_-]+/g, '');
                    const rawStatus = String(product.status || '').trim().toLowerCase().replace(/[\s_-]+/g, '');

                    if (['removed', 'rejected', 'archived', 'gray', 'red'].includes(explicitStatus) || ['removed', 'rejected', 'archived', 'gray', 'red'].includes(rawStatus)) return 'removed';
                    if (['warning', 'yellow', 'orange'].includes(explicitStatus) || ['warning', 'yellow', 'orange'].includes(rawStatus)) return 'warning';
                    if (['approved', 'green', 'active'].includes(explicitStatus) || ['approved', 'green', 'active'].includes(rawStatus)) return 'approved';

                    if (product.is_archived || product.remove_reason || product.removeReason || product.status === 'archived' || product.status === 'removed' || product.status === 'rejected' || product.status === 'red' || product.status === 'gray') return 'removed';
                    if (product.warning_reason || product.warningReason || product.status === 'warning' || product.status === 'yellow' || product.status === 'orange') return 'warning';
                    if (product.status === 'green' || product.status === 'approved' || product.status === 'active') return 'approved';

                    return 'under-review';
                }

                function getProductStatusClass(product) {
                    const status = normalizeProductModerationStatus(product);
                    if (status === 'approved') return 'approved';
                    if (status === 'warning') return 'warning';
                    if (status === 'removed') return 'removed';
                    return 'under-review';
                }

                function getProductStatusLabel(status) {
                    return { approved: 'Approved', warning: 'Warning Issued', removed: 'Removed', 'under-review': 'Under Review' }[status] || 'Under Review';
                }


                const productDecisionFlash = document.getElementById('productDecisionFlash');
                const productDecisionFlashTitle = document.getElementById('productDecisionFlashTitle');
                const productDecisionFlashMessage = document.getElementById('productDecisionFlashMessage');
                const productDecisionFlashClose = document.getElementById('productDecisionFlashClose');
                let productDecisionFlashTimer = null;

                function hideProductDecisionFlash() {
                    if (productDecisionFlashTimer) {
                        clearTimeout(productDecisionFlashTimer);
                        productDecisionFlashTimer = null;
                    }
                    productDecisionFlash?.classList.remove('show');
                }

                function showProductDecisionFlash(type, productName) {
                    if (!productDecisionFlash) return;

                    const productLabel = productName || 'Product';
                    const quotedProductLabel = productLabel ? `"${productLabel}"` : 'this product';
                    const config = {
                        approved: {
                            title: 'Product Approved',
                            message: `${productLabel} has been approved successfully.`
                        },
                        warning: {
                            title: 'Warning Issued',
                            message: `A warning has been issued for ${quotedProductLabel}.`
                        },
                        removed: {
                            title: 'Product Removed',
                            message: `${productLabel} has been removed from the seller's store.`
                        }
                    }[type] || {
                        title: 'Product Updated',
                        message: `${productLabel} has been updated.`
                    };

                    if (productDecisionFlashTitle) productDecisionFlashTitle.textContent = config.title;
                    if (productDecisionFlashMessage) productDecisionFlashMessage.textContent = config.message;

                    productDecisionFlash.classList.remove('approved', 'warning', 'removed');
                    productDecisionFlash.classList.add(type);

                    if (productDecisionFlashTimer) clearTimeout(productDecisionFlashTimer);

                    requestAnimationFrame(function () {
                        productDecisionFlash.classList.add('show');
                    });

                    productDecisionFlashTimer = setTimeout(hideProductDecisionFlash, 3800);
                }

                productDecisionFlashClose?.addEventListener('click', hideProductDecisionFlash);

                function currentProductStoreName() {
                    return activeSellerRow?.dataset?.name || '';
                }

                function refreshCurrentSellerProducts() {
                    const storeName = currentProductStoreName();
                    if (storeName) renderSellerProducts(storeName);
                }

                function addProductIssue(storeName, product, type, reason, details) {
                    const profile = sellerProfiles[storeName] || (sellerProfiles[storeName] = {});
                    profile.issues = Array.isArray(profile.issues) ? profile.issues : [];
                    profile.issues = profile.issues.filter(function(issue) { return issue.productKey !== product.productKey; });
                    profile.issues.unshift({
                        productKey: product.productKey,
                        productName: product.name,
                        status: type === 'warning' ? 'Warning' : 'Removed',
                        type: type === 'warning' ? 'warning' : 'removed',
                        issue: reason + (details ? ' — ' + details : ''),
                        date: new Date().toLocaleDateString('en-US', { month: 'long', day: 'numeric', year: 'numeric' })
                    });
                }

                function buildRecentSellerIssues(products, fallbackIssues) {
                    const productIssues = (Array.isArray(products) ? products : []).reduce(function (list, product) {
                        if (!product) return list;

                        const status = normalizeProductModerationStatus(product);
                        if (status !== 'warning' && status !== 'removed') {
                            return list;
                        }

                        const reason = status === 'warning'
                            ? (product.warningReason || product.warning_reason || 'A compliance issue was identified for this product.')
                            : (product.removeReason || product.remove_reason || 'This product has been removed from the store.');
                        const details = status === 'warning'
                            ? (product.warningDetails || product.warning_details || '')
                            : (product.removeDetails || product.remove_details || '');

                        list.push({
                            productKey: product.productKey || product.id,
                            productName: product.name || 'Product',
                            status: status === 'warning' ? 'Warning' : 'Removed',
                            type: status === 'warning' ? 'warning' : 'removed',
                            issue: reason + (details ? ' — ' + details : ''),
                            date: product.updated_at || product.created_at || new Date().toLocaleDateString('en-US', { month: 'long', day: 'numeric', year: 'numeric' })
                        });

                        return list;
                    }, []);

                    const profileIssues = Array.isArray(fallbackIssues) ? fallbackIssues : [];
                    return [...productIssues, ...profileIssues].slice(0, 8);
                }

                function removeProductIssue(storeName, product) {
                    const profile = sellerProfiles[storeName];
                    if (!profile || !Array.isArray(profile.issues)) return;
                    profile.issues = profile.issues.filter(function(issue) { return issue.productKey !== product.productKey; });
                }

                function renderProductComplianceNotice(product) {
                    if (!sellerProductComplianceNotice) return;
                    const status = normalizeProductModerationStatus(product);
                    if (status === 'approved') {
                        sellerProductComplianceNotice.className = 'seller-product-compliance-notice approved';
                        sellerProductComplianceNotice.innerHTML = '<strong>Approved</strong><span>This product is approved and available.</span>';
                    } else if (status === 'warning') {
                        const reason = product.warningReason || 'A compliance issue was identified for this product.';
                        const details = product.warningDetails ? ' — ' + product.warningDetails : '';
                        sellerProductComplianceNotice.className = 'seller-product-compliance-notice warning';
                        sellerProductComplianceNotice.innerHTML = '<strong>Issue Warning</strong><span>' + escapeModalHtml(reason + details) + '</span>';
                    } else if (status === 'removed') {
                        const reason = product.removeReason || "This product has been removed from the seller's store.";
                        const details = product.removeDetails ? ' — ' + product.removeDetails : '';
                        sellerProductComplianceNotice.className = 'seller-product-compliance-notice removed';
                        sellerProductComplianceNotice.innerHTML = '<strong>Removed</strong><span>' + escapeModalHtml(reason + details) + '</span>';
                    } else {
                        sellerProductComplianceNotice.className = 'seller-product-compliance-notice under-review';
                        sellerProductComplianceNotice.innerHTML = '<strong>Under Review</strong><span>This product is awaiting a compliance decision.</span>';
                    }
                }

                function renderSellerProducts(storeName) {
                    if (!sellerProductsGrid) return;
                    const products = sellerProductSets[storeName] || [];
                    sellerProductsGrid.innerHTML = products.map(function(product, index) {
                        const status = normalizeProductModerationStatus(product);
                        const statusClass = getProductStatusClass(product);
                        const image = product.imageUrl
                            ? `<img src="${escapeModalHtml(product.imageUrl)}" alt="${escapeModalHtml(product.name || 'Product')}">`
                            : product.image === 'snail' ? snailProductSvg() : blackShirtProductSvg();
                        return `
                            <article class="seller-product-card status-${statusClass}" tabindex="0" role="button" data-product-index="${index}" aria-label="View ${escapeModalHtml(product.name)} (${getProductStatusLabel(status)})">
                                <div class="seller-product-image">${image}</div>
                                <div class="seller-product-info">
                                    <div class="seller-product-name">${escapeModalHtml(product.name)}</div>
                                    <div class="seller-product-bottom">
                                        <span class="seller-product-price">${escapeModalHtml(product.price)}</span>
                                        <span class="seller-product-sold">${escapeModalHtml(product.sold)}</span>
                                    </div>
                                    <span class="seller-product-status-label">${escapeModalHtml(getProductStatusLabel(status))}</span>
                                </div>
                            </article>
                        `;
                    }).join('');
                    sellerProductsGrid.querySelectorAll('.seller-product-card').forEach(function(card) {
                        const open = function() {
                            const productsForStore = sellerProductSets[storeName] || [];
                            const product = productsForStore[Number(card.dataset.productIndex)];
                            if (product) openProductDetails(product);
                        };
                        card.addEventListener('click', open);
                        card.addEventListener('keydown', function(event) {
                            if (event.key === 'Enter' || event.key === ' ') { event.preventDefault(); open(); }
                        });
                    });
                }

                function productDetailsData(product) {
                    const isSnail = product.image === 'snail';

                    return {
                        image: product.imageUrl
                            ? `<img src="${escapeModalHtml(product.imageUrl)}" alt="${escapeModalHtml(product.name || 'Product')}">`
                            : isSnail ? snailProductSvg() : blackShirtProductSvg(),
                        title: product.name || 'Product',
                        price: product.price || '₱0',
                        sold: product.sold || '0 sold',
                        category: product.category || '—',
                        stock: product.stock || '0 pieces',
                        uploaded: product.uploaded || '—',
                        description: product.description || '—',
                        brand: product.brand || '',
                        material: product.material || '',
                        sizes: product.sizes || '',
                        colors: product.colors || '',
                        weight: product.weight || '',
                        origin: product.origin || '',
                        moderationStatus: normalizeProductModerationStatus(product),
                        warningReason: product.warningReason || '',
                        warningDetails: product.warningDetails || '',
                        removeReason: product.removeReason || '',
                        removeDetails: product.removeDetails || ''
                    };
                }

                function updateProductActionButtons(product) {
                    if (!sellerProductActions) return;

                    const status = normalizeProductModerationStatus(product);
                    const isUnderReview = status === 'under-review';

                    sellerProductActions
                        .querySelectorAll('.seller-product-remove, .seller-product-warning, .seller-product-approve')
                        .forEach(function (button) {
                            button.hidden = !isUnderReview;
                        });

                    if (sellerProductClose) {
                        sellerProductClose.hidden = isUnderReview;
                    }

                    const approveButton = sellerProductDetailsModal?.querySelector('.seller-product-approve');
                    if (!approveButton) return;

                    approveButton.disabled = false;
                    approveButton.textContent = status === 'approved' ? 'Approved' : 'Approve';
                }

                function openProductDetails(product) {
                    if (!sellerProductDetailsModal || !product) return;

                    activeProduct = product;
                    lastProductFocusedElement = document.activeElement;

                    const details = productDetailsData(product);

                    if (sellerProductModalTitle) sellerProductModalTitle.textContent = details.title;
                    if (sellerProductModalPrice) sellerProductModalPrice.textContent = details.price;
                    if (sellerProductModalSold) sellerProductModalSold.textContent = details.sold;
                    if (sellerProductModalCategory) sellerProductModalCategory.textContent = details.category;
                    if (sellerProductModalStock) sellerProductModalStock.textContent = details.stock;
                    if (sellerProductModalUploaded) sellerProductModalUploaded.textContent = details.uploaded;
                    if (sellerProductModalDescription) sellerProductModalDescription.textContent = details.description;
                    if (sellerProductDetailBrand) sellerProductDetailBrand.textContent = details.brand;
                    if (sellerProductDetailMaterial) sellerProductDetailMaterial.textContent = details.material;
                    if (sellerProductDetailSizes) sellerProductDetailSizes.textContent = details.sizes;
                    if (sellerProductDetailColors) sellerProductDetailColors.textContent = details.colors;
                    if (sellerProductDetailWeight) sellerProductDetailWeight.textContent = details.weight;
                    if (sellerProductDetailOrigin) sellerProductDetailOrigin.textContent = details.origin;
                    renderProductComplianceNotice(product);
                    updateProductActionButtons(product);

                    if (sellerProductMainImage) {
                        sellerProductMainImage.innerHTML = details.image;
                    }

                    if (sellerProductThumbnails) {
                        sellerProductThumbnails.innerHTML = Array.from({ length: 5 }).map(function (_, index) {
                            return `
                                <button
                                    type="button"
                                    class="seller-product-thumbnail ${index === 0 ? 'active' : ''}"
                                    aria-label="Product image ${index + 1}"
                                >
                                    ${details.image}
                                </button>
                            `;
                        }).join('');
                    }

                    sellerProductDetailsModal.classList.remove('hidden');
                    sellerProductDetailsModal.setAttribute('aria-hidden', 'false');
                    document.body.classList.add('seller-modal-open');
                    sellerProductDetailsDialog?.focus();
                }

                function closeProductDetails() {
                    if (!sellerProductDetailsModal) return;

                    sellerProductDetailsModal.classList.add('hidden');
                    sellerProductDetailsModal.setAttribute('aria-hidden', 'true');
                    activeProduct = null;

                    if (lastProductFocusedElement && typeof lastProductFocusedElement.focus === 'function') {
                        lastProductFocusedElement.focus();
                    }

                    lastProductFocusedElement = null;
                }

                async function loadSellerComplianceDetails(sellerId) {
                    if (!sellerId) {
                        return null;
                    }

                    try {
                        const url = `${sellerComplianceApiUrl}/${sellerId}`;
                        const response = await fetch(url, {
                            credentials: 'same-origin',
                            headers: {
                                Accept: 'application/json',
                                'X-Requested-With': 'XMLHttpRequest',
                            },
                        });

                        if (!response.ok) {
                            throw new Error('Unable to load seller compliance details.');
                        }

                        const payload = await response.json();
                        return payload.data || null;
                    } catch (error) {
                        console.error(error);
                        return null;
                    }
                }

                function enqueueSellerProductsFromApi(storeName, products = []) {
                    if (!storeName) {
                        return;
                    }

                    const mapped = products.map(function (product) {
                        const productStatus = String(product.status || 'pending').toLowerCase().replace(/[\s_-]+/g, '');
                        const hasExplicitWarning = Boolean(product.warning_reason || product.warningReason || ['warning', 'yellow', 'orange'].includes(productStatus));
                        const hasExplicitRemoval = Boolean(product.is_archived || product.remove_reason || product.removeReason || ['archived', 'removed', 'rejected', 'red', 'gray'].includes(productStatus));
                        const normalizedStatus = hasExplicitRemoval
                            ? 'removed'
                            : hasExplicitWarning
                                ? 'warning'
                                : ['approved', 'green', 'active'].includes(productStatus)
                                    ? 'approved'
                                    : 'under-review';

                        return {
                            id: product.id,
                            name: product.name || 'Product',
                            price: product.price ? `₱${Number(product.price).toLocaleString()}` : '₱0',
                            sold: `${Math.max(0, Number(product.sold_count || 0))} sold`,
                            status: normalizedStatus === 'approved' ? 'green' : normalizedStatus === 'removed' ? 'red' : 'orange',
                            image: product.image_url ? null : 'snail',
                            imageUrl: product.image_url || null,
                            category: product.category || 'General',
                            stock: `${product.stock_quantity ?? 0} pieces`,
                            uploaded: product.created_at ? new Date(product.created_at).toLocaleString('en-US', { month: 'long', day: 'numeric', year: 'numeric' }) : '—',
                            imageUrl: product.image_url || null,
                            brand: product.specifications?.brand || '',
                            material: product.specifications?.material || '',
                            sizes: Array.isArray(product.sizes) ? product.sizes.map(item => item.name).filter(Boolean).join(', ') : product.specifications?.sizes || product.specifications?.size || '',
                            colors: Array.isArray(product.colors) ? product.colors.map(item => item.name).filter(Boolean).join(', ') : product.specifications?.colors || product.specifications?.color || '',
                            weight: product.specifications?.weight || product.specifications?.item_weight || '',
                            origin: product.specifications?.country_of_origin || product.specifications?.origin || product.specifications?.country || '',
                            description: product.description || '',
                            moderationStatus: normalizedStatus,
                            warningReason: product.warning_reason || 'Product review is pending compliance check.',
                            warningDetails: product.warning_details || '',
                            removeReason: product.remove_reason || 'Product is not compliant.',
                            removeDetails: product.remove_details || '',
                            isArchived: Boolean(product.is_archived)
                        };
                    });

                    sellerProductSets[storeName] = mapped;
                }

                async function openSellerDetails(row) {
                    if (!sellerDetailsModal) return;

                    activeSellerRow = row;
                    lastFocusedElement = document.activeElement;

                    sellerDetailsModal.classList.remove('hidden');
                    sellerDetailsModal.setAttribute('aria-hidden', 'false');
                    document.body.classList.add('seller-modal-open');

                    const storeName = row.dataset.name || 'Seller';
                    const sellerId = row.dataset.id;
                    const owner = row.querySelector('.seller-owner-name')?.textContent?.trim() || '—';
                    const score = Number((row.querySelector('.score-number')?.textContent || '0').replace('%', '')) || 0;
                    const compliance = (row.dataset.compliance || 'compliant').toLowerCase();
                    const initialSeller = initialSellerRowsData.find(function (seller) {
                        return String(seller.id) === String(sellerId);
                    }) || {};

                    if (sellerId) {
                        activeSellerApiData = await loadSellerComplianceDetails(sellerId) || initialSeller;
                    } else {
                        activeSellerApiData = initialSeller;
                    }

                    const apiData = activeSellerApiData || {};
                    const sellerName = apiData.store_name || storeName;
                    const sellerOwner = apiData.owner_name || owner;
                    const apiScore = Number(apiData.compliance_score ?? score);
                    const apiCompliance = String(apiData.compliance || compliance).toLowerCase();
                    const apiCategories = Array.isArray(apiData.categories) ? apiData.categories : [];
                    const rowProductTotalText = (row.querySelector('.seller-products-total')?.textContent || '0').replace(/\D+/g, '');
                    const productTotal = Number(apiData.products_count ?? rowProductTotalText ?? 0);
                    const sellerProductsText = productTotal === 1 ? '1 Product' : `${productTotal} Products`;

                    sellerModalStoreName.textContent = sellerName;
                    sellerModalOwner.textContent = sellerOwner;
                    sellerModalSince.textContent = apiData.seller_since || 'Not available';
                    sellerModalEmail.textContent = apiData.email || 'Not available';
                    sellerModalPhone.textContent = apiData.phone || 'Not available';
                    sellerModalLocation.textContent = apiData.location || 'Not available';
                    sellerModalProducts.textContent = sellerProductsText;
                    sellerModalCategories.innerHTML = renderModalCategories(apiCategories.length ? apiCategories.join(' ') : (row.dataset.category || ''));
                    sellerModalScore.textContent = `${Math.max(0, Math.min(100, apiScore))}%`;
                    sellerModalScoreFill.style.width = `${Math.max(0, Math.min(100, apiScore))}%`;

                    const scoreTone = apiCompliance === 'suspended'
                        ? 'score-red'
                        : apiCompliance === 'warning' || apiCompliance === 'under-review'
                            ? 'score-orange'
                            : 'score-green';

                    sellerModalScore.className = `seller-modal-score modal-${scoreTone}`;
                    sellerModalScoreFill.className = scoreTone;

                    if (apiData.products && Array.isArray(apiData.products)) {
                        enqueueSellerProductsFromApi(sellerName, apiData.products);
                    }

                    renderSellerProducts(sellerName);

                    sellerModalCompliancePill.className = 'seller-modal-compliance-pill ' + (
                        apiCompliance === 'under-review' ? 'under-review' : apiCompliance
                    );
                    const complianceLabel = apiCompliance === 'under-review' ? 'Under Review' : apiCompliance.charAt(0).toUpperCase() + apiCompliance.slice(1);
                    sellerModalCompliancePill.textContent = complianceLabel;

                    sellerModalPolicyText.textContent =
                        apiCompliance === 'compliant'
                            ? 'This seller is following the platform policies.'
                            : apiCompliance === 'warning' || apiCompliance === 'under-review'
                                ? 'This seller has compliance items that require attention.'
                                : 'This seller currently has serious compliance concerns.';

                    const actualProductIssues = buildRecentSellerIssues(
                        apiData.products || sellerProductSets[storeName] || [],
                        sellerProfiles[storeName]?.issues || apiData.issues || []
                    );
                    const issues = actualProductIssues.length
                        ? actualProductIssues
                        : [{ status: 'Warning', type: 'warning', issue: 'No recent issues recorded.', date: '—', productName: 'No recent issues recorded.' }];

                    sellerModalIssues.innerHTML = issues.map(function (issue) {
                        const productName = issue.productName || 'Product';
                        const issueText = issue.issue || 'No recent issue details recorded.';
                        const issueStatus = issue.status || 'Warning';
                        const issueType = issue.type || 'warning';
                        const issueDate = issue.date || '—';

                        return `
                            <div class="seller-issue-item">
                                <div class="seller-issue-thumb">${shirtThumbSvg()}</div>
                                <div class="seller-issue-info">
                                    <strong>${escapeModalHtml(productName)}</strong>
                                    <p>${escapeModalHtml(issueText)}</p>
                                </div>
                                <div class="seller-issue-meta">
                                    <span class="seller-issue-status ${escapeModalHtml(issueType)}">${escapeModalHtml(issueStatus)}</span>
                                    <span class="seller-issue-date">${escapeModalHtml(issueDate)}</span>
                                </div>
                            </div>
                        `;
                    }).join('');

                    sellerOverviewPanel?.classList.remove('hidden');
                    sellerProductsPanel?.classList.add('hidden');
                    document.querySelectorAll('.seller-details-tab').forEach(function (tab) {
                        const active = tab.dataset.tab === 'overview';
                        tab.classList.toggle('active', active);
                        tab.setAttribute('aria-selected', active ? 'true' : 'false');
                    });

                    sellerDetailsModal.classList.remove('hidden');
                    sellerDetailsModal.setAttribute('aria-hidden', 'false');
                    document.body.classList.add('seller-modal-open');
                    sellerDetailsDialog?.focus();
                }

                function closeSellerDetails() {
                    if (!sellerDetailsModal) return;
                    sellerDetailsModal.classList.add('hidden');
                    sellerDetailsModal.setAttribute('aria-hidden', 'true');
                    document.body.classList.remove('seller-modal-open');
                    if (lastFocusedElement && typeof lastFocusedElement.focus === 'function') {
                        lastFocusedElement.focus();
                    }
                    activeSellerRow = null;
                }

                function bindSellerComplianceRow(row) {
                    if (!row) {
                        return;
                    }

                    row.setAttribute('tabindex', '0');
                    row.setAttribute('role', 'button');
                    row.setAttribute('aria-label', `View details for ${row.dataset.name || 'seller'}`);
                    row.style.cursor = 'pointer';

                    row.onclick = function (event) {
                        if (event.target.closest('button, a, input, select, textarea')) {
                            return;
                        }

                        openSellerDetails(row);
                    };

                    row.onkeydown = function (event) {
                        if (event.key === 'Enter' || event.key === ' ') {
                            event.preventDefault();
                            openSellerDetails(row);
                        }
                    };
                }

                rows.forEach(function (row) {
                    bindSellerComplianceRow(row);
                });

                document.getElementById('sellerComplianceTable')?.addEventListener('click', function (event) {
                    const row = event.target.closest('.seller-compliance-row');
                    if (!row || event.target.closest('button, a, input, select, textarea')) {
                        return;
                    }

                    openSellerDetails(row);
                });

                document.addEventListener('click', function (event) {
                    const actionTarget = event.target.closest('[data-seller-modal-action]');
                    if (!actionTarget) {
                        return;
                    }

                    const action = actionTarget.dataset.sellerModalAction;
                    if (action === 'close' || action === 'cancel') {
                        closeSellerDetails();
                    } else if (action === 'products') {
                        sellerOverviewPanel?.classList.add('hidden');
                        sellerProductsPanel?.classList.remove('hidden');
                    } else if (action === 'overview') {
                        sellerOverviewPanel?.classList.remove('hidden');
                        sellerProductsPanel?.classList.add('hidden');
                    }
                });

                sellerDetailsClose?.addEventListener('click', closeSellerDetails);
                sellerDetailsCancel?.addEventListener('click', closeSellerDetails);

                sellerDetailsModal?.addEventListener('click', function (event) {
                    if (event.target === sellerDetailsModal) {
                        closeSellerDetails();
                    }
                });

                sellerProductBack?.addEventListener('click', closeProductDetails);

                sellerProductClose?.addEventListener('click', closeProductDetails);

                sellerProductDetailsModal?.addEventListener('click', function (event) {
                    if (event.target === sellerProductDetailsModal) {
                        closeProductDetails();
                    }
                });

                sellerProductThumbnails?.addEventListener('click', function (event) {
                    const thumbnail = event.target.closest('.seller-product-thumbnail');
                    if (!thumbnail) return;

                    sellerProductThumbnails
                        .querySelectorAll('.seller-product-thumbnail')
                        .forEach(function (item) {
                            item.classList.toggle('active', item === thumbnail);
                        });

                    const image = activeProduct?.imageUrl
                        ? `<img src="${escapeModalHtml(activeProduct.imageUrl)}" alt="${escapeModalHtml(activeProduct.name || 'Product')}">`
                        : activeProduct?.image === 'snail'
                            ? snailProductSvg()
                            : blackShirtProductSvg();

                    if (sellerProductMainImage) {
                        sellerProductMainImage.innerHTML = image;
                    }
                });

                sellerProductThumbNext?.addEventListener('click', function () {
                    if (!sellerProductThumbnails || !sellerProductMainImage) return;

                    const thumbnails = Array.from(
                        sellerProductThumbnails.querySelectorAll('.seller-product-thumbnail')
                    );

                    if (!thumbnails.length) return;

                    const currentIndex = thumbnails.findIndex(function (item) {
                        return item.classList.contains('active');
                    });

                    const nextIndex = (currentIndex + 1) % thumbnails.length;
                    thumbnails.forEach(function (item, index) {
                        item.classList.toggle('active', index === nextIndex);
                    });

                    const image = activeProduct?.imageUrl
                        ? `<img src="${escapeModalHtml(activeProduct.imageUrl)}" alt="${escapeModalHtml(activeProduct.name || 'Product')}">`
                        : activeProduct?.image === 'snail'
                            ? snailProductSvg()
                            : blackShirtProductSvg();

                    sellerProductMainImage.innerHTML = image;
                });

                const sellerRemoveProductModal =
                    document.getElementById('sellerRemoveProductModal');

                const sellerRemoveProductDialog =
                    sellerRemoveProductModal?.querySelector('.seller-remove-product-dialog');

                const sellerRemoveProductCancel =
                    document.getElementById('sellerRemoveProductCancel');

                const sellerRemoveProductSubmit =
                    document.getElementById('sellerRemoveProductSubmit');

                const sellerRemoveProductDetails =
                    document.getElementById('sellerRemoveProductDetails');

                const sellerRemoveProductDetailsCount =
                    document.getElementById('sellerRemoveProductDetailsCount');

                let removeReturnFocus = null;

                function openRemoveProductModal() {
                    if (!sellerRemoveProductModal) return;

                    removeReturnFocus = document.activeElement;

                    sellerProductDetailsModal?.classList.add('hidden');
                    sellerProductDetailsModal?.setAttribute('aria-hidden', 'true');

                    sellerRemoveProductModal.classList.remove('hidden');
                    sellerRemoveProductModal.setAttribute('aria-hidden', 'false');
                    document.body.classList.add('seller-remove-modal-open');

                    sellerRemoveProductModal
                        .querySelectorAll('input[name=\"remove_reason\"]')
                        .forEach(function (input) {
                            input.checked = false;
                        });

                    sellerRemoveProductModal
                        .querySelector('.seller-remove-reasons')
                        ?.classList.remove('has-error');

                    if (sellerRemoveProductDetails) {
                        sellerRemoveProductDetails.value = '';
                    }

                    if (sellerRemoveProductDetailsCount) {
                        sellerRemoveProductDetailsCount.textContent = '0/300';
                    }

                    requestAnimationFrame(function () {
                        sellerRemoveProductCancel?.focus();
                    });
                }

                function closeRemoveProductModal(returnToProduct = true) {
                    if (!sellerRemoveProductModal) return;

                    sellerRemoveProductModal.classList.add('hidden');
                    sellerRemoveProductModal.setAttribute('aria-hidden', 'true');
                    document.body.classList.remove('seller-remove-modal-open');

                    if (returnToProduct && sellerProductDetailsModal) {
                        sellerProductDetailsModal.classList.remove('hidden');
                        sellerProductDetailsModal.setAttribute('aria-hidden', 'false');
                        requestAnimationFrame(function () {
                            sellerProductDetailsDialog?.focus();
                        });
                    } else if (removeReturnFocus && typeof removeReturnFocus.focus === 'function') {
                        removeReturnFocus.focus();
                    }

                    removeReturnFocus = null;
                }

                sellerRemoveProductCancel?.addEventListener('click', function () {
                    closeRemoveProductModal(true);
                });

                sellerRemoveProductDetails?.addEventListener('input', function () {
                    if (sellerRemoveProductDetailsCount) {
                        sellerRemoveProductDetailsCount.textContent = `${sellerRemoveProductDetails.value.length}/300`;
                    }
                });

                sellerRemoveProductSubmit?.addEventListener('click', function () {
                    const selectedReason = sellerRemoveProductModal?.querySelector('input[name=\"remove_reason\"]:checked');
                    if (!selectedReason) {
                        sellerRemoveProductModal?.querySelector('.seller-remove-reasons')?.classList.add('has-error');
                        return;
                    }
                    sellerRemoveProductModal?.querySelector('.seller-remove-reasons')?.classList.remove('has-error');
                    if (!activeProduct) return;

                    const storeName = currentProductStoreName();
                    activeProduct.moderationStatus = 'removed';
                    activeProduct.status = 'gray';
                    activeProduct.removeReason = selectedReason.value;
                    activeProduct.removeDetails = sellerRemoveProductDetails?.value?.trim() || '';
                    activeProduct.warningReason = '';
                    activeProduct.warningDetails = '';
                    addProductIssue(storeName, activeProduct, 'removed', activeProduct.removeReason, activeProduct.removeDetails);
                    refreshCurrentSellerProducts();
                    closeRemoveProductModal(true);
                    openProductDetails(activeProduct);
                    showProductDecisionFlash('removed', activeProduct.name);
                });

                sellerRemoveProductModal?.addEventListener('click', function (event) {
                    if (event.target === sellerRemoveProductModal) {
                        closeRemoveProductModal(true);
                    }
                });

                const sellerIssueWarningModal =
                    document.getElementById('sellerIssueWarningModal');

                const sellerIssueWarningDialog =
                    sellerIssueWarningModal?.querySelector('.seller-issue-warning-dialog');

                const sellerWarningCancel =
                    document.getElementById('sellerWarningCancel');

                const sellerWarningSubmit =
                    document.getElementById('sellerWarningSubmit');

                const sellerWarningDetails =
                    document.getElementById('sellerWarningDetails');

                const sellerWarningDetailsCount =
                    document.getElementById('sellerWarningDetailsCount');

                let warningReturnFocus = null;

                function openIssueWarningModal() {
                    if (!sellerIssueWarningModal) return;

                    warningReturnFocus = document.activeElement;

                    sellerProductDetailsModal?.classList.add('hidden');
                    sellerProductDetailsModal?.setAttribute('aria-hidden', 'true');

                    sellerIssueWarningModal.classList.remove('hidden');
                    sellerIssueWarningModal.setAttribute('aria-hidden', 'false');
                    document.body.classList.add('seller-warning-modal-open');

                    sellerIssueWarningModal
                        .querySelectorAll('input[name="warning_reason"]')
                        .forEach(function (input) {
                            input.checked = false;
                        });

                    if (sellerWarningDetails) {
                        sellerWarningDetails.value = '';
                    }

                    if (sellerWarningDetailsCount) {
                        sellerWarningDetailsCount.textContent = '0/300';
                    }

                    requestAnimationFrame(function () {
                        sellerWarningCancel?.focus();
                    });
                }

                function closeIssueWarningModal(returnToProduct = true) {
                    if (!sellerIssueWarningModal) return;

                    sellerIssueWarningModal.classList.add('hidden');
                    sellerIssueWarningModal.setAttribute('aria-hidden', 'true');
                    document.body.classList.remove('seller-warning-modal-open');

                    if (returnToProduct && sellerProductDetailsModal) {
                        sellerProductDetailsModal.classList.remove('hidden');
                        sellerProductDetailsModal.setAttribute('aria-hidden', 'false');
                        requestAnimationFrame(function () {
                            sellerProductDetailsDialog?.focus();
                        });
                    } else if (warningReturnFocus && typeof warningReturnFocus.focus === 'function') {
                        warningReturnFocus.focus();
                    }

                    warningReturnFocus = null;
                }

                document.querySelector('.seller-product-warning')?.addEventListener('click', openIssueWarningModal);

                sellerWarningCancel?.addEventListener('click', function () {
                    closeIssueWarningModal(true);
                });

                sellerWarningDetails?.addEventListener('input', function () {
                    if (sellerWarningDetailsCount) {
                        sellerWarningDetailsCount.textContent = `${sellerWarningDetails.value.length}/300`;
                    }
                });

                sellerWarningSubmit?.addEventListener('click', function () {
                    const selectedReason = sellerIssueWarningModal?.querySelector('input[name=\"warning_reason\"]:checked');
                    if (!selectedReason) {
                        sellerIssueWarningModal?.querySelector('.seller-warning-reasons')?.classList.add('has-error');
                        return;
                    }
                    sellerIssueWarningModal?.querySelector('.seller-warning-reasons')?.classList.remove('has-error');
                    if (!activeProduct) return;

                    const storeName = currentProductStoreName();
                    activeProduct.moderationStatus = 'warning';
                    activeProduct.status = 'yellow';
                    activeProduct.warningReason = selectedReason.value;
                    activeProduct.warningDetails = sellerWarningDetails?.value?.trim() || '';
                    activeProduct.removeReason = '';
                    activeProduct.removeDetails = '';
                    addProductIssue(storeName, activeProduct, 'warning', activeProduct.warningReason, activeProduct.warningDetails);
                    refreshCurrentSellerProducts();
                    closeIssueWarningModal(false);
                    openProductDetails(activeProduct);
                    showProductDecisionFlash('warning', activeProduct.name);
                });

                sellerIssueWarningModal?.addEventListener('click', function (event) {
                    if (event.target === sellerIssueWarningModal) {
                        closeIssueWarningModal(true);
                    }
                });


                sellerProductDetailsModal?.addEventListener('click', async function(event) {
                    const removeButton = event.target.closest('.seller-product-remove');
                    const warningButton = event.target.closest('.seller-product-warning');
                    const approveButton = event.target.closest('.seller-product-approve');

                    if (removeButton) {
                        openRemoveProductModal();
                        return;
                    }

                    if (warningButton) {
                        openIssueWarningModal();
                        return;
                    }

                    if (approveButton) {
                        if (!activeProduct) return;

                        const normalizedStatus = window.normalizeProductStatus(activeProduct.status);
                        const isArchived = Boolean(activeProduct.is_archived);
                        const canApprove = ['pending', 'under_review', 'review'].includes(normalizedStatus);

                        if (isArchived) {
                            showProductDecisionFlash('warning', activeProduct.name);
                            return;
                        }

                        if (!canApprove) {
                            showProductDecisionFlash('warning', activeProduct.name);
                            return;
                        }

                        const storeName = currentProductStoreName();

                        if (activeProduct.id) {
                            approveButton.disabled = true;
                            approveButton.textContent = 'Approving...';
                            try {
                                const response = await fetch(`${sellerComplianceApiUrl}/products/${activeProduct.id}/approve`, {
                                    method: 'POST',
                                    credentials: 'same-origin',
                                    headers: {
                                        Accept: 'application/json',
                                        'X-Requested-With': 'XMLHttpRequest',
                                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
                                    }
                                });

                                if (!response.ok) {
                                    throw new Error('Unable to approve this product.');
                                }
                            } catch (error) {
                                console.error(error);
                                approveButton.disabled = false;
                                approveButton.textContent = 'Approve';
                                showProductDecisionFlash('warning', activeProduct.name);
                                return;
                            }
                        }

                        activeProduct.moderationStatus = 'approved';
                        activeProduct.status = 'green';
                        activeProduct.warningReason = '';
                        activeProduct.warningDetails = '';
                        activeProduct.removeReason = '';
                        activeProduct.removeDetails = '';
                        removeProductIssue(storeName, activeProduct);
                        refreshCurrentSellerProducts();
                        openProductDetails(activeProduct);
                        showProductDecisionFlash('approved', activeProduct.name);
                    }
                });

                document.addEventListener('keydown', function (event) {
                    if (event.key === 'Escape' && sellerRemoveProductModal && !sellerRemoveProductModal.classList.contains('hidden')) {
                        closeRemoveProductModal(true);
                        return;
                    }

                    if (event.key === 'Escape' && sellerIssueWarningModal && !sellerIssueWarningModal.classList.contains('hidden')) {
                        closeIssueWarningModal(true);
                        return;
                    }

                    if (event.key === 'Escape' && sellerProductDetailsModal && !sellerProductDetailsModal.classList.contains('hidden')) {
                        closeProductDetails();
                        return;
                    }

                    if (event.key === 'Escape' && sellerDetailsModal && !sellerDetailsModal.classList.contains('hidden')) {
                        closeSellerDetails();
                    }
                });

                document.querySelectorAll('.seller-details-tab').forEach(function (tab) {
                    tab.addEventListener('click', function () {
                        const isOverview = tab.dataset.tab === 'overview';
                        document.querySelectorAll('.seller-details-tab').forEach(function (item) {
                            const active = item === tab;
                            item.classList.toggle('active', active);
                            item.setAttribute('aria-selected', active ? 'true' : 'false');
                        });
                        sellerOverviewPanel?.classList.toggle('hidden', !isOverview);
                        sellerProductsPanel?.classList.toggle('hidden', isOverview);
                    });
                });

                document.querySelectorAll('.seller-document-item').forEach(function (button) {
                    button.addEventListener('click', function () {
                        button.blur();
                        if (!button.dataset.url) {
                            window.alert('No document was uploaded for this seller.');
                        }
                    });
                });

                /* =================================================
                   INITIAL
                ================================================== */

                renderRows();

        }

        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', initializeSellerCompliance, { once: true });
        } else {
            initializeSellerCompliance();
        }

    