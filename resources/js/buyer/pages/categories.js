document.addEventListener('DOMContentLoaded', () => {
  'use strict';
  const cfg = JSON.parse(document.getElementById('buyerCategoriesConfig')?.textContent || '{}');
  const catalog = [
    ['pet-supplies','Pet Supplies'], ['electronics','Electronics & Gadgets'], ['womens-apparel',"Women's Apparel"],
    ['mens-apparel',"Men's Apparel"], ['kids-and-baby','Kids & Baby'], ['home-and-garden','Home & Garden'],
    ['sports-and-outdoors','Sports & Outdoors'], ['health-and-beauty','Health & Beauty'], ['books-and-media','Books & Media'],
    ['food-and-gourmet','Food & Gourmet'], ['automotive','Automotive & Motorcycle'], ['furniture','Furniture & Office Equipment'],
    ['jewelry-and-watches','Jewelry & Watches'], ['office-supplies','Office & School Supplies']
  ];
  const names = Object.fromEntries(catalog);
  const params = new URLSearchParams(location.search);
  const $ = id => document.getElementById(id);
  const grid = $('categoriesProducts');
  const state = {
    category: names[params.get('category')] ? params.get('category') : '',
    search: params.get('search') || '',
    sort: 'latest', min: '', max: '', stockOnly: false,
    products: [], page: 0, lastPage: 1, total: 0, requestId: 0
  };
  const escapeHtml = value => String(value ?? '').replace(/[&<>"']/g, char => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[char]));
  const money = value => '₱' + Number(value || 0).toLocaleString('en-PH', {minimumFractionDigits:2,maximumFractionDigits:2});
  const price = product => {
    const base = Number(product.original_price ?? product.price ?? 0);
    const pct = Number(product.sale_percentage || 0);
    return pct ? Math.round(base * (100 - pct)) / 100 : base;
  };
  const chooseCategory = category => {
    state.category = names[category] ? category : '';
    document.querySelectorAll('[data-category]').forEach(el => {
      const active = el.dataset.category === state.category;
      el.classList.toggle('is-active', active);
      el.setAttribute('aria-pressed',String(active));
    });
    $('categoryResultsTitle').textContent = names[state.category] || 'All products';
    const url = new URL(location.href);
    if (state.category) url.searchParams.set('category', state.category); else url.searchParams.delete('category');
    history.replaceState({},'',url);
    loadFirstPage();
  };
  const renderCard = p => {
    const target = `${cfg.productUrl || '/buyer/product'}?id=${encodeURIComponent(p.id)}`;
    const img = p.image_url || p.photos?.[0] || '';
    const base = Number(p.original_price ?? p.price ?? 0);
    const discount = Number(p.sale_percentage || 0);
    const amount = price(p);
    const seller = p.seller?.store_name || 'ShopEase Seller';
    const sellerId = p.seller?.id;
    const rating = Number(p.rating_average ?? p.average_rating ?? 0);
    const photo = img ? `<img class="product-photo" src="${escapeHtml(img)}" alt="${escapeHtml(p.name)}" loading="lazy">` : '<div class="product-art-placeholder" aria-hidden="true"></div>';
    // Same product card markup as Buyer Dashboard (including review-meta wrapper).
    return `<a href="${escapeHtml(target)}" class="product-card">
      <div class="product-image-box">
        <button class="favorite-button" type="button" aria-label="Add to favorites" aria-pressed="false" data-like-product="${escapeHtml(p.id)}" data-like-name="${escapeHtml(p.name)}" data-like-price="${escapeHtml(amount)}" data-like-image="${escapeHtml(img)}" data-like-seller="${escapeHtml(seller)}" data-like-url="${escapeHtml(target)}">♡</button>
        ${photo}
      </div>
      <h3>${escapeHtml(p.name)}</h3>
      <div class="product-review-meta">
        <p class="product-rating"><span class="rating-star" aria-hidden="true">★</span><span class="product-rating-number">${Number.isFinite(rating) ? rating.toFixed(1) : '0.0'}</span></p>
        <p class="product-shop-name">${escapeHtml(seller)}</p>
      </div>
      <div class="product-bottom-row">
        <span class="product-sale-price"><strong>${escapeHtml(money(amount))}</strong>${discount ? `<del>${escapeHtml(money(base))}</del><em>-${discount}%</em>` : ''}</span>
        <button type="button" class="product-cart-button" aria-label="Add ${escapeHtml(p.name)} to cart" data-product-id="${escapeHtml(p.id)}"><img src="${escapeHtml(cfg.cartIconUrl || '/icons/buyer/product-cart.png')}" alt="" class="clean-icon" data-clean-bg="true"></button>
      </div>
    </a>`;
  };
  const render = () => {
    let rows = state.products.slice();
    const min = state.min !== '' ? Number(state.min) : null;
    const max = state.max !== '' ? Number(state.max) : null;
    if (min !== null && Number.isFinite(min)) rows = rows.filter(p => price(p) >= min);
    if (max !== null && Number.isFinite(max)) rows = rows.filter(p => price(p) <= max);
    if (state.stockOnly) rows = rows.filter(p => Number(p.stock ?? 0) > 0);
    if (state.sort === 'price-low') rows.sort((a,b) => price(a) - price(b));
    if (state.sort === 'price-high') rows.sort((a,b) => price(b) - price(a));
    if (state.sort === 'name') rows.sort((a,b) => String(a.name).localeCompare(String(b.name)));
    grid.innerHTML = rows.length ? rows.map(renderCard).join('') : `<div class="categories-empty"><strong>No matching products found</strong>Try another category, price range, or search term.</div>`;
    $('categoriesLoadMore').hidden = !(state.page < state.lastPage);
    grid.querySelectorAll('.product-photo').forEach(img => img.addEventListener('error',() => {img.replaceWith(document.createElement('div'));},{once:true}));
    grid.querySelectorAll('.product-cart-button').forEach(btn => btn.addEventListener('click',e => {e.preventDefault();e.stopPropagation();location.href=`${cfg.productUrl || '/buyer/product'}?id=${encodeURIComponent(btn.dataset.productId)}`;}));
    window.ShopEaseBuyerLikes?.syncButtons(grid);
    renderChips();
  };
  const renderChips = () => {
    const filters = [];
    if (state.category) filters.push(`<button class="categories-filter-chip" data-clear="category">${escapeHtml(names[state.category])} ×</button>`);
    if (state.search) filters.push(`<button class="categories-filter-chip" data-clear="search">Search: ${escapeHtml(state.search)} ×</button>`);
    if (state.min || state.max) filters.push(`<button class="categories-filter-chip" data-clear="price">Price filter ×</button>`);
    if (state.stockOnly) filters.push('<button class="categories-filter-chip" data-clear="stock">In stock ×</button>');
    $('categoriesActiveFilters').innerHTML = filters.join('');
  };
  const fetchPage = async (page, reset = false) => {
    const id = ++state.requestId;
    if(reset) {grid.innerHTML = '<div class="categories-loading">Finding your next favorite…</div>'; state.products = []; state.page=0;}
    const url = new URL(cfg.productsUrl,location.origin);
    if(state.category) url.searchParams.set('category',state.category);
    if(state.search.trim()) url.searchParams.set('search',state.search.trim());
    url.searchParams.set('page',String(page)); url.searchParams.set('per_page','48');
    url.searchParams.set('sort',state.sort);
    const button = $('categoriesLoadMore'); button.disabled = true;
    try {
      const resp = await fetch(url,{headers:{'Accept':'application/json','X-Requested-With':'XMLHttpRequest',...(cfg.apiToken ? {'Authorization':'Bearer '+cfg.apiToken}:{})},credentials:'same-origin'});
      if(!resp.ok) throw new Error('Products API responded '+resp.status);
      const json = await resp.json();
      if(id !== state.requestId) return;
      state.products = state.products.concat(Array.isArray(json.data) ? json.data : []);
      state.page = Number(json.current_page || page);
      state.lastPage = Number(json.last_page || 1);
      state.total = Number(json.total || 0);
      render();
    } catch (error) {if(id === state.requestId) {if(!state.products.length) grid.innerHTML='<div class="categories-empty"><strong>Unable to load products</strong>Check your connection and try refreshing this page.</div>';}}
    finally {if(id === state.requestId) button.disabled=false;}
  };
  const loadFirstPage = () => fetchPage(1,true);
  let searchTimer;
  $('categoriesSearch').value = state.search;
  $('categoriesSearch').addEventListener('input', e => {clearTimeout(searchTimer);searchTimer=setTimeout(() => {state.search=e.target.value.trim();const url=new URL(location.href);if(state.search)url.searchParams.set('search',state.search);else url.searchParams.delete('search');history.replaceState({},'',url);loadFirstPage();},280);});
  document.querySelectorAll('[data-category]').forEach(btn=>btn.addEventListener('click',()=>chooseCategory(btn.dataset.category)));
  $('clearCategoryPill').addEventListener('click',()=>chooseCategory(''));
  const setSort = value => {
    state.sort = value;
    document.querySelectorAll('.categories-sort-button').forEach(btn => {
      const active = btn.dataset.sort === value;
      btn.classList.toggle('is-active', active);
      btn.setAttribute('aria-pressed', String(active));
    });
    loadFirstPage();
  };
  document.querySelectorAll('.categories-sort-button').forEach(btn => btn.addEventListener('click', () => setSort(btn.dataset.sort)));
  $('priceMin').addEventListener('input',e=>{state.min=e.target.value;render();});
  $('priceMax').addEventListener('input',e=>{state.max=e.target.value;render();});
  $('categoriesLoadMore').addEventListener('click',()=>fetchPage(state.page+1));
  $('mobileFilterToggle').addEventListener('click',()=>{const panel=$('categoriesFilters');panel.classList.toggle('is-open');});
  $('resetFilters').addEventListener('click',()=>{state.min='';state.max='';state.stockOnly=false;state.search='';$('priceMin').value='';$('priceMax').value='';$('categoriesSearch').value='';state.sort='latest';document.querySelectorAll('.categories-sort-button').forEach(btn=>{const active=btn.dataset.sort==='latest';btn.classList.toggle('is-active',active);btn.setAttribute('aria-pressed',String(active));});chooseCategory('');});
  $('categoriesActiveFilters').addEventListener('click',e=>{const btn=e.target.closest('[data-clear]');if(!btn)return;const key=btn.dataset.clear;if(key==='category')chooseCategory('');else if(key==='search'){state.search='';$('categoriesSearch').value='';loadFirstPage();}else if(key==='price'){state.min='';state.max='';$('priceMin').value='';$('priceMax').value='';render();}else if(key==='stock'){state.stockOnly=false;render();}});
  chooseCategory(state.category);
});
