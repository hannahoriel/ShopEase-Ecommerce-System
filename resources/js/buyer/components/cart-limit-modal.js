const CART_LIMIT_MESSAGE = 'Your cart can hold up to 200 products, including quantities.';

export function isCartLimitError(message) {
    return /cart limit|200 products/i.test(String(message || ''));
}

export function showCartLimitModal() {
    let overlay = document.getElementById('buyerCartLimitModal');
    if (!overlay) {
        overlay = document.createElement('div');
        overlay.id = 'buyerCartLimitModal';
        overlay.className = 'buyer-cart-limit-overlay';
        overlay.innerHTML = `
            <section class="buyer-cart-limit-dialog" role="alertdialog" aria-modal="true" aria-labelledby="buyerCartLimitTitle" aria-describedby="buyerCartLimitDescription">
                <span class="buyer-cart-limit-icon" aria-hidden="true">!</span>
                <h2 id="buyerCartLimitTitle">Cart limit reached</h2>
                <p id="buyerCartLimitDescription">${CART_LIMIT_MESSAGE} Remove an item or reduce a quantity before adding more.</p>
                <button type="button" class="buyer-cart-limit-close">Got it</button>
            </section>`;
        document.body.appendChild(overlay);

        if (!document.getElementById('buyerCartLimitModalStyles')) {
            const style = document.createElement('style');
            style.id = 'buyerCartLimitModalStyles';
            style.textContent = `
            .buyer-cart-limit-overlay{position:fixed;inset:0;z-index:20000;display:grid;place-items:center;padding:20px;background:rgba(30,16,20,.56);backdrop-filter:blur(3px)}
            .buyer-cart-limit-dialog{width:min(410px,100%);padding:30px 28px 24px;border-radius:18px;background:#fff;text-align:center;box-shadow:0 22px 65px rgba(30,8,14,.28);font-family:Poppins, sans-serif}
            .buyer-cart-limit-icon{display:grid;place-items:center;width:48px;height:48px;margin:0 auto 14px;border-radius:50%;background:#fff0ed;color:#8b2332;font-size:25px;font-weight:700}
            .buyer-cart-limit-dialog h2{margin:0 0 9px;color:#391d24;font-size:19px;font-weight:650}
            .buyer-cart-limit-dialog p{margin:0;color:#76696b;font-size:13px;line-height:1.7}
            .buyer-cart-limit-close{min-width:125px;margin-top:22px;padding:11px 18px;border:0;border-radius:9px;background:#7c2330;color:#fff;font:600 12px Poppins,sans-serif;cursor:pointer}
            .buyer-cart-limit-close:hover{background:#64202b}
            `;
            document.head.appendChild(style);
        }
        overlay.addEventListener('click', event => {
            if (event.target === overlay || event.target.closest('.buyer-cart-limit-close')) overlay.remove();
        });
        overlay.addEventListener('keydown', event => {
            if (event.key === 'Escape') overlay.remove();
        });
    }
    overlay.hidden = false;
    overlay.querySelector('.buyer-cart-limit-close')?.focus();
}
