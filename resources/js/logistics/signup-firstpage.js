(()=>{
 const form=document.getElementById('logisticsCompanyForm'); if(!form)return;
 let attempted=false;
 const hiddenLocations={province:'provinceSearch',city:'citySearch',barangay:'barangaySearch'};
 const locationValue=(input)=>input.value.trim();
 function visualFor(input){return document.getElementById(hiddenLocations[input.id])||input;}
 function messageFor(input){
   if(input.type==='checkbox')return !input.checked?'This field is required.':'';
   if(!locationValue(input))return 'This field is required.';
   if(input.type==='email'&&!input.validity.valid)return 'Enter a valid email address.';
   if(input.type==='url'&&!input.validity.valid)return 'Enter a valid website URL.';
   if(input.name==='password'&&!/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[^A-Za-z0-9]).{8,}$/.test(input.value))return 'Use 8+ characters with uppercase, lowercase, number, and special character.';
   if(input.name==='password_confirmation'&&input.value!==form.elements.namedItem('password').value)return 'Passwords do not match.';
   if(input.name==='sec_registration_number'&&!/^[a-z]{2}\d{9}$/i.test(input.value.trim()))return 'Use 2 letters followed by 9 digits.';
   if(input.name==='tin'&&!/^\d{3}-\d{3}-\d{3}-\d{3}$/.test(input.value.trim()))return 'Use 000-000-000-000 format.';
   if(input.name==='postal_code'&&!/^\d{4}$/.test(input.value.trim()))return 'Enter exactly 4 digits.';
   if(['company_phone','representative_phone'].includes(input.name)&&!/^(09\d{9}|\+639\d{9})$/.test(input.value.trim()))return 'Enter a valid Philippine mobile number.';
   if(!input.validity.valid)return input.validity.valueMissing?'This field is required.':'Please enter a valid value.';
   return '';
 }
 function paint(input){
   const msg=messageFor(input),visual=visualFor(input);
   visual.classList.toggle('is-invalid',!!msg);
   input.setAttribute('aria-invalid',String(!!msg));
   visual.setAttribute('aria-invalid',String(!!msg));
   const err=form.querySelector('[data-error-for="'+input.name+'"]');if(err)err.textContent=msg;
   if(input.name==='password_confirmation'){
     const match=document.getElementById('passwordConfirmationError');
     if(match)match.classList.toggle('show',!!msg && input.value.length>0);
   }
   return !!msg;
 }
 function validateAll(){
   let first=null;
   form.querySelectorAll('[required]').forEach(input=>{
     if(paint(input)&&!first)first=visualFor(input);
   });
   // Validate non-required fields only when filled.
   form.querySelectorAll('input:not([required]),select:not([required])').forEach(input=>{
     if(input.type==='hidden'||input.type==='checkbox'||!input.value.trim())return;
     if(!input.validity.valid){input.classList.add('is-invalid');const e=form.querySelector('[data-error-for="'+input.name+'"]');if(e)e.textContent='Please enter a valid value.';if(!first)first=input;}
   });
   const areas=[...form.querySelectorAll('input[name="service_areas[]"]')];
   const missingAreas=areas.length&&!areas.some(x=>x.checked);
   const areaBox=document.getElementById('serviceAreas');if(areaBox)areaBox.classList.toggle('is-invalid',!!missingAreas);
   const areaError=document.getElementById('serviceAreasError');if(areaError)areaError.textContent=missingAreas?'Choose at least one service area.':'';
   if(missingAreas&&!first)first=areas[0];
   const terms=form.elements.namedItem('accept_terms');
   if(terms){terms.classList.toggle('terms-invalid',!terms.checked);const te=document.getElementById('termsError');if(te)te.textContent=terms.checked?'':'You must accept the Terms and Privacy Policy.';if(!terms.checked&&!first)first=terms;}
   return first;
 }
 // Capture runs before the existing handlers, so all missing fields turn red together.
 form.addEventListener('submit',event=>{
   attempted=true;
   const first=validateAll();
   if(first){event.preventDefault();event.stopImmediatePropagation();first.focus({preventScroll:true});first.scrollIntoView({behavior:'smooth',block:'center'});}
 },true);
 function refresh(event){if(!attempted)return;
   if(event.target?.matches?.('[required]'))paint(event.target);
   if(event.target?.name==='accept_terms'||event.target?.name==='service_areas[]')validateAll();
 }
 form.addEventListener('input',refresh);form.addEventListener('change',refresh);
 // Hidden address values are updated by the searchable dropdown script.
 const observer=new MutationObserver(()=>{if(!attempted)return;for(const key of Object.keys(hiddenLocations)){const el=document.getElementById(key);if(el)paint(el);}});
 Object.values(hiddenLocations).forEach(id=>{const el=document.getElementById(id);if(el)observer.observe(el,{attributes:true,attributeFilter:['value']});});
})();

