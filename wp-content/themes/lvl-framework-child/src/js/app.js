import './components/chat.js';

document.addEventListener('DOMContentLoaded', (e) => {

	if (document.body.classList.contains('single-landing-page')) {
		return;
	}

	/** THROTTLE FUNCTION */
	const throttle = (fn, delay) => {
		let time = Date.now();
		return (...args) => {
			if ((time + delay - Date.now()) <= 0) {
				fn(...args);
				time = Date.now();
			}
		}
	}

	/** SET SCROLLED CLASS ON BODY WHEN SCROLLED */
	const runOnScroll = function (e) {
		const body = document.querySelector('body');
		const header = document.querySelector('#header');

		if (!header) return;

		const navbar = header.querySelector('#navbar');
		const mainFirstElement = document.querySelector('main > *:first-child');
		const mainFirstElementTheme = mainFirstElement ? (mainFirstElement.getAttribute('data-bs-theme') ? mainFirstElement.getAttribute('data-bs-theme') : 'light') : 'light';

		// add header height as additional padding to mainFirstElement
		// if (mainFirstElement && !body.classList.contains('default-nav')) {
		// 	// add .padding-adjusted class to element
		// 	if (!mainFirstElement.classList.contains('padding-adjusted')) {
		// 		const existingPadding = parseInt(window.getComputedStyle(mainFirstElement).paddingTop) / 2;
		// 		const headerHeight = header.offsetHeight;
		// 		mainFirstElement.style.cssText += `padding-top:${existingPadding + headerHeight}px !important;`;
		// 		mainFirstElement.classList.add('padding-adjusted');
		// 	}
		// }

		// adjust data-bs-theme to dark if header contains .scrolled and light on no .scrolled
		if (body.classList.contains('scrolled')) {
			header.setAttribute('data-bs-theme', 'light');
		} else {
			header.setAttribute('data-bs-theme', (mainFirstElementTheme === 'dark') ? mainFirstElementTheme : 'light');
		}

		header.classList.remove('focused');

		let previousTheme = header.getAttribute('data-bs-theme');
		// detect interaction with header then add light theme
		// Store previous theme and create a function to set theme
		const setHeaderTheme = (theme) => {
			header.setAttribute('data-bs-theme', theme);
		};

		// Create state tracking variables
		let isMouseOverHeader = false;
		let isFocusInHeader = false;
		let isHeaderActive = false;
		let activeTimeout;

		const updateHeaderTheme = () => {
			// Consider all three states: mouse over, focus in, or active
			if (isMouseOverHeader || isFocusInHeader || isHeaderActive) {
				setHeaderTheme('light');
				// add focused class to header
				header.classList.add('focused');
			} else {
				setHeaderTheme(previousTheme);
				// remove focused class from header
				header.classList.remove('focused');
			}
		};

		// Mouse events
		header.addEventListener('mouseover', () => {
			isMouseOverHeader = true;
			updateHeaderTheme();
		});

		header.addEventListener('mouseleave', () => {
			isMouseOverHeader = false;
			updateHeaderTheme();
		});

		// Focus events
		header.addEventListener('focusin', () => {
			isFocusInHeader = true;
			updateHeaderTheme();
		});

		header.addEventListener('focusout', () => {
			isFocusInHeader = false;
			updateHeaderTheme();
		});

		// Active state events
		header.addEventListener('mousedown', () => {
			isHeaderActive = true;
			updateHeaderTheme();

			// Clear any existing timeout
			clearTimeout(activeTimeout);
		});

		header.addEventListener('mouseup', () => {
			// Use timeout to ensure the active state is visible for a moment
			clearTimeout(activeTimeout);
			activeTimeout = setTimeout(() => {
				isHeaderActive = false;
				updateHeaderTheme();
			}, 200); // Short delay to maintain light theme briefly after click
		});

		// Touch event
		header.addEventListener('touchstart', () => {
			isHeaderActive = true;
			isMouseOverHeader = true;
			updateHeaderTheme();
		}, { passive: true });

		header.addEventListener('touchend', () => {
			clearTimeout(activeTimeout);
			activeTimeout = setTimeout(() => {
				isHeaderActive = false;
				// Don't immediately reset isMouseOverHeader on touch devices
				updateHeaderTheme();
			}, 200);
		}, { passive: true });
	}

	window.addEventListener('scroll', throttle((e) => runOnScroll(e), 10));
	runOnScroll();
});

// Apply body class on main nav hover
function main_nav_hover() {
	const dropdowns = document.querySelectorAll(".main-nav .dropdown");

	dropdowns.forEach((dropdown) => {
		dropdown.addEventListener("mouseenter", function () {
			if (!dropdown.classList.contains("show")) {
				dropdown.classList.add("show");
				document.querySelector("body").classList.add("nav-open");
			}
		});

		dropdown.addEventListener("mouseleave", function () {
			if (dropdown.classList.contains("show")) {
				dropdown.classList.remove("show");
				document.querySelector("body").classList.remove("nav-open");
			}
		});
	});
}

main_nav_hover();