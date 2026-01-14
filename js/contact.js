/**
 * Safa Contact Page - Form Validation & Interactivity
 * Minimal JavaScript for form validation and user interactions
 */

(function() {
    'use strict';

    // Wait for DOM to be fully loaded
    document.addEventListener('DOMContentLoaded', function() {
        initContactForm();
        initCharCounter();
        initFloatingLabels();
        initButtonRipple();
    });

    /**
     * Initialize Contact Form Validation
     */
    function initContactForm() {
        const form = document.getElementById('safaContactForm');
        const messageDiv = document.getElementById('safaContactFormMessage');
        
        if (!form) return;

        // Real-time validation for all inputs
        const inputs = form.querySelectorAll('.safa-contact-input, .safa-contact-textarea, .safa-contact-select');
        inputs.forEach(input => {
            // Remove error class on input
            input.addEventListener('input', function() {
                const formGroup = this.closest('.safa-contact-form-group');
                if (formGroup) {
                    formGroup.classList.remove('error');
                }
            });

            // Validate on blur
            input.addEventListener('blur', function() {
                validateField(this);
            });
        });

        // Form submission (AJAX to API)
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            
            let isValid = true;
            const requiredFields = form.querySelectorAll('[required]');
            
            // Validate all required fields
            requiredFields.forEach(field => {
                if (!validateField(field)) {
                    isValid = false;
                }
            });

            // Validate email format
            const emailField = document.getElementById('safaContactEmail');
            if (emailField && emailField.value) {
                const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                if (!emailRegex.test(emailField.value)) {
                    showFieldError(emailField, 'Please enter a valid email address');
                    isValid = false;
                }
            }

            // Validate message length
            const messageField = document.getElementById('safaContactMessage');
            if (messageField && messageField.value.length > 400) {
                showFieldError(messageField, 'Message must be 400 characters or less');
                isValid = false;
            }

            if (!isValid) {
                if (messageDiv) {
                    messageDiv.textContent = 'Please correct the errors below.';
                    messageDiv.className = 'safa-contact-form-message safa-contact-error';
                    messageDiv.style.display = 'block';
                    messageDiv.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
                }
                return;
            }

            const formData = new FormData(form);
            const submitBtn = form.querySelector('.safa-contact-submit-btn');
            const originalText = submitBtn ? submitBtn.textContent : 'Send Message';
            if (submitBtn) {
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<span class="loading"></span> Sending...';
            }

            fetch('api/send_inquiry.php', {
                method: 'POST',
                body: formData
            })
            .then(r => r.json())
            .then(data => {
                if (submitBtn) {
                    submitBtn.disabled = false;
                    submitBtn.textContent = originalText;
                }
                if (data && data.ok) {
                    if (messageDiv) {
                        messageDiv.textContent = '✅ Thank you! We\'ll get back to you soon.';
                        messageDiv.className = 'safa-contact-form-message safa-contact-success';
                        messageDiv.style.display = 'block';
                        messageDiv.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
                    }
                    form.reset();
                    const charCount = document.getElementById('safaContactCharCount');
                    if (charCount) charCount.textContent = '0';
                } else {
                    if (messageDiv) {
                        messageDiv.textContent = (data && data.error) ? data.error : 'Something went wrong. Please try again.';
                        messageDiv.className = 'safa-contact-form-message safa-contact-error';
                        messageDiv.style.display = 'block';
                        messageDiv.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
                    }
                }
            })
            .catch(() => {
                if (submitBtn) {
                    submitBtn.disabled = false;
                    submitBtn.textContent = originalText;
                }
                if (messageDiv) {
                    messageDiv.textContent = 'Network error. Please try again.';
                    messageDiv.className = 'safa-contact-form-message safa-contact-error';
                    messageDiv.style.display = 'block';
                    messageDiv.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
                }
            });
        });
    }

    /**
     * Validate individual field
     */
    function validateField(field) {
        const formGroup = field.closest('.safa-contact-form-group');
        if (!formGroup) return true;

        const isRequired = field.hasAttribute('required');
        const value = field.value.trim();
        
        // Check if required field is empty
        if (isRequired && !value) {
            showFieldError(field);
            return false;
        }

        // Email validation
        if (field.type === 'email' && value) {
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailRegex.test(value)) {
                showFieldError(field, 'Please enter a valid email address');
                return false;
            }
        }

        // Clear error
        formGroup.classList.remove('error');
        return true;
    }

    /**
     * Show field error
     */
    function showFieldError(field, customMessage) {
        const formGroup = field.closest('.safa-contact-form-group');
        if (!formGroup) return;

        formGroup.classList.add('error');
        
        // Update error message if custom message provided
        const errorMessage = formGroup.querySelector('.safa-contact-error-message');
        if (errorMessage && customMessage) {
            errorMessage.textContent = customMessage;
        }
    }

    /**
     * Initialize Character Counter
     */
    function initCharCounter() {
        const messageField = document.getElementById('safaContactMessage');
        const charCount = document.getElementById('safaContactCharCount');
        
        if (!messageField || !charCount) return;

        messageField.addEventListener('input', function() {
            const length = this.value.length;
            charCount.textContent = length;
            
            // Change color if approaching limit
            if (length > 350) {
                charCount.style.color = '#e74c3c';
            } else if (length > 300) {
                charCount.style.color = '#f39c12';
            } else {
                charCount.style.color = '#d4a574';
            }
        });
    }

    /**
     * Initialize Floating Labels
     */
    function initFloatingLabels() {
        const inputs = document.querySelectorAll('.safa-contact-input, .safa-contact-textarea');
        const selects = document.querySelectorAll('.safa-contact-select');
        
        // Handle regular inputs and textareas
        inputs.forEach(input => {
            // Check if field has value on load
            if (input.value && input.value.trim() !== '') {
                input.classList.add('has-value');
            }

            // Handle focus
            input.addEventListener('focus', function() {
                this.classList.add('focused');
            });

            // Handle blur
            input.addEventListener('blur', function() {
                this.classList.remove('focused');
                if (this.value && this.value.trim() !== '') {
                    this.classList.add('has-value');
                } else {
                    this.classList.remove('has-value');
                }
            });
        });

        // Handle select dropdowns
        selects.forEach(select => {
            // Check if select has a value on load
            updateSelectLabel(select);

            // Handle change
            select.addEventListener('change', function() {
                updateSelectLabel(this);
            });
        });
    }

    /**
     * Update select label based on value
     */
    function updateSelectLabel(select) {
        const formGroup = select.closest('.safa-contact-form-group');
        if (!formGroup) return;

        const label = formGroup.querySelector('.safa-contact-label');
        if (!label) return;

        if (select.value && select.value !== '') {
            label.style.color = 'var(--safa-contact-accent-start)';
            label.style.fontWeight = '600';
        } else {
            label.style.color = '#888';
            label.style.fontWeight = '500';
        }
    }

    /**
     * Initialize Button Ripple Effect
     */
    function initButtonRipple() {
        const submitBtn = document.querySelector('.safa-contact-submit-btn');
        
        if (!submitBtn) return;

        submitBtn.addEventListener('click', function(e) {
            const ripple = this.querySelector('.safa-contact-btn-ripple');
            if (!ripple) return;

            const rect = this.getBoundingClientRect();
            const x = e.clientX - rect.left;
            const y = e.clientY - rect.top;
            
            ripple.style.left = x + 'px';
            ripple.style.top = y + 'px';
            ripple.style.width = '10px';
            ripple.style.height = '10px';
            
            // Trigger animation
            ripple.style.animation = 'none';
            setTimeout(() => {
                ripple.style.animation = 'safaContactRipple 0.6s ease-out';
            }, 10);
        });
    }

    /**
     * Smooth scroll to form message
     */
    function scrollToMessage() {
        const messageDiv = document.getElementById('safaContactFormMessage');
        if (messageDiv) {
            messageDiv.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
        }
    }

    // Initialize AOS if available
    if (typeof AOS !== 'undefined') {
        AOS.init({
            duration: 800,
            easing: 'ease-out-cubic',
            once: true,
            offset: 100
        });
    }
})();

