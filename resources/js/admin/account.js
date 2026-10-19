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
 $$('[data-admin-tab]').forEach(a=>a.addEventListener('click',e=>{e.preventDefault();switchTab(a.dataset.adminTab);}));const initialTab=['profile','privacy','notifications'].includes(location.hash.slice(1))?location.hash.slice(1):'profile';
 switchTab(initialTab);
 if(location.hash) history.replaceState(null,'',location.pathname+location.search);
 window.addEventListener('pageshow',()=>{requestAnimationFrame(()=>window.scrollTo({top:0,left:0,behavior:'auto'}));});
 requestAnimationFrame(()=>window.scrollTo({top:0,left:0,behavior:'auto'}));
 function toast(text,err=false){window.showAccountFlash(text,err,err?"Action Failed":"Changes Saved");}
 const birthday=$('aaBirthday'),age=$('aaAge');function calc(){if(!birthday.value){age.value='';return;}const d=new Date(birthday.value+'T00:00:00'),n=new Date();if(isNaN(d)||d>n){age.value='';return;}let v=n.getFullYear()-d.getFullYear();if(n.getMonth()<d.getMonth()||(n.getMonth()===d.getMonth()&&n.getDate()<d.getDate()))v--;age.value=String(v);}birthday.addEventListener('change',calc);calc();
 const form=$('adminProfileForm'),fields=$$('#adminProfileForm input[name],#adminProfileForm select[name]').filter(el=>!['_token','_method'].includes(el.name));
 const original=new Map(fields.map(el=>[el,el.value]));function dirty(){const d=fields.some(el=>el.type==='file'?el.files.length>0:el.value!==original.get(el));$('aaSaveProfile').disabled=!d;}form.addEventListener('input',dirty);form.addEventListener('change',dirty);
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
 const otp=$('aaOtpModal'),reset=$('aaResetModal'),cells=$$('.sa-otp-cell');
 const otpError=$('aaOtpError'),verifyOtp=$('aaVerifyOtp'),resendOtp=$('aaResendOtp');
 const resendLabel=resendOtp.querySelector('span');
 const csrfToken=document.querySelector('meta[name="csrf-token"]')?.content;
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