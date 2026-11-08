import { readFileSync } from 'node:fs';
import vm from 'node:vm';
import test from 'node:test';
import assert from 'node:assert/strict';

test('choice fields remain distinct when fixed mode omits price', () => {
    const source = readFileSync(new URL('../resources/js/seller/inventory.js', import.meta.url), 'utf8');
    const from = source.indexOf('function findRowParts(row)');
    const until = source.indexOf('function getPriceInput(field)', from);
    const context = {};
    vm.runInNewContext(source.slice(from, until), context);
    for (const varies of [false, true]) {
        const stock = { name: 'stock' }, photo = { name: 'photo' };
        const price = varies ? { name: 'price' } : null;
        const row = { querySelector(selector) {
            if (selector === '[data-choice-field]') return stock;
            if (selector === '[data-choice-field="price"]') return price;
            if (selector === '[data-choice-field="stock"]') return stock;
            return photo;
        } };
        const parts = context.findRowParts(row);
        assert.equal(parts.priceField, price);
        assert.equal(parts.stockField, stock);
    }
});

test('one choice type selects its own price source; multiple types expose selection', () => {
    const source = readFileSync(new URL('../resources/js/seller/inventory.js', import.meta.url), 'utf8');
    const from = source.indexOf('function syncPricingUI()');
    const until = source.indexOf('function closestDirectChild(', from);
    const setup = {};
    const context = {
        pricingMode: 'varies', pricingSource: '', modeInput: {}, sourceInput: {},
        modeButtons: [], sourceButtons: [], fixedPanel: {}, variablePanel: {},
        variableSetup: setup, fixedPriceInput: {}, groups: [],
        show(element) { element.hidden = false; },
        hide(element) { element.hidden = true; },
    };
    context.getActiveGroups = () => context.groups;
    vm.runInNewContext(source.slice(from, until), context);
    for (const groupName of ['variations', 'colors', 'sizes']) {
        context.groups = [{ groupName }];
        context.syncPricingUI();
        assert.equal(context.sourceInput.value, groupName);
        assert.equal(setup.hidden, true);
    }
    context.groups = [{ groupName: 'colors' }, { groupName: 'sizes' }];
    context.syncPricingUI();
    assert.equal(setup.hidden, false);
});

test('new product goes to All Products page one and clears filters hiding it', () => {
    const source = readFileSync(new URL('../resources/js/seller/inventory.js', import.meta.url), 'utf8');
    const from = source.indexOf('function insertCreatedProductRow(');
    const until = source.indexOf('function restoreCreatedProductRows()', from);
    const rows = [], row = { hidden: true };
    const context = {
        buildCreatedProductRow: () => row,
        allTable: { prepend: item => rows.unshift(item) }, renderWarningPolicyRows() {},
        activeTab: 'archived', inventoryCurrentPage: 3,
        tabs: [], searchInput: { value: 'old search' }, categoryFilter: { value: 'old-category' }, statusFilter: { value: 'in-stock' },
        updateTabView() {}, filterProducts() { row.hidden = context.inventoryCurrentPage !== 1 || context.statusFilter.value !== 'all'; },
    };
    vm.runInNewContext(source.slice(from, until) + '\ninsertCreatedProductRow({id:123});', context);
    assert.equal(context.activeTab, 'all');
    assert.equal(context.inventoryCurrentPage, 1);
    assert.equal(context.searchInput.value, '');
    assert.equal(context.categoryFilter.value, 'all');
    assert.equal(rows[0], row);
    assert.equal(row.hidden, false);
});
