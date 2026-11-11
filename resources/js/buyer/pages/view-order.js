import QRCode from 'qrcode';

export function renderReturnItemChoices(items) {
    const safe=value=>String(value??'').replace(/[&<>"']/g,c=>({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c]));
    const money=value=>'₱'+Number(value||0).toLocaleString('en-PH',{minimumFractionDigits:2,maximumFractionDigits:2});
    return `<fieldset class="vo-return-products"><legend>Select the product you want to report <span class="vo-required" aria-hidden="true">*</span></legend>${items.map((item,index)=>`<label class="vo-return-product"><input type="radio" name="selected_item" value="${index}" required aria-label="Select ${safe(item.product_name)}"><span class="vo-return-product-photo">${item.image_url?`<img src="${safe(item.image_url)}" alt="${safe(item.product_name)}">`:'<span>Product</span>'}</span><span class="vo-return-product-copy"><strong>${safe(item.product_name)}</strong>${item.is_fragile?'<span class="se-fragile-pill">Fragile</span>':''}<span>${safe([item.variation,item.color,item.size].filter(Boolean).join(' · ')||'No variant selected')}</span><span>Quantity: ${safe(item.quantity)}</span><span>Unit price: ${money(item.unit_price)}</span></span><strong class="vo-return-product-price">${money(Number(item.quantity)*Number(item.unit_price))}</strong></label>`).join('')}</fieldset>`;
}

document.addEventListener('DOMContentLoaded', () => {
    const $ = id => document.getElementById(id);
    const config = JSON.parse($('voOrderData').textContent);
    $('voHistoryToggle')?.addEventListener('click', event => {
        const button=event.currentTarget, expanded=button.getAttribute('aria-expanded')!=='true';
        button.setAttribute('aria-expanded',String(expanded));
        $('voTimeline').classList.toggle('is-expanded',expanded);
        document.querySelectorAll('[data-vo-older-event]').forEach(row=>{row.hidden=!expanded;});
        button.textContent=expanded?'Show latest 5 updates':`Show all ${document.querySelectorAll('#voTimeline .vo-event').length} updates`;
    });
    const order = config.order, endpoint = `/api/v1/buyer/orders/${order.id}`;
    const esc = value => String(value ?? '').replace(/[&<>"']/g, c => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c]));
    let previousFocus, refreshOnClose = false, rating = 0, busy = false;
    const openModal = modal => { previousFocus=document.activeElement; modal.hidden=false; document.body.style.overflow='hidden'; modal.querySelector('button,select,input,textarea')?.focus(); };
    const closeModal = modal => { modal.hidden=true; if(!document.querySelector('.vo-modal-overlay:not([hidden])'))document.body.style.overflow=''; previousFocus?.focus(); if(modal.id==='voModal'&&refreshOnClose){refreshOnClose=false;location.reload();} };
    const notice = (title, text, refresh = false) => { $('voModalTitle').textContent=title; $('voModalText').textContent=text; refreshOnClose=refresh; openModal($('voModal')); };
    $('voModalOkay').addEventListener('click', () => { closeModal($('voModal')); });

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
    document.body.append(overlay);let requestMode='', evidenceFiles=[];
    function syncEvidencePhotos() {
        const input=overlay.querySelector('[name="evidence"]'), host=overlay.querySelector('[data-evidence-previews]');
        const transfer=new DataTransfer();evidenceFiles.forEach(file=>transfer.items.add(file));input.files=transfer.files;
        host.replaceChildren();
        evidenceFiles.forEach((file,index)=>{
            const tile=document.createElement('span');tile.className='vo-evidence-photo';
            const image=document.createElement('img'), url=URL.createObjectURL(file);image.src=url;image.alt=file.name;image.onload=()=>URL.revokeObjectURL(url);
            const remove=document.createElement('button');remove.type='button';remove.textContent='×';remove.setAttribute('aria-label',`Remove ${file.name}`);
            remove.addEventListener('click',()=>{evidenceFiles.splice(index,1);syncEvidencePhotos();});tile.append(image,remove);host.append(tile);
        });
    }
    function openLegacyRequest(mode){
        overlay.querySelector('form').style.width='min(450px,100%)';
        overlay.classList.remove('vo-return-overlay');
        overlay.querySelector('form').classList.remove('vo-return-request');
        requestMode=mode;overlay.querySelector('form').reset();overlay.querySelector('[data-request-error]').hidden=true;
        $('voRequestTitle').textContent=mode==='return'?'Request Return / Refund':'Report a Problem';
        const fieldStyle='width:100%;padding:10px;border:1px solid #ddd;border-radius:8px;margin:6px 0 14px;font:inherit;font-weight:400';
        $('voRequestFields').innerHTML=mode==='return'
            ? `<label>Request type<select name="return_type" style="${fieldStyle}"><option value="return">Return items</option><option value="refund">Refund</option></select></label><p>Select items and quantities:</p>${order.items.map((item,index)=>`<label style="display:flex;align-items:center;gap:8px;margin:10px 0"><input type="checkbox" name="selected" value="${index}" checked><span style="flex:1">${esc(item.product_name)}</span><input type="number" data-quantity="${index}" min="1" max="${item.quantity}" value="${item.quantity}" style="width:60px;padding:6px"></label>`).join('')}<label>Reason<textarea name="reason" required maxlength="2000" rows="4" style="${fieldStyle}"></textarea></label><label>Additional evidence details (optional)<textarea name="evidence_description" maxlength="2000" rows="2" style="${fieldStyle}"></textarea></label><label>Evidence (up to 5 images or PDFs, 5 MB each)<input type="file" name="evidence" multiple accept="image/jpeg,image/png,image/webp,application/pdf" style="${fieldStyle}"></label>`
            : `<label>Issue type<select name="type" style="${fieldStyle}">${['Wrong Item','Missing Item','Damaged Item','Late Delivery','Order Not Received','Seller Conduct','Other'].map(type=>`<option>${type}</option>`).join('')}</select></label><label>Description<textarea name="description" required minlength="10" maxlength="2000" rows="5" style="${fieldStyle}"></textarea></label><label>Evidence (up to 5 photos, 5 MB each)<input type="file" name="evidence" multiple accept="image/jpeg,image/png,image/webp" style="${fieldStyle}"></label>`;
        openModal(overlay);
    }
    let pickupReturn = null;
    function openRequest(mode) {
        if (mode === 'report') return openLegacyRequest(mode);
        if (mode === 'return' && !order.can_request_return) return;
        requestMode=mode;evidenceFiles=[];
        overlay.classList.toggle('vo-return-overlay',mode==='return');
        overlay.querySelector('form').classList.toggle('vo-return-request',mode==='return');
        overlay.querySelector('form').style.width=mode==='return'?'min(920px,100%)':'min(520px,100%)';
        overlay.querySelector('form').reset();overlay.querySelector('[data-request-error]').hidden=true;
        const style='width:100%;padding:11px;border:1px solid #ddd;border-radius:8px;margin:7px 0 16px;font:inherit;font-weight:400';
        $('voRequestTitle').textContent=mode==='pickup'?'Schedule Return Pickup':'Return / Refund Request';
        if(mode==='pickup') {
            const tomorrow=new Date(Date.now()+86400000);const local=new Date(tomorrow.getTime()-tomorrow.getTimezoneOffset()*60000).toISOString().slice(0,10);
            $('voRequestFields').innerHTML=`<label>Pickup date<input name="pickup_date" type="date" value="${local}" min="${new Date(Date.now()-new Date().getTimezoneOffset()*60000).toISOString().slice(0,10)}" required style="${style}"></label><label>Pickup time<input name="pickup_time" type="time" required style="${style}"></label><label>Pickup address<textarea name="pickup_address" required maxlength="1000" rows="3" style="${style}">${esc(order.delivery_address)}</textarea></label>`;
        } else {
            $('voRequestFields').innerHTML=`${renderReturnItemChoices(order.items)}<fieldset data-product-complaint-fields disabled hidden style="border:0;padding:0;margin:0"><div class="vo-complaint-basics"><label>Quantity <span class="vo-required" aria-hidden="true">*</span><input type="number" name="quantity" value="1" min="1" required style="${style}"></label><label>Common problem <span class="vo-required" aria-hidden="true">*</span><select name="problem_type" required style="${style}"><option value="">Select a problem</option>${['Damaged Item','Defective / Not Working','Wrong Item','Missing Parts / Accessories','Not as Described','Quality Issue','Other'].map(type=>`<option>${esc(type)}</option>`).join('')}</select></label></div><div class="vo-evidence-label">Evidence photos <span class="vo-required" aria-hidden="true">*</span> <small>(required · up to 5, 5 MB each)</small></div><div class="vo-evidence-row"><label class="vo-attach-tile"><input type="file" name="evidence" multiple required accept="image/jpeg,image/png,image/webp" aria-label="Attach evidence photos"><span aria-hidden="true">+</span><strong>Attach</strong></label><div class="vo-evidence-previews" data-evidence-previews></div></div><label>Describe your complaint <span class="vo-required" aria-hidden="true">*</span><textarea name="reason" required minlength="10" maxlength="2000" rows="4" style="${style}" placeholder="Tell admin and the seller what happened"></textarea></label><p class="vo-field-heading">Choose your preferred solution <span class="vo-required" aria-hidden="true">*</span></p><div class="vo-solution-options"><label><input type="radio" name="return_type" value="return" required> Return & Refund</label><label><input type="radio" name="return_type" value="refund" required> Refund Only</label></div><p class="vo-review-note" style="font-size:12px">ShopEase admin will review the evidence and may change the solution after discussing the complaint with both parties.</p></fieldset>`;
        }
        openModal(overlay);
    }
    overlay.addEventListener('change',event=>{
        if(event.target.name==='selected_item') {
            const fields=overlay.querySelector('[data-product-complaint-fields]');const chosen=event.target.value!=='';
            fields.hidden=!chosen;fields.disabled=!chosen;
            if(chosen){const input=fields.querySelector('[name="quantity"]');input.max=order.items[Number(event.target.value)].quantity;input.value='1';}
        }
        if(event.target.name==='evidence' && requestMode==='return') {
            const added=[...event.target.files];
            const message=overlay.querySelector('[data-request-error]');
            if(evidenceFiles.length+added.length>5 || added.some(file=>!['image/jpeg','image/png','image/webp'].includes(file.type)||file.size>5*1024*1024)) {
                message.textContent='Attach up to 5 JPG, PNG or WEBP photos, 5 MB each.';message.hidden=false;
            }else{evidenceFiles.push(...added);message.hidden=true;}
            syncEvidencePhotos();
        }
    });
    document.querySelectorAll('[data-return-pickup]').forEach(button=>button.addEventListener('click',()=>{pickupReturn=button.dataset.returnPickup;openRequest('pickup');}));
    overlay.querySelectorAll('[data-request-close]').forEach(button=>button.addEventListener('click',()=>closeModal(overlay)));
    overlay.querySelector('form').addEventListener('submit',event=>{
        event.preventDefault();const form=event.target;
        if(requestMode==='return' && form.elements.reason) form.elements.reason.value=form.elements.reason.value.trim();
        if(!form.reportValidity())return;
        const body=new FormData();
        if(requestMode==='pickup') {
            ['pickup_date','pickup_time','pickup_address'].forEach(name=>body.set(name,form.elements[name].value));
        }else if(requestMode==='return'){
            body.set('return_type',form.elements.return_type.value);body.set('reason',form.elements.reason.value);body.set('problem_type',form.elements.problem_type.value);
            const item=order.items[Number(form.elements.selected_item.value)];
            body.set('items[0][order_item_id]',item.id);body.set('items[0][quantity]',form.elements.quantity.value);
        }else{body.set('type',form.elements.type.value);body.set('description',form.elements.description.value);}
        if(form.elements.evidence)[...form.elements.evidence.files].forEach(file=>body.append('evidence[]',file));
        const url=requestMode==='pickup'?`/api/v1/buyer/returns/${pickupReturn}/pickup`:`${endpoint}/${requestMode==='return'?'returns':'complaints'}`;
        perform(event.submitter,async()=>{await request(url,body);closeModal(overlay);notice(requestMode==='return'?"Waiting for Admin’s Action":'Request submitted',requestMode==='return'?'Your product complaint and evidence were submitted. Admin will review the request and discuss it with both parties.':'Your request has been saved. Follow its updates in this order and My Purchases.',true);},text=>{const message=overlay.querySelector('[data-request-error]');message.textContent=text;message.hidden=false;});
    });
    document.querySelectorAll('[data-vo-action]').forEach(button=>button.addEventListener('click',()=>{
        switch(button.dataset.voAction){
            case 'seller':window.openBuyerSellerChat?.(order.id,'order');break;
            case 'cancel':$('voCancelForm').reset();$('voCancelOtherWrap').hidden=true;$('voCancelOther').required=false;error('voCancelError','');openModal($('voCancelModal'));break;
            case 'received':if(order.can_confirm_received)perform(button,async()=>{await request(`${endpoint}/confirm-received`,{},'PATCH');notice('Order received','Receipt confirmed. You can now rate products or request a return / refund.',true);});break;
            case 'rate':if(order.status==='completed'&&$('voRatingProduct').options.length){selectReview();openModal($('voRateModal'));}break;
            case 'return':openRequest('return');break;
            case 'report':if(order.complaint)location.assign(`/buyer/my-purchases?section=purchases&complaint=${order.complaint.id}`);else openRequest('report');break;
        }
    }));
    document.addEventListener('click',event=>{if(event.target.classList.contains('vo-modal-overlay'))closeModal(event.target);});
    document.addEventListener('keydown',event=>{if(event.key==='Escape')document.querySelectorAll('.vo-modal-overlay:not([hidden])').forEach(closeModal);});
});
