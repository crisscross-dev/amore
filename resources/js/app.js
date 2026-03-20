// Minimal scroll behavior: toggle navbar style
document.addEventListener('DOMContentLoaded', () => {
	const navbar = document.querySelector('.navbar-custom');
	const threshold = 30; // px

	function onScroll() {
		if (!navbar) return;
		if (window.scrollY > threshold) {
			navbar.classList.add('scrolled');
		} else {
			navbar.classList.remove('scrolled');
		}
	}

	onScroll();
	window.addEventListener('scroll', onScroll, { passive: true });
});
import './bootstrap';
