/* ShopEase Seller Account JS — dynamic Laravel values are supplied by the Blade config. */
const sellerAccountPageData = JSON.parse(document.getElementById('sellerAccountPageConfig').textContent);

      document.addEventListener('DOMContentLoaded', function () {
        // Account tabs, no navigation away or profile data loss.
        const tabLinks=[...document.querySelectorAll('[data-settings-tab]')];
        const tabPanels=[...document.querySelectorAll('[data-panel]')];
        function activateAccountTab(tab){
          const valid=['profile','privacy','notifications','policies'].includes(tab)?tab:'profile';
          tabPanels.forEach(panel=>panel.hidden=panel.dataset.panel!==valid);
          tabLinks.forEach(link=>{const active=link.dataset.settingsTab===valid;link.classList.toggle('active',active);if(active)link.setAttribute('aria-current','page');else link.removeAttribute('aria-current');});
          history.replaceState(null,'','#'+valid);
        }
        tabLinks.forEach(a=>a.addEventListener('click',e=>{e.preventDefault();activateAccountTab(a.dataset.settingsTab);}));
        activateAccountTab(location.hash.slice(1)||'profile');
        window.addEventListener('hashchange',()=>activateAccountTab(location.hash.slice(1)));

        const root = document.getElementById('sellerAccountPage');
        const sidebar = document.getElementById('sellerSidebar');
        const syncOffset = () => { if (window.innerWidth <= 760) {root.style.marginLeft = '0px';return;} root.style.marginLeft = sidebar && sidebar.classList.contains('seller-sidebar-collapsed') ? '100px' : '270px'; };
        syncOffset(); window.addEventListener('resize', syncOffset);
        if (sidebar) new MutationObserver(syncOffset).observe(sidebar, {attributes:true,attributeFilter:['class']});
        const dob = document.getElementById('saDob'), age = document.getElementById('saAge');
        function calculateAge() { if (!dob.value) {age.value='';return;} const d=new Date(dob.value+'T00:00:00'),now=new Date(); if(isNaN(d.getTime())||d>now){age.value='';return;} let years=now.getFullYear()-d.getFullYear(); if(now.getMonth()<d.getMonth()||(now.getMonth()===d.getMonth()&&now.getDate()<d.getDate())) years--;age.value=years>=0?years:''; }
        dob.addEventListener('change',calculateAge);calculateAge();
        function wireFile(id,imgId,labelId){const input=document.getElementById(id);input.addEventListener('change',()=>{const file=input.files?.[0];if(!file)return;if(file.size>5*1024*1024){window.showAccountFlash('Please choose an image smaller than 5 MB.',true);input.value='';return;}if(!['image/jpeg','image/png','image/webp'].includes(file.type)){window.showAccountFlash('Please choose a JPG, PNG or WebP image.',true);input.value='';return;}if(labelId)document.getElementById(labelId).textContent=file.name;if(imgId){const img=document.getElementById(imgId);if(img.dataset.objectUrl)URL.revokeObjectURL(img.dataset.objectUrl);img.dataset.objectUrl=URL.createObjectURL(file);img.src=img.dataset.objectUrl;img.style.display='block';if(id==='saAvatarInput')document.getElementById('saAvatarFallback')?.remove();}});}
        wireFile('saCoverInput','saCoverImage');wireFile('saAvatarInput','saAvatarImage');
                 // Registered categories are read-only on the profile. Changes require an admin request.

                 // Reset remains native: registered categories cannot be edited directly.
         const form=document.getElementById('sellerAccountForm');
         // Enable Save Changes only when an actual editable field or photo differs
         // from its initial state. Category/document request modals are separate forms.
         const saveChanges = document.getElementById('saSaveChanges');
         const editableFields = [...form.querySelectorAll('input, select, textarea')].filter(el =>
             el.name && !['_token', '_method'].includes(el.name) && !el.disabled && el.type !== 'hidden'
         );
         const initialValues = new Map(editableFields.map(el => [el, el.type === 'checkbox' || el.type === 'radio' ? el.checked : el.value]));
         const isModified = el => {
             if (el.type === 'file') return el.files && el.files.length > 0;
             if (el.type === 'checkbox' || el.type === 'radio') return el.checked !== initialValues.get(el);
             return el.value !== initialValues.get(el);
         };
         const refreshSaveButton = () => {
             const changed = editableFields.some(isModified);
             saveChanges.disabled = !changed;
             saveChanges.setAttribute('aria-disabled', String(!changed));
         };
         form.addEventListener('input', refreshSaveButton);
         form.addEventListener('change', refreshSaveButton);
         form.addEventListener('reset', () => setTimeout(() => { calculateAge(); refreshSaveButton(); }, 0));
         form.addEventListener('submit', e => { if (!editableFields.some(isModified)) e.preventDefault(); });
         refreshSaveButton();
         // Document previews (existing + newly selected files)
         const previewModal=document.getElementById('saPreviewModal');
         const previewImg=document.getElementById('saPreviewImage');
         const previewHeading=document.getElementById('saPreviewHeading');
         let previewObjectUrl=null;
         function showPreview(src,title){
           if(previewObjectUrl){URL.revokeObjectURL(previewObjectUrl);previewObjectUrl=null;}
           previewImg.src=src;previewHeading.textContent=title;
           previewModal.hidden=false;document.body.classList.add('sa-modal-open');
         }
         function closePreview(){previewModal.hidden=true;previewImg.removeAttribute('src');document.body.classList.remove('sa-modal-open');if(previewObjectUrl){URL.revokeObjectURL(previewObjectUrl);previewObjectUrl=null;}}
         document.querySelectorAll('[data-document-url]').forEach(btn=>btn.addEventListener('click',()=>showPreview(btn.dataset.documentUrl,btn.dataset.documentName)));
         document.querySelectorAll('[data-close-preview]').forEach(btn=>btn.addEventListener('click',closePreview));
         previewModal.addEventListener('click',e=>{if(e.target===previewModal)closePreview();});
         // Category change request modal: initial selection always matches approved categories.
         const requestModal=document.getElementById('saCategoryRequestModal');
         const requestCategories=new Set(sellerAccountPageData.selectedCategories);
         const allCategories=sellerAccountPageData.categoryOptions;
         const choices=document.getElementById('saRequestChoices');
         const inputs=document.getElementById('saRequestInputs');
         function renderRequestChoices(){
           choices.replaceChildren();inputs.replaceChildren();
           allCategories.forEach(name=>{
             const label=document.createElement('label');label.className='sa-request-choice';
             const check=document.createElement('input');check.type='checkbox';check.checked=requestCategories.has(name);
             check.addEventListener('change',()=>{if(check.checked)requestCategories.add(name);else requestCategories.delete(name);renderRequestChoices();});
             const t=document.createElement('span');t.textContent=name;label.append(check,t);choices.append(label);
           });
           requestCategories.forEach(name=>{const h=document.createElement('input');h.type='hidden';h.name='requested_categories[]';h.value=name;inputs.append(h);});
         }
         renderRequestChoices();
         document.getElementById('saOpenCategoryRequest')?.addEventListener('click',()=>{requestModal.hidden=false;document.body.classList.add('sa-modal-open');});
         function closeRequest(){requestModal.hidden=true;document.body.classList.remove('sa-modal-open');}
         document.querySelectorAll('[data-close-request]').forEach(btn=>btn.addEventListener('click',closeRequest));
         requestModal.addEventListener('click',e=>{if(e.target===requestModal)closeRequest();});
         document.getElementById('saCategoryRequestForm').addEventListener('submit',e=>{
           if(!requestCategories.size){e.preventDefault();alert('Select at least one category.');return;}
           const original=new Set(sellerAccountPageData.selectedCategories);
           if(original.size===requestCategories.size&&[...requestCategories].every(x=>original.has(x))){e.preventDefault();alert('Please select a different category selection to request a change.');}
         });
         document.addEventListener('keydown',e=>{if(e.key==='Escape'){if(!previewModal.hidden)closePreview();if(!requestModal.hidden)closeRequest();}});


         // Direct image click opens modal; edit controls still open the file picker.
         [['saCoverImage','Cover Photo'],['saAvatarImage','Profile Photo']].forEach(([id,title])=>{
             const img=document.getElementById(id);
             img?.addEventListener('click',()=>{if(img.src&&img.style.display!=='none')showPreview(img.src,title);});
         });
         document.getElementById('saCover')?.addEventListener('click',e=>{
             if(e.target.closest('label,input,button,img'))return;
             const img=document.getElementById('saCoverImage');if(img?.src&&img.style.display!=='none')showPreview(img.src,'Cover Photo');
         });
         // Document request modal: never directly updates approved documents.
         const docModal=document.getElementById('saDocumentRequestModal');
         const docType=document.getElementById('saDocumentRequestType');
         const docTitle=document.getElementById('saDocumentRequestTitle');
         const docFile=document.getElementById('saDocumentNewFile');
         const docPreview=document.getElementById('saDocumentNewPreview');
         let docObjectUrl=null;
         const docNames={replace_valid_id:'Replace Valid ID',add_business_permit:'Add Business Permit',replace_business_permit:'Replace Business Permit'};
         document.querySelectorAll('[data-open-doc-request]').forEach(btn=>btn.addEventListener('click',()=>{
             if(btn.disabled)return;
             document.getElementById('saDocumentRequestForm').reset();
             if(docObjectUrl){URL.revokeObjectURL(docObjectUrl);docObjectUrl=null;}
             docPreview.style.display='none';docPreview.removeAttribute('src');
             docType.value=btn.dataset.openDocRequest;docTitle.textContent=docNames[docType.value]||'Document Request';
             docModal.hidden=false;document.body.classList.add('sa-modal-open');
         }));
         document.getElementById('saDocumentRequestForm')?.addEventListener('submit', e => {
             if (e.currentTarget.dataset.connected !== '1') {
                 e.preventDefault();
                 alert('The document request backend route is not registered. Please connect the document request route first.');
             }
         });
         docFile.addEventListener('change',()=>{
             if(docObjectUrl){URL.revokeObjectURL(docObjectUrl);docObjectUrl=null;}
             const file=docFile.files?.[0];if(!file){docPreview.style.display='none';return;}
             if(file.size>5*1024*1024 || !['image/jpeg','image/png','image/webp'].includes(file.type)){
                 alert('Choose a JPG, PNG or WebP image smaller than 5 MB.');docFile.value='';docPreview.style.display='none';return;
             }
             docObjectUrl=URL.createObjectURL(file);docPreview.src=docObjectUrl;docPreview.style.display='block';
         });
         docPreview.addEventListener('click',()=>{if(docPreview.src)showPreview(docPreview.src,'Selected Document');});
         function closeDoc(){docModal.hidden=true;document.body.classList.remove('sa-modal-open');if(docObjectUrl){URL.revokeObjectURL(docObjectUrl);docObjectUrl=null;}}
         document.querySelectorAll('[data-close-doc-request]').forEach(b=>b.addEventListener('click',closeDoc));
         docModal.addEventListener('click',e=>{if(e.target===docModal)closeDoc();});
         document.addEventListener('keydown',e=>{if(e.key==='Escape'&&!docModal.hidden)closeDoc();});


        const csrf=document.querySelector('meta[name="csrf-token"]')?.content;
        const settingsBase=sellerAccountPageData.settingsBase;
        const sellerUserId=sellerAccountPageData.sellerUserId;
        async function settingsPost(path,payload){
          const r=await fetch(settingsBase+path,{method:'POST',headers:{'X-CSRF-TOKEN':csrf,'Content-Type':'application/json','Accept':'application/json'},credentials:'same-origin',body:JSON.stringify(payload)});
          let data={};try{data=await r.json();}catch(e){}
          if(!r.ok){
            const error=new Error(data.message||(data.errors&&Object.values(data.errors).flat()[0])||'The request could not be completed.');
            error.retryAfter=Number(data.retry_after||r.headers.get('Retry-After')||0);
            throw error;
          }
          return data;
        }
        function settingsToast(message,isError=false){window.showAccountFlash(message,isError,isError?"Action Failed":"Changes Saved");}
        const pwForm=document.getElementById('saChangePasswordForm');
        const currentPw=document.getElementById('saCurrentPassword'),newPw=document.getElementById('saNewPassword'),confirmPw=document.getElementById('saConfirmPassword');
        const pwButton=document.getElementById('saChangePasswordButton');
        function pwDirty(){pwButton.disabled=!(currentPw.value&&newPw.value.length>=8&&newPw.value===confirmPw.value);}
        [currentPw,newPw,confirmPw].forEach(x=>x.addEventListener('input',pwDirty));
        const otpModal=document.getElementById('saSecurityOtpModal'),forgotModal=document.getElementById('saForgotPasswordModal');
        const otpInputs=[...document.querySelectorAll('.sa-otp-digit')];
        const otpError=document.getElementById('saOtpError'),verifyOtp=document.getElementById('saVerifyOtp'),resendOtp=document.getElementById('saResendOtp');
        let securityPurpose=null,verifiedToken=null;
        let otpRequestInProgress=false,otpCooldownInterval=null;
        const otpCooldownSeconds=600;
        const otpResendLabel=resendOtp.querySelector('span');
        const otpCooldownKey=purpose=>`seller-security-resend-cooldown-v2:${sellerUserId}:${purpose}`;
        function otpCooldownRemaining(purpose){
          return Math.max(0,Math.ceil((Number(sessionStorage.getItem(otpCooldownKey(purpose))||0)-Date.now())/1000));
        }
        function renderOtpCooldown(){
          const remaining=securityPurpose?otpCooldownRemaining(securityPurpose):0;
          if(remaining>0){
            const minutes=String(Math.floor(remaining/60)).padStart(2,'0');
            const seconds=String(remaining%60).padStart(2,'0');
            otpResendLabel.textContent=`Resend code in ${minutes}:${seconds}`;
            resendOtp.disabled=true;
            if(!otpCooldownInterval)otpCooldownInterval=setInterval(renderOtpCooldown,1000);
            return;
          }
          otpResendLabel.textContent='Resend Code';
          resendOtp.disabled=otpRequestInProgress;
          if(otpCooldownInterval){clearInterval(otpCooldownInterval);otpCooldownInterval=null;}
          if(securityPurpose)sessionStorage.removeItem(otpCooldownKey(securityPurpose));
        }
        function startOtpCooldown(purpose,seconds=otpCooldownSeconds){
          sessionStorage.setItem(otpCooldownKey(purpose),String(Date.now()+seconds*1000));
          securityPurpose=purpose;
          renderOtpCooldown();
        }
        function closeSecurityModal(modal){modal.hidden=true;document.body.classList.remove('sa-modal-open');}
        function showSecurityError(message,where=otpError){where.textContent=message;where.hidden=false;}
        async function requestSecurityOtp(purpose,isResend=false){
          securityPurpose=purpose;
          verifiedToken=null;
          document.getElementById('saSecurityOtpTitle').textContent='Verify Your Email';
          otpInputs.forEach(x=>x.value='');
          otpError.hidden=true;
          if(otpCooldownRemaining(purpose)>0){
            otpModal.hidden=false;
            document.body.classList.add('sa-modal-open');
            renderOtpCooldown();
            otpInputs[0]?.focus();
            settingsToast('A verification code was already sent. Please check your email.',true);
            return;
          }
          otpRequestInProgress=true;
          renderOtpCooldown();
          try{
            const result=await settingsPost('/otp/send',{purpose});
            if(isResend)startOtpCooldown(purpose);
            otpModal.hidden=false;
            document.body.classList.add('sa-modal-open');
            otpInputs[0]?.focus();
            settingsToast(result.message||'A verification code was sent to your registered email.');
          }catch(err){
            if(err.retryAfter>0)startOtpCooldown(purpose,err.retryAfter);
            settingsToast(err.message,true);
          }finally{
            otpRequestInProgress=false;
            renderOtpCooldown();
          }
        }
        pwForm.addEventListener('submit',async e=>{e.preventDefault();if(pwButton.disabled)return;await requestSecurityOtp('change');});
        document.getElementById('saForgotPasswordButton').addEventListener('click',()=>requestSecurityOtp('forgot'));
        otpInputs.forEach((input,index)=>{
          input.addEventListener('input',()=>{input.value=input.value.replace(/\D/g,'').slice(-1);if(input.value&&index<5)otpInputs[index+1].focus();});
          input.addEventListener('keydown',e=>{if(e.key==='Backspace'&&!input.value&&index>0)otpInputs[index-1].focus();});
          input.addEventListener('paste',e=>{e.preventDefault();const digits=(e.clipboardData?.getData('text')||'').replace(/\D/g,'').slice(0,6).split('');digits.forEach((v,i)=>otpInputs[i].value=v);otpInputs[Math.min(digits.length,5)].focus();});
        });
        document.getElementById('saCloseOtp').addEventListener('click',()=>closeSecurityModal(otpModal));
        document.getElementById('saCloseForgot').addEventListener('click',()=>closeSecurityModal(forgotModal));
        resendOtp.addEventListener('click',async()=>{
          if(resendOtp.disabled||!securityPurpose)return;
          otpInputs.forEach(x=>x.value='');
          await requestSecurityOtp(securityPurpose,true);
        });
        renderOtpCooldown();
        verifyOtp.addEventListener('click',async()=>{
          otpError.hidden=true;
          const code=otpInputs.map(x=>x.value).join('');
          if(!/^\d{6}$/.test(code)){
            showSecurityError('Please enter the six-digit verification code.');
            return;
          }
          verifyOtp.disabled=true;
          try{
            const result=await settingsPost('/otp/verify',{purpose:securityPurpose,code});
            verifiedToken=result.verification_token;
            if(securityPurpose==='forgot'){
              closeSecurityModal(otpModal);
              forgotModal.hidden=false;
              document.body.classList.add('sa-modal-open');
              document.getElementById('saResetNew').focus();
            }else{
              await settingsPost('/password/change',{
                current_password:currentPw.value,
                new_password:newPw.value,
                new_password_confirmation:confirmPw.value,
                verification_token:verifiedToken
              });
              closeSecurityModal(otpModal);
              pwForm.reset();
              pwDirty();
              settingsToast('Password changed successfully.');
            }
          }catch(err){
            showSecurityError(err.message);
          }finally{
            verifyOtp.disabled=false;
          }
        });
        document.getElementById('saForgotFinalForm').addEventListener('submit',async e=>{
          e.preventDefault();
          const pw=document.getElementById('saResetNew').value;
          const confirm=document.getElementById('saResetConfirm').value;
          const box=document.getElementById('saResetError');box.hidden=true;
          if(pw!==confirm){showSecurityError('New passwords do not match.',box);return;}
          const submitButton=e.currentTarget.querySelector('button[type="submit"]');
          submitButton.disabled=true;
          try{
            await settingsPost('/password/reset',{
              new_password:pw,
              new_password_confirmation:confirm,
              verification_token:verifiedToken
            });
            closeSecurityModal(forgotModal);
            e.target.reset();
            verifiedToken=null;
            settingsToast('Password reset successfully.');
          }catch(err){
            showSecurityError(err.message,box);
          }finally{
            submitButton.disabled=false;
          }
        });
        [otpModal,forgotModal].forEach(m=>m.addEventListener('click',e=>{if(e.target===m)closeSecurityModal(m);}));
        const prefsForm=document.getElementById('saNotificationSettingsForm'),prefsButton=document.getElementById('saSaveNotifications');
        const prefCheckboxes=[...prefsForm.querySelectorAll('input[type="checkbox"]')];const initialPrefs=prefCheckboxes.map(x=>x.checked);
        function updatePrefsButton(){prefsButton.disabled=!prefCheckboxes.some((x,i)=>x.checked!==initialPrefs[i]);}
        prefCheckboxes.forEach(x=>x.addEventListener('change',updatePrefsButton));
        prefsForm.addEventListener('submit',async e=>{
          e.preventDefault();if(prefsButton.disabled)return;
          if(prefsForm.dataset.connected!=='1'){settingsToast('Notification preferences need the provided backend route to save.',true);return;}
          prefsButton.disabled=true;
          try{
            const values={};prefCheckboxes.forEach(x=>{const key=x.name.match(/\[([^\]]+)\]/)?.[1];if(key)values[key]=x.checked;});
            await settingsPost('/notifications',{preferences:values});prefCheckboxes.forEach((x,i)=>initialPrefs[i]=x.checked);updatePrefsButton();settingsToast('Notification preferences saved successfully.');
          }catch(err){settingsToast(err.message,true);updatePrefsButton();}
        });

      });
    

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
 const initialSuccess=sellerAccountPageData.initialSuccess;
 const initialError=sellerAccountPageData.initialError;
 const initialValidation=sellerAccountPageData.initialValidation;
 const which=initialError||initialValidation;
 if(which)window.showAccountFlash(which,true);
 else if(initialSuccess)window.showAccountFlash(initialSuccess,false);
})();
