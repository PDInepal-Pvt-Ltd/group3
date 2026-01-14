/**
 * Services Page JavaScript
 * 
 * Handles smooth scrolling to service sections and active tab highlighting
 * based on scroll position.
 * 
 * @package SafaFormwork
 * @version 1.0
 */

(function() {
    'use strict';
    
    /**
     * Initialize smooth scroll to service sections
     */
    function initServiceNavigation() {
        const navTabs = document.querySelectorAll('.nav-tab');
        
        navTabs.forEach(tab => {
            tab.addEventListener('click', function(e) {
                e.preventDefault();
                const targetId = this.getAttribute('href');
                const targetSection = document.querySelector(targetId);
                
                if (targetSection) {
                    // Update active tab
                    document.querySelectorAll('.nav-tab').forEach(t => t.classList.remove('active'));
                    this.classList.add('active');
                    
                    // Smooth scroll
                    const offsetTop = targetSection.offsetTop - 100;
                    window.scrollTo({
                        top: offsetTop,
                        behavior: 'smooth'
                    });
                }
            });
        });
    }
    
    /**
     * Update active tab based on scroll position
     */
    function initScrollObserver() {
        const observerOptions = {
            root: null,
            rootMargin: '-100px 0px -50% 0px',
            threshold: 0
        };
        
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const id = entry.target.getAttribute('id');
                    document.querySelectorAll('.nav-tab').forEach(tab => {
                        tab.classList.remove('active');
                        if (tab.getAttribute('href') === '#' + id) {
                            tab.classList.add('active');
                        }
                    });
                }
            });
        }, observerOptions);
        
        document.querySelectorAll('.service-section').forEach(section => {
            observer.observe(section);
        });
    }
    
    // Initialize when DOM is ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', function() {
            initServiceNavigation();
            initScrollObserver();
        });
    } else {
        initServiceNavigation();
        initScrollObserver();
    }
})();