(function(){
  const form=document.getElementById('logisticsCompanyForm');
  const next=document.getElementById('nextButton');
  // Only the Continue form submit saves these fields. The stepper is navigation only.
  const combined=document.getElementById('street_address');
  const syncStreet=()=>{combined.value=['house_no','street','building','subdivision'].map(id=>document.getElementById(id).value.trim()).filter(Boolean).join(', ')};
  ['house_no','street','building','subdivision'].forEach(id=>document.getElementById(id).addEventListener('input',syncStreet));
  form.addEventListener('submit',function(event){
    syncStreet();
    form.querySelectorAll('.is-invalid').forEach(el=>el.classList.remove('is-invalid'));
    form.querySelectorAll('.client-error').forEach(el=>el.textContent='');
    const invalid=[...form.querySelectorAll('[required]')].find(el=>el.type === 'checkbox' ? !el.checked : !el.value.trim());
    if(invalid){
      event.preventDefault();invalid.classList.add('is-invalid');
      const msg=form.querySelector('[data-error-for="'+invalid.name+'"]');
      if(msg)msg.textContent='This field is required.';
      invalid.focus();return;
    }
    const email=[...form.querySelectorAll('input[type="email"]')].find(el=>!el.validity.valid);
    if(email){event.preventDefault();email.classList.add('is-invalid');email.focus();return;}
    const invalidOther=[...form.querySelectorAll('input:not([type="hidden"]),select')].find(el=>!el.checkValidity());
    if(invalidOther){event.preventDefault();invalidOther.classList.add('is-invalid');invalidOther.focus();return;}
    const pass=form.querySelector('#password');
    const confirm=form.querySelector('#password_confirmation');
    const strong=/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[^A-Za-z0-9]).{8,}$/;
    if(!strong.test(pass.value)){
      event.preventDefault();pass.classList.add('is-invalid');
      form.querySelector('[data-error-for="password"]').textContent='Use 8+ characters with uppercase, lowercase, number, and special character.';
      pass.focus();return;
    }
    if(pass.value!==confirm.value){
      event.preventDefault();confirm.classList.add('password-mismatch');
      document.getElementById('passwordConfirmationError').classList.add('show');
      confirm.focus();return;
    }
    next.disabled=true;next.textContent='Saving...';
  });
  form.addEventListener('input',event=>{event.target.classList?.remove('is-invalid');const msg=form.querySelector('[data-error-for="'+event.target.name+'"]');if(msg)msg.textContent='';});
})();

