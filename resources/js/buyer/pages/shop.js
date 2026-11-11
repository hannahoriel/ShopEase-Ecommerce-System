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
    const state = {category:'',search:'',sort:'popular',page:1,products:[],shop:null,total:0,lastPage:1,loading:false,recommendations:[]};
    const error = message => { $('bsError').textContent=message; $('bsError').hidden=false; $('bsProductGrid').replaceChildren(); $('bsEmpty').hidden=true; $('bsResultsCount').textContent=''; };
    const headers = () => ({Accept:'application/json',...(config.apiToken?{Authorization:`Bearer ${config.apiToken}`}:{})});

    // Both the seller collection and the other-seller recommendations reuse
    // the exact same Dashboard-style card markup and sizing.
    function productCard(product) {
        return dashboardRecommendationCard({
            ...product,
            shop_name: product.shop_name || state.shop?.name || 'ShopEase Seller',
        });
    }

    function renderShop(shop) {
        state.shop=shop;
        $('bsShopName').textContent=shop.name||'ShopEase Shop';
        $('bsBreadcrumbName').textContent=shop.name||'Shop';
        $('bsShopLocation').textContent=shop.location?`Based in ${shop.location}`:'A ShopEase seller';
        $('bsProductCount').textContent=Number(shop.product_count||0).toLocaleString();
        $('bsRating').textContent=Number(shop.rating_average||0).toFixed(1);
        $('bsRatingCount').textContent=`(${Number(shop.rating_count||0).toLocaleString()} ${Number(shop.rating_count)===1?'rating':'ratings'})`;
        $('bsComplianceScore').textContent=shop.compliance_score===null||shop.compliance_score===undefined?'Not assessed':`${Number(shop.compliance_score).toFixed(0)}%`;
        $('bsJoined').textContent=shop.joined_at?new Intl.DateTimeFormat('en-PH',{month:'short',year:'numeric'}).format(new Date(shop.joined_at)):'Recently';
        const cover=$('bsCover');
        if(cover){const coverUrl=shop.cover_url||'';cover.style.backgroundImage=coverUrl?`linear-gradient(90deg,rgba(40,10,14,.34),rgba(40,10,14,.10)),url("${encodeURI(coverUrl).replace(/"/g,'%22')}")`:'';cover.classList.toggle('bs-has-cover',Boolean(coverUrl));}
        if(shop.avatar_url)$('bsAvatar').innerHTML=`<img src="${escape(shop.avatar_url)}" alt="${escape(shop.name)} shop profile">`;
        else $('bsAvatar').innerHTML=`<span>${escape((shop.name||'SE').split(/\s+/).slice(0,2).map(word=>word[0]).join('').toUpperCase())}</span>`;
        $('bsChatButton').disabled=state.products.length===0;
        const categories=shop.categories||[];
        $('bsCategories').innerHTML=`<button type="button" class="bs-category-chip ${state.category?'':'is-active'}" data-category="" role="tab" aria-selected="${state.category?'false':'true'}">All products</button>`+categories.map(category=>`<button type="button" class="bs-category-chip ${state.category===category.value?'is-active':''}" data-category="${escape(category.value)}" role="tab" aria-selected="${state.category===category.value?'true':'false'}">${escape(category.label)}</button>`).join('');
    }

    // Use the Buyer Dashboard product card structure for products from OTHER sellers.
    function dashboardRecommendationCard(product) {
        const url=`/buyer/product?id=${encodeURIComponent(product.id)}`;
        const image=product.image_url || product.photos?.[0] || '';
        const basePrice=Number(product.original_price ?? product.price ?? 0);
        const discount=Number(product.sale_percentage || 0);
        const outOfStock=Number(product.stock ?? 1)<=0;
        const currentPrice=product.sale_price != null ? Number(product.sale_price)
            : discount > 0 ? Math.round(basePrice * (100-discount))/100 : basePrice;
        const shopName=product.seller?.store_name || product.shop_name || 'ShopEase Seller';
        const rating=Number(product.rating_average ?? product.rating ?? 0);
        return `<a href="${escape(url)}" class="product-card">
            <div class="product-image-box">
                <button type="button" class="favorite-button" aria-label="Add to favorites"
                    data-like-product="${escape(product.id)}" data-like-name="${escape(product.name)}"
                    data-like-price="${escape(currentPrice)}" data-like-image="${escape(image)}"
                    data-like-seller="${escape(shopName)}" data-like-url="${escape(url)}" aria-pressed="false">♡</button>
                ${image ? `<img src="${escape(image)}" alt="${escape(product.name)}" class="product-photo" loading="lazy">`
                    : '<div class="product-art-placeholder" aria-hidden="true"></div>'}
                ${outOfStock?'<span class="bs-out-of-stock">Out of stock</span>':''}
            </div>
            <h3>${escape(product.name)}</h3>
            <div class="product-review-meta">
                <p class="product-rating"><span class="rating-star" aria-hidden="true">★</span><span class="product-rating-number">${Number.isFinite(rating)?rating.toFixed(1):'0.0'}</span></p>
                <p class="product-shop-name">${escape(shopName)}</p>
            </div>
            <div class="product-bottom-row">
                <span class="product-sale-price"><strong>${money(currentPrice)}</strong>${product.sale_discount_label?`<del>${money(basePrice)}</del><em>${escape(product.sale_discount_label)}</em>`:discount>0?`<del>${money(basePrice)}</del><em>-${discount}%</em>`:''}</span>
                <button type="button" class="product-cart-button" data-product-id="${escape(product.id)}" aria-label="${outOfStock?'Out of stock':'View '+escape(product.name)+' to add to cart'}" ${outOfStock?'disabled':''}>
                    <img src="/icons/buyer/product-cart.png" alt="" class="clean-icon" data-clean-bg="true">
                </button>
            </div>
        </a>`;
    }

    function renderRecommendations(){
        const section=$('bsYouMayLikeSection'),grid=$('bsYouMayLikeGrid');
        if(!section||!grid)return;
        section.hidden=state.recommendations.length===0;
        grid.innerHTML=state.recommendations.map(dashboardRecommendationCard).join('');
        grid.querySelectorAll('.product-photo').forEach(image=>image.addEventListener('error',()=>{
            image.replaceWith(Object.assign(document.createElement('div'),{className:'product-art-placeholder'}));
        },{once:true}));
        window.ShopEaseBuyerLikes?.syncButtons(grid);
    }

    // Same behavior as Dashboard: cart icon opens product selection; favorites use shared Likes module.
    $('bsYouMayLikeGrid')?.addEventListener('click',event=>{
        const cartButton=event.target.closest('.product-cart-button');
        if(cartButton){event.preventDefault();event.stopPropagation();
            if(cartButton.dataset.productId)window.location.assign(`/buyer/product?id=${encodeURIComponent(cartButton.dataset.productId)}`);
        }
        // Shared Likes module handles favorites when loaded.
    });

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
        renderRecommendations();
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
            if(Array.isArray(payload.recommendations))state.recommendations=payload.recommendations;
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
    $('bsChatButton').addEventListener('click',()=>{
        const firstProduct=state.products[0];if(!firstProduct)return;
        if(typeof window.openBuyerSellerChat==='function')window.openBuyerSellerChat(firstProduct.id,'product');
        else window.location.assign('/buyer/messages');
    });
    loadProducts();
});
