/* Account navigation keeps the sidebar document alive. Each content page keeps
 * its own JS lifecycle, avoiding duplicate listeners and module reinitialization. */
(() => {
    'use strict';
    const selector = '#admin-sidebar, #sellerSidebar, #lgSidebar';
    const root = document.documentElement;
    let host;
    try {
        if (window.parent !== window && window.frameElement?.id === 'se-account-content') host = window.parent.ShopEaseNavigation;
    } catch { /* Not our account content frame (e.g. the landing login modal). */ }
    if (host) {
        root.classList.add('se-content-frame', 'se-sidebar-continuation');
        document.addEventListener('DOMContentLoaded', () => {
            host.contentReady(window);
            const update = () => host.feedback(root.classList.contains('se-loading-action'));
            new MutationObserver(update).observe(root, { attributes: true, attributeFilter: ['class'] });
            update();
        });
        document.addEventListener('click', event => {
            const toggle = event.target.closest?.('#sidebar-toggle, #seller-sidebar-toggle, #lgSidebarToggle');
            if (toggle) { event.preventDefault(); event.stopImmediatePropagation(); host.toggleSidebar(); return; }
            const link = event.target.closest?.('a[href]');
            if (!eligible(event, link)) return;
            const url = new URL(link.href, location.href);
            if (url.pathname === location.pathname && url.search === location.search && url.hash) return;
            if (host.accepts(url.href)) { event.preventDefault(); host.navigate(url.href); }
            else if (url.origin === location.origin) {
                event.preventDefault(); host.leave(url.href);
            }
        });
        window.addEventListener('load', () => host.contentLoaded(window));
        return;
    }
    if (window.ShopEaseNavigation) return;
    let frame, sidebar, contentWindow, loadingTimeout, lastUrl = location.href, activeNavigation = false;
    const intervals = new Set();
    const originalInterval = window.setInterval;
    window.setInterval = function (...args) { const id = originalInterval.apply(this, args); intervals.add(id); return id; };
    function eligible(event, link) {
        return link && !event.defaultPrevented && event.button === 0 && !event.ctrlKey && !event.metaKey && !event.altKey && !event.shiftKey
            && !link.hasAttribute('download') && (!link.target || link.target === '_self')
            && !/\/(?:export|download)(?:[/?#]|$)|\.(?:pdf|csv|xlsx?|zip)(?:[?#]|$)/i.test(link.href);
    }
    function area(href) {
        const url = new URL(href, location.href);
        if (url.origin !== location.origin || /\/(?:auth|login|logout|landing|signup|register)(?:[/.?-]|$)/i.test(url.pathname)) return null;
        return url.pathname.match(/\/(admin|seller|logistics|sorting-center|delivery-hub)(?:\/|$)/)?.[1] || null;
    }
    function accepts(href) { return !!area(href) && area(href) === area(location.href); }
    function feedback(visible) { root.classList.toggle('se-loading-action', visible); }
    function modal(visible, source) {
        if (source && source !== contentWindow) return;
        root.classList.toggle('se-frame-modal', visible);
    }
    function leave(href) {
        clearTimeout(loadingTimeout); activeNavigation = false;
        root.classList.remove('se-loading-boot', 'se-loading-page', 'se-shell-loading');
        root.classList.add('se-loading-action');
        location.assign(href);
    }
    function syncLayout() {
        if (!contentWindow || !sidebar) return;
        const doc = contentWindow.document, childSidebar = doc.querySelector(selector);
        const classes = ['sidebar-collapsed', 'sidebar-hidden', 'sidebar-closed', 'seller-sidebar-collapsed', 'lg-collapsed'];
        classes.forEach(name => doc.body.classList.toggle(name, document.body.classList.contains(name)));
        if (childSidebar) {
            ['w-20', 'w-72', 'seller-sidebar-collapsed', 'sidebar-collapsed'].forEach(name => childSidebar.classList.toggle(name, sidebar.classList.contains(name)));
            childSidebar.style.setProperty('width', `${sidebar.getBoundingClientRect().width}px`, 'important');
        }
        const rect = sidebar.getBoundingClientRect();
        root.style.setProperty('--se-skeleton-left', `${Math.max(0, rect.right)}px`);
        contentWindow.dispatchEvent(new contentWindow.Event('resize'));
    }
    function contentReady(win) {
        contentWindow = win;
        syncLayout();
        syncActiveMenu(win);
    }
    function syncActiveMenu(win) {
        const destinationSidebar = win.document.querySelector(selector);
        if (!sidebar || !destinationSidebar) return;
        // Query within the matched sidebar. Appending a suffix to the comma
        // selector selected the Admin/Seller ASIDE itself instead of its links.
        const destinationLinks = [...destinationSidebar.querySelectorAll('a[href]')];
        sidebar.querySelectorAll('a[href]').forEach(link => {
            const child = destinationLinks.find(other => other.href === link.href);
            if (!child) return;
            const current = child.classList.contains('active') || child.classList.contains('bg-maroon-700/60') || child.getAttribute('aria-current') === 'page';
            ['active', 'bg-maroon-700/60', 'hover:bg-maroon-800/50', 'hover:translate-x-1'].forEach(name => link.classList.toggle(name, child.classList.contains(name)));
            link.setAttribute('data-se-current', String(current));
            if (current) link.setAttribute('aria-current', 'page');
            else link.removeAttribute('aria-current');
        });
        const orders = sidebar.querySelector?.('#sellerOrdersToggle');
        const childOrders = destinationSidebar.querySelector?.('#sellerOrdersToggle');
        if (orders && childOrders) {
            const current = childOrders.classList.contains('bg-maroon-700/60');
            orders.classList.toggle('bg-maroon-700/60', current);
            orders.setAttribute('data-se-current', String(current));
        }
    }
    function contentLoaded(win) {
        contentWindow = win;
        // A login/session-expiry/public redirect must leave the account shell.
        if (!win.document.querySelector(selector) || !accepts(win.location.href)) { leave(win.location.href); return; }
        syncLayout();
        document.title = win.document.title;
        const current = win.location.href;
        if (current !== lastUrl) { history.replaceState({ seAccountPage: true }, '', current); lastUrl = current; }
        syncActiveMenu(win);
        clearTimeout(loadingTimeout); activeNavigation = false;
        root.classList.remove('se-loading-boot', 'se-loading-page', 'se-loading-action', 'se-shell-loading');
    }
    function navigate(href, push = true) {
        if (!accepts(href)) { leave(href); return; }
        sidebar = document.querySelector(selector);
        if (!sidebar) { location.assign(href); return; }
        activeNavigation = true;
        modal(false);
        root.classList.remove('se-modal-open');
        root.classList.remove('se-loading-action', 'se-action-preparing');
        root.classList.add('se-loading-page', 'se-shell-loading');
        root.style.setProperty('--se-skeleton-left', `${Math.max(0, sidebar.getBoundingClientRect().right)}px`);
        if (!frame) {
            // Stop hidden source-page polling; its sidebar listeners stay alive.
            intervals.forEach(id => clearInterval(id)); intervals.clear();
            frame = document.createElement('iframe');
            frame.id = 'se-account-content'; frame.title = 'ShopEase account content';
            document.body.append(frame);
            root.classList.add('se-shell-mounted');
            new MutationObserver(syncLayout).observe(sidebar, { attributes: true, attributeFilter: ['class', 'style'] });
            new MutationObserver(syncLayout).observe(document.body, { attributes: true, attributeFilter: ['class'] });
        }
        if (push && href !== location.href) history.pushState({ seAccountPage: true }, '', href);
        lastUrl = href;
        frame.src = href;
        clearTimeout(loadingTimeout);
        loadingTimeout = setTimeout(() => {
            if (!activeNavigation) return;
            // Preserve a working destination if embedding is rejected or loading fails.
            location.assign(href);
        }, 20000);
    }
    window.ShopEaseNavigation = Object.freeze({ navigate, accepts, feedback, modal, contentReady, contentLoaded, leave,
        toggleSidebar() { document.querySelector('#sidebar-toggle, #seller-sidebar-toggle, #lgSidebarToggle')?.click(); syncLayout(); },
    });
    document.addEventListener('click', event => {
        const link = event.target.closest?.('a[href]');
        if (!eligible(event, link) || !link.closest(selector)) return;
        if (!accepts(link.href)) return;
        event.preventDefault();
        const next = new URL(link.href, location.href);
        if (next.pathname === location.pathname && next.search === location.search) return;
        navigate(link.href);
    });
    window.addEventListener('popstate', () => { if (frame) navigate(location.href, false); });
})();
