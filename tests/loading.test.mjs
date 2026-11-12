import { readFileSync } from 'node:fs';
import vm from 'node:vm';
import test from 'node:test';
import assert from 'node:assert/strict';

const source = readFileSync(new URL('../public/js/shopease-loading.js', import.meta.url), 'utf8');
function setup(options = {}) {
    const timers = new Map(), events = {}, documentEvents = {}, documentHandlers = {}, requests = [], readyListeners = [];
    let timerId = 0;
    const classes = new Set();
    const classList = { add: (...v) => v.forEach(x => classes.add(x)), remove: (...v) => v.forEach(x => classes.delete(x)), contains: name => classes.has(name), toggle: (name, active) => active ? classes.add(name) : classes.delete(name) };
    const bar = { hidden: true };
    const document = {
        referrer: options.referrer || '',
        querySelector: () => ({ content: options.publicPage ? 'public' : 'account' }),
        querySelectorAll: selector => selector === '[data-se-modal-layer]'
            ? (options.layers || options.modals || []).filter(el => el.hasAttribute?.('data-se-modal-layer'))
            : options.modals || [],
        documentElement: { classList }, contains: () => true,
        getElementById: () => bar,
        addEventListener: (name, fn) => {
            (documentHandlers[name] ||= []).push(fn);
            documentEvents[name] = event => documentHandlers[name].forEach(handler => handler(event));
            if (name === 'DOMContentLoaded') readyListeners.push(fn);
        },
    };
    class XHR {
        addEventListener(name, fn) { this[name] = fn; }
        send() {}
    }
    const window = {
        sessionStorage: { getItem: () => options.handoff ? JSON.stringify(options.handoff) : null, removeItem() {}, setItem() {} },
        performance: { getEntriesByType: () => [{ type: options.navigationType || 'navigate' }] },
        addEventListener: (name, fn) => { events[name] = fn; },
        fetch: () => new Promise((resolve, reject) => requests.push({ resolve, reject })),
    };
    vm.runInNewContext(source, {
        window, document, XMLHttpRequest: XHR, URL,
        MutationObserver: class { observe() {} }, requestAnimationFrame: fn => fn(),
        getComputedStyle: el => el.computed || { display: 'block', visibility: 'visible', opacity: '1' },
        location: new URL(options.url || 'https://shop.test/dashboard'), queueMicrotask,
        setTimeout: (fn, ms) => { timers.set(++timerId, { fn, ms }); return timerId; },
        clearTimeout: id => timers.delete(id),
    });
    const run = ms => {
        for (const [id, timer] of [...timers]) if (timer.ms === ms) { timers.delete(id); timer.fn(); }
    };
    return { window, events, documentEvents, requests, classes, bar, run, XHR, readyListeners };
}
function button() {
    const attrs = new Map(), classes = new Set();
    return {
        classes, attrs, closest: () => null,
        classList: { add: v => classes.add(v), remove: v => classes.delete(v) },
        getAttribute: key => attrs.get(key) ?? null,
        setAttribute: (key, value) => attrs.set(key, value), removeAttribute: key => attrs.delete(key),
    };
}

test('logistics signup skips the bag transition while its form actions retain feedback', () => {
    const landing = setup({ publicPage:true }), signup = button();
    signup.closest = selector => selector.includes('data-loading') ? {} : null;
    landing.events.load();
    landing.documentEvents.click({target:{closest:selector => selector.startsWith('button') ? signup : null},button:0});
    landing.events.beforeunload();
    assert.equal(landing.classes.has('se-loading-action'),false);
    assert.equal(landing.classes.has('se-loading-page'),false);
    const registration = setup({publicPage:true,url:'https://shop.test/logistics/register/company'});
    assert.equal(registration.classes.has('se-loading-boot'),false);
    const action = button();
    registration.window.ShopEaseLoading.start(action);
    registration.run(180);
    assert.equal(registration.classes.has('se-loading-action'),true);
});

