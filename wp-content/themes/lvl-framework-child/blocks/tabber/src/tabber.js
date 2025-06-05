document.addEventListener('DOMContentLoaded', function () {
    const tabDropdownSet = function (block) {
        let dropdown = block.querySelector('.dropdown');
        if (!dropdown) return;
        let dropdownItemsActive = dropdown.querySelector('.active');
        if (dropdownItemsActive) {
            const ddButton = dropdown.querySelector('button');
            ddButton.innerHTML = dropdownItemsActive.innerHTML;
        }
    };


    const tabbers = document.querySelectorAll('.block--tabber');

    tabbers.forEach(function (tabber) {
        if (tabber.classList.contains('--is-preview'))
            return;

        const navStyle = tabber.getAttribute('data-nav-style');
        const panes = tabber.querySelectorAll('.block--tab-pane');

        let nav = null;

        // build nav items in .nav-tabs from panes .block--tab-pane
        const navItems = [];

        const navItemTemplate = document.createElement('li');
        navItemTemplate.classList.add('nav-item');

        const navLinkTemplate = document.createElement('button');

        switch (navStyle) {
            case 'dropdown':
                nav = tabber.querySelector('.dropdown-menu');
                navLinkTemplate.classList.add('dropdown-item');
                break;
            default:
                nav = tabber.querySelector('.nav');
                navLinkTemplate.classList.add('nav-link');
        }

        navLinkTemplate.setAttribute('type', 'button');
        navLinkTemplate.setAttribute('role', 'tab');
        navLinkTemplate.setAttribute('aria-selected', 'false');
        navLinkTemplate.setAttribute('aria-controls', '');
        navLinkTemplate.setAttribute('id', '');
        navLinkTemplate.setAttribute('data-bs-toggle', 'tab');
        navLinkTemplate.setAttribute('data-bs-target', '');

        panes.forEach(function (pane, index) {
            const navItem = navItemTemplate.cloneNode(true);
            const navLink = navLinkTemplate.cloneNode(true);
            const paneId = pane.getAttribute('id');
            const paneTitle = pane.getAttribute('data-tab-title');
            pane.setAttribute('aria-labelledby', 'tab-' + paneId);

            if (index === 0) {
                pane.classList.add('active', 'show');
                navLink.classList.add('active');
            }

            navLink.setAttribute('aria-controls', paneId);
            navLink.setAttribute('id', 'tab-' + paneId);
            navLink.setAttribute('data-bs-target', '#' + paneId);
            navLink.innerHTML = paneTitle;
            navItem.appendChild(navLink);
            navItems.push(navItem);

            //Scroll to top on tab click
            navLink.addEventListener('show.bs.tab', function (event) {
                const geoCheck = event.target?.closest('.geo-check');
                if (geoCheck) return;

                // document.getElementById(tabber.getAttribute('id')).scrollIntoView();
                const id = tabber.getAttribute('id');
                const header = document.getElementById('header');
                const yOffset = -header.offsetHeight - 40;
                const element = document.getElementById(id);
                const y = element.getBoundingClientRect().top + window.scrollY + yOffset;

                window.scrollTo({top: y, behavior: 'smooth'});
            })
        });

        navItems.forEach(function (navItem) {
            nav.appendChild(navItem);
        });

        //Sticky Tabber Nav
        const throttle = (fn, delay) => {
            let time = Date.now();
            return () => {
                if ((time + delay - Date.now()) <= 0) {
                    fn();
                    time = Date.now();
                }
            }
        }

        tabDropdownSet(tabber);

        // window.addEventListener('scroll', throttle(adjustTopOffset, 100));
        //
        // function adjustTopOffset() {
        //     const body = document.querySelector('body');
        //     const header = document.querySelector('#header');
        //     const headerHeight = header.offsetHeight;
        //     const tabberNav = document.querySelector('.nav-tabs');
        //
        //     if ((body.classList.contains('scrolled--up') && body.classList.contains('peekaboo-nav')) || body.classList.contains('sticky-nav')) {
        //         tabberNav.style.top = headerHeight + 'px';
        //     } else {
        //         tabberNav.style.top = '0px';
        //     }
        // }


    });

    // TODO: do we need to do this?
    //  Seems to be handled by bootstrap still, but there could be a race condition where it wouldn't work without this.
    //  Investigate further.
    // const triggerTabList = document.querySelectorAll('[data-bs-toggle="tab"]')
    // triggerTabList.forEach(triggerEl => {
    //     const tabTrigger = new Tab(triggerEl)
    //
    //     triggerEl.addEventListener('click', event => {
    //         let hash = triggerEl.getAttribute('data-bs-target');
    //         hash = hash.replace('tab-pane-', '');
    //
    //         event.preventDefault()
    //         tabTrigger.show()
    //         tabDropdownSet(triggerEl.closest('.block--tabber'));
    //
    //         if (triggerEl?.closest('footer')) return;
    //
    //         window.location.hash = hash;
    //     })
    // })

    //if there is a hash in the url, open the tab
    let hash = window.location.hash;
    if (hash) {
        lvl_tabber_hash_link_fire(hash);
    }

    // if a hash link is clicked check if tab can be opened
    document.addEventListener('click', function (event) {
        const link = event.target.closest('a');
        if (link && link.hash) {
            lvl_tabber_hash_link_fire(link.hash);
        }
    });

    function lvl_tabber_hash_link_fire(hash){
        if(!hash) return;
        const urlHash = hash;
        hash = hash.replace('#', '#tab-pane-');

        const tab = document.querySelector('[data-bs-target="' + hash + '"]');
        if (tab) {
            tab.click();
        } else {
            const element = document.querySelector(hash);
            if (element) {
                const closestTabPane = element.closest('.block--tab-pane');
                if (closestTabPane) {
                    const tab = document.querySelector('[data-bs-target="#' + closestTabPane.getAttribute('id') + '"]');
                    if (tab) {
                        tab.click();

                        if (tab?.closest('footer')) return;
                        window.location.hash = urlHash;
                    }
                }
            }
        }
    }
});