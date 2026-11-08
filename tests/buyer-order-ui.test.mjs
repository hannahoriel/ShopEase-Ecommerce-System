import {readFileSync} from 'node:fs';
import vm from 'node:vm';
import test from 'node:test';
import assert from 'node:assert/strict';

test('return picker previews every order item with a single radio group and original item indexes',()=>{
    const source=readFileSync(new URL('../resources/js/buyer/pages/view-order.js',import.meta.url),'utf8');
    const from=source.indexOf('export function renderReturnItemChoices');
    const until=source.indexOf("document.addEventListener('DOMContentLoaded'",from);
    const context={};vm.runInNewContext(source.slice(from,until).replace('export ',''),context);
    const html=context.renderReturnItemChoices([
        {product_name:'Blouse',image_url:'/storage/blouse.png',variation:'Linen',color:'Ivory',size:'Medium',quantity:1,unit_price:1299},
        {product_name:'Glass <set>',image_url:null,variation:null,color:'Clear',size:null,quantity:2,unit_price:79,is_fragile:true},
    ]);
    assert.equal((html.match(/type="radio" name="selected_item"/g)||[]).length,2);
    assert.ok(html.includes('value="0" required'));
    assert.ok(html.includes('value="1" required'));
    assert.ok(html.includes('Linen · Ivory · Medium'));
    assert.ok(html.includes('₱1,299.00'));
    assert.ok(html.includes('Quantity: 2'));
    assert.ok(html.includes('₱158.00'));
    assert.ok(html.includes('/storage/blouse.png'));
    assert.ok(html.includes('Glass &lt;set&gt;'));
    assert.ok(html.includes('Fragile'));
    assert.equal(html.includes('<select'),false);
});
