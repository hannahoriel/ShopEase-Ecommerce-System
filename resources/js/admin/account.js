/** Admin Account page logic. Source: resources/views/pages/admin/account.blade.php */
const accountConfig = {};
document.addEventListener('DOMContentLoaded', () => {
  const element = document.getElementById('adminAccountConfig');
  if (element) Object.assign(accountConfig, JSON.parse(element.textContent));
});

// Keep the Account page at the beginning of the document on reload.
if ('scrollRestoration' in history) history.scrollRestoration = 'manual';

document.addEventListener('DOMContentLoaded',()=>{
 const $$=q=>[...document.querySelectorAll(q)], $=id=>document.getElementById(id);
 // Account margins follow the sidebar's w-72 / w-20 classes using CSS.
 // No late inline style changes, so the page won't jump during hydration.
 function switchTab(id){$$('[data-admin-tab]').forEach(a=>{const active=a.dataset.adminTab===id;a.classList.toggle('active',active);if(active)a.setAttribute('aria-current','page');else a.removeAttribute('aria-current');});$$('[data-admin-panel]').forEach(p=>p.hidden=p.dataset.adminPanel!==id);/* Avoid hash navigation and anchor scrolling. */}
 $$('[data-admin-tab]').forEach(a=>a.addEventListener('click',e=>{e.preventDefault();switchTab(a.dataset.adminTab);}));const initialTab=['profile','privacy','notifications','admin-management'].includes(location.hash.slice(1))?location.hash.slice(1):'profile';
 switchTab(initialTab);
 if(location.hash) history.replaceState(null,'',location.pathname+location.search);
 window.addEventListener('pageshow',()=>{requestAnimationFrame(()=>window.scrollTo({top:0,left:0,behavior:'auto'}));});
 requestAnimationFrame(()=>window.scrollTo({top:0,left:0,behavior:'auto'}));
 function toast(text,err=false){window.showAccountFlash(text,err,err?"Action Failed":"Changes Saved");}
 const birthday=$('aaBirthday'),age=$('aaAge');function calc(){if(!birthday.value){age.value='';return;}const d=new Date(birthday.value+'T00:00:00'),n=new Date();if(isNaN(d)||d>n){age.value='';return;}let v=n.getFullYear()-d.getFullYear();if(n.getMonth()<d.getMonth()||(n.getMonth()===d.getMonth()&&n.getDate()<d.getDate()))v--;age.value=String(v);}birthday.addEventListener('change',calc);calc();
 const form=$('adminProfileForm'),fields=$$('#adminProfileForm input[name],#adminProfileForm select[name]').filter(el=>!['_token','_method'].includes(el.name));
 const original=new Map(fields.map(el=>[el,el.value]));function dirty(){const d=fields.some(el=>el.type==='file'?el.files.length>0:el.value!==original.get(el));$('aaSaveProfile').disabled=!d;}form.addEventListener('input',dirty);form.addEventListener('change',dirty);
 const csrfToken=document.querySelector('meta[name="csrf-token"]')?.content||'';
 async function responsePayload(response){const payload=await response.json().catch(()=>({}));if(!response.ok){throw new Error(payload.message||(payload.errors&&Object.values(payload.errors).flat()[0])||'The request could not be completed.');}return payload;}
 form.addEventListener('submit',async event=>{
  event.preventDefault();
  if(!form.reportValidity())return;
  const button=$('aaSaveProfile');
  button.disabled=true;
  try{
   const response=await fetch(form.action,{method:'POST',credentials:'same-origin',headers:{Accept:'application/json','X-CSRF-TOKEN':csrfToken},body:new FormData(form)});
   const result=await responsePayload(response);
   const account=result.account;
   if(account?.user?.name){$$('.sa-shop-name').forEach(node=>node.textContent=account.user.name);}
   if(account?.profile?.profile_photo_url){$('saAvatarImage').src=account.profile.profile_photo_url;$('saAvatarImage').style.display='block';$('saAvatarFallback').hidden=true;}
   if(account?.profile?.cover_photo_url){$('saCoverImage').src=account.profile.cover_photo_url;$('saCoverImage').style.display='block';}
   fields.filter(el=>el.type==='file').forEach(el=>{el.value='';});
   fields.forEach(el=>original.set(el,el.value));
   dirty();
   toast(result.message||'Admin profile changes saved successfully.');
  }catch(error){toast(error.message,true);}
  finally{button.disabled=false;dirty();}
 });
 function photo(inputId,imageId){const input=$(inputId),img=$(imageId);input.addEventListener('change',()=>{const f=input.files[0];if(!f){dirty();return;}if(f.size>5*1024*1024||!['image/jpeg','image/png','image/webp'].includes(f.type)){toast('Please choose a JPG, PNG or WebP image under 5 MB.',true);input.value='';dirty();return;}if(img.dataset.objectUrl)URL.revokeObjectURL(img.dataset.objectUrl);img.dataset.objectUrl=URL.createObjectURL(f);img.src=img.dataset.objectUrl;img.style.display='block';if(imageId==='saAvatarImage')$('saAvatarFallback').hidden=true;dirty();});}
 photo('saCoverInput','saCoverImage');photo('saAvatarInput','saAvatarImage');
 const photoPreview=$('aaPhotoPreview'), photoPreviewImg=$('aaPhotoPreviewImage');
 function openPhotoPreview(img,title){if(!img?.src||img.style.display==='none')return;photoPreviewImg.src=img.src;$('aaPhotoPreviewTitle').textContent=title;photoPreview.hidden=false;}
 function closePhotoPreview(){photoPreview.hidden=true;photoPreviewImg.removeAttribute('src');}
 $('saCoverImage').addEventListener('click',()=>openPhotoPreview($('saCoverImage'),'Cover Photo'));
 $('saAvatarImage').addEventListener('click',()=>openPhotoPreview($('saAvatarImage'),'Profile Photo'));
 $('aaAdminIdPhotoPreviewButton')?.addEventListener('click',()=>openPhotoPreview($('aaAdminIdPhotoImage'),'Admin ID Photo'));
 $('aaPhotoPreviewClose').addEventListener('click',closePhotoPreview);
 photoPreview.addEventListener('click',e=>{if(e.target===photoPreview)closePhotoPreview();});

 const notify=$('aaNotificationsForm'),switches=$$('#aaNotificationsForm input[type=checkbox]'),start=switches.map(i=>i.checked);function checkPrefs(){$('aaSavePreferences').disabled=!switches.some((i,k)=>i.checked!==start[k]);}switches.forEach(i=>i.addEventListener('change',checkPrefs));
 notify.addEventListener('submit',async event=>{
  event.preventDefault();
  const button=$('aaSavePreferences');
  button.disabled=true;
  try{
   const response=await fetch(notify.action,{method:'POST',credentials:'same-origin',headers:{Accept:'application/json','X-CSRF-TOKEN':csrfToken},body:new FormData(notify)});
   const result=await responsePayload(response);
   switches.forEach((input,index)=>{start[index]=input.checked;});
   toast(result.message||'Notification preferences saved.');
  }catch(error){toast(error.message,true);}
  finally{checkPrefs();}
 });
 async function loadAccountData(){
  const response=await fetch(accountConfig.profileUrl,{credentials:'same-origin',headers:{Accept:'application/json'}});
  const account=await responsePayload(response);
  const profile=account.profile||{};
  const values={aaFirst:profile.first_name||'',aaLast:profile.last_name||'',aaBirthday:profile.birthday||'',aaSex:profile.sex||'',aaPhone:profile.contact_number||''};
  Object.entries(values).forEach(([id,value])=>{if($(id))$(id).value=value;});
  if($('aaEmail'))$('aaEmail').value=account.user?.email||'';
  if(account.user?.name)$$('.sa-shop-name').forEach(node=>node.textContent=account.user.name);
  const caption=document.querySelector('.sa-shop-caption');
  if(caption&&account.role_label)caption.textContent=`${account.role_label} · ShopEase Administration`;
  const photos=[['saAvatarImage',profile.profile_photo_url],['saCoverImage',profile.cover_photo_url],['aaAdminIdPhotoImage',profile.admin_id_photo_url]];
  photos.forEach(([id,url])=>{const image=$(id);if(!image)return;if(url){image.src=url;image.style.display='block';}else{image.removeAttribute('src');image.style.display='none';}});
  if(!profile.profile_photo_url&&$('saAvatarFallback'))$('saAvatarFallback').hidden=false;
  switches.forEach(input=>{const key=input.name.match(/^preferences\[([^\]]+)\]$/)?.[1];if(key)input.checked=Boolean(account.preferences?.[key]??true);});
  switches.forEach((input,index)=>{start[index]=input.checked;});
  fields.forEach(field=>original.set(field,field.value));
  calc();
  dirty();
  checkPrefs();
 }
 loadAccountData().catch(error=>{console.error(error);toast(error.message,true);});
 const otp=$('aaOtpModal'),reset=$('aaResetModal'),cells=$$('.sa-otp-cell');
 const otpError=$('aaOtpError'),verifyOtp=$('aaVerifyOtp'),resendOtp=$('aaResendOtp');
 const resendLabel=resendOtp.querySelector('span');
 const securityBase = accountConfig.securityBase;
 const adminUserId = accountConfig.adminUserId;
 let otpPurpose='forgot',verifiedToken=null,otpRequestInProgress=false,otpCooldownInterval=null;
 const otpCooldownKey=purpose=>`admin-security-resend-v1:${adminUserId}:${purpose}`;
 function cooldownRemaining(purpose){return Math.max(0,Math.ceil((Number(sessionStorage.getItem(otpCooldownKey(purpose))||0)-Date.now())/1000));}
 function renderOtpCooldown(){
  const remaining=otpPurpose?cooldownRemaining(otpPurpose):0;
  if(remaining>0){
   resendLabel.textContent=`Resend code in ${String(Math.floor(remaining/60)).padStart(2,'0')}:${String(remaining%60).padStart(2,'0')}`;
   resendOtp.disabled=true;
   if(!otpCooldownInterval)otpCooldownInterval=setInterval(renderOtpCooldown,1000);
   return;
  }
  resendLabel.textContent='Resend Code';
  resendOtp.disabled=otpRequestInProgress;
  if(otpCooldownInterval){clearInterval(otpCooldownInterval);otpCooldownInterval=null;}
  if(otpPurpose)sessionStorage.removeItem(otpCooldownKey(otpPurpose));
 }
 function startOtpCooldown(purpose,seconds=600){
  sessionStorage.setItem(otpCooldownKey(purpose),String(Date.now()+seconds*1000));
  otpPurpose=purpose;
  renderOtpCooldown();
 }
 function close(modal){modal.hidden=true;}
 function showOtpError(message){otpError.textContent=message;otpError.hidden=false;}
 async function securityPost(path,payload){
  const response=await fetch(`${securityBase}${path}`,{
   method:'POST',
   credentials:'same-origin',
   headers:{'X-CSRF-TOKEN':csrfToken,'Content-Type':'application/json','Accept':'application/json'},
   body:JSON.stringify(payload)
  });
  let result={};try{result=await response.json();}catch(error){}
  if(!response.ok){
   const error=new Error(result.message||(result.errors&&Object.values(result.errors).flat()[0])||'The request could not be completed.');
   error.retryAfter=Number(result.retry_after||response.headers.get('Retry-After')||0);
   throw error;
  }
  return result;
 }
 async function requestOtp(purpose,isResend=false){
  otpPurpose=purpose;
  verifiedToken=null;
  otpError.hidden=true;
  if(isResend&&cooldownRemaining(purpose)>0){renderOtpCooldown();return;}
  otpRequestInProgress=true;
  resendOtp.disabled=true;
  try{
   const payload={purpose};
   if(purpose==='change')payload.current_password=$('aaCurrentPassword').value;
   const result=await securityPost('/otp/send',payload);
   if(isResend)startOtpCooldown(purpose);
   else renderOtpCooldown();
   toast(result.message);
  }catch(error){
   if(error.retryAfter>0)startOtpCooldown(purpose,error.retryAfter);
   showOtpError(error.message);
  }finally{
   otpRequestInProgress=false;
   renderOtpCooldown();
  }
 }
 async function openOtp(purpose){
  otpPurpose=purpose;
  cells.forEach(cell=>cell.value='');
  otpError.hidden=true;
  otp.hidden=false;
  renderOtpCooldown();
  cells[0].focus();
  await requestOtp(purpose);
 }
 $('aaForgotPasswordButton').addEventListener('click',()=>openOtp('forgot'));
 const changeForm=$('aaChangePasswordForm'),pw=$('aaCurrentPassword'),np=$('aaNewPassword'),cp=$('aaConfirmPassword');
 function verifyPwForm(){$('aaChangePasswordButton').disabled=!(pw.value&&np.value.length>=8&&np.value===cp.value);}
 [pw,np,cp].forEach(input=>input.addEventListener('input',verifyPwForm));
 changeForm.addEventListener('submit',async event=>{
  event.preventDefault();
  if(!changeForm.reportValidity())return;
  await openOtp('change');
 });
 cells.forEach((cell,index)=>{
  cell.addEventListener('input',()=>{cell.value=cell.value.replace(/\D/g,'').slice(-1);if(cell.value&&index<5)cells[index+1].focus();});
  cell.addEventListener('keydown',event=>{if(event.key==='Backspace'&&!cell.value&&index>0)cells[index-1].focus();});
  cell.addEventListener('paste',event=>{
   event.preventDefault();
   const digits=(event.clipboardData?.getData('text')||'').replace(/\D/g,'').slice(0,6);
   digits.split('').forEach((digit,position)=>cells[position].value=digit);
   cells[Math.min(digits.length,5)].focus();
  });
 });
 resendOtp.addEventListener('click',async()=>{
  if(resendOtp.disabled||otpRequestInProgress||!otpPurpose)return;
  cells.forEach(cell=>cell.value='');
  cells[0].focus();
  await requestOtp(otpPurpose,true);
 });
 renderOtpCooldown();
 verifyOtp.addEventListener('click',async()=>{
  otpError.hidden=true;
  const code=cells.map(cell=>cell.value).join('');
  if(!/^\d{6}$/.test(code)){showOtpError('Enter all six digits.');return;}
  verifyOtp.disabled=true;
  try{
   const result=await securityPost('/otp/verify',{purpose:otpPurpose,code});
   verifiedToken=result.verification_token;
   if(otpPurpose==='forgot'){
    close(otp);
    reset.hidden=false;
    $('aaResetNew').focus();
    return;
   }
   await securityPost('/password/change',{
    current_password:pw.value,
    new_password:np.value,
    new_password_confirmation:cp.value,
    verification_token:verifiedToken
   });
   close(otp);
   changeForm.reset();
   verifyPwForm();
   verifiedToken=null;
   toast('Password changed successfully.');
  }catch(error){
   showOtpError(error.message);
  }finally{
   verifyOtp.disabled=false;
  }
 });
 $('aaCloseOtp').addEventListener('click',()=>close(otp));
 $('aaCloseReset').addEventListener('click',()=>close(reset));
 [otp,reset].forEach(modal=>modal.addEventListener('click',event=>{if(event.target===modal)close(modal);}));
 $('aaResetForm').addEventListener('submit',async event=>{
  event.preventDefault();
  const newPassword=$('aaResetNew').value,confirmation=$('aaResetConfirm').value,errorBox=$('aaResetError');
  errorBox.hidden=true;
  if(newPassword!==confirmation){errorBox.textContent='Passwords do not match.';errorBox.hidden=false;return;}
  const submitButton=event.currentTarget.querySelector('button[type="submit"]');
  submitButton.disabled=true;
  try{
   await securityPost('/password/reset',{
    new_password:newPassword,
    new_password_confirmation:confirmation,
    verification_token:verifiedToken
   });
   close(reset);
   event.currentTarget.reset();
   verifiedToken=null;
   toast('Password reset successfully.');
  }catch(error){
   errorBox.textContent=error.message;
   errorBox.hidden=false;
  }finally{
   submitButton.disabled=false;
  }
 });
 document.addEventListener('keydown',e=>{if(e.key==='Escape'){close(otp);close(reset);closePhotoPreview();}});
});

