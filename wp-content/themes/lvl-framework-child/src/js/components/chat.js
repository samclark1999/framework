/**
 * Chat Link Handler
 *
 * Attaches event listeners to all links with href="#start-chat" to open the chat interface
 * Provides keyboard accessibility and proper ARIA attributes
 */
document.addEventListener('DOMContentLoaded', () => {
    // Find all links with #start-chat href
    const chatLinks = document.querySelectorAll('a[href="#start-chat"]');

    // Process each link to add click handler
    chatLinks.forEach(link => {
        // Add ARIA attributes for accessibility
        link.setAttribute('role', 'button');
        link.setAttribute('aria-label', 'Start chat support');

        // Handle click event
        link.addEventListener('click', function (event) {
            // Prevent default anchor behavior
            event.preventDefault();

            try {

                // Check if embedded_svc exists before calling
                if (typeof embedded_svc !== 'undefined') {
                    // set cursor to wait so user knows something is happening, disable clicked link
                    document.body.style.cursor = 'wait';
                    link.classList.add('--waiting');
                    link.setAttribute('aria-disabled', 'true');
                    link.setAttribute('tabindex', '-1');
                    link.setAttribute('aria-hidden', 'true');
                    link.style.pointerEvents = 'none';
                    // when embedded_svc loaded event fires, set cursor back to default
                    embedded_svc.addEventHandler('afterMaximize', function () {
                            document.body.style.cursor = 'default';
                            if(link.classList.contains('--waiting')) {
                                link.classList.remove('active');
                                link.removeAttribute('aria-disabled');
                                link.removeAttribute('tabindex');
                                link.removeAttribute('aria-hidden');
                                link.style.pointerEvents = 'auto';
                            }
                        }
                    );

                    // Set any required pre-chat fields if needed
                    // embedded_svc.settings.prepopulatedPrechatFields = {
                    //     // Default values can be set here
                    //     How_Can_We_Help_You__c: 'General Inquiry',
                    //     Are_you_a_current_customer__c: ''
                    // };

                    // Launch the chat interface
                    embedded_svc.bootstrapEmbeddedService();

                    // // Announce to screen readers
                    // const announcement = document.createElement('div');
                    // announcement.setAttribute('aria-live', 'polite');
                    // announcement.textContent = 'Chat window opened';
                    // announcement.style.position = 'absolute';
                    // announcement.style.left = '-9999px';
                    // document.body.appendChild(announcement);
                    //
                    // // Remove announcement after it's been read
                    // setTimeout(() => document.body.removeChild(announcement), 3000);
                } else {
                    console.error('Chat service not available');
                    // Provide user feedback
                    showToast('Chat service is currently unavailable. Please try again later.');
                }
            } catch (error) {
                console.error('Error starting chat:', error);
            }
        });

        // Add keyboard support for accessibility
        link.addEventListener('keydown', function (event) {
            // Trigger on Enter or Space key
            if (event.key === 'Enter' || event.key === ' ') {
                event.preventDefault();
                link.click();
            }
        });
    });

    // Show a custom toast notification
    const showToast = (message) => {
        // Create toast container if it doesn't exist
        let toastContainer = document.getElementById('chat-toast-container');
        if (!toastContainer) {
            toastContainer = document.createElement('div');
            toastContainer.id = 'chat-toast-container';
            toastContainer.style.cssText = 'position:fixed; bottom:20px; right:20px; z-index:9999;';
            document.body.appendChild(toastContainer);
        }

        // Create toast element
        const toast = document.createElement('div');
        toast.className = 'chat-toast';
        toast.setAttribute('role', 'alert');
        toast.setAttribute('aria-live', 'assertive');
        toast.style.cssText = 'background-color:#f8d7da; color:#721c24; padding:12px 20px; ' +
            'margin-top:10px; border-radius:4px; box-shadow:0 2px 5px rgba(0,0,0,0.2); ' +
            'max-width:350px; opacity:0; transition:opacity 0.3s;';

        // Add message and close button
        toast.innerHTML = `
            <div class="d-flex align-items-center">
                <span>${message}</span>
                <button type="button" aria-label="Close" style="background:none; border:none; margin-left:10px; cursor:pointer;">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        `;

        // Add to container
        toastContainer.appendChild(toast);

        // Fade in
        setTimeout(() => {
            toast.style.opacity = '1';
        }, 10);

        // Set up close button
        const closeBtn = toast.querySelector('button');
        closeBtn.addEventListener('click', () => {
            toast.style.opacity = '0';
            setTimeout(() => {
                toast.remove();
            }, 300);
        });

        // Auto dismiss after 5 seconds
        setTimeout(() => {
            toast.style.opacity = '0';
            setTimeout(() => {
                toast.remove();
            }, 300);
        }, 5000);
    };
});