test('overlapping requests keep feedback until the last success or failure', async () => {
    const s = setup();
    const a = s.window.fetch('/a'), b = s.window.fetch('/b');
    s.run(180); assert.equal(s.bar.hidden, false);
    s.requests[0].resolve({ ok: true }); await a;
    assert.equal(s.bar.hidden, false);
    s.requests[1].reject(new Error('Offline')); await assert.rejects(b);
    assert.equal(s.bar.hidden, true);
});
test('button state is restored after nested actions and failure', async () => {
    const s = setup(), b = button();
    b.setAttribute('aria-busy', 'false');
    const endA = s.window.ShopEaseLoading.start(b), endB = s.window.ShopEaseLoading.start(b);
    endA(); endA(); assert.equal(b.classes.has('se-action-busy'), true);
    endB(); assert.equal(b.classes.size, 0); assert.equal(b.getAttribute('aria-busy'), 'false');
    await assert.rejects(s.window.ShopEaseLoading.during(async () => { throw Error('Failed'); }, b));
    assert.equal(b.classes.size, 0);
});
test('Back navigation and navigation recovery remove the overlay', () => {
    const s = setup();
    s.window.ShopEaseLoading.showPage(); s.run(150);
    assert.equal(s.classes.has('se-loading-page'), true);
    s.events.pageshow({ persisted: true }); assert.equal(s.classes.size, 0);
    s.window.ShopEaseLoading.showPage(); s.run(150); s.run(12000);
    assert.equal(s.classes.size, 0);
});
test('cancelled navigation, new tabs, downloads and same-page anchors do not block the page', async () => {
    for (const options of [{ prevented: true }, { target: '_blank' }, { download: true }, { href: 'https://shop.test/dashboard#details' }]) {
        const s = setup(); s.events.load();
        const link = {
            href: options.href || 'https://shop.test/orders', target: options.target || '',
            closest: () => null, hasAttribute: () => !!options.download, relList: { contains: () => false },
        };
        s.documentEvents.click({ target: { closest: selector => selector === 'a[href]' ? link : null }, button: 0, defaultPrevented: !!options.prevented });
        await Promise.resolve(); s.run(150);
        assert.equal(s.classes.has('se-loading-page'), false);
    }
});
test('XHR failures/aborts clear progress through loadend', () => {
    const s = setup(), xhr = new s.XHR();
    xhr.send(); s.run(180); assert.equal(s.bar.hidden, false);
    xhr.loadend(); assert.equal(s.bar.hidden, true);
});
test('sidebar continuation skips entrances, but a browser reload keeps entrances', () => {
    const handoff = { url: 'https://shop.test/dashboard', at: Date.now(), scroll: 120 };
    assert.equal(setup({ handoff }).classes.has('se-sidebar-continuation'), true);
    assert.equal(setup({ handoff, navigationType: 'reload' }).classes.has('se-sidebar-continuation'), false);
    assert.equal(setup().classes.has('se-sidebar-continuation'), false);
});
test('public pages use the bag marker and never suppress fresh account entrances', () => {
    const s = setup({ publicPage: true, referrer: 'https://shop.test/auth/login' });
    assert.equal(s.classes.has('se-public-page'), true);
    assert.equal(s.classes.has('se-sidebar-continuation'), false);
});
test('page skeleton waits for initial data and action bag resets independently', async () => {
    const s = setup(), b = button();
    const request = s.window.fetch('/initial-data');
    s.events.load();
    assert.equal(s.classes.has('se-loading-boot'), true);
    s.requests[0].resolve({ ok: true }); await request;
    assert.equal(s.classes.has('se-loading-boot'), false);
    const end = s.window.ShopEaseLoading.start(b); s.run(180);
    assert.equal(s.classes.has('se-loading-action'), true);
    assert.equal(s.classes.has('se-loading-page'), false);
    end(); assert.equal(s.classes.has('se-loading-action'), false);
});
test('modal opening stays immediate even if the dialog loads details', async () => {
    const s = setup(), opener = button();
    s.events.load();
    s.documentEvents.click({ target: { closest: selector => selector.startsWith('button') ? opener : null }, button: 0 });
    const request = s.window.fetch('/case/details');
    s.run(180);
    assert.equal(s.classes.has('se-loading-action'), false);
    assert.equal(s.classes.has('se-action-preparing'), false);
    s.requests[0].resolve({ ok: true });
    await request;
});
test('a decision in an open modal shows the bag until its request ends', async () => {
    const s = setup(), decision = button();
    decision.closest = selector => selector.startsWith('dialog,') ? {} : null;
    s.events.load();
    s.documentEvents.click({ target: { closest: selector => selector.startsWith('button') ? decision : null }, button: 0 });
    assert.equal(s.classes.has('se-loading-action'), false);
    const request = s.window.fetch('/case/decision', { method: 'PATCH' });
    assert.equal(s.classes.has('se-loading-action'), true);
    s.requests[0].resolve({ ok: true });
    await Promise.resolve();
    assert.equal(s.classes.has('se-loading-action'), true);
    s.run(280);
    await request;
    assert.equal(s.classes.has('se-loading-action'), false);
});
test('in-page tab switching has no loader even when the tab fetches data', async () => {
    for (const className of ['order-status-tab active', 'tab-btn', 'aam-page-tab']) {
        const s = setup(), b = button(); s.events.load();
        b.className = className;
        s.documentEvents.click({ target: { closest: selector => selector.startsWith('button') ? b : null }, button: 0 });
        const request = s.window.fetch('/tab-data');
        s.run(180); s.run(260);
        assert.equal(s.classes.has('se-loading-action'), false);
        assert.equal(s.classes.has('se-action-preparing'), false);
        assert.equal(s.bar.hidden, true);
        assert.equal(b.classes.has('se-action-busy'), false);
        s.requests[0].resolve({ ok: true }); await request;
    }
});
test('changing a table filter shows bag feedback and tracks its request', async () => {
    const s = setup(), field = button(); s.events.load();
    field.id = 'user-type-filter';
    field.matches = selector => selector.includes('select');
    s.documentEvents.change({ target: field });
    assert.equal(s.classes.has('se-loading-action'), true);
    const request = s.window.fetch('/filtered-results');
    s.run(260);
    assert.equal(s.classes.has('se-loading-action'), true);
    s.requests[0].resolve({ ok: true }); await request;
    assert.equal(s.classes.has('se-loading-action'), false);
});
test('opening a calendar has no loader; committing its date does', () => {
    const s = setup(), calendar = button(); s.events.load();
    calendar.id = 'date-calendar-button';
    calendar.setAttribute('aria-label', 'Open calendar');
    s.documentEvents.click({ target: { closest: selector => selector.startsWith('button') ? calendar : null }, button: 0 });
    assert.equal(s.classes.has('se-loading-action'), false);
    assert.equal(s.classes.has('se-action-preparing'), false);
    const date = button(); date.id = 'lr-from';
    date.matches = selector => selector.includes('input');
    s.documentEvents.change({ target: date });
    assert.equal(s.classes.has('se-loading-action'), true);
});
test('navbar dropdown controls stay quiet while logout submits with feedback', async () => {
    const s = setup(), dropdown = button(); s.events.load();
    dropdown.closest = selector => selector.includes('#seller-navbar') ? {} : null;
    dropdown.matches = () => false;
    s.documentEvents.click({ target: { closest: selector => selector.startsWith('button') ? dropdown : null }, button: 0 });
    assert.equal(s.classes.has('se-loading-action'), false);
    assert.equal(s.classes.has('se-action-preparing'), false);
    const logout = button();
    logout.closest = selector => selector.includes('#seller-navbar') ? {} : null;
    logout.matches = selector => selector.includes('submit');
    s.documentEvents.submit({ target: { closest: () => null, method: 'post' }, submitter: logout });
    await Promise.resolve();
    assert.equal(s.classes.has('se-loading-action'), true);
});
test('Inventory open-modal buttons never blur the sidebar, actual open dialogs do', () => {
    const opener = { matches: () => true };
    const hidden = { matches: () => false, hidden: true };
    const closed = setup({ modals: [opener, hidden] });
    closed.readyListeners.find(fn => fn.toString().includes('modalSelector'))();
    assert.equal(closed.classes.has('se-modal-open'), false);
    const dialog = { matches: () => false, getClientRects: () => [{}], getAttribute: () => null, parentElement: null };
    const open = setup({ modals: [opener, dialog] });
    open.readyListeners.find(fn => fn.toString().includes('modalSelector'))();
    assert.equal(open.classes.has('se-modal-open'), true);
});

