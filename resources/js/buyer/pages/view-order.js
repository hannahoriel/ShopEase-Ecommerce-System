import QRCode from 'qrcode';

document.addEventListener('DOMContentLoaded', () => {
    const $ = id => document.getElementById(id);
    const config = JSON.parse($('voOrderData').textContent);
    const order = config.order, endpoint = `/api/v1/buyer/orders/${order.id}`;
    const esc = value => String(value ?? '').replace(/[&<>"']/g, c => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c]));
    let previousFocus, refreshOnClose = false, rating = 0, busy = false;
    const openModal = modal => { previousFocus=document.activeElement; modal.hidden=false; document.body.style.overflow='hidden'; modal.querySelector('button,select,input,textarea')?.focus(); };
    const closeModal = modal => { modal.hidden=true; if(!document.querySelector('.vo-modal-overlay:not([hidden])'))document.body.style.overflow=''; previousFocus?.focus(); };
    const notice = (title, text, refresh = false) => { $('voModalTitle').textContent=title; $('voModalText').textContent=text; refreshOnClose=refresh; openModal($('voModal')); };
    $('voModalOkay').addEventListener('click', () => { closeModal($('voModal')); if(refreshOnClose)location.reload(); });

    async function request(url, body, method='POST') {
        const multipart=body instanceof FormData;
        const response=await fetch(url,{method,credentials:'same-origin',headers:{Accept:'application/json',
            'X-CSRF-TOKEN':document.querySelector('meta[name="csrf-token"]').content,
            Authorization:`Bearer ${config.apiToken}`, ...(!multipart?{'Content-Type':'application/json'}:{})},body:multipart?body:JSON.stringify(body)});
        const payload=await response.json().catch(()=>({}));
        if(!response.ok)throw new Error(Object.values(payload.errors||{}).flat()[0]||payload.message||'Unable to complete this action. Please try again.');
        return payload;
    }
    async function perform(button, task, onError) {
        if(busy)return;
        button ||= document.activeElement;
        busy=true; button.disabled=true;
        const end=window.ShopEaseLoading?.start(button);
        try { await task(); } catch(error) { if(onError)onError(error.message); else notice('Action could not be completed',error.message); }
        finally { busy=false; button.disabled=false; end?.(); }
    }
    const error = (id,text) => { $(id).textContent=text; $(id).hidden=!text; };
    if(order.qr_payload)QRCode.toCanvas($('voOrderQr'),order.qr_payload,{width:160,margin:1,errorCorrectionLevel:'M'}).catch(()=>{});

    // Product reviews remain product-specific, including multi-item orders.
    const reviewFor = () => (order.reviews||[]).find(review=>String(review.product_id)===$('voRatingProduct').value);
    function stars() {
        document.querySelectorAll('[data-vo-star]').forEach(button=>{
            const active=Number(button.dataset.voStar)<=rating;
            button.textContent=active?'★':'☆'; button.setAttribute('aria-pressed',String(active));
            button.disabled=!!reviewFor();
        });
        $('voRatingSubmit').disabled=!rating||!!reviewFor();
    }
    function selectReview() {
        const saved=reviewFor(); rating=saved?.rating||0;
        $('voRatingHeading').textContent=saved?'Your Product Review':'Rate Your Purchase';
        $('voRatingReview').value=saved?.body||''; $('voRatingReview').readOnly=!!saved;
        $('voRatingAnonymous').checked=!!saved?.is_anonymous; $('voRatingAnonymous').disabled=!!saved;
        $('voRatingPhotos').value=''; $('voRatingPhotos').disabled=!!saved;
        $('voRatingUploadLabel').hidden=!!saved;
        $('voRatingPhotoPreviews').replaceChildren();
        if(saved?.photo_url){const image=document.createElement('img');image.src=saved.photo_url;image.alt='Your review photo';image.style.cssText='width:90px;height:90px;object-fit:cover;border-radius:8px';$('voRatingPhotoPreviews').append(image);}
        $('voRatingSubmit').hidden=!!saved; error('voRatingError',''); stars();
    }
    $('voRatingProduct').addEventListener('change',selectReview);
    document.querySelectorAll('[data-vo-star]').forEach(button=>button.addEventListener('click',()=>{rating=Number(button.dataset.voStar);stars();}));
    ['voRatingClose','voRatingCancel'].forEach(id=>$(id)?.addEventListener('click',()=>closeModal($('voRateModal'))));
    $('voRatingPhotos').addEventListener('change',()=>{
        const file=$('voRatingPhotos').files[0]; $('voRatingPhotoPreviews').replaceChildren();
        if(!file)return;
        if(!['image/jpeg','image/png','image/webp'].includes(file.type)||file.size>5*1024*1024){$('voRatingPhotos').value='';error('voRatingError','Choose a JPG, PNG or WEBP photo up to 5 MB.');return;}
        const image=document.createElement('img'); const url=URL.createObjectURL(file);
        image.onload=()=>URL.revokeObjectURL(url);image.src=url;image.alt='Review photo preview';image.style.cssText='width:90px;height:90px;object-fit:cover;border-radius:8px';$('voRatingPhotoPreviews').append(image);
    });
    $('voRatingForm').addEventListener('submit',event=>{
        event.preventDefault();if(!rating||!event.target.reportValidity())return;
        const body=new FormData();body.set('rating',rating);body.set('body',$('voRatingReview').value.trim());body.set('is_anonymous',$('voRatingAnonymous').checked?'1':'0');
        const photo=$('voRatingPhotos').files[0];if(photo)body.set('photo',photo);
        perform($('voRatingSubmit'),async()=>{await request(`/api/v1/buyer/products/${$('voRatingProduct').value}/reviews`,body);closeModal($('voRateModal'));notice('Review submitted','Your product review has been saved.',true);},text=>error('voRatingError',text));
    });
    const rateAction=$('voRateAction');
    if(rateAction && order.status!=='completed'){rateAction.disabled=true;}

    // Existing cancellation workflow: immediate cancellation or seller review.
    $('voCancelReason').addEventListener('change',()=>{$('voCancelOtherWrap').hidden=$('voCancelReason').value!=='other';$('voCancelOther').required=$('voCancelReason').value==='other';});
    ['voCancelClose','voCancelKeep'].forEach(id=>$(id).addEventListener('click',()=>closeModal($('voCancelModal'))));
    $('voCancelForm').addEventListener('submit',event=>{
        event.preventDefault();if(!event.target.reportValidity())return;
        perform(event.submitter,async()=>{const result=await request(`${endpoint}/cancel`,{reason:$('voCancelReason').value,other_reason:$('voCancelOther').value.trim()});closeModal($('voCancelModal'));notice('Cancellation update',result.message,true);},text=>error('voCancelError',text));
    });

    // Returns and complaints use the same order-scoped server actions as purchases.
    const overlay=document.createElement('div');overlay.className='vo-modal-overlay';overlay.hidden=true;
    overlay.innerHTML=`<form class="vo-modal" role="dialog" aria-modal="true" aria-labelledby="voRequestTitle" style="max-height:85dvh;overflow:auto;text-align:left"><button type="button" class="vo-rating-close" data-request-close aria-label="Close">×</button><h2 id="voRequestTitle"></h2><div id="voRequestFields"></div><p class="vo-rating-error" data-request-error hidden role="alert"></p><div class="vo-modal-buttons"><button type="button" class="vo-outline" data-request-close>Cancel</button><button type="submit" class="vo-primary">Submit Request</button></div></form>`;
    document.body.append(overlay);let requestMode='';
    function openRequest(mode){
        requestMode=mode;overlay.querySelector('form').reset();overlay.querySelector('[data-request-error]').hidden=true;
        $('voRequestTitle').textContent=mode==='return'?'Request Return / Refund':'Report a Problem';
        const fieldStyle='width:100%;padding:10px;border:1px solid #ddd;border-radius:8px;margin:6px 0 14px;font:inherit;font-weight:400';
        $('voRequestFields').innerHTML=mode==='return'
            ? `<label>Request type<select name="return_type" style="${fieldStyle}"><option value="return">Return items</option><option value="refund">Refund</option></select></label><p>Select items and quantities:</p>${order.items.map((item,index)=>`<label style="display:flex;align-items:center;gap:8px;margin:10px 0"><input type="checkbox" name="selected" value="${index}" checked><span style="flex:1">${esc(item.product_name)}</span><input type="number" data-quantity="${index}" min="1" max="${item.quantity}" value="${item.quantity}" style="width:60px;padding:6px"></label>`).join('')}<label>Reason<textarea name="reason" required maxlength="2000" rows="4" style="${fieldStyle}"></textarea></label><label>Additional evidence details (optional)<textarea name="evidence_description" maxlength="2000" rows="2" style="${fieldStyle}"></textarea></label><label>Evidence (up to 5 images or PDFs, 5 MB each)<input type="file" name="evidence" multiple accept="image/jpeg,image/png,image/webp,application/pdf" style="${fieldStyle}"></label>`
            : `<label>Issue type<select name="type" style="${fieldStyle}">${['Wrong Item','Missing Item','Damaged Item','Late Delivery','Order Not Received','Seller Conduct','Other'].map(type=>`<option>${type}</option>`).join('')}</select></label><label>Description<textarea name="description" required minlength="10" maxlength="2000" rows="5" style="${fieldStyle}"></textarea></label><label>Evidence (up to 5 photos, 5 MB each)<input type="file" name="evidence" multiple accept="image/jpeg,image/png,image/webp" style="${fieldStyle}"></label>`;
        openModal(overlay);
    }
    overlay.querySelectorAll('[data-request-close]').forEach(button=>button.addEventListener('click',()=>closeModal(overlay)));
    overlay.querySelector('form').addEventListener('submit',event=>{
        event.preventDefault();const form=event.target;if(!form.reportValidity())return;
        const body=new FormData();
        if(requestMode==='return'){
            body.set('return_type',form.elements.return_type.value);body.set('reason',form.elements.reason.value);body.set('evidence_description',form.elements.evidence_description.value);
            const chosen=[...form.querySelectorAll('[name="selected"]:checked')];
            if(!chosen.length){const message=overlay.querySelector('[data-request-error]');message.textContent='Select at least one item.';message.hidden=false;return;}
            chosen.forEach((check,index)=>{const item=order.items[Number(check.value)];body.set(`items[${index}][order_item_id]`,item.id);body.set(`items[${index}][quantity]`,form.querySelector(`[data-quantity="${check.value}"]`).value);});
        }else{body.set('type',form.elements.type.value);body.set('description',form.elements.description.value);}
        [...form.elements.evidence.files].forEach(file=>body.append('evidence[]',file));
        perform(event.submitter,async()=>{await request(`${endpoint}/${requestMode==='return'?'returns':'complaints'}`,body);closeModal(overlay);notice('Request submitted','Your request has been saved. You can follow its updates in My Purchases.',true);},text=>{const message=overlay.querySelector('[data-request-error]');message.textContent=text;message.hidden=false;});
    });
    document.querySelectorAll('[data-vo-action]').forEach(button=>button.addEventListener('click',()=>{
        switch(button.dataset.voAction){
            case 'seller':window.openBuyerSellerChat?.(order.id,'order');break;
            case 'cancel':$('voCancelForm').reset();$('voCancelOtherWrap').hidden=true;$('voCancelOther').required=false;error('voCancelError','');openModal($('voCancelModal'));break;
            case 'received':if(order.can_confirm_received)perform(button,async()=>{await request(`${endpoint}/confirm-received`,{},'PATCH');notice('Order received','Receipt confirmed. You can now rate your products.',true);});break;
            case 'rate':if(order.status==='completed'&&$('voRatingProduct').options.length){selectReview();openModal($('voRateModal'));}break;
            case 'return':openRequest('return');break;
            case 'report':if(order.complaint)location.assign(`/buyer/my-purchases?section=purchases&complaint=${order.complaint.id}`);else openRequest('report');break;
        }
    }));
    document.addEventListener('click',event=>{if(event.target.classList.contains('vo-modal-overlay'))closeModal(event.target);});
    document.addEventListener('keydown',event=>{if(event.key==='Escape')document.querySelectorAll('.vo-modal-overlay:not([hidden])').forEach(closeModal);});
});
