document.addEventListener('DOMContentLoaded', () => {
  'use strict';
  const $ = id => document.getElementById(id);
  const cfg = JSON.parse($('buyerSearchConfig')?.textContent || '{}');
  const qs = new URLSearchParams(location.search);
  const state = {q:(qs.get('q')||qs.get('search')||'').trim(),subcategory:'',brand:'',province:'',min_price:'',max_price:'',min_rating:'',sort:'relevance',page:1,facets:{},total:0,lastPage:1};
  const escape = s => String(s??'').replace(/[&<>"']/g,c=>({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c]));
  const money = n => '₱'+Number(n||0).toLocaleString('en-PH',{minimumFractionDigits:2,maximumFractionDigits:2});
  const price = p => Number(p.sale_price ?? (Number(p.price)*(100-Number(p.sale_percentage||0))/100));
  const grid=$('searchProductGrid');
  const searchField=$('buyerSearchInput');
  if(searchField) searchField.value=state.q;
  $('searchedTerm').textContent=state.q ? `“${state.q}”` : 'all products';
  $('resultsQuery').textContent=state.q ? `“${state.q}”` : 'all products';
  let requestCount=0;
  const renderCard=p=>{
    const url=`${cfg.productUrl}?id=${encodeURIComponent(p.id)}`;
    const img=p.image_url||'';
    const base=Number(p.price||0);
    const pct=Number(p.sale_percentage||0);
    return `<a href="${escape(url)}" class="product-card">
      <div class="product-image-box"><button class="favorite-button" type="button" aria-label="Add to favorites" aria-pressed="false" data-like-product="${escape(p.id)}" data-like-name="${escape(p.name)}" data-like-price="${escape(price(p))}" data-like-image="${escape(img)}" data-like-seller="${escape(p.seller?.store_name||'ShopEase Seller')}" data-like-url="${escape(url)}">♡</button>
        ${img?`<img src="${escape(img)}" alt="${escape(p.name)}" loading="lazy" class="product-photo">`:'<div class="product-art-placeholder"></div>'}</div>
      <h3>${escape(p.name)}</h3><div class="product-review-meta"><p class="product-rating"><span class="rating-star">★</span> <span class="product-rating-number">${Number(p.rating_average||0).toFixed(1)}</span><span class="sold-count">${Number(p.sold_count||0).toLocaleString()} sold</span></p>
      <p class="product-shop-name">${escape(p.seller?.store_name||'ShopEase Seller')}</p></div>
      <div class="product-bottom-row"><span class="product-sale-price"><strong>${escape(money(price(p)))}</strong>${pct?`<del>${escape(money(base))}</del><em>-${pct}%</em>`:''}</span><button type="button" class="product-cart-button" data-product-id="${escape(p.id)}" aria-label="View ${escape(p.name)}"><img src="${escape(cfg.cartIcon)}" alt="" class="clean-icon"></button></div>
    </a>`;
  };
  const facetConfig=[['provinces','provinceOptions','province'],['brands','brandOptions','brand'],['subcategories','subcategoryOptions','subcategory']];
  function drawFacets(){
    facetConfig.forEach(([key,id,field])=>{
      const options=state.facets[key]||[];
      $(id).innerHTML=options.length ? options.map(v=>`<label class="search-check-option"><input type="checkbox" data-facet="${field}" value="${escape(v)}" ${state[field]===v?'checked':''}><span>${escape(v)}</span></label>`).join('') : '<span class="search-filter-none">No available options for this search.</span>';
    });
  }
  function drawPager(){
    const panel=$('searchPager');
    if(state.lastPage<=1){panel.innerHTML='';return;}
    const pages=new Set([1,state.lastPage,state.page-1,state.page,state.page+1]);
    panel.innerHTML=`<button data-page="${state.page-1}" ${state.page===1?'disabled':''}>‹</button>`+
      [...pages].filter(n=>n>=1&&n<=state.lastPage).sort((a,b)=>a-b).map(n=>`<button data-page="${n}" class="${n===state.page?'current':''}" ${n===state.page?'aria-current="page"':''}>${n}</button>`).join('')+
      `<button data-page="${state.page+1}" ${state.page===state.lastPage?'disabled':''}>›</button>`;
  }
  function drawChips(){
    const fields=[['province','Shipped from'],['brand','Brand'],['subcategory','Subcategory'],['min_rating','Rating']];
    const chips=fields.filter(([k])=>state[k]).map(([k,l])=>`<button data-clear="${k}">${escape(l)}: ${escape(state[k])} ×</button>`);
    if(state.min_price!==''||state.max_price!=='')chips.push('<button data-clear="price">Price range ×</button>');
    $('activeFilterChips').innerHTML=chips.join('');
  }
  async function load(){
    const req=++requestCount;
    grid.innerHTML='<div class="search-feedback">Finding products that match your search…</div>';
    const url=new URL(cfg.apiUrl,location.origin);
    if(state.q)url.searchParams.set('q',state.q);
    for(const key of ['subcategory','brand','province','min_price','max_price','min_rating'])if(state[key]!==''&&state[key]!==null)url.searchParams.set(key,state[key]);
    url.searchParams.set('sort',state.sort);url.searchParams.set('page',state.page);
    try{
      const r=await fetch(url,{headers:{Accept:'application/json',Authorization:`Bearer ${cfg.token}`}});
      if(!r.ok)throw new Error(`HTTP ${r.status}`);
      const data=await r.json();if(req!==requestCount)return;
      state.facets=data.facets||{};state.total=Number(data.total||0);state.lastPage=Number(data.last_page||1);
      const shops=Array.isArray(data.related_shops)?data.related_shops:[];
      $('relatedShopsSection').hidden=shops.length===0;
      $('relatedShopsList').innerHTML=shops.map(shop=>`<a href="${escape(cfg.shopUrl)}?seller=${encodeURIComponent(shop.id)}" class="search-shop-tile"><span class="search-shop-avatar">${escape((shop.name||'S').slice(0,1).toUpperCase())}</span><strong>${escape(shop.name)}</strong><small>${escape(shop.province||'ShopEase Seller')}</small><em>Visit Shop ›</em></a>`).join('');
      drawFacets();drawChips();drawPager();
      $('resultTotal').textContent=`${state.total.toLocaleString()} result${state.total===1?'':'s'}`;
      grid.innerHTML=data.data?.length ? data.data.map(renderCard).join('') : '<div class="search-feedback">No matching products found. Try a different keyword or clear some filters.</div>';
      grid.querySelectorAll('.product-cart-button').forEach(btn=>btn.addEventListener('click',e=>{e.preventDefault();e.stopPropagation();location.href=`${cfg.productUrl}?id=${encodeURIComponent(btn.dataset.productId)}`;}));
      window.ShopEaseBuyerLikes?.syncButtons(grid);
    }catch(error){if(req!==requestCount)return;grid.innerHTML='<div class="search-feedback">Unable to load results. Please check your connection and try again.</div>';console.error('ShopEase search error:',error);}
  }
  function refresh(){state.page=1;load();}
  document.querySelectorAll('[data-sort]').forEach(btn=>btn.addEventListener('click',()=>{state.sort=btn.dataset.sort;document.querySelectorAll('[data-sort]').forEach(b=>b.classList.toggle('is-selected',b===btn));$('priceSort').value='';$('priceSort').classList.remove('is-selected');refresh();}));
  $('priceSort').addEventListener('change',e=>{if(!e.target.value)return;state.sort=e.target.value;e.target.classList.add('is-selected');document.querySelectorAll('[data-sort]').forEach(b=>b.classList.remove('is-selected'));refresh();});
  facetConfig.forEach(([,id])=>$(id).addEventListener('change',e=>{if(!e.target.matches('input[data-facet]'))return;const key=e.target.dataset.facet;state[key]=e.target.checked?e.target.value:'';refresh();}));
  $('ratingOptions').addEventListener('change',e=>{state.min_rating=e.target.value;refresh();});
  $('applyPrice').addEventListener('click',()=>{
    const min=$('minPrice').value,max=$('maxPrice').value;
    const err=$('priceError');
    if(min!==''&&max!==''&&Number(min)>Number(max)){err.hidden=false;err.textContent='Minimum price must not exceed maximum price.';return;}
    err.hidden=true;state.min_price=min;state.max_price=max;refresh();
  });
  function clearFilters(){
    Object.assign(state,{subcategory:'',brand:'',province:'',min_price:'',max_price:'',min_rating:'',page:1});
    $('minPrice').value='';$('maxPrice').value='';$('priceError').hidden=true;
    document.querySelectorAll('input[name="minRating"]').forEach(x=>x.checked=false);refresh();
  }
  $('clearAllFilters').addEventListener('click',clearFilters);
  $('clearBottomFilters').addEventListener('click',clearFilters);
  $('activeFilterChips').addEventListener('click',e=>{const button=e.target.closest('[data-clear]');if(!button)return;const key=button.dataset.clear;if(key==='price'){state.min_price='';state.max_price='';$('minPrice').value='';$('maxPrice').value='';}else{state[key]='';if(key==='min_rating')document.querySelectorAll('input[name="minRating"]').forEach(x=>x.checked=false);}refresh();});
  $('searchPager').addEventListener('click',e=>{const b=e.target.closest('[data-page]');if(!b||b.disabled)return;state.page=Number(b.dataset.page);load();document.querySelector('.search-results-section')?.scrollIntoView({behavior:'smooth',block:'start'});});
  $('toggleMobileFilters').addEventListener('click',()=>$('searchSidebar').classList.toggle('is-open'));
  load();
});
