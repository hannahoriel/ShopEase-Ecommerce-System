(()=>{
 const form=document.getElementById('logisticsPermitsForm');if(!form)return;
 let attempted=false;
 const required=['business_registration','mayors_permit','bir_certificate','representative_authorization'];
 function check(input){
   const file=input.files?.[0],saved=document.getElementById('stored_'+input.id);
   let msg='';
   if(!file&&!saved)msg='This document is required.';
   if(file&&!['image/jpeg','image/png','application/pdf'].includes(file.type))msg='Please upload a JPG, PNG, or PDF file.';
   if(file&&file.size>5*1024*1024)msg='The file must be 5 MB or smaller.';
   document.getElementById('box_'+input.id)?.classList.toggle('is-invalid',!!msg);
   const error=document.getElementById('error_'+input.id);if(error)error.textContent=msg;
   input.setAttribute('aria-invalid',String(!!msg));
   input.setCustomValidity(msg);
   return !!msg;
 }
 form.addEventListener('submit',event=>{
   attempted=true;let first=null;
   for(const id of required){const input=document.getElementById(id);if(input&&check(input)&&!first)first=input;}
   for(const input of form.querySelectorAll('input[type="file"]')){
     if(required.includes(input.id)||!input.files?.length)continue;
     if(check(input)&&!first)first=input;
   }
   if(first){event.preventDefault();event.stopImmediatePropagation();first.closest('.upload-box')?.scrollIntoView({behavior:'smooth',block:'center'});}
 },true);
 form.querySelectorAll('input[type="file"]').forEach(input=>input.addEventListener('change',()=>{if(attempted||input.files.length)check(input);}));
})();

(() => {
 const form = document.getElementById('logisticsPermitsForm');
 const maxBytes = 5 * 1024 * 1024;
 const accepted = ['image/jpeg', 'image/png', 'application/pdf'];
 const objectUrls = [];
 function setError(input, msg) {
   const error = document.getElementById('error_' + input.id);
   const box = document.getElementById('box_' + input.id);
   error.textContent = msg;
   box.classList.toggle('is-invalid', Boolean(msg));
   input.setCustomValidity(msg);
 }
 form.querySelectorAll('input[type="file"]').forEach(input => {
  input.addEventListener('change', () => {
   setError(input, '');
   const file = input.files[0];
   const box = document.getElementById('box_' + input.id);
   const text = document.getElementById('label_' + input.id);
   const preview = document.getElementById('preview_' + input.id);
   preview.style.display = 'none'; preview.removeAttribute('src');
   const saved = document.getElementById('stored_' + input.id);
   if(!file) {text.textContent='Click to upload a document'; box.classList.remove('has-file'); return;}
   if(!accepted.includes(file.type) || file.size > maxBytes) {
      setError(input, !accepted.includes(file.type) ? 'Please upload a JPG, PNG, or PDF file.' : 'The file must be 5 MB or smaller.');
      return;
   }
   box.classList.add('has-file'); text.textContent = file.name;
   if (saved) saved.textContent = 'New document selected; saving will replace the previous upload.';
   if(file.type.startsWith('image/')) {const url=URL.createObjectURL(file);objectUrls.push(url);preview.src=url;preview.style.display='block';}
  });
 });
 form.addEventListener('submit', event => {
    let firstInvalid = null;
    form.querySelectorAll('input[type="file"]').forEach(input => {
     const saved = document.getElementById('stored_' + input.id);
     const file = input.files[0];
     if (!file && input.required && !saved) setError(input,'This document is required.');
     if (input.validationMessage && !firstInvalid) firstInvalid = input;
    });
    if(firstInvalid){event.preventDefault();firstInvalid.focus();return;}
    const button=document.getElementById('savePermitsButton');button.disabled=true;button.textContent='Saving...';
 });
 window.addEventListener('pagehide',()=>objectUrls.forEach(url=>URL.revokeObjectURL(url)));
})();