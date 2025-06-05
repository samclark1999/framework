document.addEventListener('DOMContentLoaded', () => {
    // if the modal is on the page and there is a hash link matching the modal id, wire link to modal
    const modals = document.querySelectorAll('.block--modal');
    modals.forEach(modal => {
        const modalId = modal.getAttribute('id');
        if (!modalId) {
            console.warn('Modal ID not found');
            return;
        }
        const modalType = modal.getAttribute('data-bs-toggle-type');
        const modalLink = document.querySelectorAll(`a[href="#${modalId}"]`);
        modalLink.forEach(link => {
            link.setAttribute('data-bs-toggle', modalType);
            link.setAttribute('data-bs-target', `#${modalId}`);
            link.setAttribute('role', 'button');
            link.setAttribute('aria-controls', modalId);
            link.setAttribute('id', `${modalId}Label`);

            // move modal to the end of the body
            document.body.appendChild(modal);

            if (modalType === 'modal') {
                const initModal = new Modal(modal, {});
                modal.addEventListener('shown.bs.modal', () => {
                    link.focus();
                });
            } else if (modalType === 'offcanvas') {
                const initModal = new Offcanvas(modal, {});
                modal.addEventListener('shown.bs.offcanvas', () => {
                    link.focus();
                });
            }

            // if (!initModal) {
            //     console.error('Modal not found');
            // }

        });
    });
});