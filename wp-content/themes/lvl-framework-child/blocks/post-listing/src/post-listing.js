document.addEventListener('DOMContentLoaded', (e) => {

    const blocks = document.querySelectorAll('.block--post-listing');

    function create_post_listing() {

        return {
            block: null,
            loaded: true,
            page: 1,
            perPage: 12,

            init: function (block) {
                this.block = block;

                // check filters and if any have dataset.isActive set to true, then update the URL with the filter
                const filters = this.block.querySelectorAll('.filter .dropdown-item');
                let presetFilters = [];
                filters.forEach(filter => {
                    if (filter.dataset.isActive === 'true') {
                        presetFilters.push(filter);
                    }
                });

                if (presetFilters.length > 0) {
                    let url = new URL(window.location);
                    let params = new URLSearchParams(url.search);

                    presetFilters.forEach(filter => {
                        this.params(filter.dataset.filterType, filter.dataset.filterValue);
                        this.filtered(filter.dataset.filterType, filter.dataset.filterValue, filter.innerText);
                        filter.removeAttribute('data-if-active');
                    });

                    this.block.scrollIntoView();
                }

                // check for url params on page load and set filters
                let url = new URL(window.location);
                let params = new URLSearchParams(url.search);

                params.forEach((value, key) => {
                    let values = value.split(',');
                    values.forEach(value => {
                        if (value) {
                            let text = '';

                            let filter = this.block.querySelector('.filter .dropdown-item[data-filter-value="' + value + '"]');
                            if (!filter) {
                                filter = this.block.querySelector('.filter .dropdown-item[data-filter-value-id="' + value + '"]');
                            }

                            if (filter) {
                                text = filter.innerText;
                                if (text) {
                                    this.filtered(key, value, text);
                                }
                            }
                        }
                    });
                });

                // add event listeners for all dropdown items
                let dropdowns = this.block.querySelectorAll('.filter .dropdown');
                dropdowns.forEach(filter => {
                    this.dropdowns(filter);
                });

                let keyword = this.block.querySelector('#keyword');
                if (keyword) {
                    this.keyword(keyword);
                }

                // additional listeners and load the posts
                this.setupSubmitButton();
                this.setupResetButton();
                this.setupLoadMoreButton();

                // Only load via AJAX if we're not on initial load
                // Mark as initialized but don't load via AJAX
                if (this.block.dataset.init === 'true') {
                    this.block.dataset.init = 'false';
                }

                // Add listeners to pagination elements that were server-rendered
                this.initPaginationListeners();
            },

            params: function (type, value) {
                let url = new URL(window.location);
                let param = (url.searchParams.get(type)) ? url.searchParams.get(type).split(',') : [];

                if (!param.includes(value)) {
                    param.push(value);
                    url.searchParams.set(type, param.join(','));
                    url.search = decodeURIComponent(url.search);
                    window.history.replaceState({}, '', url);
                }
            },

            dropdowns: function (filter) {
                let toggle = filter.querySelector('.dropdown-toggle');
                let options = filter.querySelectorAll('.dropdown-item');
                const self = this;

                options.forEach(option => {
                    option.addEventListener('click', function (e) {
                        let type = e.target.dataset.filterType;
                        let value = e.target.dataset.filterValue;
                        let text = e.target.innerText;
                        self.filtered(type, value, text);
                    });

                    option.addEventListener('keydown', function (e) {
                        if (e.key === 'Enter') {
                            let type = e.target.dataset.filterType;
                            let value = e.target.dataset.filterValue;
                            let text = e.target.innerText;
                            self.filtered(type, value, text);
                            toggle.focus();
                            toggle.setAttribute('aria-expanded', 'false');
                        }
                    });
                });
            },

            keyword: function (keyword) {
                const self = this;
                keyword.addEventListener('keydown', function (event) {
                    if (event.key === 'Enter') {
                        let type = 'keyword';
                        let value = keyword.value;
                        let text = keyword.value;

                        if (keyword.value !== '') {
                            keyword.value = '';
                            self.loaded = false;
                            self.page = 1;
                            self.filtered(type, value, text);
                        }
                    }
                });
            },

            filtered: function (type, value, text) {
                let filtered = this.block.querySelector('.filtered');
                let filter = filtered.querySelector('[data-filter-type="' + type + '"][data-filter-value="' + value + '"]');
                const self = this;

                if (!filter) {
                    const button = document.createElement('button');
                    button.classList.add('btn', 'btn-primary', 'btn-filter');
                    button.setAttribute('data-filter-type', type);
                    button.setAttribute('data-filter-value', value);
                    button.innerText = text;

                    button.addEventListener('click', (e) => {
                        let url = new URL(window.location);
                        let param = url.searchParams.get(e.target.dataset.filterType);

                        if (param) {
                            let values = param.split(',');
                            values = values.filter(value => value !== e.target.dataset.filterValue);
                            values = values.join(',');

                            if (values != '') {
                                url.searchParams.set(e.target.dataset.filterType, values);
                            } else {
                                url.searchParams.delete(e.target.dataset.filterType);
                            }

                            window.history.replaceState(null, '', url);
                        }

                        e.target.remove();
                        self.loaded = false;
                        self.page = 1;
                        self.load();
                    });

                    filtered.appendChild(button);

                    if (this.block.dataset.init === 'true') {
                        return;
                    }

                    this.loaded = false;
                    this.page = 1;
                    this.params(type, value);
                    this.load();
                }
            },

            setupSubmitButton: function () {
                let submit = this.block.querySelector('.filter-submit');
                let keyword = this.block.querySelector('#keyword');
                const self = this;

                if (submit && keyword) {
                    submit.addEventListener('click', function (e) {
                        e.preventDefault();
                        let type = 'keyword';
                        let value = keyword.value;
                        let text = keyword.value;

                        if (keyword.value !== '') {
                            keyword.value = '';
                            self.loaded = false;
                            self.page = 1;
                            self.filtered(type, value, text);
                        }
                    });
                }
            },

            setupResetButton: function () {
                const self = this;
                this.block.addEventListener('click', function (e) {
                    if (e.target.classList.contains('filter-reset')) {
                        let url = new URL(window.location);
                        url.search = '';

                        window.history.replaceState({}, '', url);

                        let filtered = self.block.querySelector('.filtered');
                        filtered.innerHTML = '';

                        self.loaded = false;
                        self.page = 1;
                        self.load();
                    }
                });
            },

            setupLoadMoreButton: function () {
                const self = this;
                this.block.addEventListener('click', function (e) {
                    // remove all .--loaded classes
                    const cards = self.block.querySelectorAll('.--loaded');
                    cards.forEach(function (card) {
                            card.classList.remove('--loaded');
                        }
                    );

                    if (e.target.classList.contains('load-more')) {
                        self.loaded = true;
                        self.page = self.page + 1;
                        self.load();

                        // blur the button to remove focus
                        e.target.blur();
                    }
                });
            },

            initPaginationListeners: function () {
                const paginationLinks = this.block.querySelectorAll('.pagination .page-link');
                const self = this;

                paginationLinks.forEach(link => {
                    if (link.dataset.page && link.dataset.page !== '...') {
                        link.addEventListener('click', (e) => {
                            e.preventDefault();

                            const target = self.block.querySelector('.post-listing-target');
                            target.innerHTML = '';

                            self.loaded = false;
                            self.page = parseInt(e.currentTarget.dataset.page);
                            self.load();
                            self.block.scrollIntoView();
                        });
                    }
                });
            },

            pagination: function (pagination) {
                pagination = JSON.parse(pagination);

                let totalPages = pagination.totalPages;
                let currentPage = pagination.currentPage;

                const paginationContainer = this.block.querySelector('.pagination');
                if (!paginationContainer) return;

                paginationContainer.innerHTML = '';
                paginationContainer.classList.remove('d-none');
                const self = this;

                if (totalPages > 1) {
                    // add previous arrow
                    if (currentPage > 1) {
                        const prevLink = document.createElement('a');
                        prevLink.classList.add('page-link');
                        prevLink.setAttribute('href', window.location.href.split('?')[0] + 'page/' + (currentPage - 1));
                        prevLink.setAttribute('data-page', currentPage - 1);
                        prevLink.innerHTML = '<svg xmlns="http://www.w3.org/2000/svg" width="15" height="25" viewBox="0 0 15 25" fill="none"><path d="m12.5 2-10.5 10.5 10.5 10.5" fill="none" stroke="currentColor" stroke-width="3"/></svg>';

                        paginationContainer.appendChild(prevLink);
                    }

                    const range = 2; // The range of pages around the current page
                    let start = currentPage - range;
                    let end = currentPage + range;

                    if (start < 1) {
                        start = 1;
                        end = start + range * 2;
                    }

                    if (end > totalPages) {
                        end = totalPages;
                        start = end - range * 2;
                        if (start < 1) start = 1;
                    }

                    if (totalPages <= range * 2) {
                        for (let i = 1; i <= totalPages; i++) {
                            this.appendPageItem(paginationContainer, i, currentPage);
                        }
                    } else {
                        if (start > 2) {
                            this.appendPageItem(paginationContainer, 1, currentPage);
                            this.appendPageItem(paginationContainer, '...', currentPage);
                        } else if (start === 2) {
                            this.appendPageItem(paginationContainer, 1, currentPage);
                        }

                        for (let i = start; i <= end; i++) {
                            this.appendPageItem(paginationContainer, i, currentPage);
                        }

                        if (end < totalPages - 1) {
                            this.appendPageItem(paginationContainer, '...', currentPage);
                            this.appendPageItem(paginationContainer, totalPages, currentPage);
                        } else if (end === totalPages - 1) {
                            this.appendPageItem(paginationContainer, totalPages, currentPage);
                        }
                    }

                    // add next arrow
                    if (currentPage != totalPages) {
                        const nextLink = document.createElement('a');
                        nextLink.classList.add('page-link');
                        nextLink.setAttribute('href', window.location.href.split('?')[0] + 'page/' + (currentPage + 1));
                        nextLink.setAttribute('data-page', currentPage + 1);
                        nextLink.innerHTML = '<svg xmlns="http://www.w3.org/2000/svg" width="15" height="25" viewBox="0 0 15 25" fill="none"><path d="M2 23L12.5 12.5L2 2" stroke="currentColor" stroke-width="3"/></svg>';

                        paginationContainer.appendChild(nextLink);
                    }

                    const paginationItems = this.block.querySelectorAll('.page-link');
                    paginationItems.forEach(item => {
                        if (item.dataset.page != '...') {
                            item.addEventListener('click', (e) => {
                                e.preventDefault();

                                const target = self.block.querySelector('.post-listing-target');
                                target.innerHTML = '';

                                self.loaded = true;
                                self.page = parseInt(e.currentTarget.dataset.page);
                                self.load();
                                self.block.scrollIntoView();
                            });
                        } else {
                            item.classList.add('disabled');
                        }
                    });
                }
            },

            appendPageItem: function (container, page, currentPage) {
                const paginationItem = document.createElement('a');
                paginationItem.classList.add('page-link');
                paginationItem.setAttribute('href', window.location.href.split('?')[0] + 'page/' + page);
                paginationItem.setAttribute('data-page', page);
                paginationItem.innerText = page;

                if (page === currentPage) {
                    paginationItem.classList.add('active');
                }

                container.appendChild(paginationItem);
            },

            load: function () {
                let spinner = this.block.querySelector('.spinner');
                let target = this.block.querySelector('.post-listing-target');
                const filterBar = this.block.querySelector('.filter-bar');
                const perPage = this.block.querySelector('#perpage');
                const pagination = this.block.querySelector('.pagination');
                const self = this;

                if (perPage) {
                    this.perPage = perPage.value;
                }

                let more = this.block.querySelector('.load-more');
                // if (more) {
                //     more.disabled = true;
                //     more.classList.remove('d-block');
                // }

                if (!this.loaded) {
                    target.innerHTML = '';
                }

                let filtereds = [];
                let filters = this.block.querySelectorAll('.btn-filter');
                const filterReset = this.block.querySelector('.filter-reset');

                if (filters.length != 0) {
                    filterReset?.classList.remove('d-none');
                } else {
                    filterReset?.classList.add('d-none');
                }

                filters.forEach(item => {
                    const obj = {
                        type: item.dataset.filterType,
                        value: item.dataset.filterValue
                    };

                    filtereds.push(obj);
                });

                const displayOptions = {};
                for (let i = 0; i < this.block.attributes.length; i++) {
                    const attr = this.block.attributes[i];
                    if (attr.name.startsWith('data-lvl-')) {
                        let name = attr.name.replace('data-lvl-', '');
                        displayOptions[name] = attr.value;
                    }
                }

                spinner.style.display = 'block';

                const preFilters = (this.block.dataset?.preFilter ? JSON.parse(this.block.dataset.preFilter) : []);
                if (preFilters && Array.isArray(preFilters)) {
                    preFilters.forEach(item => {
                        const obj = {
                            type: item.type,
                            value: item.value
                        };

                        filtereds.push(obj);
                    });
                }

                const data = new FormData();

                data.append('filters', JSON.stringify(filtereds));
                data.append('pre_filter', JSON.stringify(preFilters));
                data.append('action', 'lvl_block_post_listing_get');
                data.append('nonce', lvl_block_post_listing_ajax.nonce);
                data.append('page', this.page);
                data.append('current_id', this.block.dataset.postId);
                data.append('post_types', filterBar?.dataset?.postTypes);
                data.append('is_disable_click', this.block.dataset?.disableClick);
                data.append('event_status', this.block.dataset?.eventStatus);
                data.append('posts_per_page', this.perPage);
                data.append('display_options', JSON.stringify(displayOptions));
                data.append('link_text', this.block.dataset.linkText);
                data.append('featured_posts', this.block.dataset.featuredPosts);
                data.append('card_layout', this.block.dataset.cardLayout);
                data.append('show_count', this.block.dataset.showCount);

                if (window.location.pathname.includes('/author/')) {
                    const authorName = window.location.pathname.split('/author/')?.pop()?.replace('/', '');
                    data.append('author', authorName);
                }

                fetch(lvl_block_post_listing_ajax.ajax_url, {
                    method: 'POST',
                    credentials: 'same-origin',
                    body: data
                }).then((response) => {
                    if (!response.ok) {
                        // Get HTTP status code and additional error information
                        const errorMsg = `Error getting posts: ${response.status} ${response.statusText}`;
                        self.log(errorMsg);

                        // Try to parse error response if available
                        return response.text().then(errorText => {
                            try {
                                const errorJson = JSON.parse(errorText);
                                self.log('Server error details:', errorJson);
                            } catch (e) {
                                self.log('Server error response:', errorText);
                            }
                            throw new Error(errorMsg);
                        });
                    }
                    return response;
                }).then((response) => {
                    if (more) {
                        if (response?.headers?.get('loadmore') === "true") {
                            more.classList.add('d-block');
                            more.disabled = false;
                        } else {
                            more.classList.remove('d-block');
                            more.disabled = true;
                        }
                    }

                    if (pagination) {
                        self.pagination(response.headers.get('pagination'));
                    }

                    return response?.text();
                }).then((data) => {
                    if (self.loaded) {
                        target.innerHTML += data;
                    } else {
                        target.innerHTML = data;
                    }

                    self.listeners();

                    spinner.style.display = 'none';
                }).then(() => {
                    const event = new Event('lvl-stretch-links');
                    document.dispatchEvent(event);
                }).then(() => {
                    // change --loading to --loaded with a 100ms delay per index
                    const cards = self.block.querySelectorAll('.--loading');
                    cards.forEach(function (card, index) {
                        setTimeout(function () {
                            card.classList.remove('--loading');
                            card.classList.add('--loaded');
                        }, index * 100);
                    });
                }).then(() => {
                    //set min-height on container equal to the first card height
                    const card = self.block.querySelector('.card');
                    if (card) {
                        const cardHeight = card.offsetHeight;
                        target.style.minHeight = cardHeight + 'px';
                    }
                }).catch(error => {
                    spinner.style.display = 'none';
                    self.log('Fetch error:', error);
                    // Show user-friendly error message in the target container
                    target.innerHTML = '<div class="alert alert-danger">Failed to load posts. Please try again later.</div>';
                });
            },

            reload: function () {
                // clear listing and reload posts
                let target = this.block.querySelector('.post-listing-target');
                target.innerHTML = '';
                this.loaded = false;
                this.page = 1;
                this.load();
            },

            listeners: function () {
                let cards = this.block.querySelectorAll('.card');
                // Your card event listeners can go here
            },

            log: function (message) {
                console.log(message);
            }
        };
    }

    blocks.forEach(block => {
        const post_listing = create_post_listing();
        post_listing.init(block);
    });
});