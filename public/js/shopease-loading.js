(() => {
    'use strict';
    if (window.ShopEaseLoading) return;
    const root = document.documentElement;
    const publicPage = document.querySelector?.('meta[name="se-page-kind"]')?.content === 'public';
    if (publicPage) root.classList.add('se-public-page');
    // Logistics sign-up already expands the actual registration preview.
    // Its first paint should not add a second bag transition.
    if (!/^\/logistics\/register\/company\/?$/.test(location.pathname)) root.classList.add('se-loading-boot');
    const sidebarSelector = '#admin-sidebar, #sellerSidebar, #lgSidebar';
    let sidebarHandoff;
    try {
        sidebarHandoff = JSON.parse(window.sessionStorage?.getItem('se-sidebar-navigation') || 'null');
        window.sessionStorage?.removeItem('se-sidebar-navigation');
        const navigationType = window.performance?.getEntriesByType('navigation')[0]?.type;
        const previous = document.referrer ? new URL(document.referrer) : null;
        const accountArea = url => url.pathname.match(/\/(admin|seller|logistics|sorting-center|delivery-hub)(?:\/|$)/)?.[1];
        const sameAccount = !publicPage && previous?.origin === location.origin && accountArea(previous) && accountArea(previous) === accountArea(location);
        if (navigationType !== 'reload' && ((sidebarHandoff?.url === location.href && Date.now() - sidebarHandoff.at < 15000) || sameAccount)) {
            root.classList.add('se-sidebar-continuation');
        } else sidebarHandoff = null;
    } catch { sidebarHandoff = null; }
    let pending = 0, actions = 0, actionTimer, feedbackTimer, progressTimer, pageTimer, pageRecovery, trigger = null, skipUnloadUntil = 0, documentLoaded = false, leavingByAction = false;
    const busy = new Map();
    const isTab = el => !!el && (
        el.matches?.('[role="tab"], [data-tab], [data-side-panel], [data-admin-tab], [data-aam-view], [data-bs-toggle="tab"]')
        || !!el.closest?.('[role="tablist"]')
        || String(el.className || '').split(/\s+/).some(name => /(^|[-_])tabs?([-_]|$)/i.test(name))
    );
    let revealTimer;
    function finishActionVisual() {
        root.classList.remove('se-loading-action');
        clearTimeout(revealTimer);
        // Reveal dialogs only after the bag has finished fading out.
        revealTimer = setTimeout(() => root.classList.remove('se-action-preparing'), 240);
    }
    const skipped = el => el?.closest('[data-loading="off"], [data-no-loading]');
    const isProductEditorControl = el => !!el?.closest('#addProductForm, #addProductModal, #editProductModal')
        && !el.matches?.('button[type="submit"], input[type="submit"]');
    const isNavbarControl = el => !!el?.closest('.lg-navbar, #seller-navbar, #buyer-navbar, .buyer-navbar, header:has(#sidebar-toggle), header:has(#profile-button), [data-navbar]')
        && !el.matches?.('[type="submit"]') && !el.closest?.('form[action*="logout"]');
    function filterFeedback(event) {
        const field = event.target;
        if (skipped(field) || isTab(field) || field.closest?.('[role="dialog"], [aria-modal="true"], [id$="-modal"], [id$="Modal"]')) return;
        const identity = `${field.id || ''} ${field.name || ''} ${field.className || ''}`;
        const filter = field.matches?.('[data-table-filter], input[type="search"], input[type="date"], input[type="datetime-local"]')
            || /filter|search|sort|per[-_]?page|items[-_]?per[-_]?page/i.test(identity)
            || !!field.closest?.('[class*="toolbar"], [class*="filters"]');
        if (!filter || !field.matches?.('select, input')) return;
        trigger = field;
        setTimeout(() => { if (trigger === field) trigger = null; }, 0);
        root.classList.add('se-loading-action');
        clearTimeout(feedbackTimer);
        feedbackTimer = setTimeout(() => { if (!actions && !leavingByAction) finishActionVisual(); }, 260);
    }
    document.addEventListener('change', filterFeedback, true);
    // Searches are debounced by their page handlers; show feedback without
    // moving focus or preventing typing.
    let searchFeedbackTimer;
    document.addEventListener('input', event => {
        if (!event.target.matches?.('input[type="search"], input[id*="search"], input[id*="Search"]')) return;
        clearTimeout(searchFeedbackTimer);
        searchFeedbackTimer = setTimeout(() => filterFeedback(event), 350);
    }, true);
    const paintProgress = () => {
        const bar = document.getElementById('se-request-progress');
        if (bar) bar.hidden = pending === 0;
    };
    function start(button) {
        // Switching an in-page tab should not show bag, spinner or progress.
        if (isTab(button) || isNavbarControl(button) || isProductEditorControl(button)) return () => {};
        pending++;
        const action = !!button && !skipped(button);
        if (action) clearTimeout(revealTimer);
        if (action && ++actions === 1) actionTimer = setTimeout(() => root.classList.add('se-loading-action'), 180);
        if (pending === 1) progressTimer = setTimeout(paintProgress, 180);
        if (button && document.contains(button) && !skipped(button)) {
            const state = busy.get(button) || { count: 0, aria: button.getAttribute('aria-busy') };
            state.count++;
            busy.set(button, state);
            button.classList.add('se-action-busy');
            button.setAttribute('aria-busy', 'true');
        }
        let ended = false;
        return () => {
            if (ended) return;
            ended = true;
            pending = Math.max(0, pending - 1);
            if (action) {
                actions = Math.max(0, actions - 1);
                if (!actions) { clearTimeout(actionTimer); finishActionVisual(); }
            }
            if (!pending) {
                clearTimeout(progressTimer); paintProgress();
                if (documentLoaded) hidePage();
            }
            const state = busy.get(button);
            if (state && --state.count === 0) {
                button.classList.remove('se-action-busy');
                if (state.aria === null) button.removeAttribute('aria-busy');
                else button.setAttribute('aria-busy', state.aria);
                busy.delete(button);
            }
        };
    }
    function hidePage() {
        clearTimeout(pageTimer); clearTimeout(pageRecovery);
        root.classList.remove('se-loading-boot', 'se-loading-page');
    }
    function showPage() {
        clearTimeout(feedbackTimer);
        clearTimeout(revealTimer);
        root.classList.remove('se-loading-action', 'se-action-preparing');
        clearTimeout(pageTimer);
        pageTimer = setTimeout(() => root.classList.add('se-loading-page'), 150);
        // A cancelled navigation must never leave the user trapped behind a loader.
        clearTimeout(pageRecovery);
        pageRecovery = setTimeout(hidePage, 12000);
    }
    function reset() {
        hidePage();
        leavingByAction = false;
        clearTimeout(feedbackTimer);
        clearTimeout(revealTimer); root.classList.remove('se-action-preparing');
        actions = 0; clearTimeout(actionTimer); root.classList.remove('se-loading-action');
        pending = 0; clearTimeout(progressTimer); paintProgress();
        for (const [button, state] of busy) {
            button.classList.remove('se-action-busy');
            if (state.aria === null) button.removeAttribute('aria-busy');
            else button.setAttribute('aria-busy', state.aria);
        }
        busy.clear();
    }
    // Track actual requests, including errors/aborts, without changing response handling.
    const originalFetch = window.fetch;
    if (originalFetch) window.fetch = function (...args) {
        const end = start(trigger);
        try { return originalFetch.apply(this, args).finally(end); }
        catch (error) { end(); throw error; }
    };
    const originalSend = XMLHttpRequest.prototype.send;
    XMLHttpRequest.prototype.send = function (...args) {
        const end = start(trigger);
        this.addEventListener('loadend', end, { once: true });
        try { return originalSend.apply(this, args); }
        catch (error) { end(); throw error; }
    };
    document.addEventListener('click', event => {
        const button = event.target.closest?.('button, input[type="submit"], input[type="button"], [role="button"]');
        if (button && skipped(button)) { trigger = null; skipUnloadUntil = Date.now() + 10000; return; }
        if (isProductEditorControl(button)) { trigger = null; return; }
        if (isNavbarControl(button)) { trigger = null; return; }
        if (button) {
            const label = `${button.getAttribute('aria-label') || ''} ${button.getAttribute('title') || ''}`;
            const id = button.id || '';
            const calendarOpener = /open\s+(?:the\s+)?(?:calendar|date\s*picker)|choose\s+(?:a\s+)?date/i.test(label)
                || (/calendar|datepicker|date-picker|date-(?:button|btn|toggle)/i.test(id) && !/reset|apply|clear|today/i.test(id));
            if (calendarOpener || button.matches?.('[data-datepicker-toggle]')) return;
        }
        if (isTab(button)) {
            trigger = button;
            setTimeout(() => { if (trigger === button) trigger = null; }, 0);
            return;
        }
        if (button && !button.disabled && button.getAttribute('aria-disabled') !== 'true' && !skipped(button)) {
            if (busy.has(button)) { event.preventDefault(); event.stopImmediatePropagation(); return; }
            trigger = button;
            setTimeout(() => { if (trigger === button) trigger = null; }, 0);
            // Local UI actions (opening a modal, filtering, etc.) share the bag
            // feedback too. Do not delay their handlers or their result.
            if (!event.target.closest?.('a[href]')) {
                // Apply before target handlers can open a dialog on this click.
                clearTimeout(revealTimer);
                root.classList.add('se-action-preparing', 'se-loading-action');
                clearTimeout(feedbackTimer);
                feedbackTimer = setTimeout(() => {
                    if (!actions && !leavingByAction) finishActionVisual();
                }, 260);
            }
        }
        const link = event.target.closest?.('a[href]');
        if (link && (skipped(link) || link.hasAttribute('download') || /\/(?:export|download)(?:[/?#]|$)|\.(?:pdf|csv|xlsx?|zip)(?:[?#]|$)/i.test(link.href))) {
            skipUnloadUntil = Date.now() + (skipped(link) ? 10000 : 1000);
            return;
        }
        if (!link || skipped(link) || event.button !== 0 || event.ctrlKey || event.metaKey || event.shiftKey || event.altKey
            || link.hasAttribute('download') || (link.target && link.target !== '_self') || link.relList.contains('external')) return;
        const url = new URL(link.href, location.href);
        if (!['http:', 'https:'].includes(url.protocol) || url.origin !== location.origin
            || (url.pathname === location.pathname && url.search === location.search)) return;
        // Let existing modal, tab and confirmation handlers decide whether navigation happens.
        queueMicrotask(() => {
            if (event.defaultPrevented) return;
            const sidebar = link.closest(sidebarSelector);
            if (sidebar) {
                const nav = sidebar.querySelector('nav');
                try { window.sessionStorage?.setItem('se-sidebar-navigation', JSON.stringify({ url: url.href, at: Date.now(), scroll: nav?.scrollTop || 0 })); } catch { /* Storage may be unavailable. */ }
            }
            showPage();
        });
    }, true);
    function productPhotoFeedback(event) {
        if (!event.target.closest?.('#addProductForm, #addProductModal, #editProductModal')) return;
        const files = event.type === 'drop' ? event.dataTransfer?.files : event.target.files;
        if (!Array.from(files || []).some(file => file.type?.startsWith('image/'))) return;
        root.classList.add('se-loading-action');
        clearTimeout(feedbackTimer);
        feedbackTimer = setTimeout(() => { if (!actions && !leavingByAction) finishActionVisual(); }, 260);
    }
    document.addEventListener('change', productPhotoFeedback, true);
    document.addEventListener('drop', productPhotoFeedback, true);
    document.addEventListener('submit', event => {
        const form = event.target;
        if (skipped(form)) { skipUnloadUntil = Date.now() + 1000; return; }
        if ((form.target && form.target !== '_self') || form.method === 'dialog') return;
        const button = event.submitter;
        trigger = button;
        setTimeout(() => { if (trigger === button) trigger = null; }, 0);
        queueMicrotask(() => {
            if (event.defaultPrevented) return;
            leavingByAction = true;
            root.classList.add('se-loading-action');
            clearTimeout(actionTimer);
            actionTimer = setTimeout(() => { leavingByAction = false; root.classList.remove('se-loading-action'); }, 12000);
        });
    }, true);
    // Also covers location.assign/href and legacy form.submit() navigation.
    window.addEventListener('beforeunload', () => { if (!leavingByAction && Date.now() >= skipUnloadUntil) showPage(); });
    window.addEventListener('pageshow', event => {
        if (event?.persisted) reset();
        else if (!pending) hidePage();
    });
    window.addEventListener('load', () => { documentLoaded = true; if (!pending) hidePage(); }, { once: true });
    function fitSkeleton() {
        const sidebar = document.querySelector(sidebarSelector);
        const nav = document.querySelector('header.fixed, nav.fixed, .lg-navbar, #admin-navbar, #seller-navbar');
        const sideRect = sidebar?.getBoundingClientRect();
        const navRect = nav?.getBoundingClientRect();
        root.style.setProperty('--se-skeleton-left', `${sideRect ? Math.max(0, sideRect.right) : 0}px`);
        root.style.setProperty('--se-skeleton-top', `${navRect ? Math.max(0, navRect.bottom) : 0}px`);
        if (sidebarHandoff && sidebar) {
            const menu = sidebar.querySelector('nav');
            if (menu) menu.scrollTop = sidebarHandoff.scroll;
            sidebarHandoff = null;
        }
    }
    document.addEventListener('DOMContentLoaded', fitSkeleton);
    document.addEventListener('DOMContentLoaded', () => {
        if (typeof MutationObserver === 'undefined') return;
        let scheduled = false, previous = null;
        const modalSelector = 'dialog[open], [role="dialog"], [aria-modal="true"], .modal, .lg-modal, .acr-overlay, [id$="-modal"], [id$="Modal"]';
        const update = () => {
            scheduled = false;
            // Once content is hosted separately, old source-page dialogs are
            // behind that content and must not keep its persistent sidebar blurred.
            const visibleModals = root.classList.contains('se-shell-mounted') ? [] : [...document.querySelectorAll(modalSelector)].filter(modal => {
                // Inventory's openAddProductModal is a visible BUTTON, not a dialog.
                if (modal.matches('button, a, input, select, textarea, label')) return false;
                if (modal.hidden || !modal.getClientRects().length) return false;
                for (let element = modal; element; element = element.parentElement) {
                    if (element.hidden || element.getAttribute('aria-hidden') === 'true') return false;
                    const style = getComputedStyle(element);
                    if (style.display === 'none' || style.visibility === 'hidden' || style.visibility === 'collapse' || Number(style.opacity) === 0) return false;
                }
                return true;
            });
            const layers = new Set();
            visibleModals.forEach(modal => {
                if (modal.tagName === 'DIALOG' && modal.open) return;
                let layer = null;
                for (let element = modal; element && element !== document.body; element = element.parentElement) {
                    if (getComputedStyle(element).position === 'fixed') layer = element;
                }
                if (layer) layers.add(layer);
            });
            document.querySelectorAll('[data-se-modal-layer]').forEach(layer => {
                if (!layers.has(layer)) layer.removeAttribute('data-se-modal-layer');
            });
            layers.forEach(layer => {
                if (!layer.hasAttribute('data-se-modal-layer')) layer.setAttribute('data-se-modal-layer', '');
            });
            const visible = visibleModals.length > 0;
            if (visible === previous) return;
            previous = visible;
            root.classList.toggle('se-modal-open', visible);
            try { if (window.parent !== window) window.parent.ShopEaseNavigation?.modal(visible, window); } catch { /* Other host. */ }
        };
        new MutationObserver(() => {
            if (!scheduled) { scheduled = true; requestAnimationFrame(update); }
        }).observe(root, { childList: true, subtree: true, attributes: true, attributeFilter: ['class', 'style', 'hidden', 'aria-hidden', 'open'] });
        update();
    });
    function releaseSidebarPaint() {
        if (publicPage || document.getElementById('se-sidebar-ready')) return;
        const ready = document.createElement('span');
        ready.id = 'se-sidebar-ready';
        ready.hidden = true;
        document.body.append(ready);
    }
    document.addEventListener('DOMContentLoaded', () => {
        // Keep the outgoing document visible until modules and fonts have
        // settled. The sidebar never paints once with fallback fonts/styles.
        if (document.fonts?.ready) document.fonts.ready.then(releaseSidebarPaint, releaseSidebarPaint);
        else releaseSidebarPaint();
    });
    // Recovery for a failed module or font; never leave rendering blocked.
    setTimeout(releaseSidebarPaint, 3000);
    window.addEventListener('resize', fitSkeleton);
    setTimeout(hidePage, 8000);
    window.ShopEaseLoading = Object.freeze({
        start, showPage, hidePage,
        async during(task, button) { const end = start(button); try { return await task(); } finally { end(); } },
    });
})();
