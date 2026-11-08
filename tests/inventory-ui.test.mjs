import { readFileSync } from 'node:fs';
import vm from 'node:vm';
import test from 'node:test';
import assert from 'node:assert/strict';

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
