'use strict';




const nav = document.getElementById('nav');
if (nav) {
  window.addEventListener('scroll', () => {
    nav.classList.toggle('scrolled', window.scrollY > 40);
  }, { passive: true });
}


const toggle = document.getElementById('navToggle');
const drawer = document.getElementById('navDrawer');

if (toggle && drawer) {
  toggle.addEventListener('click', () => {
    const open = drawer.classList.toggle('open');
    toggle.setAttribute('aria-expanded', String(open));
    document.body.style.overflow = open ? 'hidden' : '';
  });

  drawer.querySelectorAll('a').forEach(a => {
    a.addEventListener('click', () => {
      drawer.classList.remove('open');
      toggle.setAttribute('aria-expanded', 'false');
      document.body.style.overflow = '';
    });
  });
}


const yr = document.getElementById('year');
if (yr) yr.textContent = new Date().getFullYear();


const io = new IntersectionObserver(entries => {
  entries.forEach(e => {
    if (!e.isIntersecting) return;
    e.target.classList.add('in-view');
    io.unobserve(e.target);
  });
}, { threshold: 0.12, rootMargin: '0px 0px -8% 0px' });

document.querySelectorAll('.reveal, .reveal-stagger').forEach(el => io.observe(el));


const form = document.getElementById('reservationForm');
if (form) {
  const dateEl = document.getElementById('r_date');
  if (dateEl) dateEl.min = new Date().toISOString().split('T')[0];

  form.addEventListener('submit', e => {
    e.preventDefault();

    
    if (document.getElementById('r_website')?.value) return;

    const status = document.getElementById('formStatus');

    const name  = document.getElementById('r_name').value.trim();
    const email = document.getElementById('r_email').value.trim();
    const date  = document.getElementById('r_date').value;
    const time  = document.getElementById('r_time').value;
    const party = document.getElementById('r_party').value;

    if (!name || !email || !date || !time || !party) {
      status.className = 'form-status show err';
      status.textContent = 'Please fill in all required fields.';
      return;
    }

    if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
      status.className = 'form-status show err';
      status.textContent = 'Please enter a valid email address.';
      return;
    }

    
    status.className = 'form-status show ok';
    status.textContent = `Thank you, ${name.split(' ')[0]}. We'll confirm your table by email within the hour.`;
    form.reset();

    setTimeout(() => status.classList.remove('show'), 8000);
  });
}
