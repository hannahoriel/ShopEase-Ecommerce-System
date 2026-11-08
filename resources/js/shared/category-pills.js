import '../../css/shared/category-pills.css';
const labels={"pet-supplies":"Pet Supplies","electronics-and-gadgets":"Electronics and Gadgets","womens-apparel":"Women's Apparel","mens-apparel":"Men's Apparel","kids-and-baby":"Kids and Baby","home-and-garden":"Home and Garden","sports-and-outdoors":"Sports and Outdoors","health-and-beauty":"Health and Beauty","books-and-media":"Books and Media","food-and-gourmet":"Food and Gourmet","automotive-motorcycle":"Automotive Motorcycle","furniture-and-office-equipment":"Furniture and Office Equipment","jewelry-and-watches":"Jewelry and Watches","office-and-school-supplies":"Office and School Supplies"};
export function categoryPresentation(value){
 const original=String(value||'').trim();
 const slug=original.toLowerCase().replace(/['’]/g,'').replace(/&/g,'and').replace(/[^a-z0-9]+/g,'-').replace(/^-|-$/g,'');
 return {className:'se-category-pill category-'+(labels[slug]?slug:'default'),label:labels[slug]||original||'—'};
}
export function applyCategoryPill(element,value){const item=categoryPresentation(value);element.className='complaints-modal-category-pill '+item.className;element.textContent=item.label;}
export function renderCategoryPill(value){const item=categoryPresentation(value);const text=item.label.replace(/[&<>"']/g,c=>({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c]));return '<span class="complaints-modal-category-pill '+item.className+'">'+text+'</span>';}
