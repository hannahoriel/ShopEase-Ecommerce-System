<dialog id="sellerOrderReportDialog" class="seller-order-report-dialog" aria-labelledby="sellerOrderReportTitle">
    <form method="post" action="{{ route('seller.api.order-complaints.store') }}" data-order-report-form>
        @csrf
        <h2 id="sellerOrderReportTitle">Report Buyer</h2>
        <div class="seller-report-buyer"><span>Buyer being reported</span><strong data-reported-buyer>Buyer on this order</strong></div>
        <p>ShopEase will review the complaint. The buyer can view it and respond through ShopEase.</p>
        <label for="sellerReportReason">Reason</label>
        <select id="sellerReportReason" name="type" required>
            <option value="">Choose a reason</option>
            @foreach (\App\Http\Controllers\Api\Seller\OrderComplaintController::REASONS as $reason)
                <option value="{{ $reason }}">{{ $reason }}</option>
            @endforeach
        </select>
        <label for="sellerReportDescription">Description</label>
        <textarea id="sellerReportDescription" name="description" rows="4" minlength="10" maxlength="2000" placeholder="Describe what happened" required></textarea>
        <label for="sellerReportEvidence">Evidence photos</label>
        <div class="seller-report-photos">
            <label class="seller-report-attach" for="sellerReportEvidence">
                <span aria-hidden="true">+</span><strong>Attach</strong>
                <input type="file" id="sellerReportEvidence" accept="image/jpeg,image/png,image/webp" multiple aria-label="Attach evidence photos">
            </label>
            <div class="seller-report-previews" data-report-previews></div>
        </div>
        <small>Up to 5 JPG, PNG, or WebP images, 5 MB each.</small>
        <p class="seller-order-report-error" data-order-report-error role="alert" hidden></p>
        <div class="seller-order-report-actions">
            <button type="button" class="seller-order-report-button" data-order-report-close>Cancel</button>
            <button type="submit" class="seller-order-report-button seller-order-report-primary">Submit Report</button>
        </div>
    </form>
    <div data-order-report-success hidden>
        <h2 id="sellerOrderReportSuccessTitle">Report Submitted</h2>
        <p data-order-report-reference role="status"></p>
        <div class="seller-order-report-actions">
            <button type="button" class="seller-order-report-button seller-order-report-primary" data-order-report-close>Done</button>
        </div>
    </div>
