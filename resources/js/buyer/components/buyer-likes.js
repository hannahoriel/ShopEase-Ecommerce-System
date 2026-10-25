/* Shared buyer likes backed by the Laravel buyer_likes table. */
(() => {
    if (window.ShopEaseBuyerLikes) return;
    let products = [];
    let ready = false;
    const csrf = document.querySelector('meta[name="csrf-token"]')?.content || '';
    const emit = () => {
        syncButtons();
        window.dispatchEvent(new CustomEvent('shopease:likes-changed', {detail:{count:products.length}}));
    };
    const has = id => products.some(product => String(product.id) === String(id));
    const getAll = () => products.map(product => ({...product}));
    const syncButtons = (root = document) => root.querySelectorAll('[data-like-product]').forEach(button => {
        const liked = has(button.dataset.likeProduct);
        button.classList.toggle('is-favorite', liked);
        button.setAttribute('aria-pressed', String(liked));
        button.setAttribute('aria-label', liked ? 'Remove from likes' : 'Add to likes');
        if (button.classList.contains('favorite-button')) button.textContent = liked ? '♥' : '♡';
    });
    async function request(url, method, body) {
        const response = await fetch(url, {
            method,
            credentials: 'same-origin',
            headers: {
                Accept: 'application/json',
                ...(body ? {'Content-Type':'application/json'} : {}),
                ...(method !== 'GET' ? {'X-CSRF-TOKEN':csrf} : {}),
            },
            ...(body ? {body:JSON.stringify(body)} : {}),
        });
        const result = await response.json().catch(() => ({}));
        if (!response.ok) throw new Error(result.message || 'Unable to update liked products.');
        return result;
    }
    async function load() {
        const result = await request('/buyer/likes', 'GET');
        products = Array.isArray(result.data) ? result.data : [];
        ready = true;
        emit();
        window.dispatchEvent(new Event('shopease:likes-ready'));
        return getAll();
    }
    async function remove(id) {
        await request('/buyer/likes/' + encodeURIComponent(id), 'DELETE');
        products = products.filter(product => String(product.id) !== String(id));
        emit();
    }
    async function toggle(product) {
        const id = String(product?.id ?? '').trim();
        if (!id) return false;
        if (has(id)) {
            await remove(id);
            return false;
        }
        const result = await request('/buyer/likes', 'POST', {product_id:id});
        const saved = result.data || product;
        products = [saved, ...products.filter(item => String(item.id) !== id)];
        emit();
        return true;
    }
    window.ShopEaseBuyerLikes = {getAll, has, toggle, remove, syncButtons, load, get ready(){return ready;}};
    document.addEventListener('click', async event => {
        const button = event.target.closest('[data-like-product]');
        if (!button) return;
        event.preventDefault();
        event.stopPropagation();
        const card = button.closest('.product-card, [data-like-card], .product-detail, .product-card-item');
        try {
            await toggle({
                id: button.dataset.likeProduct,
                name: button.dataset.likeName || card?.querySelector('h3, h2, [data-product-name]')?.textContent?.trim() || 'Product',
                price: button.dataset.likePrice || card?.dataset.productPrice || 0,
                image: button.dataset.likeImage || card?.querySelector('img.product-photo, img[data-product-image]')?.getAttribute('src') || '',
                seller: button.dataset.likeSeller || card?.querySelector('.sold-count, [data-product-seller]')?.textContent?.trim() || 'ShopEase Seller',
                url: button.dataset.likeUrl || card?.querySelector('a[href*="product"]')?.getAttribute('href') || '/buyer/product?id=' + encodeURIComponent(button.dataset.likeProduct),
            });
        } catch (error) {
            window.dispatchEvent(new CustomEvent('shopease:likes-error', {detail:{message:error.message}}));
        }
    });
    load().catch(error => {
        window.dispatchEvent(new CustomEvent('shopease:likes-error', {detail:{message:error.message}}));
        window.dispatchEvent(new Event('shopease:likes-ready'));
    });
})();
