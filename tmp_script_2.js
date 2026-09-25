
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
            });
        });
    