test('modal stacking uses its fixed overlay and releases it when closed', () => {
    const overlay = button();
    Object.assign(overlay, { matches: () => false, computed: {position:'fixed',display:'flex',visibility:'visible',opacity:'1'}, parentElement:null });
    overlay.hasAttribute = key => overlay.attrs.has(key);
    const dialog = { matches: () => false, getClientRects: () => [{}], getAttribute: () => null, parentElement:overlay };
    const s = setup({ modals:[dialog], layers:[overlay] });
    const update = s.readyListeners.find(fn => fn.toString().includes('modalSelector'));
    update();
    assert.equal(overlay.attrs.has('data-se-modal-layer'),true);
    assert.equal(s.classes.has('se-modal-open'),true);
    overlay.hidden = true;
    update();
    assert.equal(overlay.attrs.has('data-se-modal-layer'),false);
    assert.equal(s.classes.has('se-modal-open'),false);
});
test('product info controls stay quiet while selected product photos show the bag', () => {
    const s = setup(), control = button(); s.events.load();
    control.closest = selector => selector.includes('#addProductForm') ? {} : null;
    control.matches = () => false;
    s.documentEvents.click({ target: { closest: selector => selector.startsWith('button') ? control : null }, button: 0 });
    assert.equal(s.classes.has('se-loading-action'), false);
    assert.equal(s.classes.has('se-action-preparing'), false);
    control.files = [{ type: 'image/png' }];
    s.documentEvents.change({ target: control, type: 'change' });
    assert.equal(s.classes.has('se-loading-action'), true);
    s.run(260);
    assert.equal(s.classes.has('se-loading-action'), false);
});
