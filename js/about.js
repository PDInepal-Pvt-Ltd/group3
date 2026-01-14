// ============================================
// SAFA ABOUT PAGE - MINIMAL JS
// Handles: scroll animations, counters, lightbox
// ============================================

(function() {
    'use strict';

    // Wait for DOM to be ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }

    function init() {
        initCounters();
        initLightbox();
        initScrollAnimations();
        initSwipeTextAnimations();
    }

    // ============================================
    // ANIMATED COUNTERS
    // ============================================
    function initCounters() {
        const counterElements = document.querySelectorAll('.safa-about-metric-number');
        if (counterElements.length === 0) return;

        const observerOptions = {
            root: null,
            rootMargin: '0px',
            threshold: 0.5
        };

        const counterObserver = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting && !entry.target.classList.contains('counted')) {
                    animateCounter(entry.target);
                    entry.target.classList.add('counted');
                }
            });
        }, observerOptions);

        counterElements.forEach(counter => {
            counterObserver.observe(counter);
        });
    }

    function animateCounter(element) {
        const target = parseInt(element.getAttribute('data-count')) || 0;
        const duration = 2000; // 2 seconds
        const increment = target / (duration / 16); // 60fps
        let current = 0;

        const updateCounter = () => {
            current += increment;
            if (current < target) {
                element.textContent = Math.floor(current);
                requestAnimationFrame(updateCounter);
            } else {
                element.textContent = target;
            }
        };

        updateCounter();
    }

    // ============================================
    // SIMPLE LIGHTBOX (Removed - Gallery section removed)
    // ============================================
    function initLightbox() {
        // Gallery section removed - lightbox no longer needed
        return;
    }

    function openLightbox(imageSrc, imageAlt) {
        // Create lightbox overlay
        const overlay = document.createElement('div');
        overlay.className = 'safa-about-lightbox-overlay';
        overlay.setAttribute('role', 'dialog');
        overlay.setAttribute('aria-label', 'Image lightbox');
        overlay.setAttribute('aria-hidden', 'false');
        overlay.setAttribute('tabindex', '-1');

        // Create lightbox content
        const content = document.createElement('div');
        content.className = 'safa-about-lightbox-content';

        const img = document.createElement('img');
        img.src = imageSrc;
        img.alt = imageAlt || 'Gallery image';
        img.className = 'safa-about-lightbox-image';

        const closeBtn = document.createElement('button');
        closeBtn.className = 'safa-about-lightbox-close';
        closeBtn.setAttribute('aria-label', 'Close lightbox');
        closeBtn.innerHTML = '<i class="bi bi-x-lg"></i>';

        content.appendChild(img);
        content.appendChild(closeBtn);
        overlay.appendChild(content);

        document.body.appendChild(overlay);
        document.body.style.overflow = 'hidden';

        // Focus trap
        overlay.focus();

        // Close handlers
        const closeLightbox = () => {
            overlay.setAttribute('aria-hidden', 'true');
            overlay.style.opacity = '0';
            setTimeout(() => {
                document.body.removeChild(overlay);
                document.body.style.overflow = '';
            }, 300);
        };

        closeBtn.addEventListener('click', closeLightbox);
        overlay.addEventListener('click', function(e) {
            if (e.target === overlay) closeLightbox();
        });

        // Keyboard handlers
        const handleKeydown = (e) => {
            if (e.key === 'Escape') {
                closeLightbox();
                document.removeEventListener('keydown', handleKeydown);
            }
        };
        document.addEventListener('keydown', handleKeydown);

        // Animate in
        setTimeout(() => {
            overlay.style.opacity = '1';
        }, 10);
    }

    // ============================================
    // SCROLL ANIMATIONS (Fallback if AOS not available)
    // ============================================
    function initScrollAnimations() {
        // Only run if AOS is not available
        if (typeof AOS !== 'undefined') return;

        const animatedElements = document.querySelectorAll('[data-safa-animate]');
        if (animatedElements.length === 0) return;

        const observerOptions = {
            root: null,
            rootMargin: '0px',
            threshold: 0.1
        };

        const animationObserver = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const animationType = entry.target.getAttribute('data-safa-animate');
                    entry.target.style.opacity = '1';
                    entry.target.style.transform = 'translateY(0)';
                    animationObserver.unobserve(entry.target);
                }
            });
        }, observerOptions);

        animatedElements.forEach(element => {
            element.style.opacity = '0';
            element.style.transform = 'translateY(30px)';
            element.style.transition = 'opacity 0.8s ease, transform 0.8s ease';
            animationObserver.observe(element);
        });
    }

    // ============================================
    // SWIPE TEXT ANIMATIONS ON SCROLL
    // ============================================
    function initSwipeTextAnimations() {
        const swipeTexts = document.querySelectorAll('.safa-about-swipe-text-inner');
        if (swipeTexts.length === 0) return;

        const observerOptions = {
            root: null,
            rootMargin: '0px',
            threshold: 0.3
        };

        const swipeObserver = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting && !entry.target.classList.contains('swiped')) {
                    entry.target.style.animation = 'safaSwipeIn 1s cubic-bezier(0.16, 1, 0.3, 1) forwards';
                    entry.target.classList.add('swiped');
                }
            });
        }, observerOptions);

        swipeTexts.forEach(text => {
            text.style.transform = 'translateX(-100%)';
            text.style.opacity = '0';
            swipeObserver.observe(text);
        });
    }
})();