</dialog>
<style>
    .seller-order-report-heading { display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:8px; }
    .seller-order-report-button { padding:7px 13px; border:1px solid #e8d6d2; border-radius:8px; background:#fff; color:#791820; font:600 13px Poppins,sans-serif; cursor:pointer; }
    .seller-order-report-button:disabled { opacity:.5; cursor:default; }
    .seller-order-report-primary { background:#791820; border-color:#791820; color:#fff; }
    .seller-order-report-dialog { position:fixed; inset:0; margin:auto; width:min(620px,calc(100% - 32px)); max-height:calc(100dvh - 32px); overflow:auto; box-sizing:border-box; border:1px solid #eadbd6; border-radius:18px; padding:24px; background:#fff; color:#211715; font:400 14px Poppins,sans-serif; }
    .seller-order-report-dialog::backdrop { background:rgba(20,5,6,.42); backdrop-filter:blur(7px); }
    .seller-order-report-dialog h2 { margin:0 0 8px; font-size:20px; font-weight:600; }
    .seller-order-report-dialog p { margin:0 0 12px; }
    .seller-order-report-dialog label { display:block; margin:14px 0 5px; font-weight:600; }
    .seller-order-report-dialog :is(select,textarea,input) { box-sizing:border-box; width:100%; padding:9px 10px; border:1px solid #dfd3ce; border-radius:8px; background:#fff; color:inherit; font:inherit; }
    .seller-order-report-dialog textarea { resize:vertical; }
    .seller-order-report-dialog small { display:block; margin-top:6px; color:#756660; }
    .seller-order-report-actions { display:flex; justify-content:flex-end; gap:8px; margin-top:18px; }
    .seller-order-report-dialog .seller-order-report-error { color:#a42020; margin-top:10px; }
    .seller-report-buyer { display:grid; gap:3px; padding:12px 14px; margin:14px 0; border:1px solid #eadbd6; border-radius:12px; background:#fffbfa; }
    .seller-report-buyer span { font-size:12px; color:#756660; }
    .seller-report-photos { display:flex; flex-wrap:wrap; gap:12px; align-items:center; }
    .seller-report-previews { display:contents; }
    .seller-order-report-dialog label.seller-report-attach { position:relative; display:grid; place-content:center; justify-items:center; gap:4px; width:76px; height:76px; margin:0; border:1px solid #dfceca; border-radius:12px; color:#791820; cursor:pointer; overflow:hidden; }
    .seller-report-attach span { display:grid; place-items:center; width:34px; height:34px; border-radius:9px; background:#fae9e6; font-size:25px; line-height:1; }
    .seller-report-attach strong { font-size:12px; font-weight:500; }
    .seller-order-report-dialog .seller-report-attach input { position:absolute; inset:0; height:100%; opacity:0; cursor:pointer; }
    .seller-report-attach:focus-within { outline:2px solid #9ca3af; outline-offset:2px; }
    .seller-report-photo { position:relative; width:76px; height:76px; }
    .seller-report-photo img { width:100%; height:100%; object-fit:cover; border:1px solid #dfceca; border-radius:10px; }
    .seller-report-photo button { position:absolute; right:-4px; top:-4px; width:23px; height:23px; border:1px solid #dfceca; border-radius:50%; background:#fff; color:#791820; cursor:pointer; }
    .seller-order-report-dialog [hidden] { display:none!important; }
</style>
<script>
    (() => {
        const dialog = document.getElementById('sellerOrderReportDialog');
        const form = dialog.querySelector('[data-order-report-form]');
        const success = dialog.querySelector('[data-order-report-success]');
        const error = dialog.querySelector('[data-order-report-error]');
        const submit = form.querySelector('[type=submit]');
        const picker = form.querySelector('[type=file]');
        const previews = form.querySelector('[data-report-previews]');
        let trigger;
        let photos = [];

        function removePhoto(index) {
            URL.revokeObjectURL(photos[index].url);
            photos.splice(index, 1);
            renderPhotos();
        }

        function renderPhotos() {
            previews.replaceChildren(...photos.map(({ file, url }, index) => {
                const tile = document.createElement('div');
                tile.className = 'seller-report-photo';
                const image = new Image();
                image.src = url;
                image.alt = file.name;
                const remove = document.createElement('button');
                remove.type = 'button';
                remove.textContent = '×';
                remove.setAttribute('aria-label', 'Remove ' + file.name);
                remove.addEventListener('click', () => removePhoto(index));
                tile.append(image, remove);
                return tile;
            }));
        }

        picker.addEventListener('change', () => {
            const files = [...picker.files];
            picker.value = '';
            if (photos.length + files.length > 5 || files.some(file => file.size > 5 * 1024 * 1024 || !['image/jpeg', 'image/png', 'image/webp'].includes(file.type))) {
                error.textContent = 'Attach up to 5 JPG, PNG, or WebP photos, 5 MB each.';
                error.hidden = false;
                return;
            }
            error.hidden = true;
            photos.push(...files.map(file => ({ file, url: URL.createObjectURL(file) })));
            renderPhotos();
        });

        document.addEventListener('click', event => {
            const button = event.target.closest('[data-seller-order-report]');
            if (!button || button.disabled || dialog.open) return;
            trigger = button;
            form.querySelector('[data-reported-buyer]').textContent = button.dataset.buyerName || 'Buyer on this order';
            form.reset();
            form.hidden = false;
            success.hidden = true;
            error.hidden = true;
            dialog.setAttribute('aria-labelledby', 'sellerOrderReportTitle');
            dialog.showModal();
        });
        dialog.addEventListener('click', event => {
            if (event.target.closest('[data-order-report-close]') && !submit.disabled) dialog.close();
        });
        dialog.addEventListener('cancel', event => {
            event.preventDefault();
            event.stopPropagation();
            if (!submit.disabled) dialog.close();
        });
        dialog.addEventListener('keydown', event => {
            if (event.key === 'Escape') event.stopPropagation();
        });
        dialog.addEventListener('close', () => {
            photos.forEach(photo => URL.revokeObjectURL(photo.url));
            photos = [];
            renderPhotos();
            trigger?.focus();
        });

        form.addEventListener('submit', async event => {
            event.preventDefault();
            if (submit.disabled || !trigger) return;
            error.hidden = true;
            if (!photos.length) {
                error.textContent = 'Attach at least one evidence photo.';
                error.hidden = false;
                return;
            }
            const body = new FormData(form);
            photos.forEach(({ file }) => body.append('evidence[]', file));
            if (trigger.dataset.orderId) body.set('order_id', trigger.dataset.orderId);
            else body.set('order_number', trigger.dataset.orderNumber);
            const stopLoading = window.ShopEaseLoading?.startBag?.(submit);
            submit.disabled = true;
            try {
                const response = await fetch(form.action, {
                    method: 'POST',
                    headers: { Accept: 'application/json', 'X-CSRF-TOKEN': body.get('_token') },
                    body,
                });
                const data = await response.json().catch(() => ({}));
                if (!response.ok) throw new Error(Object.values(data.errors || {}).flat()[0] || data.message || 'Unable to submit the report.');
                stopLoading?.();
                form.hidden = true;
                success.hidden = false;
                dialog.setAttribute('aria-labelledby', 'sellerOrderReportSuccessTitle');
                success.querySelector('[data-order-report-reference]').textContent = data.reference + ' · ShopEase will review your complaint.';
                success.querySelector('button').focus();
            } catch (failure) {
                error.textContent = failure.message;
                error.hidden = false;
            } finally {
                stopLoading?.();
                submit.disabled = false;
            }
        });
    })();
</script>

