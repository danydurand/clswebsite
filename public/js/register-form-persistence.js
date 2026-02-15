/**
 * Registration Form Data Persistence
 * 
 * Automatically saves and restores registration form data using localStorage
 * to preserve user input when navigating to Terms & Conditions and back.
 */

(function() {
    'use strict';

    const STORAGE_KEY = 'dreambet_register_form_data';
    
    // Fields to persist (excluding passwords for security)
    const FIELDS_TO_PERSIST = ['name', 'email', 'phone', 'birth_date'];

    /**
     * Save form data to localStorage
     */
    function saveFormData() {
        const formData = {};
        
        FIELDS_TO_PERSIST.forEach(fieldName => {
            const field = document.querySelector(`[name="${fieldName}"]`);
            if (field && field.value) {
                formData[fieldName] = field.value;
            }
        });

        // Only save if there's actual data
        if (Object.keys(formData).length > 0) {
            localStorage.setItem(STORAGE_KEY, JSON.stringify(formData));
        }
    }

    /**
     * Restore form data from localStorage
     */
    function restoreFormData() {
        const savedData = localStorage.getItem(STORAGE_KEY);
        
        if (!savedData) {
            return;
        }

        try {
            const formData = JSON.parse(savedData);
            
            FIELDS_TO_PERSIST.forEach(fieldName => {
                if (formData[fieldName]) {
                    const field = document.querySelector(`[name="${fieldName}"]`);
                    if (field && !field.value) {
                        field.value = formData[fieldName];
                        
                        // Trigger input event for any listeners (e.g., Flux UI components)
                        field.dispatchEvent(new Event('input', { bubbles: true }));
                        field.dispatchEvent(new Event('change', { bubbles: true }));
                    }
                }
            });
        } catch (error) {
            console.error('Error restoring form data:', error);
            // Clear corrupted data
            localStorage.removeItem(STORAGE_KEY);
        }
    }

    /**
     * Clear saved form data
     */
    function clearFormData() {
        localStorage.removeItem(STORAGE_KEY);
    }

    /**
     * Initialize form persistence
     */
    function init() {
        // Restore data when page loads
        restoreFormData();

        // Save data on input changes
        FIELDS_TO_PERSIST.forEach(fieldName => {
            const field = document.querySelector(`[name="${fieldName}"]`);
            if (field) {
                field.addEventListener('input', saveFormData);
                field.addEventListener('change', saveFormData);
            }
        });

        // Clear data on successful form submission
        const form = document.querySelector('form[action*="register"]');
        if (form) {
            form.addEventListener('submit', function(e) {
                // Only clear if form is valid (will actually submit)
                if (form.checkValidity()) {
                    clearFormData();
                }
            });
        }

        // Save data before navigating to T&C (extra safety)
        const tcLink = document.querySelector('a[href*="terms-and-conditions"]');
        if (tcLink) {
            tcLink.addEventListener('click', saveFormData);
        }
    }

    // Initialize when DOM is ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }

    // Expose clear function globally for debugging
    window.clearRegisterFormData = clearFormData;
})();