document.addEventListener('DOMContentLoaded', () => {
/* Shared flash component: Admin Registrations appearance and 3800 ms timing. */
(function(){
 const flash=document.getElementById('accountDecisionFlash');
 if(!flash)return;
 const icon=flash.querySelector('#accountFlashIcon');
 const title=flash.querySelector('#accountFlashTitle');
 const message=flash.querySelector('#accountFlashMessage');
 const close=flash.querySelector('#accountFlashClose');
 let timer=null;
 const check='<svg viewBox="0 0 24 24"><path d="m5 12 4 4L19 6"/></svg>';
 const cross='<svg viewBox="0 0 24 24"><path d="M6 6l12 12M18 6 6 18"/></svg>';
 function dismiss(){clearTimeout(timer);flash.classList.remove('show');}
 window.showAccountFlash=function(text,isError=false,heading){
   if(!text)return;
   flash.classList.remove('approved','rejected','error');
   flash.classList.add(isError?'error':'approved');
   flash.setAttribute('role',isError?'alert':'status');
   icon.innerHTML=isError?cross:check;
   title.textContent=heading||(isError?'Action Failed':'Changes Saved');
   message.textContent=String(text);
   clearTimeout(timer);
   flash.classList.add('show');
   timer=setTimeout(dismiss,3800);
 };
 close.addEventListener('click',dismiss);
 const initialSuccess = accountConfig.initialSuccess;
 const initialError = accountConfig.initialError;
 const initialValidation = accountConfig.initialValidation;
 const which=initialError||initialValidation;
 if(which)window.showAccountFlash(which,true);
 else if(initialSuccess)window.showAccountFlash(initialSuccess,false);
})();
});

