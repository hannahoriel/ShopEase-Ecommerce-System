<header class="lg-navbar">
    <div class="lg-nav-left"><button type="button" id="lgSidebarToggle" class="lg-icon-button" aria-label="Toggle sidebar" aria-expanded="true"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 6h16M4 12h16M4 18h16"/></svg></button><h2>{{ collect(['dashboard'=>'Dashboard','courier-management'=>'Courier Management','delivery-hubs'=>'Delivery Hubs','scanners'=>'Scanners','parcel-tracking'=>'Parcel Tracking','parcel-disputes'=>'Parcel Disputes','vehicles-drivers'=>'Vehicles and Drivers','earnings'=>'Earnings','messages'=>'Messages','reports'=>'Reports','account'=>'Account'])->first(fn($label,$slug) => request()->routeIs('sorting-center.'.$slug)) ?: 'Sorting Center' }}</h2></div>
    <div class="lg-nav-right"><span class="lg-navbar-caption">Sorting Center Management</span>
        <button class="lg-icon-button lg-notify-button" type="button" id="lgNotifyButton" title="Notifications" aria-label="Show notification message"><img src="{{ asset('icons/admin/dashboard/sidebar&navbar/notification.png') }}" alt="" onerror="this.style.display='none'"></button>
        <div class="lg-profile-wrap"><button class="lg-profile-btn" id="lgProfileButton" type="button" aria-expanded="false"><x-account-avatar :name="data_get(auth()->user(), 'name') ?: 'Sorting Center'" :image="auth()->user()->profile_photo_path ? \Illuminate\Support\Facades\Storage::disk('public')->url(auth()->user()->profile_photo_path) : null" size="34px" /><span><strong>{{ data_get(auth()->user(), 'name') ?: 'Sorting Center' }}</strong><small>Sorting Center Account · Preview</small></span><span class="lg-profile-chevron">⌄</span></button>
            <div class="lg-profile-menu" id="lgProfileMenu" hidden><span>ShopEase Sorting Center</span><button type="button" id="lgChangeProfilePhoto">Change Profile Photo</button><input id="lgProfilePhotoInput" type="file" accept="image/jpeg,image/png,image/webp" hidden><a href="{{ url('/logistics/landing') }}">Visit Landing Page</a>@if(\Illuminate\Support\Facades\Route::has('logout'))<form method="POST" action="{{ route('logout') }}">@csrf<button type="submit">Log Out</button></form>@endif</div></div>
    </div>
</header>
<script>
document.addEventListener('DOMContentLoaded', function () {
  const $ = id => document.getElementById(id);
  const toggle=$('lgSidebarToggle'), profile=$('lgProfileButton'), menu=$('lgProfileMenu');
  if(toggle && !toggle.dataset.scBound){toggle.dataset.scBound='1';toggle.addEventListener('click',function(){document.body.classList.toggle('lg-collapsed');toggle.setAttribute('aria-expanded',String(!document.body.classList.contains('lg-collapsed')));});}
  if(profile && menu && !profile.dataset.scBound){profile.dataset.scBound='1';profile.addEventListener('click',function(e){e.stopPropagation();menu.hidden=!menu.hidden;profile.setAttribute('aria-expanded',String(!menu.hidden));});document.addEventListener('click', e=>{if(!e.target.closest('.lg-profile-wrap')){menu.hidden=true;profile.setAttribute('aria-expanded','false');}});document.addEventListener('keydown',e=>{if(e.key==='Escape'){menu.hidden=true;profile.setAttribute('aria-expanded','false');}});}
  const notify=$('lgNotifyButton'); if(notify)notify.addEventListener('click',()=>{window.alert('No live notifications connected yet.');});
  const change=$('lgChangeProfilePhoto'),fileInput=$('lgProfilePhotoInput');
  if(change && fileInput){change.addEventListener('click',()=>fileInput.click());fileInput.addEventListener('change',async event=>{
    const file=event.target.files?.[0];if(!file)return;
    if(!['image/jpeg','image/png','image/webp'].includes(file.type)||file.size>5*1024*1024){event.target.value='';window.alert('Choose a JPG, PNG, or WebP image under 5 MB.');return;}
    const form=new FormData();form.append('profile_photo',file);
    try{const response=await fetch('/api/v1/profile/photo',{method:'PUT',credentials:'same-origin',headers:{'Accept':'application/json','X-CSRF-TOKEN':document.querySelector('meta[name="csrf-token"]')?.content||''},body:form});const result=await response.json();if(!response.ok)throw Error(result.message||'Unable to update profile photo.');const avatar=document.querySelector('#lgProfileButton .account-avatar');if(avatar&&result.profile_photo_url){let img=avatar.querySelector('img');if(!img){img=document.createElement('img');avatar.append(img);}img.src=result.profile_photo_url;img.style.cssText='position:absolute;inset:0;width:100%;height:100%;border-radius:inherit;object-fit:cover;';}window.alert('Profile photo updated.');}
    catch(error){window.alert(error.message||'Unable to update profile photo.');}finally{event.target.value='';}
  });}
});
</script>