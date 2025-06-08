document.addEventListener('DOMContentLoaded', function() {
  // Ripple effect for buttons
  const buttons = document.querySelectorAll('.btn');
  buttons.forEach(button => {
    button.addEventListener('click', function(e) {
      const x = e.clientX - e.target.getBoundingClientRect().left;
      const y = e.clientY - e.target.getBoundingClientRect().top;
      
      const ripple = document.createElement('span');
      ripple.classList.add('ripple-effect');
      ripple.style.left = `${x}px`;
      ripple.style.top = `${y}px`;
      
      this.appendChild(ripple);
      
      setTimeout(() => {
        ripple.remove();
      }, 600);
    });
  });
  
  // Intersection Observer for scroll animations
  const observerOptions = {
    threshold: 0.1,
    rootMargin: '0px 0px -50px 0px'
  };
  
  const observer = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
      if (entry.isIntersecting) {
        entry.target.classList.add('animated');
        observer.unobserve(entry.target);
      }
    });
  }, observerOptions);
  
  // Elements to animate when scrolled into view
  const animatedElements = document.querySelectorAll('.animate-on-scroll');
  animatedElements.forEach(el => {
    observer.observe(el);
  });
  
  // Smooth scroll for anchor links
  document.querySelectorAll('a[href^="#"]').forEach(anchor => {
    anchor.addEventListener('click', function(e) {
      e.preventDefault();
      
      const targetId = this.getAttribute('href');
      if (targetId === '#') return;
      
      const targetElement = document.querySelector(targetId);
      if (targetElement) {
        window.scrollTo({
          top: targetElement.offsetTop - 80,
          behavior: 'smooth'
        });
      }
    });
  });
  
  // File upload preview
  const fileUploads = document.querySelectorAll('.file-upload');
  fileUploads.forEach(upload => {
    const input = upload.querySelector('input[type="file"]');
    const preview = upload.querySelector('.file-preview');
    
    if (input && preview) {
      input.addEventListener('change', function() {
        if (this.files && this.files[0]) {
          const reader = new FileReader();
          
          reader.onload = function(e) {
            if (this.files[0].type.includes('image')) {
              preview.innerHTML = `<img src="${e.target.result}" alt="Preview">`;
            } else {
              preview.innerHTML = `
                <div class="file-info">
                  <i class="fas fa-file-archive"></i>
                  <span>${this.files[0].name}</span>
                </div>
              `;
            }
          }.bind(this);
          
          reader.readAsDataURL(this.files[0]);
        }
      });
    }
  });
  
  // Tooltips
  const tooltipElements = document.querySelectorAll('[data-tooltip]');
  tooltipElements.forEach(el => {
    const tooltip = document.createElement('div');
    tooltip.className = 'tooltip';
    tooltip.textContent = el.getAttribute('data-tooltip');
    document.body.appendChild(tooltip);
    
    el.addEventListener('mouseenter', function(e) {
      const rect = this.getBoundingClientRect();
      tooltip.style.left = `${rect.left + rect.width / 2 - tooltip.offsetWidth / 2}px`;
      tooltip.style.top = `${rect.top - tooltip.offsetHeight - 10}px`;
      tooltip.style.opacity = '1';
    });
    
    el.addEventListener('mouseleave', function() {
      tooltip.style.opacity = '0';
    });
  });
});