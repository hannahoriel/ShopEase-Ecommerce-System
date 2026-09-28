{{-- partials/seller_products-modal.blade.php --}}
<div id="sellerProductDetailsModal" class="sc-overlay sc-overlay--product" aria-hidden="true" hidden>
    <div class="seller-product-details-dialog" role="dialog" aria-modal="true" aria-labelledby="sellerProductModalTitle" tabindex="-1">

        <button type="button" class="seller-product-back" data-action="product-close" aria-label="Back to products">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                <path d="M19 12H5" /><path d="M11 6l-6 6 6 6" />
            </svg>
        </button>

        <div class="seller-product-main-card">
            <div class="seller-product-gallery">
                <div id="sellerProductMainImage" class="seller-product-main-image"></div>
                <div id="sellerProductThumbnails" class="seller-product-thumbnails"></div>
                <button type="button" id="sellerProductThumbNext" class="seller-product-thumb-next"
                        data-action="product-next-image" aria-label="Next product image">›</button>
            </div>

            <div class="seller-product-main-info">
                <h2 id="sellerProductModalTitle" data-bind="title"></h2>

                <div class="seller-product-price-row">
                    <strong data-bind="price"></strong>
                    <span data-bind="sold"></span>
                </div>

                <div class="seller-product-field">
                    <span>Category</span>
                    <div class="seller-product-category-pill" data-bind="category"></div>
                </div>

                <div class="seller-product-field seller-product-stock-field">
                    <span>Stock</span>
                    <strong data-bind="stock"></strong>
                </div>

                <div id="sellerProductComplianceNotice" class="seller-product-compliance-notice" hidden></div>
            </div>

            <div class="seller-product-uploaded">Uploaded: <strong data-bind="uploaded"></strong></div>
        </div>

        <div class="seller-product-description-card">
            <h3>Product Description</h3>
            <p data-bind="description"></p>

            <div class="seller-product-divider"></div>

            <h3>Product Details</h3>
            <div class="seller-product-detail-grid">
                <span>Brand</span><strong data-bind="brand"></strong>
                <span>Material</span><strong data-bind="material"></strong>
                <span>Sizes</span><strong data-bind="sizes"></strong>
                <span>Colors</span><strong data-bind="colors"></strong>
                <span>Quantity per Pack</span><strong data-bind="quantity"></strong>
                <span>Country of Origin</span><strong data-bind="origin"></strong>
            </div>
        </div>

        <div id="sellerProductBuyerOptionsSection" class="seller-product-extra-section" hidden>
            <h3>Buyer Options</h3>
            <div id="sellerProductBuyerOptionsList" class="seller-product-extra-list"></div>
        </div>

        <div id="sellerProductSpecificationsSection" class="seller-product-extra-section" hidden>
            <h3>Product Specifications</h3>
            <div id="sellerProductSpecificationsList" class="seller-product-extra-list"></div>
        </div>

        {{-- Moderation buttons only show while the product is "under review" --}}
        <div id="sellerProductActions" class="seller-product-actions">
            <button type="button" class="seller-product-remove"  data-action="product-remove">Remove</button>
            <button type="button" class="seller-product-warning" data-action="product-warn">Issue Warning</button>
            <button type="button" class="seller-product-approve" data-action="product-approve">Approve</button>
            <button type="button" class="seller-product-close-action" data-action="product-close">Close</button>
        </div>
    </div>
</div>
