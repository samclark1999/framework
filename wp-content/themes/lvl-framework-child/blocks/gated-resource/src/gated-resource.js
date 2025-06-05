class GatedResource {
    constructor() {
        this.blockSelector = '.block--gated-resource';
        this.spinnerClass = 'loading'; // CSS class for the spinner
    }

    setCookie(name, value, days) {
        const date = new Date();
        date.setTime(date.getTime() + (days * 24 * 60 * 60 * 1000));
        document.cookie = `${name}=${value};expires=${date.toUTCString()};path=/`;
    }

    getCookie(name) {
        const cookies = document.cookie.split(';');
        for (let i = 0; i < cookies.length; i++) {
            const cookie = cookies[i].trim();
            if (cookie.startsWith(`${name}=`)) {
                return cookie.substring(name.length + 1);
            }
        }
        return null;
    }

    encodeResourceData(resourceToken) {
        const data = {token: resourceToken, timestamp: Date.now()};
        return btoa(JSON.stringify(data)); // Encode as base64
    }

    decodeResourceData(encodedData) {
        try {
            const decoded = atob(encodedData); // Decode base64
            return JSON.parse(decoded);
        } catch (error) {
            console.error('Failed to decode resource data:', error);
            return null;
        }
    }

    showSpinner(container) {
        const spinner = jQuery(`<div class="${this.spinnerClass}">Loading...</div>`);
        container.append(spinner);
    }

    hideSpinner(container) {
        container.find(`.${this.spinnerClass}`).remove();
    }

    handleFormSubmission() {
        const blockContainers = jQuery(this.blockSelector);
        blockContainers.each((_, container) => {
            const $container = jQuery(container);
            const wrapper = $container.find('.gated-resource-wrapper');
            const resourceForm = wrapper.find('.resource-form');
            const resourceDownload = wrapper.find('.resource-download');

            if (resourceForm.length && resourceDownload.length) {
                resourceForm.hide();
                this.showSpinner(wrapper);

                const downloadBtn = resourceDownload.find('.resource-download-btn');
                const resourceToken = downloadBtn.data('resource');
                const pageId = $container.data('page-id');
                const cookieName = `gated_resource_data_${pageId}`;

                fetch(`/wp-admin/admin-post.php?action=get_resource_token&token=${resourceToken}`)
                    .then(response => response.json())
                    .then(data => {
                        if (data.success && data.token_url) {
                            downloadBtn.attr('href', data.token_url);
                            resourceDownload.removeClass('d-none');
                            const encodedData = this.encodeResourceData(resourceToken);
                            this.setCookie(cookieName, encodedData, 1); // Store encoded data in cookie
                        } else {
                            console.error('Invalid response from server:', data);
                        }
                    })
                    .catch(error => console.error('Error fetching resource token:', error))
                    .finally(() => this.hideSpinner(wrapper));
            }
        });
    }

    checkCookieOnLoad() {
        const blockContainers = jQuery(this.blockSelector);
        blockContainers.each((_, container) => {
            const $container = jQuery(container);
            const pageId = $container.data('page-id');
            const wrapper = $container.find('.gated-resource-wrapper');
            const resourceForm = wrapper.find('.resource-form');
            const resourceDownload = wrapper.find('.resource-download');
            const cookieName = `gated_resource_data_${pageId}`;
            const encodedData = this.getCookie(cookieName);

            if (encodedData) {
                this.showSpinner(wrapper);

                const resourceData = this.decodeResourceData(encodedData);
                if (resourceData && resourceData.token) {
                    resourceForm.hide();
                    const downloadBtn = resourceDownload.find('.resource-download-btn');
                    fetch(`/wp-admin/admin-post.php?action=get_resource_token&token=${resourceData.token}`)
                        .then(response => response.json())
                        .then(data => {
                            if (data.success && data.token_url) {
                                downloadBtn.attr('href', data.token_url);
                                resourceDownload.removeClass('d-none');
                            } else {
                                console.error('Invalid response from server:', data);
                            }
                        })
                        .catch(error => console.error('Error fetching resource token:', error))
                        .finally(() => this.hideSpinner(wrapper));
                }
            }
        });
    }

    init() {
        jQuery(document).on('gform_confirmation_loaded', () => {
            this.handleFormSubmission();
        });

        this.checkCookieOnLoad();
    }
}

jQuery(document).ready(() => {
    const gatedResource = new GatedResource();
    gatedResource.init();
});