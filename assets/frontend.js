document.addEventListener('DOMContentLoaded', function() {
    const triggers = document.querySelectorAll('.amoeba-modal-trigger');
    const overlays = document.querySelectorAll('.amoeba-modal-overlay');

    // Open Modal
    triggers.forEach(trigger => {
        trigger.addEventListener('click', function() {
            const modalId = this.getAttribute('data-modal-target');
            const modal = document.getElementById(modalId);
            if (modal) {
                modal.classList.add('active');
                document.body.style.overflow = 'hidden'; // Prevent scrolling
            }
        });
    });

    // Close Modal by Clicking Close Button or Overlay
    overlays.forEach(overlay => {
        overlay.addEventListener('click', function(e) {
            // Close if clicking outside the content or on the close button
            if (e.target === this || e.target.classList.contains('amoeba-modal-close')) {
                closeModal(this);
            }
        });
    });

    // Close Modal with ESC key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            const activeModal = document.querySelector('.amoeba-modal-overlay.active');
            if (activeModal) {
                closeModal(activeModal);
            }
        }
    });

    function closeModal(modal) {
        modal.classList.remove('active');
        document.body.style.overflow = ''; // Restore scrolling
    }
});
