import {readFileSync} from 'node:fs';
import vm from 'node:vm';
import test from 'node:test';
import assert from 'node:assert/strict';

test('category pills normalize stored slugs and labels and retain the inventory palette',()=>{
    const source=readFileSync(new URL('../resources/js/shared/category-pills.js',import.meta.url),'utf8');
    const context={};vm.runInNewContext(source.replace(/^import .*;\r?\n/,'').replaceAll('export ',''),context);
    for(const value of ["Women's Apparel",'womens-apparel','Women’s Apparel']){
        assert.equal(context.categoryPresentation(value).className,'se-category-pill category-womens-apparel');
        assert.equal(context.categoryPresentation(value).label,"Women's Apparel");
    }
    assert.equal(context.categoryPresentation('Health & Beauty').className,'se-category-pill category-health-and-beauty');
    assert.match(context.renderCategoryPill('<unknown>'),/&lt;unknown&gt;/);
    const shared=readFileSync(new URL('../resources/css/shared/category-pills.css',import.meta.url),'utf8');
    const inventory=readFileSync(new URL('../resources/css/seller/inventory.css',import.meta.url),'utf8');
    const palette=[...inventory.matchAll(/\.category-([a-z-]+) \{ background: (#[A-Fa-f0-9]+); color: (#[A-Fa-f0-9]+); \}/g)].slice(0,14);
    assert.equal(palette.length,14);
    for(const [,slug,bg,fg] of palette)assert.ok(shared.includes(`.se-category-pill.category-${slug}{background:${bg}!important;color:${fg}!important}`));
});
