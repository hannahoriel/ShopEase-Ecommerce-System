function showView(view){
  document.getElementById('loginView').classList.toggle('active', view==='login');
  document.getElementById('signupView').classList.toggle('active', view==='signup');
}
const EYE_OFF = '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 3l18 18"/><path d="M10.6 5.1A10.9 10.9 0 0112 5c5 0 9 4 10 7-0.4 1.2-1.3 2.7-2.6 4M6.7 6.7C4.5 8.1 2.9 10.1 2 12c1 3 5 7 10 7 1.5 0 2.9-.3 4.1-.9"/><path d="M9.9 9.9a3 3 0 004.2 4.2"/></svg>';
const EYE_ON = '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M2 12c1-3 5-7 10-7s9 4 10 7c-1 3-5 7-10 7s-9-4-10-7z"/><circle cx="12" cy="12" r="3"/></svg>';

function togglePass(inputId, btnId){
  const input = document.getElementById(inputId);
  const btn = document.getElementById(btnId);
  const isHidden = input.type === 'password';
  input.type = isHidden ? 'text' : 'password';
  btn.innerHTML = isHidden ? EYE_ON : EYE_OFF;
}
function onPassInput(inputId, btnId){
  const input = document.getElementById(inputId);
  const btn = document.getElementById(btnId);
  btn.classList.toggle('show', input.value.length > 0);
}
function selectRole(role){
  const sellerCard = document.getElementById('roleSeller');
  const buyerCard = document.getElementById('roleBuyer');
  const roleInput = document.getElementById('roleInput');

  if (sellerCard) sellerCard.classList.toggle('selected', role === 'seller');
  if (buyerCard) buyerCard.classList.toggle('selected', role === 'buyer');
  if (roleInput) roleInput.value = role;
}

window.selectRole = selectRole;

document.addEventListener('DOMContentLoaded', () => {
  const roleCards = document.querySelectorAll('.role-card');
  roleCards.forEach((card) => {
    card.addEventListener('click', () => {
      selectRole(card.dataset.role);
    });
  });

  const initialRole = document.getElementById('roleInput')?.value || 'seller';
  selectRole(initialRole);
});

