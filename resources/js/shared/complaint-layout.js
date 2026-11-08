import '../../css/shared/complaint-layout.css';
document.addEventListener('DOMContentLoaded',()=>{
 const modal=document.getElementById('complaints-details-modal');if(!modal)return;
 const cards=['description','parties','order','item'].map(name=>modal.querySelector('.complaints-modal-'+name+'-card'));
 if(cards.some(card=>!card))return;
 const [description,parties,order,item]=cards, parents=[...new Set(cards.map(card=>card.parentElement))];
 const layout=document.createElement('div');layout.className='se-complaint-columns';
 const left=document.createElement('div'),right=document.createElement('div');left.className=right.className='se-complaint-column';
 description.parentElement.before(layout);left.append(description,order);right.append(parties,item);layout.append(left,right);
 if(modal.querySelector('#complaints-modal-update-status')){
  const notes=modal.querySelector('.complaints-modal-notes-card');
  if(notes){notes.parentElement.style.display='block';right.append(notes);}
 }
 parents.forEach(parent=>{if(!parent.children.length)parent.remove();});
 modal.addEventListener('error',event=>{const image=event.target;if(image.matches?.('.complaints-modal-party-avatar img')){const row=image.closest('.complaints-modal-party-row');image.parentElement.textContent=row?.querySelector('.complaints-modal-party-copy strong')?.textContent.trim().charAt(0)||'?';}},true);
});
