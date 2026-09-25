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

<div id="sellerSuspendModal" class="sc-overlay sc-overlay--decision" aria-hidden="true" hidden>
    <div class="decision-dialog decision-dialog--warn" role="dialog" aria-modal="true" aria-labelledby="sellerSuspendTitle" tabindex="-1">
        <h2 id="sellerSuspendTitle">Suspend Account</h2>
        <p class="decision-intro">Suspending an account will temporarily disable the user’s access. You can reactivate the account anytime.</p>

        <fieldset class="decision-reasons" style="display: block;">
            <legend>Reason<span>*</span></legend>

            <label class="decision-option">
                <input type="radio" name="seller-suspend-reason" value="Violation of platform policies">
                <span class="decision-radio"></span>
                <span>Violation of platform policies</span>
            </label>

            <label class="decision-option">
                <input type="radio" name="seller-suspend-reason" value="Inappropriate behavior">
                <span class="decision-radio"></span>
                <span>Inappropriate behavior</span>
            </label>

            <label class="decision-option">
                <input type="radio" name="seller-suspend-reason" value="Listing of prohibited products">
                <span class="decision-radio"></span>
                <span>Listing of prohibited products</span>
            </label>

            <label class="decision-option">
                <input type="radio" name="seller-suspend-reason" value="Fraudulent activity">
                <span class="decision-radio"></span>
                <span>Fraudulent activity</span>
            </label>

            <label class="decision-option">
                <input type="radio" name="seller-suspend-reason" value="Multiple complaints from users">
                <span class="decision-radio"></span>
                <span>Multiple complaints from users</span>
            </label>

            <label class="decision-option">
                <input type="radio" name="seller-suspend-reason" value="Other (please specify)">
                <span class="decision-radio"></span>
                <span>Other (please specify)</span>
            </label>
        </fieldset>

        <div id="sellerSuspendDurationWrap" class="decision-details" style="margin-top: 12px; display: block;">
            <label for="sellerSuspendDuration">Suspension Duration<span>*</span></label>
            <div class="decision-textarea-wrap" style="margin-top: 8px;">
                <input
                    id="sellerSuspendDuration"
                    type="number"
                    min="1"
                    step="1"
                    value="7"
                    placeholder="Select number of days"
                    class="seller-suspend-duration"
                    style="width: 100%; border: 1px solid #d1d5db; border-radius: 0.75rem; padding: 0.7rem 0.9rem; font-size: 0.9rem;"
                >
            </div>
            <div class="decision-help" style="margin-top: 8px; font-size: 0.8rem; color: #6b7280; line-height: 1.5;">
                After the selected number of days, the account will be automatically reactivated.
            </div>
        </div>

        <div id="sellerSuspendDetailsWrap" class="decision-details" style="margin-top: 14px; display: block;">
            <label for="sellerSuspendDetails">Additional Details (Optional)</label>
            <div class="decision-textarea-wrap">
                <textarea id="sellerSuspendDetails" maxlength="300" rows="3" placeholder="Write additional details here..."></textarea>
                <span data-counter>0/300</span>
            </div>
        </div>

        <div class="decision-actions">
            <button type="button" class="decision-cancel" data-action="seller-suspend-cancel">Cancel</button>
            <button type="button" class="decision-submit" data-action="seller-suspend-submit">Suspend</button>
        </div>
    </div>
</div>
