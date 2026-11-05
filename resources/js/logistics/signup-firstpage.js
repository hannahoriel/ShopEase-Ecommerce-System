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

// The Laravel POST endpoint is the source of truth: only a successful Continue saves data.
// No localStorage/sessionStorage copy of private information or passwords is created.
(() => {
  const form = document.getElementById('logisticsCompanyForm');
  if (!form) return;
  // Any unsaved modifications are discarded when using the stepper/back navigation.
  window.addEventListener('pageshow', (e) => { if (e.persisted) window.location.reload(); });
  // Re-enable Continue if server-side validation sends the user back.
  window.addEventListener('pageshow', () => {
    const button = document.getElementById('nextButton');
    if (button) { button.disabled = false; button.textContent = 'Continue'; }
  });
})();
// Numeric-only fields; allow navigation/editing keys and paste cleaning.
(() => {
 const form=document.getElementById('logisticsCompanyForm');if(!form)return;
 for(const name of ['postal_code','years_in_operation','branch_count']) {
  const el=form.elements.namedItem(name);if(!el)continue;
  el.setAttribute('inputmode','numeric');
  el.addEventListener('beforeinput',e=>{if(e.data && /[^0-9]/.test(e.data))e.preventDefault();});
  el.addEventListener('input',()=>{const clean=el.value.replace(/[^0-9]/g,'');if(el.value!==clean)el.value=clean;});
 }
 for(const name of ['company_phone','representative_phone']) {
  const el=form.elements.namedItem(name);if(!el)continue;el.setAttribute('inputmode','tel');
  el.addEventListener('beforeinput',e=>{if(e.data && /[^0-9+]/.test(e.data))e.preventDefault();});
  el.addEventListener('input',()=>{const clean=el.value.replace(/(?!^)\+|[^0-9+]/g,'');if(el.value!==clean)el.value=clean;});
 }
 // TIN uses digits and formatting dashes.
 const tin=form.elements.namedItem('tin');if(tin){tin.setAttribute('inputmode','numeric');tin.addEventListener('input',()=>{const digits=tin.value.replace(/\D/g,'').slice(0,12);tin.value=(digits.match(/.{1,3}/g)||[]).join('-');});}
 // SEC number intentionally allows the two required letters at the start.
 const sec=form.elements.namedItem('sec_registration_number');if(sec){sec.addEventListener('input',()=>{sec.value=sec.value.toUpperCase().replace(/[^A-Z0-9]/g,'').slice(0,11);});}
})();

