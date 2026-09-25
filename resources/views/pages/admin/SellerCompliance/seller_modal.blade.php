{{-- partials/seller-modal.blade.php --}}
<div id="sellerDetailsModal" class="sc-overlay sc-overlay--seller" aria-hidden="true" hidden>
    <div class="seller-details-dialog" role="dialog" aria-modal="true" aria-labelledby="sellerDetailsTitle" tabindex="-1">

        <div class="seller-details-title-row">
            <h2 id="sellerDetailsTitle">Seller Details</h2>
            <button type="button" class="seller-details-close" data-action="close-seller" aria-label="Close seller details">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round">
                    <path d="M6 6l12 12" /><path d="M18 6L6 18" />
                </svg>
            </button>
        </div>

        <div class="seller-details-profile-card">
            <div class="seller-details-avatar">
                <svg viewBox="0 0 100 100" aria-hidden="true">
                    <circle cx="50" cy="31" r="13" fill="currentColor" />
                    <path d="M23 70c0-11.5 10.5-21 27-21s27 9.5 27 21v3c0 2.8-2.2 5-5 5H28c-2.8 0-5-2.2-5-5z" fill="currentColor" />
                </svg>
            </div>

            <div class="seller-details-main">
                <div class="seller-details-store-wrap">
                    <h3 data-bind="storeName"></h3>
                    <p>Seller since <span data-bind="since"></span></p>
                </div>

                <div class="seller-details-contact-list">
                    <div>
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                            <circle cx="8" cy="8" r="3" /><circle cx="17" cy="9" r="2.5" />
                            <path d="M2.5 20c.5-4 2.4-6 5.5-6s5 2 5.5 6" /><path d="M13.5 15c2.9-.6 5.1.7 5.9 4" />
                        </svg>
                        <span data-bind="owner"></span>
                    </div>
                    <div>
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                            <rect x="3" y="5" width="18" height="14" rx="2" /><path d="M4 7l8 6 8-6" />
                        </svg>
                        <span data-bind="email"></span>
                    </div>
                    <div>
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                            <path d="M7 3h3l2 5-2.2 1.7a14.3 14.3 0 0 0 4.5 4.5L16 12l5 2v3c0 1.1-.9 2-2 2C10.7 19 5 13.3 5 5c0-1.1.9-2 2-2z" />
                        </svg>
                        <span data-bind="phone"></span>
                    </div>
                    <div>
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                            <path d="M12 21s7-6.3 7-12a7 7 0 1 0-14 0c0 5.7 7 12 7 12z" /><circle cx="12" cy="9" r="2.5" />
                        </svg>
                        <span data-bind="location"></span>
                    </div>
                </div>
            </div>

            <div class="seller-details-summary">
                <div class="seller-details-summary-block">
                    <span>Category</span>
                    <div id="sellerModalCategories" class="seller-modal-category-list"></div>
                </div>
                <div class="seller-details-summary-block">
                    <span>Total Products</span>
                    <strong data-bind="productsCount"></strong>
                </div>
            </div>
        </div>

        <div class="seller-details-tabs" role="tablist" aria-label="Seller details sections">
            <button type="button" class="seller-details-tab active" data-tab="overview" data-action="tab-overview"
                    role="tab" aria-selected="true">Compliance Overview</button>
            <button type="button" class="seller-details-tab" data-tab="products" data-action="tab-products"
                    role="tab" aria-selected="false">Products</button>
        </div>

        <div id="sellerOverviewPanel" class="seller-overview-panel">
            <div class="seller-overview-grid">
                <div class="seller-compliance-summary">
                    <p class="seller-overview-heading">Compliant Score</p>
                    <div id="sellerModalScore" class="seller-modal-score" data-bind="score"></div>
                    <div class="seller-modal-score-bar"><span id="sellerModalScoreFill"></span></div>
                    <span id="sellerModalCompliancePill" class="status-pill"></span>
                    <p id="sellerModalPolicyText" class="seller-policy-text"></p>
                </div>

                <div class="seller-documents-card">
                    <h4>Documents</h4>
                    <div id="sellerDocuments"></div>
                </div>
            </div>

            <div class="seller-issues-card">
                <h4>Recent Issues/Warnings</h4>
                <div id="sellerModalIssues" class="seller-issues-list"></div>
            </div>
        </div>

        <div id="sellerProductsPanel" class="seller-products-panel" hidden>
            <div id="sellerProductsGrid" class="seller-products-grid"></div>
        </div>

        <div class="seller-details-actions">
            <button type="button" class="seller-suspend-button" data-action="suspend">Suspend</button>
            <button type="button" class="seller-cancel-button" data-action="close-seller">Cancel</button>
        </div>
    </div>
</div>
