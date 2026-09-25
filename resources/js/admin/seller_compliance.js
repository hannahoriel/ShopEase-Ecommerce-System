/*
 * resources/js/admin/seller-compliance.js
 *
 * Seller Compliance page.
 * - No hardcoded sellers / products / labels: everything comes from the API
 *   or from the JSON config rendered by Blade (#sellerComplianceConfig).
 * - One modal system, one status normalizer, event delegation via data-action.
 */

const configEl = document.getElementById('sellerComplianceConfig');

if (configEl) {
    initSellerCompliance(JSON.parse(configEl.textContent));
}

function initSellerCompliance(cfg) {
    /* ------------------------------------------------------------------ *
     * Helpers
     * ------------------------------------------------------------------ */

    const $ = (selector, scope = document) => scope.querySelector(selector);
    const $$ = (selector, scope = document) =>
        Array.from(scope.querySelectorAll(selector));

    const ESCAPES = {
        '&': '&amp;',
        '<': '&lt;',
        '>': '&gt;',
        '"': '&quot;',
        "'": '&#039;',
    };

    const esc = (value) =>
        String(value ?? '').replace(/[&<>"']/g, (c) => ESCAPES[c]);

    const clamp = (n) =>
        Math.max(0, Math.min(100, Number(n) || 0));

    const peso = (n) =>
        Number(n || 0).toLocaleString('en-PH', {
            style: 'currency',
            currency: 'PHP',
        });

    const formatDate = (v) =>
        v
            ? new Date(v).toLocaleDateString('en-US', {
                  month: 'long',
                  day: 'numeric',
                  year: 'numeric',
              })
            : '—';

    const plural = (n, word) =>
        `${n} ${word}${n === 1 ? '' : 's'}`;

    const titleCase = (v) =>
        String(v ?? '')
            .split(/[-_\s]+/)
            .filter(Boolean)
            .map((p) => p[0].toUpperCase() + p.slice(1))
            .join(' ');

    const display = (value) => {
        const t = String(value ?? '').trim();

        return !t ||
            ['null', 'undefined'].includes(t.toLowerCase())
            ? '—'
            : t;
    };

    /*
     * Fill every [data-bind="key"] inside scope with textContent.
     */
    const bind = (scope, values) => {
        Object.entries(values).forEach(([key, value]) => {
            $$(`[data-bind="${key}"]`, scope).forEach((node) => {
                node.textContent = display(value);
            });
        });
    };

    const scoreTone = (compliance) =>
        compliance === 'suspended'
            ? 'red'
            : ['warning', 'under-review'].includes(compliance)
              ? 'orange'
              : 'green';

    const DOC_ICON = `
        <svg
            viewBox="0 0 24 24"
            fill="none"
            stroke="currentColor"
            stroke-width="1.7"
            aria-hidden="true"
        >
            <path d="M7 3h7l4 4v14H7z"/>
            <path d="M14 3v5h5"/>
            <path d="M9 13h6M9 17h5"/>
        </svg>
    `;

    /* ------------------------------------------------------------------ *
     * Elements
     * ------------------------------------------------------------------ */

    const root = document.getElementById('admin-content');

    const csrf =
        $('meta[name="csrf-token"]')?.content || '';

    const ui = {
        search: $('#sellerSearch'),
        category: $('#sellerTypeFilter'),
        compliance: $('#complianceFilter'),
        perPage: $('#sellerItemsPerPage'),
        refreshIcon: $('#complianceRefreshIcon'),
        table: $('#sellerComplianceTable'),
        noResults: $('#sellerComplianceNoResults'),
        showing: $('#sellerShowingText'),
        pageNumbers: $('#sellerPageNumbers'),
        prev: $('#sellerPreviousPage'),
        next: $('#sellerNextPage'),
    };

    const modals = {
        seller: $('#sellerDetailsModal'),
        product: $('#sellerProductDetailsModal'),
        suspend: $('#sellerSuspendModal'),
        warn: $('#decisionModal-warn'),
        remove: $('#decisionModal-remove'),
    };

    /* ------------------------------------------------------------------ *
     * State
     * ------------------------------------------------------------------ */

    const state = {
        items: [],
        total: 0,
        page: 1,
        perPage: Number(ui.perPage?.value) || cfg.perPage || 7,
        serverPaged: false,
        serverLastPage: 1,
        seller: null,
        product: null,
        imageIndex: 0,
    };

    /*
     * Normalize the API base URL.
     *
     * Blade should provide:
     * /api/v1/admin/seller-compliance
     */
    const apiBase = String(cfg.apiUrl || '').replace(/\/+$/, '');

    /* ------------------------------------------------------------------ *
     * API
     * ------------------------------------------------------------------ */

    async function api(path = '', options = {}) {
        const response = await fetch(`${apiBase}${path}`, {
            credentials: 'same-origin',
            ...options,
            headers: {
                Accept: 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': csrf,
                ...(options.body
                    ? {
                          'Content-Type': 'application/json',
                      }
                    : {}),
                ...(options.headers || {}),
            },
        });

        if (!response.ok) {
            let message = `Request failed (${response.status})`;
            let payload = null;

            const contentType =
                response.headers.get('content-type') || '';

            if (contentType.includes('application/json')) {
                payload = await response.json().catch(() => null);

                if (payload?.message) {
                    message = payload.message;
                }

                if (payload?.errors) {
                    const firstError = Object.values(payload.errors)
                        .flat()
                        .find(Boolean);

                    if (firstError) {
                        message = firstError;
                    }
                }
            } else {
                const text = await response.text().catch(() => '');

                if (text) {
                    console.error('API response:', text);
                }
            }

            throw Object.assign(new Error(message), {
                status: response.status,
                payload,
            });
        }

        return response.json();
    }

    /* ------------------------------------------------------------------ *
     * Product status
     * ------------------------------------------------------------------ */

    const PRODUCT_STATUS_LABEL = {
        approved: 'Approved',
        warning: 'Warning Issued',
        removed: 'Removed',
        'under-review': 'Under Review',
    };

    function productStatus(product) {
        const s = String(product.status || '')
            .toLowerCase()
            .replace(/[\s_]+/g, '-');

        /*
         * Your controller uses archive_reason when a product is removed.
         */
        if (
            product.is_archived ||
            product.remove_reason ||
            product.archive_reason ||
            [
                'removed',
                'rejected',
                'archived',
                'red',
                'gray',
            ].includes(s)
        ) {
            return 'removed';
        }

        if (
            product.warning_reason ||
            ['warning', 'yellow', 'orange'].includes(s)
        ) {
            return 'warning';
        }

        if (
            ['approved', 'green', 'active'].includes(s)
        ) {
            return 'approved';
        }

        return 'under-review';
    }

    /* ------------------------------------------------------------------ *
     * Modal helpers
     * ------------------------------------------------------------------ */

    function show(modal) {
        if (!modal) return;

        modal.hidden = false;
        modal.setAttribute('aria-hidden', 'false');

        document.body.classList.add('sc-modal-open');

        $('[role="dialog"]', modal)?.focus();
    }

    function hide(modal) {
        if (!modal) return;

        modal.hidden = true;
        modal.setAttribute('aria-hidden', 'true');

        if (!$('.sc-overlay:not([hidden])')) {
            document.body.classList.remove('sc-modal-open');
        }
    }

    /* ------------------------------------------------------------------ *
     * Flash message
     * ------------------------------------------------------------------ */

    const flashEl = $('#productDecisionFlash');
    let flashTimer = null;

    const FLASH = {
        approved: (name) => [
            'Product Approved',
            `${name} has been approved successfully.`,
        ],

        warning: (name) => [
            'Warning Issued',
            `A warning has been issued for "${name}".`,
        ],

        removed: (name) => [
            'Product Removed',
            `${name} has been removed from the seller's store.`,
        ],

        success: (message) => [
            'Seller Suspended',
            message,
        ],

        error: (message) => [
            'Something went wrong',
            message,
        ],
    };

    function hideFlash() {
        if (!flashEl) return;

        clearTimeout(flashTimer);
        flashEl.classList.remove('show');
    }

    function showFlash(type, value) {
        if (!flashEl) return;

        const [title, message] =
            (FLASH[type] || FLASH.error)(
                value || 'Product'
            );

        $('[data-flash="title"]', flashEl).textContent = title;

        $('[data-flash="message"]', flashEl).textContent =
            message;

        flashEl.classList.remove(
            'approved',
            'warning',
            'removed',
            'error'
        );

        flashEl.classList.add(type);

        requestAnimationFrame(() =>
            flashEl.classList.add('show')
        );

        clearTimeout(flashTimer);

        flashTimer = setTimeout(
            hideFlash,
            3800
        );
    }

    /* ------------------------------------------------------------------ *
     * List: summary, table, pagination
     * ------------------------------------------------------------------ */

    function categoryPills(categories, small = false) {
        const list = Array.isArray(categories)
            ? categories
            : [];

        const size = small
            ? ' category-pill--sm'
            : '';

        if (!list.length) {
            return `
                <span class="category-pill${size} category-default">
                    —
                </span>
            `;
        }

        return list
            .map((slug) => {
                const key = String(slug).toLowerCase();

                const label =
                    cfg.categories?.[key] ||
                    titleCase(key);

                return `
                    <span
                        class="category-pill${size} category-${esc(key)}"
                    >
                        ${esc(label)}
                    </span>
                `;
            })
            .join('');
    }

    function renderSummary(summary = {}) {
        $$('[data-summary]').forEach((node) => {
            node.textContent = Number(
                summary[node.dataset.summary] || 0
            );
        });

        $$('[data-trend]').forEach((node) => {
            const value =
                summary.trends?.[node.dataset.trend];

            if (
                value === undefined ||
                value === null
            ) {
                node.hidden = true;
                return;
            }

            const down = Number(value) < 0;

            node.classList.toggle(
                'is-down',
                down
            );

            node.innerHTML = `
                <span class="growth-arrow">
                    ${down ? '↓' : '↑'}
                </span>
                <strong>${Math.abs(value)}%</strong>
                ${esc(node.dataset.trendLabel || '')}
            `;

            node.hidden = false;
        });
    }

    function rowMarkup(seller) {
        const compliance = String(
            seller.compliance || 'compliant'
        ).toLowerCase();

        const label =
            seller.compliance_label ||
            cfg.statuses?.[compliance]?.label ||
            titleCase(compliance);

        const score = clamp(
            seller.compliance_score
        );

        const count = Number(
            seller.products_count || 0
        );

        const name =
            seller.store_name || 'Seller';

        return `
            <article
                class="seller-compliance-row"
                role="button"
                tabindex="0"
                data-id="${esc(seller.id)}"
                aria-label="View details for ${esc(name)}"
            >
                <div class="seller-cell">
                    <div class="seller-avatar"></div>

                    <div class="seller-name-group">
                        <div class="seller-store-name">
                            ${esc(name)}
                        </div>

                        <div class="seller-owner-name">
                            ${esc(seller.owner_name || '—')}
                        </div>
                    </div>
                </div>

                <div class="category-pill-group">
                    ${categoryPills(seller.categories)}
                </div>

                <div class="seller-products">
                    <span class="seller-products-total">
                        ${plural(count, 'Product')}
                    </span>

                    <span
                        class="seller-products-review"
                        title="Products currently under admin review"
                    >
                        ${Number(
                            seller.products_under_review || 0
                        )}
                        Under Review
                    </span>
                </div>

                <div class="score-cell">
                    <div class="score-number">
                        ${score}%
                    </div>

                    <div class="score-bar">
                        <span
                            class="score-fill score-${scoreTone(compliance)}"
                            style="width:${score}%"
                        ></span>
                    </div>
                </div>

                <div>
                    <span
                        class="status-pill status-${esc(compliance)}"
                    >
                        ${esc(label)}
                    </span>
                </div>
            </article>
        `;
    }

    const lastPage = () =>
        state.serverPaged
            ? state.serverLastPage
            : Math.max(
                  1,
                  Math.ceil(
                      state.total / state.perPage
                  )
              );

    const pageItems = () =>
        state.serverPaged
            ? state.items
            : state.items.slice(
                  (state.page - 1) * state.perPage,
                  state.page * state.perPage
              );

    function renderPagination() {
        const pages = lastPage();
        const items = pageItems();

        const from =
            state.total === 0
                ? 0
                : (state.page - 1) *
                      state.perPage +
                  1;

        const to =
            state.total === 0
                ? 0
                : from + items.length - 1;

        if (ui.showing) {
            ui.showing.innerHTML = `
                Showing
                <span>${from}-${to}</span>
                out of ${state.total}
                ${state.total === 1 ? 'entry' : 'entries'}
            `;
        }

        let start = Math.max(
            1,
            state.page - 2
        );

        const end = Math.min(
            pages,
            start + 4
        );

        start = Math.max(
            1,
            end - 4
        );

        if (ui.pageNumbers) {
            ui.pageNumbers.innerHTML =
                Array.from(
                    {
                        length:
                            end - start + 1,
                    },
                    (_, i) => {
                        const n =
                            start + i;

                        return `
                            <button
                                type="button"
                                class="pagination-button${
                                    n === state.page
                                        ? ' current'
                                        : ''
                                }"
                                data-action="page-go"
                                data-page="${n}"
                            >
                                ${n}
                            </button>
                        `;
                    }
                ).join('');
        }

        ui.prev?.classList.toggle(
            'disabled',
            state.page <= 1
        );

        ui.next?.classList.toggle(
            'disabled',
            state.page >= pages
        );
    }

    function renderList() {
        const items = pageItems();

        if (ui.table) {
            ui.table.innerHTML =
                items.map(rowMarkup).join('');
        }

        if (ui.noResults) {
            ui.noResults.hidden =
                state.total !== 0;
        }

        renderPagination();
    }

    function applyPayload(payload) {
        const items = Array.isArray(
            payload?.data
        )
            ? payload.data
            : [];

        const meta = payload?.meta || {};

        state.items = items;

        state.serverPaged =
            Number(meta.last_page) > 0;

        state.serverLastPage =
            Number(meta.last_page) || 1;

        state.total = state.serverPaged
            ? Number(
                  meta.total ?? items.length
              )
            : items.length;

        if (state.page > lastPage()) {
            state.page = lastPage();
        }

        renderSummary(
            payload?.summary || {}
        );

        renderList();
    }

    let loadToken = 0;

    async function loadSellers({
        silent = false,
    } = {}) {
        const token = ++loadToken;

        const params = new URLSearchParams({
            page: state.page,
            per_page: state.perPage,
        });

        if (ui.search?.value.trim()) {
            params.set(
                'search',
                ui.search.value.trim()
            );
        }

        if (
            ui.category &&
            ui.category.value !== 'all'
        ) {
            params.set(
                'category',
                ui.category.value
            );
        }

        if (
            ui.compliance &&
            ui.compliance.value !== 'all'
        ) {
            params.set(
                'compliance',
                ui.compliance.value
            );
        }

        try {
            const payload = await api(
                `?${params}`
            );

            if (token === loadToken) {
                applyPayload(payload);
            }
        } catch (error) {
            console.error(error);

            if (!silent) {
                showFlash(
                    'error',
                    error.message ||
                        'Unable to load seller compliance data.'
                );
            }
        }
    }

    function goToPage(page) {
        state.page = Math.max(
            1,
            Math.min(lastPage(), page)
        );

        if (state.serverPaged) {
            loadSellers();
        } else {
            renderList();
        }
    }

    /* ------------------------------------------------------------------ *
     * Seller modal
     * ------------------------------------------------------------------ */

    let lastFocus = null;

    function syncSellerSuspendButton(seller = state.seller) {
        const button = $('.seller-suspend-button', modals.seller);

        if (!button) return;

        const isSuspended = String(seller?.status || '').toLowerCase() === 'suspended';

        button.textContent = isSuspended ? 'Reactivate' : 'Suspend';
        button.classList.toggle('is-reactivate', isSuspended);
        button.style.background = isSuspended ? '#E7F7EE' : '#D41F1F';
        button.style.borderColor = isSuspended ? '#2E9D5C' : '#D41F1F';
        button.style.color = isSuspended ? '#1F7A45' : '#fff';
    }

    function renderSeller(seller) {
        state.seller = seller;

        const compliance = String(
            seller.compliance || 'compliant'
        ).toLowerCase();

        const status =
            cfg.statuses?.[compliance] || {};

        const score = clamp(
            seller.compliance_score
        );

        const tone =
            scoreTone(compliance);

        const count = Number(
            seller.products_count ??
                seller.products?.length ??
                0
        );

        bind(modals.seller, {
            storeName: seller.store_name,
            since: seller.seller_since,
            owner: seller.owner_name,
            email: seller.email,
            phone: seller.phone,
            location: seller.location,
            productsCount: plural(
                count,
                'Product'
            ),
            score: `${score}%`,
        });

        $('#sellerModalCategories').innerHTML =
            categoryPills(
                seller.categories,
                true
            );

        $('#sellerModalScore').className =
            `seller-modal-score tone-${tone}`;

        const fill =
            $('#sellerModalScoreFill');

        fill.style.width = `${score}%`;
        fill.className =
            `score-${tone}`;

        const pill =
            $('#sellerModalCompliancePill');

        pill.className =
            `status-pill status-${compliance}`;

        pill.textContent =
            seller.compliance_label ||
            status.label ||
            titleCase(compliance);

        $('#sellerModalPolicyText').textContent =
            status.policy || '';

        renderDocuments(seller);
        renderProducts();
        renderIssues();
        syncSellerSuspendButton(seller);
    }

    function renderDocuments(seller) {
        const documents = [
            {
                name:
                    seller.business_permit_name ||
                    'Business permit',
                url: seller.business_permit_url,
            },
            {
                name:
                    seller.valid_id_name ||
                    'Valid ID',
                url: seller.valid_id_url,
            },
        ];

        $('#sellerDocuments').innerHTML =
            documents
                .map((doc) =>
                    doc.url
                        ? `
                            <a
                                class="seller-document-item"
                                href="${esc(doc.url)}"
                                target="_blank"
                                rel="noopener"
                            >
                                ${DOC_ICON}
                                <span>
                                    ${esc(doc.name)}
                                </span>
                            </a>
                        `
                        : `
                            <span class="seller-document-item is-disabled">
                                ${DOC_ICON}
                                <span>
                                    ${esc(doc.name)}
                                    — not uploaded
                                </span>
                            </span>
                        `
                )
                .join('');
    }

    function renderProducts() {
        const products =
            state.seller?.products || [];

        $('#sellerProductsGrid').innerHTML =
            products
                .map((product) => {
                    const status =
                        productStatus(product);

                    const name =
                        product.name ||
                        'Product';

                    const image =
                        product.image_url
                            ? `
                                <img
                                    src="${esc(
                                        product.image_url
                                    )}"
                                    alt="${esc(name)}"
                                >
                            `
                            : `
                                <div class="seller-product-image-placeholder">
                                    No image
                                </div>
                            `;

                    return `
                        <article
                            class="seller-product-card status-${status}"
                            role="button"
                            tabindex="0"
                            data-product-id="${esc(product.id)}"
                            aria-label="View ${esc(
                                name
                            )} (${PRODUCT_STATUS_LABEL[status]})"
                        >
                            <div class="seller-product-image">
                                ${image}
                            </div>

                            <div class="seller-product-info">
                                <div class="seller-product-name">
                                    ${esc(name)}
                                </div>

                                <div class="seller-product-bottom">
                                    <span class="seller-product-price">
                                        ${peso(product.price)}
                                    </span>

                                    <span class="seller-product-sold">
                                        ${Number(
                                            product.sold_count ||
                                                0
                                        )}
                                        sold
                                    </span>
                                </div>

                                <span class="seller-product-status-label">
                                    ${PRODUCT_STATUS_LABEL[status]}
                                </span>
                            </div>
                        </article>
                    `;
                })
                .join('') ||
            `
                <p class="seller-products-empty">
                    No products found for this seller.
                </p>
            `;
    }

    /*
     * Recent Issues are derived from actual product moderation data.
     */
    function renderIssues() {
        const issues = (
            state.seller?.products || []
        )
            .map((product) => ({
                product,
                status: productStatus(product),
            }))
            .filter(
                (item) =>
                    item.status === 'warning' ||
                    item.status === 'removed'
            )
            .sort(
                (a, b) =>
                    new Date(
                        b.product.updated_at || 0
                    ) -
                    new Date(
                        a.product.updated_at || 0
                    )
            )
            .slice(0, 8);

        $('#sellerModalIssues').innerHTML =
            issues
                .map(
                    ({
                        product,
                        status,
                    }) => {
                        const removed =
                            status === 'removed';

                        /*
                         * Removed products from your controller use
                         * archive_reason.
                         */
                        const reason = removed
                            ? product.remove_reason ||
                              product.archive_reason
                            : product.warning_reason;

                        const details = removed
                            ? product.remove_details ||
                              product.archive_details
                            : product.warning_details;

                        const text =
                            reason ||
                            (removed
                                ? 'Removed from the store.'
                                : 'A compliance issue was identified.') +
                                (details
                                    ? ` — ${details}`
                                    : '');

                        const thumb =
                            product.image_url
                                ? `
                                    <img
                                        src="${esc(
                                            product.image_url
                                        )}"
                                        alt=""
                                    >
                                `
                                : '';

                        return `
                            <div class="seller-issue-item">
                                <div class="seller-issue-thumb">
                                    ${thumb}
                                </div>

                                <div class="seller-issue-info">
                                    <strong>
                                        ${esc(
                                            product.name ||
                                                'Product'
                                        )}
                                    </strong>

                                    <p>
                                        ${esc(text)}
                                    </p>
                                </div>

                                <div class="seller-issue-meta">
                                    <span
                                        class="seller-issue-status ${status}"
                                    >
                                        ${
                                            removed
                                                ? 'Removed'
                                                : 'Warning'
                                        }
                                    </span>

                                    <span class="seller-issue-date">
                                        ${esc(
                                            formatDate(
                                                product.updated_at ||
                                                    product.created_at
                                            )
                                        )}
                                    </span>
                                </div>
                            </div>
                        `;
                    }
                )
                .join('') ||
            `
                <p class="seller-issues-empty">
                    No recent issues recorded.
                </p>
            `;
    }

    function setTab(name) {
        const overview =
            name === 'overview';

        $('#sellerOverviewPanel').hidden =
            !overview;

        $('#sellerProductsPanel').hidden =
            overview;

        $$('.seller-details-tab').forEach(
            (tab) => {
                const active =
                    tab.dataset.tab === name;

                tab.classList.toggle(
                    'active',
                    active
                );

                tab.setAttribute(
                    'aria-selected',
                    String(active)
                );
            }
        );
    }

    async function openSeller(id) {
        const item = state.items.find(
            (s) =>
                String(s.id) === String(id)
        );

        if (!item) return;

        lastFocus =
            document.activeElement;

        renderSeller(item);
        setTab('overview');
        show(modals.seller);

        try {
            const { data } = await api(
                `/${id}`
            );

            if (
                data &&
                String(state.seller?.id) ===
                    String(id)
            ) {
                renderSeller({
                    ...item,
                    ...data,
                });
            }
        } catch (error) {
            console.error(error);
        }
    }

    function closeSeller() {
        hide(modals.seller);

        state.seller = null;

        lastFocus?.focus?.();
    }

    function openSuspendModal() {
        const seller = state.seller;

        if (!seller || !modals.suspend) return;

        const modal = modals.suspend;
        const isSuspended = String(seller.status || '').toLowerCase() === 'suspended';

        const title = $('#sellerSuspendTitle', modal);
        const intro = $('.decision-intro', modal);
        const submitButton = $('.decision-submit', modal);

        if (title) {
            title.textContent = isSuspended ? 'Reactivate Account' : 'Suspend Account';
        }

        if (intro) {
            intro.textContent = isSuspended
                ? 'This account is currently suspended. Reactivating it will restore access immediately. Are you sure you want to reactivate this account?'
                : "Suspending an account will temporarily disable the user’s access. You can reactivate the account anytime.";
        }

        if (submitButton) {
            submitButton.textContent = isSuspended ? 'Reactivate' : 'Suspend';
            submitButton.style.background = isSuspended ? '#E7F7EE' : '#FFE0E0';
            submitButton.style.borderColor = isSuspended ? '#2E9D5C' : '#D41F1F';
            submitButton.style.color = isSuspended ? '#1F7A45' : '#AE0000';
        }

        syncSellerSuspendButton(seller);

        const reasons = $('.decision-reasons', modal);
        const durationWrap = $('#sellerSuspendDurationWrap', modal);
        const detailsWrap = $('#sellerSuspendDetailsWrap', modal);

        if (reasons) reasons.style.display = isSuspended ? 'none' : 'block';
        if (durationWrap) durationWrap.style.display = isSuspended ? 'none' : 'block';
        if (detailsWrap) detailsWrap.style.display = isSuspended ? 'none' : 'block';

        $$('input[name="seller-suspend-reason"]', modal).forEach((input) => {
            input.checked = false;
        });

        const duration = $('#sellerSuspendDuration', modal);
        const details = $('#sellerSuspendDetails', modal);

        if (duration) duration.value = isSuspended ? '1' : '7';
        if (details) details.value = '';

        const counter = $('[data-counter]', modal);
        if (counter) counter.textContent = '0/300';

        show(modal);
        duration?.focus();
    }

    function closeSuspendModal() {
        hide(modals.suspend);
    }

    async function submitSuspend(button) {
        const seller = state.seller;

        if (!seller || !modals.suspend) return;

        const isSuspended = String(seller.status || '').toLowerCase() === 'suspended';
        const reason = $$('input[name="seller-suspend-reason"]', modals.suspend).find((input) => input.checked)?.value;
        const duration = Number($('#sellerSuspendDuration', modals.suspend)?.value || 0);
        const details = $('#sellerSuspendDetails', modals.suspend)?.value.trim() || '';

        if (!isSuspended && !reason) {
            $('.decision-reasons', modals.suspend)?.classList.add('has-error');
            return;
        }

        if (!isSuspended && (!Number.isFinite(duration) || duration < 1)) {
            $('#sellerSuspendDuration', modals.suspend)?.focus();
            return;
        }

        if (isSuspended && !window.confirm('Are you sure you want to reactivate the account?')) {
            return;
        }

        button.disabled = true;

        try {
            const payload = await api(
                `/${seller.id}/suspend`,
                {
                    method: 'POST',
                    body: JSON.stringify(
                        isSuspended
                            ? { action: 'reactivate' }
                            : {
                                  reason,
                                  duration,
                                  details,
                              }
                    ),
                }
            );

            closeSuspendModal();
            closeSeller();

            await loadSellers({
                silent: true,
            });

            showFlash(
                'success',
                payload?.message || (isSuspended ? 'Seller reactivated successfully.' : 'Seller suspended successfully.')
            );
        } catch (error) {
            console.error(error);

            showFlash(
                'error',
                isSuspended
                    ? 'Unable to reactivate this seller. Please try again.'
                    : 'Unable to suspend this seller. Please try again.'
            );
        } finally {
            button.disabled = false;
        }
    }

    async function suspendSeller(button) {
        const seller = state.seller;

        if (!seller) return;

        openSuspendModal();
    }

    /* ------------------------------------------------------------------ *
     * Product modal
     * ------------------------------------------------------------------ */

    const DETAIL_KEYS = [
        'brand',
        'material',
        'sizes',
        'size',
        'colors',
        'color',
        'quantity_per_pack',
        'weight',
        'item_weight',
        'country_of_origin',
        'origin',
        'country',
        'subcategory',
    ];

    function specMap(source) {
        const entries = Array.isArray(source)
            ? source
                  .filter(
                      (i) =>
                          i &&
                          typeof i ===
                              'object'
                  )
                  .map((i) => [
                      i.key ||
                          i.name ||
                          'detail',
                      i.value ??
                          i.label ??
                          '',
                  ])
            : Object.entries(
                  source &&
                      typeof source ===
                          'object'
                      ? source
                      : {}
              );

        return Object.fromEntries(
            entries.filter(
                ([, v]) =>
                    v !== null &&
                    v !== undefined &&
                    display(v) !== '—'
            )
        );
    }

    function toOptions(items) {
        const list = Array.isArray(items)
            ? items
            : items &&
                typeof items === 'object'
              ? Object.entries(items).map(
                    ([key, v]) =>
                        v &&
                        typeof v === 'object'
                            ? {
                                  name: key,
                                  ...v,
                              }
                            : {
                                  name: String(
                                      v || key
                                  ),
                              }
                )
              : [];

        return list
            .filter(
                (i) =>
                    i !== null &&
                    i !== undefined
            )
            .map((i) =>
                typeof i === 'object'
                    ? {
                          name:
                              i.name ||
                              i.label ||
                              i.value ||
                              'Option',
                          price: Number(
                              i.price ??
                                  i.amount ??
                                  0
                          ),
                          stock: Number(
                              i.stock ??
                                  i.quantity ??
                                  0
                          ),
                      }
                    : {
                          name: String(i),
                          price: 0,
                          stock: null,
                      }
            );
    }

    const optionLine = (o) =>
        `${o.name}${
            o.price > 0
                ? ` — ${peso(o.price)}`
                : ''
        }${
            o.stock !== null
                ? ` — Stock: ${o.stock}`
                : ''
        }`;

    const optionNames = (items) =>
        toOptions(items)
            .map((o) => o.name)
            .join(', ');

    function productPhotos(product) {
        if (
            Array.isArray(product.photos) &&
            product.photos.length
        ) {
            return product.photos;
        }

        return product.image_url
            ? [product.image_url]
            : [];
    }

    function renderProductImages(product) {
        const photos =
            productPhotos(product);

        const main =
            $('#sellerProductMainImage');

        main.innerHTML = photos.length
            ? `
                <img
                    src="${esc(
                        photos[
                            state.imageIndex
                        ] || photos[0]
                    )}"
                    alt="${esc(
                        product.name ||
                            'Product'
                    )}"
                >
            `
            : `
                <div class="seller-product-image-placeholder">
                    No image
                </div>
            `;

        $('#sellerProductThumbnails').innerHTML =
            photos.length > 1
                ? photos
                      .map(
                          (src, i) => `
                            <button
                                type="button"
                                class="seller-product-thumbnail${
                                    i ===
                                    state.imageIndex
                                        ? ' active'
                                        : ''
                                }"
                                data-action="product-image"
                                data-index="${i}"
                                aria-label="Product image ${
                                    i + 1
                                }"
                            >
                                <img
                                    src="${esc(
                                        src
                                    )}"
                                    alt=""
                                >
                            </button>
                        `
                      )
                      .join('')
                : '';

        $('#sellerProductThumbNext').hidden =
            photos.length < 2;
    }

    function renderProduct(product) {
        const status =
            productStatus(product);

        const specs = specMap(
            product.specifications
        );

        const spec = (...keys) => {
            const key = keys.find(
                (k) =>
                    specs[k] !== undefined
            );

            return key
                ? specs[key]
                : '';
        };

        bind(modals.product, {
            title: product.name,
            price: peso(product.price),
            sold: `${Number(
                product.sold_count || 0
            )} sold`,
            category:
                cfg.categories?.[
                    product.category
                ] ||
                product.category,
            stock: `${Number(
                product.stock_quantity || 0
            )} pieces`,
            uploaded: formatDate(
                product.created_at
            ),
            description:
                product.description,
            brand: spec('brand'),
            material: spec('material'),
            sizes:
                optionNames(
                    product.sizes
                ) ||
                spec('sizes', 'size'),
            colors:
                optionNames(
                    product.colors
                ) ||
                spec('colors', 'color'),
            quantity: spec(
                'quantity_per_pack',
                'weight',
                'item_weight'
            ),
            origin: spec(
                'country_of_origin',
                'origin',
                'country'
            ),
        });

        renderProductImages(product);

        /* Buyer options */
        const options = [
            product.variations,
            product.colors,
            product.sizes,
        ]
            .flatMap(toOptions)
            .map(optionLine);

        $('#sellerProductBuyerOptionsList').innerHTML =
            options
                .map(
                    (line) => `
                        <div class="seller-product-option-row">
                            ${esc(line)}
                        </div>
                    `
                )
                .join('');

        $('#sellerProductBuyerOptionsSection').hidden =
            !options.length;

        /* Extra specifications */
        const extra = Object.entries(
            specs
        ).filter(
            ([key]) =>
                !DETAIL_KEYS.includes(key)
        );

        $('#sellerProductSpecificationsList').innerHTML =
            extra
                .map(
                    ([key, value]) => `
                        <div class="seller-product-spec-row">
                            <span>
                                ${esc(
                                    titleCase(
                                        key.replace(
                                            /^category_specifications\[\]$/g,
                                            ''
                                        )
                                    )
                                )}
                            </span>

                            <strong>
                                ${esc(
                                    display(value)
                                )}
                            </strong>
                        </div>
                    `
                )
                .join('');

        $('#sellerProductSpecificationsSection').hidden =
            !extra.length;

        /* Compliance notice */
        const notice =
            $('#sellerProductComplianceNotice');

        const reason =
            status === 'removed'
                ? product.remove_reason ||
                  product.archive_reason
                : product.warning_reason;

        const details =
            status === 'removed'
                ? product.remove_details ||
                  product.archive_details
                : product.warning_details;

        const copy = {
            approved: [
                'Approved',
                'This product is approved and available.',
            ],

            warning: [
                'Warning Issued',
                `${
                    reason ||
                    'A compliance issue was identified for this product.'
                }${
                    details
                        ? ` — ${details}`
                        : ''
                }`,
            ],

            removed: [
                'Removed',
                `${
                    reason ||
                    "This product has been removed from the seller's store."
                }${
                    details
                        ? ` — ${details}`
                        : ''
                }`,
            ],

            'under-review': [
                'Under Review',
                'This product is awaiting a compliance decision.',
            ],
        }[status];

        notice.className =
            `seller-product-compliance-notice ${status}`;

        notice.innerHTML = `
            <strong>
                ${esc(copy[0])}
            </strong>

            <span>
                ${esc(copy[1])}
            </span>
        `;

        notice.hidden = false;

        /*
         * Moderation buttons only while under review.
         */
        const underReview =
            status === 'under-review';

        $$(
            '[data-action="product-remove"], [data-action="product-warn"], [data-action="product-approve"]',
            modals.product
        ).forEach((button) => {
            button.hidden = !underReview;
        });

        const closeAction = $(
            '[data-action="product-close"].seller-product-close-action',
            modals.product
        );

        if (closeAction) {
            closeAction.hidden =
                underReview;
        }
    }

    function openProduct(id) {
        const product = (
            state.seller?.products || []
        ).find(
            (p) =>
                String(p.id) === String(id)
        );

        if (!product) return;

        state.product = product;
        state.imageIndex = 0;

        renderProduct(product);
        show(modals.product);
    }

    function closeProduct() {
        hide(modals.product);
        state.product = null;
    }

    /* ------------------------------------------------------------------ *
     * Moderation
     * ------------------------------------------------------------------ */

    function applyDecision(
        product,
        type,
        reason = '',
        details = ''
    ) {
        const cleared = {
            warning_reason: '',
            warning_details: '',
            remove_reason: '',
            remove_details: '',
            archive_reason: '',
            archive_details: '',
            is_archived: false,
        };

        const changes = {
            approve: {
                status: 'active',
            },

            warn: {
                status: 'warning',
                warning_reason: reason,
                warning_details: details,
            },

            remove: {
                status: 'archived',
                is_archived: true,
                remove_reason: reason,
                remove_details: details,

                /*
                 * Match the Laravel controller.
                 */
                archive_reason: reason,
                archive_details: details,
            },
        }[type];

        Object.assign(
            product,
            cleared,
            changes,
            {
                updated_at:
                    new Date().toISOString(),
            }
        );
    }

    async function moderate(
        type,
        body
    ) {
        const product =
            state.product;

        if (!product) return false;

        try {
            const payload =
                await api(
                    `/products/${product.id}/${type}`,
                    {
                        method: 'POST',

                        ...(body
                            ? {
                                  body: JSON.stringify(
                                      body
                                  ),
                              }
                            : {}),
                    }
                );

            applyDecision(
                product,
                type,
                body?.reason,
                body?.details
            );

            if (
                payload?.data &&
                typeof payload.data ===
                    'object'
            ) {
                Object.assign(
                    product,
                    payload.data
                );
            }

            renderProducts();
            renderIssues();

            /*
             * Keep table score and summary synchronized.
             */
            loadSellers({
                silent: true,
            });

            return true;
        } catch (error) {
            console.error(error);

            showFlash(
                'error',
                {
                    approve:
                        'Unable to approve this product.',
                    warn:
                        'Unable to issue a warning for this product. Please try again.',
                    remove:
                        'Unable to remove this product. Please try again.',
                }[type] ||
                    'Unable to update this product.'
            );

            return false;
        }
    }

    async function approveProduct(
        button
    ) {
        if (
            !state.product ||
            productStatus(state.product) !==
                'under-review'
        ) {
            return;
        }

        button.disabled = true;

        const ok =
            await moderate('approve');

        button.disabled = false;

        if (ok) {
            renderProduct(
                state.product
            );

            showFlash(
                'approved',
                state.product.name
            );
        }
    }

    /* ------------------------------------------------------------------ *
     * Decision modals
     * ------------------------------------------------------------------ */

    function openDecision(type) {
        const modal =
            modals[type];

        if (!modal) return;

        $$(
            'input[type="radio"]',
            modal
        ).forEach(
            (input) => {
                input.checked = false;
            }
        );

        $('.decision-reasons', modal)
            ?.classList.remove(
                'has-error'
            );

        const textarea =
            $('textarea', modal);

        if (textarea) {
            textarea.value = '';
        }

        $('[data-counter]', modal).textContent =
            '0/300';

        hide(modals.product);
        show(modal);

        $('.decision-cancel', modal)?.focus();
    }

    function closeDecision(type) {
        hide(modals[type]);

        if (state.product) {
            show(modals.product);
        }
    }

    async function submitDecision(
        type,
        button
    ) {
        const modal =
            modals[type];

        if (!modal) return;

        const reason = $(
            'input[type="radio"]:checked',
            modal
        )?.value;

        if (!reason) {
            $('.decision-reasons', modal)
                ?.classList.add(
                    'has-error'
                );

            return;
        }

        button.disabled = true;

        const textarea =
            $('textarea', modal);

        const ok =
            await moderate(type, {
                reason,
                details:
                    textarea?.value.trim() ||
                    '',
            });

        button.disabled = false;

        if (!ok) return;

        hide(modal);

        renderProduct(
            state.product
        );

        show(modals.product);

        showFlash(
            type === 'warn'
                ? 'warning'
                : 'removed',
            state.product.name
        );
    }

    /* ------------------------------------------------------------------ *
     * Event delegation
     * ------------------------------------------------------------------ */

    const decisionType = (node) =>
        node.closest(
            '[data-decision]'
        )?.dataset.decision;

    const actions = {
        refresh: () => {
            ui.refreshIcon?.classList.add(
                'compliance-spin'
            );

            if (ui.search) {
                ui.search.value = '';
            }

            if (ui.category) {
                ui.category.value = 'all';
            }

            if (ui.compliance) {
                ui.compliance.value = 'all';
            }

            state.page = 1;

            loadSellers().finally(() =>
                setTimeout(
                    () =>
                        ui.refreshIcon?.classList.remove(
                            'compliance-spin'
                        ),
                    350
                )
            );
        },

        'page-prev': () =>
            goToPage(state.page - 1),

        'page-next': () =>
            goToPage(state.page + 1),

        'page-go': (node) =>
            goToPage(
                Number(node.dataset.page)
            ),

        'close-seller': closeSeller,

        'tab-overview': () =>
            setTab('overview'),

        'tab-products': () =>
            setTab('products'),

        suspend: (node) =>
            suspendSeller(node),

        'product-close': closeProduct,

        'product-remove': () =>
            openDecision('remove'),

        'product-warn': () =>
            openDecision('warn'),

        'product-approve': (node) =>
            approveProduct(node),

        'product-image': (node) => {
            state.imageIndex =
                Number(
                    node.dataset.index
                );

            renderProductImages(
                state.product
            );
        },

        'product-next-image': () => {
            const total =
                productPhotos(
                    state.product || {}
                ).length;

            if (total < 2) return;

            state.imageIndex =
                (state.imageIndex + 1) %
                total;

            renderProductImages(
                state.product
            );
        },

        'decision-cancel': (node) => {
            const type =
                decisionType(node);

            if (type) {
                closeDecision(type);
            }
        },

        'seller-suspend-cancel': () => {
            closeSuspendModal();
        },

        'seller-suspend-submit': (node) => {
            submitSuspend(node);
        },

        'decision-submit': (node) => {
            const type =
                decisionType(node);

            if (type) {
                submitDecision(
                    type,
                    node
                );
            }
        },

        'flash-close': hideFlash,
    };

    document.addEventListener(
        'click',
        (event) => {
            /*
             * Backdrop click closes the matching modal.
             */
            if (
                event.target.classList?.contains(
                    'sc-overlay'
                )
            ) {
                closeTopModal();
                return;
            }

            const actionNode =
                event.target.closest(
                    '[data-action]'
                );

            if (
                actionNode &&
                root?.contains(actionNode) &&
                actions[
                    actionNode.dataset.action
                ]
            ) {
                actions[
                    actionNode.dataset.action
                ](actionNode, event);

                return;
            }

            const row =
                event.target.closest(
                    '.seller-compliance-row'
                );

            if (row) {
                return openSeller(
                    row.dataset.id
                );
            }

            const card =
                event.target.closest(
                    '.seller-product-card'
                );

            if (card) {
                openProduct(
                    card.dataset.productId
                );
            }
        }
    );

    document.addEventListener(
        'keydown',
        (event) => {
            if (event.key === 'Escape') {
                return closeTopModal();
            }

            if (
                event.key === 'Enter' ||
                event.key === ' '
            ) {
                const target =
                    event.target.closest?.(
                        '.seller-compliance-row, .seller-product-card'
                    );

                if (!target) return;

                event.preventDefault();

                if (
                    target.classList.contains(
                        'seller-compliance-row'
                    )
                ) {
                    openSeller(
                        target.dataset.id
                    );
                } else {
                    openProduct(
                        target.dataset.productId
                    );
                }
            }
        }
    );

    function closeTopModal() {
        if (
            modals.suspend &&
            !modals.suspend.hidden
        ) {
            return closeSuspendModal();
        }

        if (
            modals.remove &&
            !modals.remove.hidden
        ) {
            return closeDecision(
                'remove'
            );
        }

        if (
            modals.warn &&
            !modals.warn.hidden
        ) {
            return closeDecision(
                'warn'
            );
        }

        if (
            modals.product &&
            !modals.product.hidden
        ) {
            return closeProduct();
        }

        if (
            modals.seller &&
            !modals.seller.hidden
        ) {
            return closeSeller();
        }
    }

    /* ------------------------------------------------------------------ *
     * Decision modal listeners
     * ------------------------------------------------------------------ */

    ['warn', 'remove'].forEach(
        (type) => {
            const modal =
                modals[type];

            if (!modal) return;

            const textarea =
                $('textarea', modal);

            if (textarea) {
                textarea.addEventListener(
                    'input',
                    () => {
                        const counter =
                            $('[data-counter]', modal);

                        if (counter) {
                            counter.textContent =
                                `${textarea.value.length}/${textarea.maxLength}`;
                        }
                    }
                );
            }

            modal.addEventListener(
                'change',
                () =>
                    $('.decision-reasons', modal)
                        ?.classList.remove(
                            'has-error'
                        )
            );
        }
    );

    /* ------------------------------------------------------------------ *
     * Filters
     * ------------------------------------------------------------------ */

    let searchTimer = null;

    const reload = () => {
        state.page = 1;
        loadSellers();
    };

    ui.search?.addEventListener(
        'input',
        () => {
            clearTimeout(
                searchTimer
            );

            searchTimer = setTimeout(
                reload,
                300
            );
        }
    );

    ui.category?.addEventListener(
        'change',
        reload
    );

    ui.compliance?.addEventListener(
        'change',
        reload
    );

    ui.perPage?.addEventListener(
        'change',
        () => {
            state.perPage =
                Number(
                    ui.perPage.value
                ) ||
                cfg.perPage ||
                7;

            reload();
        }
    );

    /* ------------------------------------------------------------------ *
     * Sidebar
     * ------------------------------------------------------------------ */

    function syncWithSidebar() {
        const sidebar = $(
            '#admin-sidebar, #adminSidebar, #sellerSidebar, [data-admin-sidebar], .admin-sidebar'
        );

        if (!sidebar || !root) return;

        const baseMargin =
            parseFloat(
                getComputedStyle(
                    root
                ).marginLeft
            ) || 0;

        const gap =
            baseMargin -
            (
                sidebar.getBoundingClientRect()
                    .width || 256
            );

        const COLLAPSED_WIDTH = 80;

        const apply = () => {
            if (
                window.innerWidth <= 900
            ) {
                root.style.removeProperty(
                    'margin-left'
                );

                return;
            }

            root.style.marginLeft =
                sidebar.classList.contains(
                    'w-20'
                )
                    ? `${COLLAPSED_WIDTH + gap}px`
                    : `${baseMargin}px`;
        };

        new MutationObserver(() =>
            requestAnimationFrame(
                apply
            )
        ).observe(sidebar, {
            attributes: true,
            attributeFilter: ['class'],
        });

        window.addEventListener(
            'resize',
            apply
        );

        apply();
    }

    /* ------------------------------------------------------------------ *
     * Boot
     * ------------------------------------------------------------------ */

    syncWithSidebar();

    /*
     * First paint from server data.
     * No extra request is required on initial page load.
     */
    applyPayload(
        cfg.initial || {}
    );
}