/* Uses the SAME ShopEase seller location endpoints: /api/v1/locations */
(function(){
  const api='/api/v1/locations';
  const controls={};
  const locationName=o=>String(o?.name||o?.prov_name||o?.city_name||o?.mun_name||o?.brgy_name||'');
  const locationCode=o=>String(o?.code||o?.psgc_code||o?.prov_code||o?.city_code||o?.mun_code||o?.brgy_code||'');
  async function load(path){const r=await fetch(api+'/'+path,{headers:{Accept:'application/json'}});if(!r.ok)throw Error('Locations API error '+r.status);const data=await r.json();return Array.isArray(data)?data:(Array.isArray(data.data)?data.data:[])}
  for(const type of ['province','city','barangay']){
    const wrap=document.querySelector('[data-location="'+type+'"]');
    if(!wrap)return;
    const search=wrap.querySelector('.lg-location-search'), hidden=document.getElementById(type), list=wrap.querySelector('.lg-location-options');
    const c={wrap,search,hidden,list,items:[],chosen:null,focusIndex:-1};controls[type]=c;
    function close(){wrap.classList.remove('open');search.setAttribute('aria-expanded','false');c.focusIndex=-1}
    c.close=close;
    c.message=t=>{list.innerHTML='';const msg=document.createElement('div');msg.className='lg-location-message';msg.textContent=t;list.appendChild(msg);wrap.classList.add('open')};
    c.render=()=>{
      list.innerHTML='';c.focusIndex=-1;
      const q=search.value.trim().toLocaleLowerCase();const filtered=c.items.filter(o=>locationName(o).toLocaleLowerCase().includes(q));c.filtered=filtered;
      if(!filtered.length){c.message('No matching result found.');return}
      for(const [i,o] of filtered.entries()){
        const b=document.createElement('button');b.type='button';b.className='lg-location-option'+(hidden.value===locationName(o)?' is-selected':'');b.textContent=locationName(o);b.setAttribute('role','option');
        b.addEventListener('mousedown',e=>e.preventDefault());b.addEventListener('click',()=>choose(o));list.appendChild(b)
      }
      wrap.classList.add('open');search.setAttribute('aria-expanded','true');
    };
    const choose=o=>{c.chosen=o;hidden.value=locationName(o);search.value=hidden.value;close();hidden.dispatchEvent(new Event('change',{bubbles:true}));const msg=document.querySelector('[data-error-for="'+type+'"]');if(msg)msg.textContent='';
      if(type==='province')changeProvince(o);if(type==='city')changeCity(o);
    };
    c.select=choose;
    search.addEventListener('focus',()=>{if(!search.disabled)c.render()});
    search.addEventListener('input',()=>{hidden.value='';c.chosen=null;c.render()});
    search.addEventListener('keydown',e=>{if(e.key==='Escape'){close();return}if(e.key==='ArrowDown'||e.key==='ArrowUp'){e.preventDefault();if(!wrap.classList.contains('open'))c.render();const opts=list.querySelectorAll('button');if(!opts.length)return;c.focusIndex=(c.focusIndex+(e.key==='ArrowDown'?1:-1)+opts.length)%opts.length;opts.forEach((b,i)=>b.classList.toggle('is-active',i===c.focusIndex));opts[c.focusIndex].scrollIntoView({block:'nearest'})}if(e.key==='Enter'&&wrap.classList.contains('open')){const chosen=c.filtered?.[Math.max(0,c.focusIndex)];if(chosen){e.preventDefault();choose(chosen)}}});
    search.addEventListener('blur',()=>setTimeout(()=>{if(!wrap.contains(document.activeElement)){search.value=hidden.value;close()}},160));
  }
  document.addEventListener('mousedown',e=>{for(const c of Object.values(controls)){if(!c.wrap.contains(e.target))c.close()}});
  const reset=(type,placeholder)=>{const c=controls[type];c.items=[];c.chosen=null;c.hidden.value='';c.search.value='';c.search.disabled=true;c.search.placeholder=placeholder;c.close()};
  async function changeProvince(o){reset('city','Loading municipalities...');reset('barangay','Select municipality first');const p=locationCode(o);if(!p)return;try{const suffix=o.isRegion===true?'?is_region=1':'';const items=await load('provinces/'+encodeURIComponent(p)+'/cities'+suffix);if(controls.province.chosen!==o)return;controls.city.items=items;controls.city.search.disabled=false;controls.city.search.placeholder='Type to search municipality'}catch(e){console.error(e);controls.city.search.placeholder='Unable to load municipalities'}}
  async function changeCity(o){reset('barangay','Loading barangays...');const code=locationCode(o);if(!code)return;try{const items=await load('cities/'+encodeURIComponent(code)+'/barangays');if(controls.city.chosen!==o)return;controls.barangay.items=items;controls.barangay.search.disabled=false;controls.barangay.search.placeholder='Type to search barangay'}catch(e){console.error(e);controls.barangay.search.placeholder='Unable to load barangays'}}
  async function initialize(){
    const saved={province:controls.province.hidden.value,city:controls.city.hidden.value,barangay:controls.barangay.hidden.value};
    reset('city','Select province first');reset('barangay','Select municipality first');
    const p=controls.province;p.search.placeholder='Loading provinces...';p.search.disabled=true;
    try{
      p.items=await load('provinces');p.search.disabled=false;p.search.placeholder='Type to search province';
      if(saved.province){const obj=p.items.find(o=>locationName(o).toLocaleLowerCase()===saved.province.toLocaleLowerCase());if(obj){p.select(obj);await waitUntilEnabled(controls.city);if(saved.city){const city=controls.city.items.find(o=>locationName(o).toLocaleLowerCase()===saved.city.toLocaleLowerCase());if(city){controls.city.select(city);await waitUntilEnabled(controls.barangay);if(saved.barangay){const brgy=controls.barangay.items.find(o=>locationName(o).toLocaleLowerCase()===saved.barangay.toLocaleLowerCase());if(brgy)controls.barangay.select(brgy)}}}}}
    }catch(e){console.error('ShopEase location lookup:',e);p.search.placeholder='Unable to load provinces';p.search.disabled=false}
  }
  async function waitUntilEnabled(c){for(let i=0;i<80;i++){if(!c.search.disabled)return;await new Promise(resolve=>setTimeout(resolve,50))}}
  initialize();
})();

