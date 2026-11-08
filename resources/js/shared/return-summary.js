import '../../css/shared/return-summary.css';
export function renderReturnSummary(record){
 const safe=value=>String(value??'').replace(/[&<>"']/g,c=>({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c]));
 const solution=value=>value==='refund'?'Refund Only':'Return & Refund';
 const status={pending:'Under Investigation',accepted:'Accepted',declined:'Declined',refunded:'Refund Completed'}[record.status]||record.status;
 const rows=[['Product',record.return_items?.[0]?.product_name||'—'],['Quantity',record.return_items?.[0]?.quantity||1],['Buyer requested',solution(record.buyer_solution||record.return_type)],['Current solution',solution(record.return_type)],['Refund amount','₱'+Number(record.refund_amount||0).toLocaleString('en-PH',{minimumFractionDigits:2})]];
 return '<h4 class="se-return-title">Return / Refund Request</h4><dl class="se-return-details">'+rows.map(([label,value])=>'<div><dt>'+safe(label)+'</dt><dd>'+safe(value)+'</dd></div>').join('')+'<div><dt>Status</dt><dd><span class="se-return-status">'+safe(status)+'</span></dd></div></dl>';
}