/* Administrator account management is persisted through the protected admin API. */
document.addEventListener('DOMContentLoaded', () => {
  const form = document.getElementById('aamCreateForm');
  const $ = id => document.getElementById(id);
  const list = $('aamAccountsList');
  if (!form || !list) return;
  const permissions = [...form.querySelectorAll('[data-aam-permission]')];
  const messagePermissions = [...form.querySelectorAll('[data-aam-message]')];
  const profileSection = form.querySelector('[data-aam-profile]');
  const credentialsSection = form.querySelector('[data-aam-credentials]');
  const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '';
  let temporaryPassword = '';
  const flash = (message, error=false, title=undefined) => {
    if (typeof window.showAccountFlash === 'function') window.showAccountFlash(message,error,title);
  };
  function setEditMode(admin=null) {
    if (admin) {
      form.dataset.editAdminId = String(admin.id);
      profileSection.hidden = true;
      credentialsSection.hidden = true;
      $('aamPassword').required = false;
      $('aamCreate').textContent = 'Save Admin Access';
      $('aamReset').textContent = 'Cancel Edit';
      permissions.forEach(box => { box.checked = admin.permissions?.includes(box.value) || false; });
      messagePermissions.forEach(box => { box.checked = admin.message_permissions?.includes(box.value) || false; });
      updatePermissions();
      form.scrollIntoView({ behavior: 'smooth', block: 'start' });
      return;
    }
    delete form.dataset.editAdminId;
    profileSection.hidden = false;
    credentialsSection.hidden = false;
    $('aamPassword').required = true;
    $('aamCreate').textContent = 'Create Admin Account';
    $('aamReset').textContent = 'Clear Form';
  }
  async function readResponse(response) {
    const payload = await response.json().catch(() => ({}));
    if (!response.ok) {
      const validationMessage = payload.errors ? Object.values(payload.errors).flat()[0] : null;
      throw new Error(validationMessage || payload.message || 'Unable to complete administrator account request.');
    }
    return payload;
  }
  async function loadAccounts() {
    const errorElement = $('aamAccountsError');
    errorElement.hidden = true;
    try {
      const response = await fetch(accountConfig.adminManagementUrl, {
        credentials: 'same-origin',
        headers: { Accept: 'application/json' }
      });
      const payload = await readResponse(response);
      list.replaceChildren();
      if (!payload.data?.length) {
        const empty = document.createElement('p');
        empty.className = 'sa-request-hint';
        empty.textContent = 'No administrator accounts were found.';
        list.append(empty);
        return;
      }
      payload.data.forEach(admin => {
        const row = document.createElement('article');
        row.className = 'aam-account-row';
        const details = document.createElement('div');
        const name = document.createElement('strong');
        name.textContent = admin.name;
        const email = document.createElement('span');
        email.textContent = admin.email;
        const access = document.createElement('small');
        access.textContent = admin.permissions?.length
          ? `Access: ${admin.permissions.join(', ')}${admin.message_permissions?.length ? ` · Messages: ${admin.message_permissions.join(', ')}` : ''}`
          : 'Full administrator access';
        details.append(name, email, access);
        row.append(details);
        const state = document.createElement('span');
        state.className = 'aam-account-state';
        state.textContent = admin.is_enabled ? 'Active' : 'Disabled';
        row.append(state);
        if (admin.id !== accountConfig.adminUserId) {
          const edit = document.createElement('button');
          edit.type = 'button';
          edit.className = 'aam-outline-btn';
          edit.textContent = 'Edit Access';
          edit.addEventListener('click', () => setEditMode(admin));
          row.append(edit);
          const toggle = document.createElement('button');
          toggle.type = 'button';
          toggle.className = 'aam-outline-btn';
          toggle.textContent = admin.is_enabled ? 'Disable' : 'Enable';
          toggle.addEventListener('click', async () => {
            toggle.disabled = true;
            try {
              const update = await fetch(`${accountConfig.adminManagementUrl}/${admin.id}`, {
                method: 'PATCH',
                credentials: 'same-origin',
                headers: {
                  Accept: 'application/json',
                  'Content-Type': 'application/json',
                  'X-CSRF-TOKEN': csrfToken
                },
                body: JSON.stringify({ is_enabled: !admin.is_enabled })
              });
              const result = await readResponse(update);
              flash(result.message);
              await loadAccounts();
            } catch (error) {
              toggle.disabled = false;
              flash(error.message, true);
            }
          });
          row.append(toggle);
        }
        list.append(row);
      });
    } catch (error) {
      errorElement.textContent = error.message;
      errorElement.hidden = false;
      list.replaceChildren();
    }
  }
  function generatePassword() {
    if (!globalThis.crypto?.getRandomValues) { flash('Secure password generation is unavailable in this browser.',true); return; }
    const groups = ['ABCDEFGHJKLMNPQRSTUVWXYZ','abcdefghijkmnopqrstuvwxyz','23456789','!@#$%*-_'];
    const random = max => { const buf = new Uint32Array(1); const limit = Math.floor(0x100000000/max)*max; let x; do {crypto.getRandomValues(buf); x=buf[0];} while (x>=limit); return x%max; };
    const chars=groups.map(g=>g[random(g.length)]);
    const all=groups.join('');
    for(let i=chars.length;i<18;i++)chars.push(all[random(all.length)]);
    for(let i=chars.length-1;i>0;i--){const j=random(i+1);[chars[i],chars[j]]=[chars[j],chars[i]];}
    temporaryPassword=chars.join('');
    $('aamPassword').value=temporaryPassword;
    $('aamPassword').type='password';$('aamRevealPassword').textContent='Show';
    $('aamResult').hidden=true;
    flash('A new strong temporary password has been generated.',false,'Password Generated');
  }
  function updatePermissions() {
    const enabled=$('aamMessages').checked;
    messagePermissions.forEach(box=>{box.disabled=!enabled;if(!enabled)box.checked=false;});
    const count=permissions.filter(x=>x.checked).length;
    $('aamRoleCount').textContent=`${count} module${count===1?'':'s'} selected`;
  }
  permissions.forEach(box=>box.addEventListener('change',updatePermissions));
  $('aamSelectAll').addEventListener('click',()=>{permissions.forEach(x=>x.checked=true);updatePermissions();messagePermissions.forEach(x=>x.checked=true);});
  $('aamClearAll').addEventListener('click',()=>{permissions.forEach(x=>x.checked=false);updatePermissions();});
  $('aamGenerate').addEventListener('click',generatePassword);
  $('aamRevealPassword').addEventListener('click',()=>{if(!temporaryPassword){flash('Generate a password first.',true);return;}$('aamPassword').type=$('aamPassword').type==='password'?'text':'password';$('aamRevealPassword').textContent=$('aamPassword').type==='password'?'Show':'Hide';});
  async function copy(text){if(!text){flash('Generate a password first.',true);return;}try{await navigator.clipboard.writeText(text);flash('Copied to clipboard.',false,'Copied');}catch(_){flash('Clipboard is unavailable. Use Show to copy the password manually.',true);}}
  $('aamCopyPassword').addEventListener('click',()=>copy(temporaryPassword));
  $('aamCopyCredentials').addEventListener('click',()=>copy(`ShopEase Administrator\nEmail: ${$('aamEmail').value.trim()}\nTemporary password: ${temporaryPassword}`));
  $('aamBirthday').addEventListener('change',()=>{const value=$('aamBirthday').value;const b=value?new Date(value+'T00:00:00'):null;let age='';if(b&&!isNaN(b)&&b<=new Date()){const n=new Date();age=n.getFullYear()-b.getFullYear()-(n.getMonth()<b.getMonth()||(n.getMonth()===b.getMonth()&&n.getDate()<b.getDate())?1:0);} $('aamAge').value=age;});
  ['aamProfilePhoto','aamIdPhoto'].forEach(id=>$(id).addEventListener('change',e=>{const file=e.target.files[0];if(file&&(file.size>5*1024*1024||!['image/jpeg','image/png','image/webp'].includes(file.type))){e.target.value='';flash('Please select a JPG, PNG or WebP image under 5 MB.',true);}}));
  form.addEventListener('submit',async event=>{
    event.preventDefault();
    const editId = form.dataset.editAdminId;
    if(!editId && !form.reportValidity())return;
    if(!permissions.some(x=>x.checked)){flash('Select at least one administrator module.',true,'Permissions Required');return;}
    if($('aamMessages').checked&&!messagePermissions.some(x=>x.checked)){flash('Select at least one Messages category.',true,'Messages Access Required');return;}
    if(!editId && !temporaryPassword){flash('Generate a temporary password first.',true);return;}
    const fullname=[$('aamFirst').value.trim(),$('aamMiddle').value.trim(),$('aamLast').value.trim()].filter(Boolean).join(' ');
    const chosen=permissions.filter(x=>x.checked).map(x=>x.value);
    const messages=messagePermissions.filter(x=>x.checked).map(x=>x.value);
    const button=$('aamCreate');
    button.disabled=true;
    try {
      const response=await fetch(editId ? `${accountConfig.adminManagementUrl}/${editId}` : form.action,{
        method:editId ? 'PATCH' : 'POST',
        credentials:'same-origin',
        headers:editId
          ? {Accept:'application/json','Content-Type':'application/json','X-CSRF-TOKEN':csrfToken}
          : {Accept:'application/json','X-CSRF-TOKEN':csrfToken},
        body:editId
          ? JSON.stringify({permissions:chosen,message_permissions:messages})
          : new FormData(form)
      });
      const result=await readResponse(response);
      if (editId) {
        setEditMode();
        permissions.forEach(box => { box.checked = false; });
        messagePermissions.forEach(box => { box.checked = false; });
        updatePermissions();
        flash(result.message,false,'Access Updated');
      } else {
        $('aamResultSummary').textContent=`${fullname} — administrator account created successfully.`;
        $('aamResultEmail').textContent=$('aamEmail').value.trim();
        $('aamResultPassword').textContent='Hidden — use Copy Credentials';
        $('aamResultRoles').textContent=`Module access: ${chosen.join(', ')}${messages.length?'. Messages: '+messages.join(', '):''}.`;
        $('aamResult').hidden=false;
        flash(result.message,false,'Administrator Created');
        $('aamResult').scrollIntoView({behavior:'smooth',block:'nearest'});
      }
      await loadAccounts();
    } catch(error) {
      flash(error.message,true);
    } finally {
      button.disabled=false;
    }
  });
  form.addEventListener('reset',()=>setTimeout(()=>{setEditMode();temporaryPassword='';$('aamPassword').value='';$('aamAge').value='';$('aamResult').hidden=true;$('aamPassword').type='password';$('aamRevealPassword').textContent='Show';updatePermissions();flash('The administrator form has been cleared.',false,'Form Cleared');},0));
  updatePermissions();
  loadAccounts();
});