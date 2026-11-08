import { readFileSync } from 'node:fs';
import vm from 'node:vm';
import test from 'node:test';
import assert from 'node:assert/strict';
const source = readFileSync(new URL('../public/js/shopease-navigation.js', import.meta.url), 'utf8');
function classes() {
    const values = new Set();
    return { add: (...names) => names.forEach(n => values.add(n)), remove: (...names) => names.forEach(n => values.delete(n)),
        contains: name => values.has(name), toggle(name, active) { active ? values.add(name) : values.delete(name); } };
}
function setup(options = {}) {
    const children = [], events = {}, style = { setProperty() {} };
    const root = { classList: classes(), style };
    const sidebar = { classList: classes(), querySelectorAll: () => [], getBoundingClientRect: () => ({ width: 260, right: 260 }) };
    const location = new URL('https://shop.test/admin/dashboard'); location.assign = href => { location.assigned = href; };
    const document = { documentElement: root, title: 'Admin', body: { classList: classes(), append: el => children.push(el) },
        querySelector: () => sidebar, createElement: () => ({}), addEventListener: (name, fn) => { events[name] = fn; } };
    const window = { setInterval: () => 1, addEventListener: (name, fn) => { events[name] = fn; } }; window.parent = window;
    if (options.authModal) { window.parent = { ShopEaseNavigation: {} }; window.frameElement = { id: 'loginModalIframe' }; }
    const history = { pushState: (_, title, href) => { location.href = href; }, replaceState: (_, title, href) => { location.href = href; } };
    vm.runInNewContext(source, { document, window, location, history, URL, MutationObserver: class { observe() {} }, setTimeout: () => 1, clearTimeout() {}, clearInterval() {} });
    return { api: window.ShopEaseNavigation, sidebar, root, children, location, events, document };
}
test('multiple page switches keep the same sidebar and a single content frame', () => {
    const s = setup(), original = s.sidebar;
    s.api.navigate('https://shop.test/admin/reports');
    const frame = s.children[0];
    s.api.navigate('https://shop.test/admin/registrations');
    assert.equal(s.children.length, 1);
    assert.equal(s.children[0], frame);
    assert.equal(s.sidebar, original);
    assert.equal(frame.src, 'https://shop.test/admin/registrations');
    assert.equal(s.location.pathname, '/admin/registrations');
    assert.equal(s.root.classList.contains('se-shell-loading'), true);
});
test('landing login iframe is never treated as persistent account content', () => {
    const s = setup({ authModal: true });
    assert.equal(s.root.classList.contains('se-content-frame'), false);
    assert.equal(s.root.classList.contains('se-sidebar-continuation'), false);
});
test('public and cross-account destinations leave the shell with bag feedback', () => {
    const s = setup();
    assert.equal(s.api.accepts('https://shop.test/auth/login'), false);
    assert.equal(s.api.accepts('https://shop.test/seller/dashboard'), false);
    s.api.leave('https://shop.test/auth/login');
    assert.equal(s.location.assigned, 'https://shop.test/auth/login');
    assert.equal(s.root.classList.contains('se-loading-action'), true);
    assert.equal(s.root.classList.contains('se-shell-loading'), false);
});
test('Back reuses the content frame without replacing the sidebar', () => {
    const s = setup();
    s.api.navigate('https://shop.test/admin/reports');
    s.location.href = 'https://shop.test/admin/dashboard';
    s.events.popstate();
    assert.equal(s.children.length, 1);
    assert.equal(s.children[0].src, 'https://shop.test/admin/dashboard');
});
test('content modal blur includes the persistent sidebar and resets on navigation', () => {
    const s = setup(), original = s.sidebar;
    s.api.modal(true);
    assert.equal(s.root.classList.contains('se-frame-modal'), true);
    assert.equal(s.sidebar, original);
    s.api.modal(false);
    assert.equal(s.root.classList.contains('se-frame-modal'), false);
    s.api.modal(true);
    s.root.classList.add('se-modal-open');
    s.api.navigate('https://shop.test/admin/reports');
    assert.equal(s.root.classList.contains('se-frame-modal'), false);
    assert.equal(s.root.classList.contains('se-modal-open'), false);
});
test('active indication follows the loaded sidebar and clears the old item', () => {
    const s = setup();
    const link = (path, active) => {
        const attrs = new Map(), classList = classes();
        if (active) classList.add('bg-maroon-700/60');
        return { href: `https://shop.test/admin/${path}`, classList,
            setAttribute: (key, value) => attrs.set(key, value), getAttribute: key => attrs.get(key) ?? null,
            removeAttribute: key => attrs.delete(key) };
    };
    const dashboard = link('dashboard', true), reports = link('reports', false);
    s.sidebar.querySelectorAll = () => [dashboard, reports];
    s.api.navigate('https://shop.test/admin/reports');
    const childSidebar = { classList: classes(), style: { setProperty() {} },
        querySelectorAll: () => [link('dashboard', false), link('reports', true)] };
    const win = { location: new URL('https://shop.test/admin/reports'), Event: class {}, dispatchEvent() {},
        document: { title: 'Reports', body: { classList: classes() }, querySelector: () => childSidebar } };
    s.api.contentLoaded(win);
    assert.equal(reports.getAttribute('aria-current'), 'page');
    assert.equal(reports.getAttribute('data-se-current'), 'true');
    assert.equal(dashboard.getAttribute('aria-current'), null);
    assert.equal(dashboard.classList.contains('bg-maroon-700/60'), false);
    assert.equal(dashboard.getAttribute('data-se-current'), 'false');
    assert.equal(s.children.length, 1);
});
