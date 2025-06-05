document.addEventListener('DOMContentLoaded', (e) => {

    const blocks = document.querySelectorAll('.block--comparison-table');

    function create_block_comparison_table() {

        return {
            block: null,

            init: function (block) {
                this.block = block;

                this.slider();
                this.setHeaderHeight();
            },

            slider: function () {
                const target = this.block.querySelector('.plans');

                const wrapper = document.createElement('div');
                wrapper.classList.add('swiper');

                target.parentNode.insertBefore(wrapper, target);
                wrapper.appendChild(target);

                let plans = this.block.querySelectorAll('.plan');
                plans.forEach(plan => {
                    plan.classList.add('swiper-slide');
                });

                const nav = document.createElement('div');
                nav.classList.add('swiper-nav');

                this.block.prepend(nav);

                const prev = document.createElement('div');
                prev.classList.add('swiper-arrow', 'swiper-prev');
                prev.innerHTML = '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="23" viewBox="0 0 24 23" fill="none"><path d="M16.9285 21.3569L7.07132 11.4998L16.9285 1.64265" stroke="currentColor" stroke-width="3"/></svg>';

                nav.appendChild(prev);

                const dots = document.createElement('div');
                dots.classList.add('swiper-pagination');

                nav.appendChild(dots);

                const next = document.createElement('div');
                next.classList.add('swiper-arrow', 'swiper-next');
                next.innerHTML = '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="23" viewBox="0 0 24 23" fill="none"><path d="M7.07153 1.64307L16.9287 11.5002L7.07153 21.3574" stroke="currentColor" stroke-width="3"/></svg>';

                nav.appendChild(next);

                const navTitle = document.createElement('p');
                navTitle.classList.add('d-lg-none', 'text-center', 'mb-3');
                navTitle.innerText = 'Swipe to compare.';

                this.block.prepend(navTitle);

                target.classList.add('swiper-wrapper');

                this.swiper();

            },

            swiper: function () {

                const plansCount = this.block.dataset.plans;
                if (plansCount === undefined) {
                    this.log('No plans found');
                    return;
                }

                const swiper = new Swiper('#' + this.block.id + ' .swiper', {
                    slidesPerView: (plansCount > 2) ? 2 : plansCount,
                    spaceBetween: 0,
                    loop: true,
                    pagination: {
                        el: this.block.querySelector('.swiper-pagination'),
                        clickable: true,
                    },
                    navigation: {
                        nextEl: this.block.querySelector('.swiper-next'),
                        prevEl: this.block.querySelector('.swiper-prev'),
                    },
                    breakpoints: {
                        768: {
                            slidesPerView: (plansCount > 3) ? 3 : plansCount,
                        },
                        992: {
                            slidesPerView: 99,
                            draggable: false,
                            loop: false
                        }
                    },
                    modules: [Navigation, Pagination, A11y]
                });
            },

            setHeaderHeight: function () {
                // get tallest .plan-header content height and set block style --table-header-height to that
                const planHeaders = this.block.querySelectorAll('.plan-header > span');
                let maxHeight = 0;
                planHeaders.forEach(header => {
                    const height = header.offsetHeight;
                    if (height > maxHeight) {
                        maxHeight = height;
                    }
                });
                this.block.style.setProperty('--table-header-height', maxHeight + 'px');
            },

            log: (message) => {
                console.log(message);
            }
        }
    }

    function create_block_comparison_table_scroll() {
        return {
            block: null,
            tableWrapper: null,
            columnWidth: 0,

            init: function (block) {
                this.block = block;
                this.tableWrapper = block.querySelector('.comparison-table.table-responsive');

                if (!this.tableWrapper) {
                    console.warn('No table wrapper found');
                    return;
                }

                this.calculateColumnWidth();
                this.createNavigation();
                // this.setupHeaderNavigation();

                // Check visibility after content loads
                setTimeout(() => {
                    this.toggleNavVisibility(window.matchMedia('(min-width: 992px)'));
                }, 100);

                // Update navigation visibility on resize
                window.addEventListener('resize', () => {
                    this.calculateColumnWidth();
                    this.toggleNavVisibility(window.matchMedia('(min-width: 992px)'));
                });
            },

            setupHeaderNavigation: function () {
                // Get plan headers
                const planHeaders = this.block.querySelectorAll('th.plan-header');

                // Check if content is scrollable before setting up the event listeners
                if (this.tableWrapper.scrollWidth <= this.tableWrapper.clientWidth) {
                    // Remove any existing event listeners and styling if table is not scrollable
                    planHeaders.forEach(header => {
                        header.style.cursor = 'default';
                        header.removeAttribute('tabindex');
                        header.removeAttribute('role');
                        header.removeAttribute('aria-label');
                    });
                    return;
                }

                // Only add listeners if table is scrollable
                planHeaders.forEach((header, index) => {
                    header.style.cursor = 'pointer';
                    header.setAttribute('tabindex', '0');
                    header.setAttribute('role', 'button');
                    header.setAttribute('aria-label', `View ${header.textContent.trim()} column`);

                    // Handle click and keyboard events
                    const handleAction = () => {
                        const columnIndex = index + 1;
                        this.scrollToColumn(columnIndex);
                    };

                    // Remove any existing listeners first to prevent duplicates
                    header.removeEventListener('click', handleAction);
                    header.removeEventListener('keydown', handleKeydown);

                    // Add new event listeners
                    header.addEventListener('click', handleAction);

                    // Define keydown handler separately so we can remove it later
                    const handleKeydown = (e) => {
                        if (e.key === 'Enter' || e.key === ' ') {
                            handleAction();
                            e.preventDefault();
                        }
                    };
                    header.addEventListener('keydown', handleKeydown);

                    // Store the handler reference for potential removal
                    header._keydownHandler = handleKeydown;
                });
            },

            scrollToColumn: function (columnIndex) {
                if (!this.tableWrapper) return;

                const planHeaders = this.block.querySelectorAll('th.plan-header');
                if (columnIndex <= planHeaders.length) {
                    // Get the header element
                    const targetHeader = planHeaders[columnIndex - 1]; // -1 because array is 0-indexed

                    if (targetHeader) {
                        // Calculate position (account for the first column which is fixed)
                        const featureColumn = this.block.querySelector('th.feature');
                        const offsetLeft = featureColumn ? featureColumn.offsetWidth : 0;

                        // Get the actual width of the target column for accurate centering
                        const targetWidth = targetHeader.offsetWidth;

                        // Calculate target position to center the column in the viewport
                        const targetPosition = targetHeader.offsetLeft - offsetLeft;
                        const centerOffset = (this.tableWrapper.clientWidth - targetWidth) / 2;

                        // Ensure we don't scroll beyond the table boundaries
                        const maxScroll = this.tableWrapper.scrollWidth - this.tableWrapper.clientWidth;
                        const scrollTarget = Math.min(maxScroll, Math.max(0, targetPosition - centerOffset));

                        this.tableWrapper.scrollTo({
                            left: scrollTarget,
                            behavior: 'smooth'
                        });
                    }
                }
            },

            calculateColumnWidth: function () {
                // Get width of a plan column from the table
                const headerCells = this.block.querySelectorAll('th.plan-header');
                if (headerCells.length) {
                    this.columnWidth = headerCells[0].offsetWidth;
                }
            },

            createNavigation: function () {
                const nav = document.createElement('div');
                nav.classList.add('swiper-nav');
                nav.setAttribute('role', 'region');
                nav.setAttribute('aria-label', 'Table navigation controls');

                // Create prev button
                const prev = document.createElement('button');
                prev.classList.add('swiper-arrow', 'swiper-prev');
                prev.setAttribute('type', 'button');
                prev.setAttribute('aria-label', 'Scroll table left');
                prev.innerHTML = '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="23" viewBox="0 0 24 23" fill="none" aria-hidden="true"><path d="M16.9285 21.3569L7.07132 11.4998L16.9285 1.64265" stroke="currentColor" stroke-width="3"/></svg>';
                prev.addEventListener('click', () => this.scrollTable(-1));

                // Create pagination dots (optional)
                const dots = document.createElement('div');
                dots.classList.add('swiper-pagination');
                dots.setAttribute('role', 'presentation');

                // Create next button
                const next = document.createElement('button');
                next.classList.add('swiper-arrow', 'swiper-next');
                next.setAttribute('type', 'button');
                next.setAttribute('aria-label', 'Scroll table right');
                next.innerHTML = '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="23" viewBox="0 0 24 23" fill="none" aria-hidden="true"><path d="M7.07153 1.64307L16.9287 11.5002L7.07153 21.3574" stroke="currentColor" stroke-width="3"/></svg>';
                next.addEventListener('click', () => this.scrollTable(1));

                // Append navigation elements
                nav.appendChild(prev);
                nav.appendChild(dots);
                nav.appendChild(next);

                // Add swipe instruction for mobile
                const navTitle = document.createElement('p');
                navTitle.classList.add('d-lg-none', 'text-center', 'mb-3', 'small');
                navTitle.innerText = 'Swipe to compare or use arrow buttons.';

                // Add navigation elements to block
                this.block.prepend(nav);
                this.block.prepend(navTitle);

                this.toggleNavVisibility();

                // Add keyboard event listener for arrow key navigation
                this.tableWrapper.setAttribute('tabindex', '0');
                this.tableWrapper.setAttribute('role', 'region');
                this.tableWrapper.setAttribute('aria-label', 'Comparison table, use arrow keys to scroll');

                this.tableWrapper.addEventListener('keydown', (e) => {
                    if (e.key === 'ArrowLeft') {
                        this.scrollTable(-1);
                        e.preventDefault();
                    } else if (e.key === 'ArrowRight') {
                        this.scrollTable(1);
                        e.preventDefault();
                    }
                });

                window.addEventListener('resize', () => this.toggleNavVisibility());
            },

            scrollTable: function (direction) {
                if (!this.tableWrapper) return;

                // Get current scroll position
                const currentScroll = this.tableWrapper.scrollLeft;

                // Calculate new scroll position
                const newScroll = currentScroll + (direction * this.columnWidth);

                // Smooth scroll to new position
                this.tableWrapper.scrollTo({
                    left: newScroll,
                    behavior: 'smooth'
                });
            },

            toggleNavVisibility: function () {
                const nav = this.block.querySelector('.swiper-nav');
                const title = this.block.querySelector('p.d-lg-none');

                if (!nav || !title) return;

                // Check if content is wider than container
                const isScrollable = this.tableWrapper.scrollWidth > this.tableWrapper.clientWidth;

                // Only show navigation if there's content to scroll
                nav.style.display = isScrollable ? 'flex' : 'none';
                title.style.display = isScrollable ? 'block' : 'none';

                // Add appropriate ARIA attributes to the table wrapper
                this.tableWrapper.setAttribute('aria-scrollable', isScrollable.toString());

                // Update table classes for shadow indication
                const table = this.tableWrapper.querySelector('table');
                if (table) {
                    if (isScrollable) {
                        table.classList.add('overflowed');
                    } else {
                        table.classList.remove('overflowed');
                    }
                }

                // Update header navigation based on scrollability
                this.setupHeaderNavigation();
            },
        };
    }

    blocks.forEach(block => {
        // const block_comparison_table = create_block_comparison_table();
        // block_comparison_table.init(block);

        const block_comparison_table = create_block_comparison_table_scroll();
        block_comparison_table.init(block);
    });
});