{{-- Admin Product Details: mirrors Seller Inventory's read-only Product Details UI --}}
<div id="sellerProductDetailsModal" class="sc-overlay sc-overlay--product" aria-hidden="true" hidden>
  <div class="sc-inventory-product-dialog product-details-inspo-panel" role="dialog" aria-modal="true" aria-labelledby="sellerProductModalTitle" tabindex="-1">
    <header class="product-details-inspo-header">
      <h2 id="sellerProductModalTitle">Product Details</h2>
      <button type="button" class="product-details-inspo-close" data-action="product-close" aria-label="Close product details">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M6 6l12 12M18 6L6 18"/></svg>
      </button>
    </header>
    <div class="product-details-inspo-body">
      <aside class="product-details-inspo-gallery">
        <div class="product-details-inspo-main-photo" id="sellerProductMainImage"><div class="product-details-image-fallback">No image</div></div>
        <div class="product-details-thumbnail-list" id="sellerProductThumbnails"></div>
        <button class="sc-photo-next" type="button" id="sellerProductThumbNext" data-action="product-next-image" aria-label="Next product photo">›</button>
      </aside>
      <section class="product-details-inspo-content">
        <div class="product-details-inspo-summary">
          <h3 class="product-details-title-display" data-bind="title">Product</h3>
          <div class="product-details-inspo-sku"><span>SKU:</span><strong data-bind="sku">—</strong></div>
          <div class="product-details-inspo-badges">
            <span class="product-details-category-display" data-bind="category">—</span>
            <span class="product-details-soft-badge" data-bind="subcategory">—</span>
            <span class="sc-product-status" id="sellerProductStatusBadge">Under Review</span>
          </div>
          <div class="product-details-inspo-price"><strong data-bind="price">₱0.00</strong></div>
          <div class="product-details-inspo-pricing-meta">
            <div><span>Pricing Type</span><strong data-bind="pricingType">—</strong></div>
            <div><span>Base Price Source</span><strong data-bind="pricingSource">—</strong></div>
          </div>
        </div>
        <section id="sellerProductArchiveInfo" class="sc-product-archive-notice" hidden>
          <h3>Archived Product</h3><p>This listing was archived and is read-only.</p>
          <div class="sc-product-archive-grid">
            <div><span>Archived by</span><strong data-bind="archivedBy">—</strong></div>
            <div><span>Date archived</span><strong data-bind="archivedAt">—</strong></div>
            <div><span>Reason for archiving</span><strong data-bind="archiveReason">—</strong></div>
            <div><span>Additional details</span><strong data-bind="archiveDetails">—</strong></div>
          </div>
        </section>
        <nav class="product-details-inspo-tabs" role="tablist" aria-label="Product details sections">
          <button type="button" class="product-details-tab is-active" data-action="admin-product-tab" data-admin-product-tab="details" role="tab" aria-selected="true">Details</button>
          <button type="button" class="product-details-tab" data-action="admin-product-tab" data-admin-product-tab="variations" role="tab" aria-selected="false">Variations <span id="sellerProductVariationCount" class="product-details-tab-count">0</span></button>
          <button type="button" class="product-details-tab" data-action="admin-product-tab" data-admin-product-tab="stock-history" role="tab" aria-selected="false">Stock history</button>
          <button type="button" class="product-details-tab" data-action="admin-product-tab" data-admin-product-tab="price-history" role="tab" aria-selected="false">Price history</button>
        </nav>
        <div class="product-details-inspo-tab-scroll">
          <div class="product-details-tab-panel" data-admin-product-panel="details">
            <div class="product-details-inspo-basic-grid">
              <div class="product-details-inspo-detail-item"><span>Total stock (all variations)</span><strong data-bind="stock">—</strong></div>
              <div class="product-details-inspo-detail-item"><span>Date added</span><strong data-bind="uploaded">—</strong></div>
              <div class="product-details-inspo-detail-item"><span>Pricing type</span><strong data-bind="pricingType">—</strong></div>
              <div class="product-details-inspo-detail-item"><span>Base price source</span><strong data-bind="pricingSource">—</strong></div>
              <div class="product-details-inspo-detail-item"><span>Variation</span><strong data-bind="variationNames">—</strong></div>
              <div class="product-details-inspo-detail-item"><span>Color</span><strong data-bind="colors">—</strong></div>
              <div class="product-details-inspo-detail-item"><span>Size</span><strong data-bind="sizes">—</strong></div>
            </div>
            <section class="product-details-inspo-specs" id="sellerProductSpecificationsSection">
              <h3>Product Specifications</h3>
              <div id="sellerProductSpecificationsList" class="product-created-details-grid product-details-inspo-spec-grid"></div>
            </section>
            <section class="sc-product-compliance-notice" id="sellerProductComplianceNotice" hidden></section>
            <div class="product-details-inspo-description"><h3>Product Description</h3><p data-bind="description">—</p></div>
          </div>
          <div class="product-details-tab-panel" data-admin-product-panel="variations" hidden>
            <section class="product-details-inspo-variations"><div id="sellerProductBuyerOptionsList" class="product-details-buyer-options-layout"></div></section>
          </div>
          <div class="product-details-tab-panel" data-admin-product-panel="stock-history" hidden>
            <div id="sellerProductStockHistory" class="product-details-history-table"></div>
          </div>
          <div class="product-details-tab-panel" data-admin-product-panel="price-history" hidden>
            <div id="sellerProductPriceHistory" class="product-details-history-table"></div>
          </div>
        </div>
      </section>
    </div>
    <footer class="product-details-inspo-footer sc-product-admin-footer" id="sellerProductActions">
      <button type="button" class="seller-product-remove" data-action="product-remove">Remove</button>
      <div>
        <button type="button" class="seller-product-warning" data-action="product-warn">Issue Warning</button>
        <button type="button" class="seller-product-approve" data-action="product-approve">Approve</button>
        <button type="button" class="seller-product-close-action" data-action="product-close">Close</button>
      </div>
    </footer>
  </div>
</div>
