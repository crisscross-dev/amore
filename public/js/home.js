// Hide Content

document.addEventListener('DOMContentLoaded', function() {
  const toggler = document.querySelector('.navbar-toggler');
  const heroContent = document.getElementById('hero-content');
  const navbarCollapse = document.getElementById('navbarNav');

  
  let heroParent = heroContent.parentNode;
  let heroNext = heroContent.nextSibling;

  function removeHero() {
    if (heroContent && heroContent.parentNode) {
      heroContent.parentNode.removeChild(heroContent);
    }
  }

  function restoreHero() {
    if (!document.getElementById('hero-content')) {
      if (heroNext) {
        heroParent.insertBefore(heroContent, heroNext);
      } else {
        heroParent.appendChild(heroContent);
      }
    }
  }

  toggler.addEventListener('click', function() {
    setTimeout(function() {
      if (navbarCollapse.classList.contains('show')) {
        removeHero();
      } else {
        restoreHero();
      }
    }, 350);
  });

  navbarCollapse.addEventListener('hidden.bs.collapse', function () {
    restoreHero();
  });
  navbarCollapse.addEventListener('shown.bs.collapse', function () {
    removeHero();
  });
});

// Reload page on screen resolution change 
// let lastWidth = window.innerWidth;
// let lastHeight = window.innerHeight;

// window.addEventListener('resize', function() {
//   if (window.innerWidth !== lastWidth || window.innerHeight !== lastHeight) {
//     setTimeout(function() {
//       location.reload();
//     }, 2000); // 2 seconds delay
//   }
// });

// Show - Hide Password
document.getElementById('togglePassword').addEventListener('click', function () {
  const passwordInput = document.getElementById('password');
  const icon = document.getElementById('togglePasswordIcon');
  if (passwordInput.type === 'password') {
    passwordInput.type = 'text';
    icon.classList.remove('bi-eye');
    icon.classList.add('bi-eye-slash');
  } else {
    passwordInput.type = 'password';
    icon.classList.remove('bi-eye-slash');
    icon.classList.add('bi-eye');
  }
});
document.getElementById('togglePasswordConfirm').addEventListener('click', function () {
  const passwordInput = document.getElementById('password_confirmation');
  const icon = document.getElementById('togglePasswordConfirmIcon');
  if (passwordInput.type === 'password') {
    passwordInput.type = 'text';
    icon.classList.remove('bi-eye');
    icon.classList.add('bi-eye-slash');
  } else {
    passwordInput.type = 'password';
    icon.classList.remove('bi-eye-slash');
    icon.classList.add('bi-eye');
  }
});