// Single source of truth for field-level validation (no conflicting submit handlers).
(() => {
  const form = document.getElementById('logisticsCompanyForm');
  if (!form) return;
  const savedPassword = form.dataset.hasSavedPassword === '1';
  const $ = id => document.getElementById(id);
  const pass = $('password'), confirm = $('password_confirmation');
  let attempted = false;
  const strong = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[^A-Za-z0-9]).{8,}$/;
  const address = {province:'provinceSearch', city:'citySearch', barangay:'barangaySearch'};
  const fieldControl = el => $(address[el.name]) || el;
  const message = el => {
    const value = (el.value || '').trim();
    if (el.name === 'password' || el.name === 'password_confirmation') {
      if (el.name === 'password') {
        if (!value) return savedPassword && !confirm.value ? '' : 'This field is required.';
        return strong.test(value) ? '' : 'Use at least 8 characters, uppercase, lowercase, number and special character.';
      }
      if (!value && !pass.value && savedPassword) return '';
      if (!value && (pass.value || !savedPassword)) return 'Please confirm your password.';
      if (value !== pass.value) return 'Passwords do not match.';
      return '';
    }
    if (!value) return el.required ? 'This field is required.' : '';
    if (el.name === 'sec_registration_number' && !/^[A-Za-z]{2}\d{9}$/.test(value)) return 'Use 2 letters followed by 9 digits (e.g. CS202012345).';
    if (el.name === 'tin' && !/^\d{3}-\d{3}-\d{3}-\d{3}$/.test(value)) return 'Use 000-000-000-000 format.';
    if (el.name === 'postal_code' && !/^\d{4}$/.test(value)) return 'Enter exactly 4 digits.';
    if (['company_phone','representative_phone'].includes(el.name) && !/^(09\d{9}|\+639\d{9})$/.test(value)) return 'Enter a valid phone number (09XXXXXXXXX).';
    if (el.type === 'email' && !el.validity.valid) return 'Enter a valid email address.';
    if (el.type === 'url' && !el.validity.valid) return 'Enter a valid website URL.';
    if (!el.validity.valid) return el.validity.valueMissing ? 'This field is required.' : 'Please enter a valid value.';
    return '';
  };
  function paint(el, force = false) {
    const err = form.querySelector('[data-error-for="' + el.name + '"]');
    const control = fieldControl(el);
    const text = force ? message(el) : '';
    control.classList.toggle('is-invalid', !!text);
    control.setAttribute('aria-invalid', text ? 'true' : 'false');
    if (err) err.textContent = text;
    return !!text;
  }
  function mismatch() {
    // As soon as confirmation is being typed, continuously compare both passwords.
    const show = confirm.value.length > 0 && confirm.value !== pass.value;
    const indicator = $('passwordConfirmationError');
    if (indicator) indicator.classList.toggle('show', show);
    confirm.classList.toggle('password-mismatch', show);
    if (show) confirm.setAttribute('aria-invalid', 'true');
    else if (!attempted || !message(confirm)) confirm.setAttribute('aria-invalid', 'false');
  }
  function setGroupError(containerId, errorId, bad, text) {
    const container = $(containerId), error = $(errorId);
    if (container) container.classList.toggle('is-invalid', bad);
    if (error) error.textContent = bad ? text : '';
  }
  function validate() {
    let first = null;
    const fields = [...form.querySelectorAll('input[name]:not([type="hidden"]):not([type="checkbox"]), select[name]')];
    // Hidden location selectors hold the submitted values; validate them separately.
    for (const el of fields) {
      if (['provinceSearch','citySearch','barangaySearch'].includes(el.id)) continue;
      if (paint(el,true) && !first) first = fieldControl(el);
    }
    for (const name of Object.keys(address)) {
      const el = $(name);
      if (el && paint(el,true) && !first) first = fieldControl(el);
    }
    const areas = [...form.querySelectorAll('input[name="service_areas[]"]')];
    const missingAreas = areas.length && !areas.some(el=>el.checked);
    setGroupError('serviceAreas','serviceAreasError',missingAreas,'Choose at least one service area.');
    if (missingAreas && !first) first = $('serviceAreas');
    const terms = $('accept_terms');
    const missingTerms = terms && !terms.checked;
    setGroupError(null,'termsError',missingTerms,'You must accept the Terms and Privacy Policy.');
    if (terms) terms.classList.toggle('is-invalid',missingTerms);
    if (missingTerms && !first) first = terms;
    mismatch();
    return first;
  }
  ['house_no','street','building','subdivision'].forEach(id=>$(id)?.addEventListener('input',()=>{
    const combined = $('street_address');
    if (combined) combined.value = ['house_no','street','building','subdivision'].map(x=>$(x)?.value.trim()).filter(Boolean).join(', ');
  }));
  form.addEventListener('submit',event=>{
    attempted = true;
    const combined=$('street_address');
    if(combined) combined.value=['house_no','street','building','subdivision'].map(x=>$(x)?.value.trim()).filter(Boolean).join(', ');
    const first = validate();
    if (first) {
      event.preventDefault(); event.stopImmediatePropagation();
      first.scrollIntoView({behavior:'smooth',block:'center'});
      if (typeof first.focus === 'function') first.focus({preventScroll:true});
      return;
    }
    const next = $('nextButton');
    if (next) {next.disabled=true;next.textContent='Saving...';}
  },true);
  function refresh(e) {
    const el=e.target;
    if (!el) return;
    if(el === pass || el === confirm) {
      mismatch();
      if (attempted) {paint(pass,true);paint(confirm,true);}
      return;
    }
    if (attempted) {
      if(el.name === 'service_areas[]' || el.id==='accept_terms') {validate();return;}
      if(el.name && form.contains(el)) paint(el,true);
    }
  }
  form.addEventListener('input',refresh);
  form.addEventListener('change',refresh);
  ['province','city','barangay'].forEach(id=>$(id)?.addEventListener('change',()=>{
    if (attempted) paint($(id),true);
  }));
  mismatch();
  // Laravel validation errors are displayed beneath their own controls on initial render.
  form.querySelectorAll('.client-error').forEach(el=>{
    if(!el.textContent.trim())return;
    const target=form.elements.namedItem(el.dataset.errorFor);
    if(target)fieldControl(target).classList.add('is-invalid');
  });
})();