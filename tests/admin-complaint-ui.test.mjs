import {readFileSync} from 'node:fs';
import vm from 'node:vm';
import test from 'node:test';
import assert from 'node:assert/strict';

test('complaint evidence and return review render without interrupting details',()=>{
    const source=readFileSync(new URL('../resources/js/admin/complaints-disputes.js',import.meta.url),'utf8');
    const helper=source.slice(source.indexOf('    const escapeHtml ='),source.indexOf('\n',source.indexOf('    const escapeHtml =')));
    const functions=source.slice(source.indexOf('    function renderComplaintEvidence'),source.indexOf('    function syncComplaintModal'));
    const evidence={innerHTML:''}, preview={innerHTML:''}, accountability={hidden:false};
    let panel;
    const host={style:{},append(value){if(value.dataset.adminReturnReview!==undefined)panel=value;}};
    const context={
        detailsModal:{querySelectorAll:()=>[evidence],querySelector:selector=>selector.startsWith('[data-admin-return-')?null:host},
        document:{querySelector:()=>preview,getElementById:()=>accountability,createElement:()=>({dataset:{},style:{},querySelectorAll:()=>[],append(){},addEventListener(){}})},
    };
    vm.createContext(context);
    const summary=readFileSync(new URL('../resources/js/shared/return-summary.js',import.meta.url),'utf8').replace(/^import .*;\r?\n/,'').replaceAll('export ','');
    vm.runInContext(summary+'\n'+helper+'\n'+functions,context);
    context.renderComplaintEvidence({evidence:[]});
    context.syncReturnControls({});
    assert.match(evidence.innerHTML,/No evidence attached/);
    assert.equal(accountability.hidden,false);
    context.renderComplaintEvidence({evidence:['/storage/evidence.jpg']});
    context.syncReturnControls({returnRequest:{status:'pending',return_type:'return',return_items:[{product_name:'Glass <set>',quantity:1}],refund_amount:100}});
    assert.match(evidence.innerHTML,/evidence.jpg/);
    assert.match(panel.innerHTML,/Glass &lt;set&gt;/);
    assert.match(panel.innerHTML,/data-return-action="accept"/);
    assert.match(panel.innerHTML,/Resolve this case here/);
    assert.equal(accountability.hidden,true);
    assert.equal(panel.className,'se-return-summary');
    assert.match(panel.innerHTML,/<dt>Current solution<\/dt>/);
    assert.match(panel.innerHTML,/Under Investigation/);
});