(function(){
  const hiddenIcon=document.querySelector('meta[name="logistics-hidden-icon"]')?.content || '/icons/login/hide-password.png';
  const shownIcon=document.querySelector('meta[name="logistics-shown-icon"]')?.content || '/icons/login/show-password.png';
  function setup(fieldId,buttonId,iconId){
    const input=document.getElementById(fieldId),button=document.getElementById(buttonId),icon=document.getElementById(iconId);
    if(!input||!button||!icon)return;
    button.addEventListener('click',()=>{
      const showing=input.type==='password';input.type=showing?'text':'password';
      icon.src=showing?shownIcon:hiddenIcon;
      icon.alt=showing?'Password visible':'Password hidden';
      button.setAttribute('aria-label',showing?'Hide password':'Show password');
      button.setAttribute('aria-pressed',String(showing));
    });
  }
  setup('password','passwordEyeButton','passwordEyeIcon');
  setup('password_confirmation','passwordConfirmationEyeButton','passwordConfirmationEyeIcon');
  const pass=document.getElementById('password'),confirm=document.getElementById('password_confirmation');
  const error=document.getElementById('passwordConfirmationError');
  const check=()=>{const mismatch=confirm.value.length>0&&pass.value!==confirm.value;confirm.classList.toggle('password-mismatch',mismatch);error.classList.toggle('show',mismatch)};
  pass.addEventListener('input',check);confirm.addEventListener('input',check);
})();

(function(){
  const input=document.getElementById('password');
  const list=document.getElementById('logisticsPasswordRules');
  if(!input||!list)return;
  function update(){
    const p=input.value;
    const valid={length:p.length>=8,upper:/[A-Z]/.test(p),lower:/[a-z]/.test(p),number:/[0-9]/.test(p),special:/[^A-Za-z0-9]/.test(p)};
    for(const [rule,ok] of Object.entries(valid))list.querySelector('[data-rule="'+rule+'"]').classList.toggle('ok',ok);
  }
  input.addEventListener('input',update);update();
})();

(()=>{const form=document.getElementById('logisticsCompanyForm');if(!form)return;const names=['company_name','registered_business_name','representative_name'];const namePattern=/^[\p{L} .'-]+$/u;const phone=/^(09\d{9}|\+639\d{9})$/;let sending=false;
function error(field,message){const el=form.elements.namedItem(field);if(!el)return;el.classList.add('is-invalid');let msg=form.querySelector('[data-error-for="'+field+'"]');if(msg)msg.textContent=message;}
form.addEventListener('submit',e=>{let errors=0;form.querySelectorAll('.client-error').forEach(n=>n.textContent='');form.querySelectorAll('.is-invalid').forEach(n=>n.classList.remove('is-invalid'));
for(const name of names){let el=form.elements.namedItem(name),val=el?.value.trim()||'';if(!val){error(name,'This field is required.');errors++;}else if(name==='representative_name' && (val.length<2||val.length>101||!namePattern.test(val))){error(name,'Use letters and allowed punctuation only (2–50 characters per name).');errors++;}}
for(const name of ['company_email','representative_email']){let el=form.elements.namedItem(name);if(!el)continue;el.value=el.value.trim().toLowerCase();if(!el.validity.valid){error(name,'Enter a valid email address.');errors++;}}
for(const name of ['company_phone','representative_phone']){let el=form.elements.namedItem(name);if(el&&!phone.test(el.value.trim())){error(name,'Use 09XXXXXXXXX or +639XXXXXXXXX.');errors++;}else if(el&&el.value.startsWith('+63'))el.value='0'+el.value.slice(3);}
const sec=form.elements.namedItem('sec_registration_number');sec.value=sec.value.trim().toUpperCase();if(!/^[A-Z]{2}\d{9}$/.test(sec.value)){error('sec_registration_number','Use 2 letters followed by 9 digits (e.g. CS202012345).');errors++;}
const tin=form.elements.namedItem('tin');tin.value=tin.value.trim();if(!/^\d{3}-\d{3}-\d{3}-\d{3}$/.test(tin.value)){error('tin','Use 000-000-000-000 format.');errors++;}
const zip=form.elements.namedItem('postal_code');if(!/^\d{4}$/.test(zip?.value||'')){error('postal_code','Enter exactly 4 digits.');errors++;}
if(!form.querySelector('input[name="service_areas[]"]:checked')){document.getElementById('serviceAreasError').textContent='Choose at least one service area.';errors++;}
if(!form.elements.namedItem('accept_terms').checked){document.getElementById('termsError').textContent='You must accept the Terms and Privacy Policy.';errors++;}
if(errors){e.preventDefault();e.stopImmediatePropagation();(form.querySelector('.is-invalid')||document.getElementById('serviceAreasError')||document.getElementById('termsError'))?.focus?.();return;}
try { sessionStorage.setItem('shopease_logistics_ui_extras',JSON.stringify({sec_registration_number:sec.value,tin:tin.value,service_areas:[...form.querySelectorAll('input[name="service_areas[]"]:checked')].map(el=>el.value)})); }catch(_e){}
if(sending){e.preventDefault();e.stopImmediatePropagation();return;}
sending=true;setTimeout(()=>{if(document.visibilityState==='visible')sending=false},8000);
},true);
})();