/* ShopEase Buyer Likes — frontend demo, shared by Buyer Dashboard and My Purchases.
   Include with @vite('resources/js/buyer/components/buyer-likes.js') on any buyer page
   where a data-like-product heart control should work. Backend integration pending. */
(() => {
    if (window.ShopEaseBuyerLikes) return;
    const user = document.querySelector('meta[name="buyer-likes-user-id"]')?.content || 'guest';
    const key = `shopease:buyer:likes:v1:${user}`;
    // Sample data appears once in a new browser profile for preview purposes.
    const demo = [
        {id:'DEMO-LIKE-001',name:'Wireless Bluetooth Earbuds Pro',price:1299,seller:'Tech Haven',image:'',url:'/buyer/product?id=DEMO-LIKE-001',demo:true},
        {id:'DEMO-LIKE-002',name:'Everyday Canvas Tote Bag',price:459,seller:'Mia Boutique',image:'',url:'/buyer/product?id=DEMO-LIKE-002',demo:true},
        {id:'DEMO-LIKE-003',name:'Hydrating Skincare Essentials Set',price:899,seller:'Glow Essentials',image:'',url:'/buyer/product?id=DEMO-LIKE-003',demo:true},
        {id:'DEMO-LIKE-004',name:'Minimalist Smart Watch',price:2199,seller:'Tech Haven',image:'',url:'/buyer/product?id=DEMO-LIKE-004',demo:true}
    ];
    const read = () => {
        try { const v = JSON.parse(localStorage.getItem(key) || 'null'); return Array.isArray(v) ? v.filter(x => x && String(x.id || '').trim()) : null; }
        catch { return null; }
    };
    let products = read();
    if (products === null) {
        products = demo;
        try { localStorage.setItem(key, JSON.stringify(products)); } catch {}
    }
    const getAll = () => products.map(p => ({...p}));
    const has = id => products.some(p => String(p.id) === String(id));
    function syncButtons(root = document) {
        root.querySelectorAll('[data-like-product]').forEach(button => {
            const liked = has(button.dataset.likeProduct);
            button.classList.toggle('is-favorite', liked);
            button.setAttribute('aria-pressed', String(liked));
            button.setAttribute('aria-label', liked ? 'Remove from likes' : 'Add to likes');
            if (button.classList.contains('favorite-button')) button.textContent = liked ? '♥' : '♡';
        });
    }
    function commit() {
        try {localStorage.setItem(key, JSON.stringify(products));} catch {}
        syncButtons();
        window.dispatchEvent(new CustomEvent('shopease:likes-changed', {detail:{count:products.length}}));
    }
    function remove(id) { products = products.filter(p => String(p.id) !== String(id)); commit(); }
    function toggle(product) {
        const id = String(product?.id ?? '').trim();
        if (!id) return false;
        if (has(id)) {remove(id); return false;}
        products.unshift({id,name:String(product.name || 'Product'),price:Number(product.price || 0),image:String(product.image || ''),seller:String(product.seller || 'ShopEase Seller'),url:String(product.url || '/buyer/product?id='+encodeURIComponent(id))});
        commit(); return true;
    }
    window.ShopEaseBuyerLikes = {getAll,has,toggle,remove,syncButtons};
    // Event delegation supports dynamically loaded heart buttons on all opted-in buyer pages.
    document.addEventListener('click', event => {
        const button = event.target.closest('[data-like-product]');
        if (!button) return;
        event.preventDefault(); event.stopPropagation();
        const card = button.closest('.product-card, [data-like-card], .product-detail, .product-card-item');
        const id = button.dataset.likeProduct;
        toggle({
            id,
            name:button.dataset.likeName || card?.querySelector('h3, h2, [data-product-name]')?.textContent?.trim() || 'Product',
            price:button.dataset.likePrice || card?.dataset.productPrice || 0,
            image:button.dataset.likeImage || card?.querySelector('img.product-photo, img[data-product-image]')?.getAttribute('src') || '',
            seller:button.dataset.likeSeller || card?.querySelector('.sold-count, [data-product-seller]')?.textContent?.trim() || 'ShopEase Seller',
            url:button.dataset.likeUrl || card?.querySelector('a[href*="product"]')?.getAttribute('href') || '/buyer/product?id='+encodeURIComponent(id)
        });
    });
    window.addEventListener('storage', event => {
        if (event.key !== key) return;
        products = read() || [];
        syncButtons();
        window.dispatchEvent(new Event('shopease:likes-changed'));
    });
    if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', () => syncButtons(), {once:true});
    else syncButtons();
    window.dispatchEvent(new Event('shopease:likes-ready'));
})();