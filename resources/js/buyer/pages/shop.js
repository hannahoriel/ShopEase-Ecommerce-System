document.addEventListener('DOMContentLoaded', () => {
    const config = JSON.parse(document.getElementById('buyerShopConfig')?.textContent || '{}');
    const $ = id => document.getElementById(id);
    const escape = value => String(value ?? '').replace(/[&<>"']/g, char => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[char]));
    const money = value => `₱${Number(value || 0).toLocaleString('en-PH',{minimumFractionDigits:2,maximumFractionDigits:2})}`;
    const categoryLabel = value => {
        const category=String(value||'').trim();
        const key=category.toLowerCase().replace(/[^a-z0-9]+/g,'');
        if(key==='womensapparel')return "Women's Apparel";
        return category.replace(/[_-]+/g,' ').replace(/\b\w/g,letter=>letter.toUpperCase());
    };
    const state = {category:'',search:'',sort:'popular',page:1,products:[],shop:null,total:0,lastPage:1,loading:false};
    const error = message => { $('bsError').textContent=message; $('bsError').hidden=false; $('bsProductGrid').replaceChildren(); $('bsEmpty').hidden=true; $('bsResultsCount').textContent=''; };
    const headers = () => ({Accept:'application/json',...(config.apiToken?{Authorization:`Bearer ${config.apiToken}`}:{})});

    function productCard(product) {
        const hasSale=Number(product.sale_percentage)>0;
        return `<a class="bs-product-card" href="/buyer/product?id=${encodeURIComponent(product.id)}">
            <div class="bs-product-image">${product.image_url?`<img src="${escape(product.image_url)}" alt="${escape(product.name)}" loading="lazy">`:'<div class="bs-product-image-empty">No product photo</div>'}${product.category?`<span class="bs-product-tag">${escape(categoryLabel(product.category))}</span>`:''}</div>
            <div class="bs-product-copy"><span class="bs-product-name">${escape(product.name)}</span><span class="bs-product-category">${escape(product.category?categoryLabel(product.category):'Shop collection')}</span>
            <div class="bs-product-price-row"><strong class="bs-product-price">${money(product.sale_price ?? product.price)}</strong>${hasSale?`<del class="bs-product-original">${money(product.price)}</del><span class="bs-product-discount">-${Number(product.sale_percentage)}%</span>`:''}</div>
            <div class="bs-product-meta"><span class="bs-product-rating"><span>★</span> ${Number(product.rating||0).toFixed(1)} <span style="color:#a18f8a;font-weight:500">(${Number(product.rating_count||0)})</span></span><span>${Number(product.sold_count||0).toLocaleString()} sold</span></div></div></a>`;
    }

    function renderShop(shop) {
        state.shop=shop;
        $('bsShopName').textContent=shop.name||'ShopEase Shop';
        $('bsBreadcrumbName').textContent=shop.name||'Shop';
        $('bsShopLocation').textContent=shop.location?`Based in ${shop.location}`:'A ShopEase seller';
        $('bsProductCount').textContent=Number(shop.product_count||0).toLocaleString();
        $('bsRating').textContent=Number(shop.rating_average||0).toFixed(1);
        $('bsRatingCount').textContent=`(${Number(shop.rating_count||0).toLocaleString()} ${Number(shop.rating_count)===1?'rating':'ratings'})`;
        $('bsFollowers').textContent=Number(shop.followers||0).toLocaleString();
        $('bsJoined').textContent=shop.joined_at?new Intl.DateTimeFormat('en-PH',{month:'short',year:'numeric'}).format(new Date(shop.joined_at)):'Recently';
        if(shop.avatar_url)$('bsAvatar').innerHTML=`<img src="${escape(shop.avatar_url)}" alt="${escape(shop.name)} shop profile">`;
        else $('bsAvatar').innerHTML=`<span>${escape((shop.name||'SE').split(/\s+/).slice(0,2).map(word=>word[0]).join('').toUpperCase())}</span>`;
        const follow=$('bsFollowButton');
        follow.disabled=false;follow.classList.toggle('is-following',Boolean(shop.is_following));
        follow.innerHTML=shop.is_following?'<span aria-hidden="true">✓</span> Following':'<span aria-hidden="true">＋</span> Follow shop';
        $('bsChatButton').disabled=state.products.length===0;
        const categories=shop.categories||[];
        $('bsCategories').innerHTML=`<button type="button" class="bs-category-chip ${state.category?'':'is-active'}" data-category="" role="tab" aria-selected="${state.category?'false':'true'}">All products</button>`+categories.map(category=>`<button type="button" class="bs-category-chip ${state.category===category.value?'is-active':''}" data-category="${escape(category.value)}" role="tab" aria-selected="${state.category===category.value?'true':'false'}">${escape(category.label)}</button>`).join('');
    }

    function renderProducts(append=false) {
        const products=state.products;
        if(!append){
            $('bsProductGrid').innerHTML=products.map(productCard).join('');
            $('bsFeaturedSection').hidden=Boolean(state.category||state.search)||products.length<3;
            $('bsFeaturedGrid').innerHTML=[...products].sort((a,b)=>Number(b.sold_count||0)-Number(a.sold_count||0)).slice(0,5).map(productCard).join('');
        }else $('bsProductGrid').insertAdjacentHTML('beforeend',products.slice((state.page-1)*60).map(productCard).join(''));
        $('bsProductsHeading').textContent=state.category||'All products';
        $('bsResultsCount').textContent=`${state.total.toLocaleString()} ${state.total===1?'product':'products'}`;
        $('bsEmpty').hidden=state.total!==0;
        $('bsEmpty').textContent=state.search?`No products match “${state.search}”.`:'This shop has no products in this collection yet.';
        $('bsError').hidden=true;
        $('bsLoadMore')?.remove();
        if(state.page<state.lastPage){
            const button=document.createElement('button');button.type='button';button.id='bsLoadMore';button.className='bs-load-more';button.textContent='Load more products';
            button.addEventListener('click',()=>{state.page++;loadProducts(true);});
            $('bsProductGrid').after(button);
        }
        $('bsChatButton').disabled=products.length===0;
    }

    async function loadProducts(append=false) {
        if(!config.sellerId){error('We could not identify this shop. Please return to the product and try again.');return;}
        if(state.loading)return;
        state.loading=true;$('bsError').hidden=true;
        if(!append){$('bsProductGrid').innerHTML='<div class="bs-empty-state">Loading this shop’s collection…</div>';}
        const params=new URLSearchParams({per_page:'60',page:String(state.page),sort:state.sort});
        if(state.category)params.set('category',state.category);
        if(state.search)params.set('search',state.search);
        try{
            const response=await fetch(`${config.apiUrl}/${encodeURIComponent(config.sellerId)}?${params}`,{credentials:'same-origin',headers:headers()});
            const payload=await response.json().catch(()=>({}));
            if(!response.ok)throw new Error(payload.message||'This shop could not be loaded.');
            if(payload.shop)renderShop(payload.shop);
            const incoming=payload.data||[];
            state.products=append?[...state.products,...incoming]:incoming;
            state.total=Number(payload.total||0);state.lastPage=Number(payload.last_page||1);
            renderProducts(append);
        }catch(exception){error(exception.message);}
        finally{state.loading=false;}
    }

    $('bsCategories').addEventListener('click',event=>{
        const button=event.target.closest('[data-category]');if(!button)return;
        state.category=button.dataset.category||'';state.page=1;loadProducts();
    });
    let searchTimer;
    $('bsSearch').addEventListener('input',event=>{clearTimeout(searchTimer);state.search=event.target.value.trim();state.page=1;searchTimer=setTimeout(()=>loadProducts(),280);});
    $('bsSort').addEventListener('change',event=>{state.sort=event.target.value;state.page=1;loadProducts();});
    $('bsFollowButton').addEventListener('click',async event=>{
        const button=event.currentTarget;if(!state.shop||button.disabled)return;
        button.disabled=true;
        try{
            const following=!state.shop.is_following;
            const response=await fetch(`${config.apiUrl}/${state.shop.id}/follow`,{method:following?'POST':'DELETE',credentials:'same-origin',headers:headers()});
            const payload=await response.json().catch(()=>({}));if(!response.ok)throw new Error(payload.message||'Could not update your followed shops.');
            state.shop.is_following=payload.is_following;state.shop.followers=payload.followers;renderShop(state.shop);
        }catch(exception){button.title=exception.message;}
        finally{button.disabled=false;}
    });
    $('bsChatButton').addEventListener('click',()=>{
        const firstProduct=state.products[0];if(!firstProduct)return;
        if(typeof window.openBuyerSellerChat==='function')window.openBuyerSellerChat(firstProduct.id,'product');
        else window.location.assign('/buyer/messages');
    });
    loadProducts();
});
