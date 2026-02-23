// FAJ Niger - Main JavaScript
// ============================================================

document.addEventListener('DOMContentLoaded', function () {

  // ===== LOADER =====
  const loader = document.getElementById('loaderOverlay');
  if (loader) {
    window.addEventListener('load', () => {
      setTimeout(() => loader.classList.add('hidden'), 500);
    });
  }

  // ===== STICKY HEADER =====
  const header = document.getElementById('header');
  if (header) {
    window.addEventListener('scroll', () => {
      header.classList.toggle('scrolled', window.scrollY > 80);
    });
  }

  // ===== MOBILE MENU =====
  const toggle = document.getElementById('mobileToggle');
  const navMenu = document.querySelector('.navbar-nav');
  if (toggle && navMenu) {
    toggle.addEventListener('click', function () {
      navMenu.classList.toggle('open');
      this.classList.toggle('active');
      document.body.classList.toggle('menu-open');
    });

    // Close on outside click
    document.addEventListener('click', function (e) {
      if (!toggle.contains(e.target) && !navMenu.contains(e.target)) {
        navMenu.classList.remove('open');
        toggle.classList.remove('active');
        document.body.classList.remove('menu-open');
      }
    });
  }

  // Mobile dropdown toggles
  document.querySelectorAll('.nav-item.dropdown').forEach(item => {
    const link = item.querySelector('.nav-link');
    if (link && window.innerWidth <= 768) {
      link.addEventListener('click', function (e) {
        e.preventDefault();
        item.classList.toggle('open');
      });
    }
  });

  // ===== BACK TO TOP =====
  const backToTop = document.getElementById('backToTop');
  if (backToTop) {
    window.addEventListener('scroll', () => {
      backToTop.classList.toggle('visible', window.scrollY > 400);
    });
    backToTop.addEventListener('click', () => {
      window.scrollTo({ top: 0, behavior: 'smooth' });
    });
  }

  // ===== HERO SLIDER =====
  const slides = document.querySelectorAll('.hero-slide');
  const dots = document.querySelectorAll('.hero-dot');
  let currentSlide = 0;
  let slideInterval;

  function goToSlide(n) {
    slides.forEach(s => s.classList.remove('active'));
    dots.forEach(d => d.classList.remove('active'));
    currentSlide = (n + slides.length) % slides.length;
    if (slides[currentSlide]) slides[currentSlide].classList.add('active');
    if (dots[currentSlide]) dots[currentSlide].classList.add('active');
  }

  function startAutoSlide() {
    slideInterval = setInterval(() => goToSlide(currentSlide + 1), 5000);
  }

  if (slides.length > 0) {
    goToSlide(0);
    startAutoSlide();
    dots.forEach((dot, i) => {
      dot.addEventListener('click', () => {
        clearInterval(slideInterval);
        goToSlide(i);
        startAutoSlide();
      });
    });
  }

  // ===== COUNTER ANIMATION =====
  const counters = document.querySelectorAll('[data-count]');
  if (counters.length > 0 && typeof CountUp !== 'undefined') {
    const observer = new IntersectionObserver((entries) => {
      entries.forEach(entry => {
        if (entry.isIntersecting) {
          const el = entry.target;
          const target = parseInt(el.getAttribute('data-count'));
          const suffix = el.getAttribute('data-suffix') || '';
          const countUp = new CountUp.CountUp(el, target, {
            duration: 2.5,
            suffix: suffix,
            separator: ' '
          });
          if (!countUp.error) countUp.start();
          observer.unobserve(el);
        }
      });
    }, { threshold: 0.3 });
    counters.forEach(counter => observer.observe(counter));
  }

  // ===== PROGRESS BARS ANIMATION =====
  const progressBars = document.querySelectorAll('.progress-fill');
  if (progressBars.length > 0) {
    const progressObserver = new IntersectionObserver((entries) => {
      entries.forEach(entry => {
        if (entry.isIntersecting) {
          const bar = entry.target;
          const width = bar.getAttribute('data-width') || bar.style.width;
          bar.style.width = '0%';
          setTimeout(() => { bar.style.width = width; }, 100);
          progressObserver.unobserve(bar);
        }
      });
    }, { threshold: 0.3 });
    progressBars.forEach(bar => progressObserver.observe(bar));
  }

  // ===== AMOUNT PRESETS (DON FORM) =====
  const amountBtns = document.querySelectorAll('.amount-btn');
  const amountInput = document.getElementById('montant');

  amountBtns.forEach(btn => {
    btn.addEventListener('click', function () {
      amountBtns.forEach(b => b.classList.remove('selected'));
      this.classList.add('selected');
      if (amountInput) {
        amountInput.value = this.getAttribute('data-amount');
        amountInput.dispatchEvent(new Event('input'));
      }
    });
  });

  if (amountInput) {
    amountInput.addEventListener('input', function () {
      amountBtns.forEach(btn => {
        btn.classList.toggle('selected', btn.getAttribute('data-amount') === this.value);
      });
      updateDonSummary();
    });
  }

  // ===== PAYMENT METHODS =====
  const paymentBtns = document.querySelectorAll('.payment-method-btn');
  paymentBtns.forEach(btn => {
    btn.addEventListener('click', function () {
      paymentBtns.forEach(b => b.classList.remove('active'));
      this.classList.add('active');
      const method = this.getAttribute('data-method');
      const input = document.getElementById('methode_paiement');
      if (input) input.value = method;

      // Show/hide payment forms
      document.querySelectorAll('.payment-detail-panel').forEach(p => p.style.display = 'none');
      const panel = document.getElementById('panel-' + method);
      if (panel) panel.style.display = 'block';

      updateDonSummary();
    });
  });

  // Update summary
  function updateDonSummary() {
    const montant = amountInput ? parseInt(amountInput.value) || 0 : 0;
    const devise = 'FCFA';
    const summaryMontant = document.getElementById('summaryMontant');
    const summaryMethode = document.getElementById('summaryMethode');
    const activeMethod = document.querySelector('.payment-method-btn.active');

    if (summaryMontant) {
      summaryMontant.textContent = montant.toLocaleString('fr-FR') + ' ' + devise;
    }
    if (summaryMethode && activeMethod) {
      summaryMethode.textContent = activeMethod.querySelector('span')?.textContent || '';
    }
  }

  // ===== DON FORM SUBMIT =====
  const donForm = document.getElementById('donForm');
  if (donForm) {
    donForm.addEventListener('submit', async function (e) {
      e.preventDefault();

      const submitBtn = this.querySelector('[type="submit"]');
      const originalText = submitBtn.innerHTML;
      submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Traitement...';
      submitBtn.disabled = true;

      // Validation
      const montant = parseInt(document.getElementById('montant')?.value);
      if (!montant || montant < 500) {
        showAlert('Le montant minimum est de 500 FCFA', 'error');
        submitBtn.innerHTML = originalText;
        submitBtn.disabled = false;
        return;
      }

      const methode = document.getElementById('methode_paiement')?.value;
      if (!methode) {
        showAlert('Veuillez sélectionner un moyen de paiement', 'error');
        submitBtn.innerHTML = originalText;
        submitBtn.disabled = false;
        return;
      }

      const formData = new FormData(this);
      const data = Object.fromEntries(formData.entries());

      try {
        const response = await fetch('/api/don.php', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify(data)
        });

        const result = await response.json();

        if (result.success) {
          if (result.redirect_url) {
            window.location.href = result.redirect_url;
          } else {
            showDonSuccessModal(result);
          }
        } else {
          showAlert(result.message || 'Une erreur est survenue', 'error');
        }
      } catch (err) {
        showAlert('Erreur de connexion. Veuillez réessayer.', 'error');
      }

      submitBtn.innerHTML = originalText;
      submitBtn.disabled = false;
    });
  }

  // ===== CONTACT FORM =====
  const contactForm = document.getElementById('contactForm');
  if (contactForm) {
    contactForm.addEventListener('submit', async function (e) {
      e.preventDefault();
      const btn = this.querySelector('[type="submit"]');
      const originalText = btn.innerHTML;
      btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Envoi...';
      btn.disabled = true;

      const formData = new FormData(this);
      const data = Object.fromEntries(formData.entries());

      try {
        const response = await fetch('/api/contact.php', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify(data)
        });
        const result = await response.json();

        if (result.success) {
          showAlert(result.message || 'Message envoyé avec succès !', 'success');
          this.reset();
        } else {
          showAlert(result.message || 'Erreur lors de l\'envoi', 'error');
        }
      } catch (err) {
        showAlert('Erreur de connexion', 'error');
      }

      btn.innerHTML = originalText;
      btn.disabled = false;
    });
  }

  // ===== TESTIMONIALS SWIPER =====
  if (document.querySelector('.testimonials-slider') && typeof Swiper !== 'undefined') {
    new Swiper('.testimonials-slider', {
      slidesPerView: 1,
      spaceBetween: 30,
      loop: true,
      autoplay: { delay: 4000, disableOnInteraction: false },
      pagination: { el: '.swiper-pagination', clickable: true },
      breakpoints: {
        640: { slidesPerView: 2 },
        1024: { slidesPerView: 3 }
      }
    });
  }

  // ===== ALERTS =====
  function showAlert(message, type = 'info') {
    const existing = document.querySelector('.dynamic-alert');
    if (existing) existing.remove();

    const alert = document.createElement('div');
    alert.className = `alert alert-${type} dynamic-alert`;
    alert.style.cssText = `
      position: fixed; top: 90px; right: 20px; z-index: 9999;
      max-width: 400px; animation: slideIn 0.3s ease;
    `;
    alert.innerHTML = `
      <i class="fas fa-${type === 'success' ? 'check-circle' : type === 'error' ? 'exclamation-circle' : 'info-circle'}"></i>
      <span>${message}</span>
      <button onclick="this.parentElement.remove()" style="margin-left:auto; background:none; border:none; cursor:pointer; font-size:18px;">&times;</button>
    `;
    document.body.appendChild(alert);
    setTimeout(() => alert.remove(), 6000);
  }

  window.showAlert = showAlert;

  // ===== DON SUCCESS MODAL =====
  function showDonSuccessModal(data) {
    const modal = document.getElementById('donSuccessModal');
    if (modal) {
      const refEl = modal.querySelector('#successRef');
      const montantEl = modal.querySelector('#successMontant');
      if (refEl) refEl.textContent = data.reference || '';
      if (montantEl) montantEl.textContent = (parseInt(data.montant) || 0).toLocaleString('fr-FR') + ' FCFA';
      modal.classList.add('active');
    }
  }

  document.querySelectorAll('[data-modal-close]').forEach(btn => {
    btn.addEventListener('click', () => {
      btn.closest('.modal-overlay').classList.remove('active');
    });
  });

  // ===== FILTER PROJECTS =====
  const filterBtns = document.querySelectorAll('.filter-btn');
  const projectItems = document.querySelectorAll('.project-item');

  filterBtns.forEach(btn => {
    btn.addEventListener('click', function () {
      filterBtns.forEach(b => b.classList.remove('active'));
      this.classList.add('active');
      const filter = this.getAttribute('data-filter');

      projectItems.forEach(item => {
        const cat = item.getAttribute('data-cat');
        if (filter === 'all' || cat === filter) {
          item.style.display = '';
          item.style.animation = 'fadeIn 0.4s ease';
        } else {
          item.style.display = 'none';
        }
      });
    });
  });

  // ===== SMOOTH SCROLL FOR ANCHORS =====
  document.querySelectorAll('a[href^="#"]').forEach(anchor => {
    anchor.addEventListener('click', function (e) {
      const href = this.getAttribute('href');
      if (href === '#') return;
      const target = document.querySelector(href);
      if (target) {
        e.preventDefault();
        target.scrollIntoView({ behavior: 'smooth', block: 'start' });
      }
    });
  });

  // ===== INPUT FORMATTING =====
  // Phone number
  document.querySelectorAll('input[type="tel"]').forEach(input => {
    input.addEventListener('input', function () {
      this.value = this.value.replace(/[^0-9+\s-]/g, '');
    });
  });

  // Amount formatting
  document.querySelectorAll('input[data-amount-format]').forEach(input => {
    input.addEventListener('input', function () {
      this.value = this.value.replace(/[^0-9]/g, '');
    });
  });

});
