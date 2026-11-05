(() => {
  'use strict';
  document.addEventListener('DOMContentLoaded', () => {
    const root = document.getElementById('logistics-reports');
    const configNode = document.getElementById('lr-demo-data');
    if (!root || !configNode) return;
    let data;
    try { data = JSON.parse(configNode.textContent); } catch { return; }
    const all = Array.isArray(data.parcels) ? data.parcels : [];
    const centers = Array.isArray(data.centers) ? data.centers : [];
    const $ = id => document.getElementById(id);
    const from = $('lr-from'), to = $('lr-to'), reset = $('lr-reset'), error = $('lr-error');
    const today = data.today;
    const money = value => '₱' + Number(value).toLocaleString('en-PH', {minimumFractionDigits:2, maximumFractionDigits:2});
    const integer = value => Number(value).toLocaleString('en-PH');
    const percent = (num, den) => den ? (100 * num / den).toFixed(1) + '%' : '0.0%';
    const escape = value => String(value ?? '').replace(/[&<>"']/g, c => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c]));
    const statusLabels = {delivered:'Delivered',in_transit:'In transit',at_hub:'At delivery hub',exception:'For investigation'};
    const toLabel = iso => new Date(iso + 'T12:00:00').toLocaleDateString('en-PH',{month:'short',day:'numeric'});
    let selected = [];

    function resetPeriod() {
      from.value = today.slice(0,7) + '-01';
      to.value = today;
      render();
    }
    function render() {
      if (!from.value || !to.value || from.value > to.value) {
        error.textContent = 'Please select a valid From and To date.';
        return;
      }
      error.textContent = '';
      selected = all.filter(row => row.date >= from.value && row.date <= to.value);
      const delivered = selected.filter(r => r.status === 'delivered');
      const onTime = delivered.filter(r => r.on_time).length;
      $('lr-period-label').textContent = `${toLabel(from.value)} – ${toLabel(to.value)} ${to.value.slice(0,4)}`;
      $('lr-parcels').textContent = integer(selected.length);
      $('lr-delivered').textContent = integer(delivered.length);
      $('lr-on-time').textContent = percent(onTime, delivered.length);
      $('lr-shipping').textContent = money(delivered.length * 50);
      $('lr-status-total').textContent = `${integer(selected.length)} records`;
      const statusContainer = $('lr-status-bars');
      statusContainer.replaceChildren();
      for (const key of ['delivered','in_transit','at_hub','exception']) {
        const count = selected.filter(r => r.status === key).length;
        const wrap = document.createElement('div'); wrap.className='ar-meter-row';
        const line = document.createElement('div');
        const name = document.createElement('span'); name.textContent=statusLabels[key];
        const value=document.createElement('strong'); value.textContent=integer(count);
        line.append(name,value);
        const track=document.createElement('div');track.className='ar-meter';
        const fill=document.createElement('i'); fill.style.width=percent(count,selected.length);
        track.appendChild(fill);wrap.append(line,track);statusContainer.appendChild(wrap);
      }
      drawChart();
      renderCenters();
      renderDelivery(delivered, onTime);
      renderInsights(delivered, onTime);
    }
    function renderCenters() {
      const centerRows=$('lr-center-rows'), revenueRows=$('lr-revenue-rows'), tree=$('lr-hub-tree');
      centerRows.replaceChildren(); revenueRows.replaceChildren(); tree.replaceChildren();
      for (const center of centers) {
        const rows=selected.filter(x=>x.center===center.name);
        const delivered=rows.filter(x=>x.status==='delivered').length;
        const tr=document.createElement('tr');
        tr.innerHTML=`<td class="ar-table-strong">${escape(center.name)}</td><td>${integer(rows.length)}</td><td>${integer(delivered)}</td><td class="ar-num">${percent(rows.length,selected.length)}</td>`;
        centerRows.appendChild(tr);
        const revenue=document.createElement('tr');
        revenue.innerHTML=`<td class="ar-table-strong">${escape(center.name)}</td><td>${integer(delivered)}</td><td class="ar-num">${money(delivered*50)}</td>`;
        revenueRows.appendChild(revenue);
        const box=document.createElement('article');box.className='ar-center';
        const head=document.createElement('div');head.className='ar-center-head';
        const strong=document.createElement('strong');strong.textContent=center.name;
        const small=document.createElement('small');small.textContent=`${integer(rows.length)} parcel records`;
        head.append(strong,small);box.appendChild(head);
        for (const hub of center.hubs) {
          const hubRows=rows.filter(r=>r.hub===hub);
          const child=document.createElement('div');child.className='ar-hub-child';
          const name=document.createElement('span');name.textContent=hub;
          const count=document.createElement('strong');count.textContent=`${integer(hubRows.length)} parcels`;
          child.append(name,count);box.appendChild(child);
        }
        tree.appendChild(box);
      }
    }
    function renderDelivery(delivered, onTime) {
      const late=delivered.length-onTime;
      const pending=selected.length-delivered.length;
      $('lr-timeliness').innerHTML=`<div><span><i class="ar-dot"></i>Within 48 hours</span><strong>${integer(onTime)}</strong></div><div><span><i class="ar-dot"></i>Over 48 hours</span><strong>${integer(late)}</strong></div><div><span><i class="ar-dot"></i>Not yet delivered</span><strong>${integer(pending)}</strong></div>`;
      const issues=selected.filter(r=>r.status==='exception');
      const lost=issues.filter(r=>r.issue==='lost').length, damaged=issues.filter(r=>r.issue==='damaged').length;
      $('lr-issue-total').textContent=integer(issues.length)+' cases';
      $('lr-issues').innerHTML=`<div><span><i class="ar-dot"></i>Lost parcels</span><strong>${integer(lost)}</strong></div><div><span><i class="ar-dot"></i>Damaged parcels</span><strong>${integer(damaged)}</strong></div><div><span><i class="ar-dot"></i>Exception share</span><strong>${percent(issues.length, selected.length)}</strong></div>`;
      const hours=delivered.map(r=>r.hours);
      $('lr-average-hours').textContent=hours.length ? (hours.reduce((a,b)=>a+b,0)/hours.length).toFixed(1)+' hrs' : '—';
      $('lr-fastest').textContent=hours.length ? Math.min(...hours)+' hrs' : '—';
      $('lr-longest').textContent=hours.length ? Math.max(...hours)+' hrs' : '—';
    }
    function renderInsights(delivered, onTime) {
      const counts=centers.map(center=>({name:center.name,total:selected.filter(r=>r.center===center.name).length})).sort((a,b)=>b.total-a.total);
      const highest=counts[0];
      const problemCount=selected.filter(r=>r.status==='exception').length;
      $('lr-insights').innerHTML=`<div><span>Highest parcel throughput</span><strong>${escape(highest?.name || '—')}</strong></div><div><span>On-time among delivered</span><strong>${percent(onTime, delivered.length)}</strong></div><div><span>Parcels under investigation</span><strong>${integer(problemCount)}</strong></div><div><span>Gross fee basis</span><strong>₱50 × ${integer(selected.length)}</strong></div>`;
    }

    let chartToken = 0;
    function drawChart(animated = true) {
      const target=$('lr-volume-chart');
      const sums=new Map();
      for(const row of selected) sums.set(row.date,(sums.get(row.date)||0)+1);
      const entries=[...sums.entries()].sort((a,b)=>a[0].localeCompare(b[0]));
      if (!entries.length) {target.innerHTML='<p class="ar-chart-empty">No sample parcel records for this period.</p>';return;}
      const step=Math.max(1,Math.ceil(entries.length/14));
      const points=[];
      for(let i=0;i<entries.length;i+=step){
        const chunk=entries.slice(i,i+step);
        points.push({label:chunk.length===1?toLabel(chunk[0][0]):`${toLabel(chunk[0][0])} – ${toLabel(chunk[chunk.length-1][0])}`,
          short:toLabel(chunk[0][0]),value:chunk.reduce((sum,r)=>sum+r[1],0)});
      }
      const w=Math.max(340,Math.round(target.getBoundingClientRect().width||700));
      const h=Math.max(320,Math.round(target.clientHeight||365));
      const l=54,r=9,t=15,b=44,ph=h-t-b,pw=w-l-r;
      const ceiling=Math.max(...points.map(p=>p.value),1)*1.1;
      const ns='http://www.w3.org/2000/svg';
      const svg=document.createElementNS(ns,'svg');svg.setAttribute('viewBox',`0 0 ${w} ${h}`);svg.classList.add('ar-chart-svg');svg.setAttribute('role','img');svg.setAttribute('aria-label','Parcel volume over selected reporting period');
      const add=(tag,attrs,text)=>{const element=document.createElementNS(ns,tag);for(const [key,value] of Object.entries(attrs))element.setAttribute(key,value);if(text!==undefined)element.textContent=text;svg.appendChild(element);return element;};
      [0,.25,.5,.75,1].forEach(frac=>{const y=t+ph*(1-frac);add('line',{x1:l,y1:y,x2:w-r,y2:y,stroke:'#edf0f3','stroke-dasharray':'4 5'});add('text',{x:l-8,y:y+4,'text-anchor':'end',fill:'#9ca3af','font-size':10},String(Math.round(ceiling*frac)));});
      const tip=document.createElement('div');tip.className='ar-bar-tooltip';tip.hidden=true;
      const slot=pw/points.length,barWidth=Math.min(64,slot*.78),bars=[];
      points.forEach((point,index)=>{
        const x=l+slot*index+(slot-barWidth)/2;
        const finalHeight=point.value/ceiling*ph,finalY=t+ph-finalHeight;
        const bar=add('rect',{x,y:animated?t+ph:finalY,width:barWidth,height:animated?0:finalHeight,rx:4,fill:'#a21c41',tabindex:0,class:'ar-trend-bar','aria-label':`${point.label}: ${point.value} parcels`});
        const title=document.createElementNS(ns,'title');title.textContent=`${point.label}: ${point.value} parcels`;bar.appendChild(title);
        bars.push({bar,finalY,finalHeight,index});
        const show=()=>{tip.textContent='';const span=document.createElement('span');span.className='ar-bar-tooltip-date';span.textContent=point.label;const strong=document.createElement('strong');strong.textContent=integer(point.value)+' parcels';tip.append(span,strong);tip.hidden=false;const limit=Math.max(85,Math.min(w-85,x+barWidth/2));tip.style.left=limit+'px';tip.style.top=Math.max(50,finalY-8)+'px';};
        bar.addEventListener('mouseenter',show);bar.addEventListener('mousemove',show);bar.addEventListener('mouseleave',()=>tip.hidden=true);bar.addEventListener('focus',show);bar.addEventListener('blur',()=>tip.hidden=true);
        add('text',{x:x+barWidth/2,y:h-14,'text-anchor':'middle',fill:'#9ca3af','font-size':10},point.short);
      });
      target.replaceChildren(svg,tip);
      const token=++chartToken;
      if (animated && !window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
        const start=performance.now();
        const frame=now=>{
          if(token!==chartToken)return;
          let running=false;
          for(const {bar,finalY,finalHeight,index} of bars){
            const elapsed=Math.max(0,now-start-index*55),fraction=Math.min(1,elapsed/750),eased=1-Math.pow(1-fraction,3);
            const height=finalHeight*eased;
            bar.setAttribute('height',height);bar.setAttribute('y',t+ph-height);
            if(fraction<1)running=true;
          }
          if(running)requestAnimationFrame(frame);
        };
        requestAnimationFrame(frame);
      }else{bars.forEach(({bar,finalHeight,finalY})=>{bar.setAttribute('height',finalHeight);bar.setAttribute('y',finalY);});}
    }

    from.addEventListener('change',render);to.addEventListener('change',render);
    reset.addEventListener('click',()=>{
      reset.classList.remove('is-spinning');void reset.offsetWidth;reset.classList.add('is-spinning');resetPeriod();
    });
    reset.addEventListener('animationend',()=>reset.classList.remove('is-spinning'));
    $('lgSidebarToggle')?.addEventListener('click',()=>{
      document.body.classList.toggle('lg-collapsed');
      $('lgSidebarToggle').setAttribute('aria-expanded',String(!document.body.classList.contains('lg-collapsed')));
    });
    $('lgProfileButton')?.addEventListener('click',()=>{
      const menu=$('lgProfileMenu');if(!menu)return;menu.hidden=!menu.hidden;$('lgProfileButton').setAttribute('aria-expanded',String(!menu.hidden));
    });
    document.addEventListener('click',e=>{
      if(!e.target.closest('.lg-profile-wrap')){const menu=$('lgProfileMenu');if(menu)menu.hidden=true;}
    });
    $('lgNotifyButton')?.addEventListener('click',()=>window.alert('No live notifications connected yet.'));
    $('lgChangeProfilePhoto')?.addEventListener('click',()=>$('lgProfilePhotoInput')?.click());
    let width=Math.round($('lr-volume-chart').getBoundingClientRect().width);
    const ro=new ResizeObserver(entries=>{const newWidth=Math.round(entries[0].contentRect.width);if(newWidth>0&&Math.abs(newWidth-width)>12){width=newWidth;drawChart(false);}});
    ro.observe($('lr-volume-chart'));
    // Start Reports navigation at Overview / Dashboard. Do not hide earlier menu items.
    const sidebarMenu = document.querySelector('#lgSidebar .lg-sidebar-menu');
    if (sidebarMenu) sidebarMenu.scrollTop = 0;
    window.addEventListener('pageshow', event => {
      if (event.persisted && sidebarMenu) sidebarMenu.scrollTop = 0;
    });
    resetPeriod();
  });
})();
