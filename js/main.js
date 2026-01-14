// Main JavaScript File

// Fix horizontal overflow immediately on page load
function fixHorizontalOverflow() {
    // Force body and html to prevent horizontal scroll
    document.documentElement.style.overflowX = 'hidden';
    document.body.style.overflowX = 'hidden';
    document.documentElement.style.width = '100%';
    document.body.style.width = '100%';
    document.documentElement.style.maxWidth = '100%';
    document.body.style.maxWidth = '100%';
    
    // Force header wrapper to stay within viewport
    const headerWrapper = document.getElementById('headerWrapper');
    if (headerWrapper) {
        headerWrapper.style.width = '100%';
        headerWrapper.style.maxWidth = '100%';
        headerWrapper.style.overflowX = 'hidden';
        headerWrapper.style.overflowY = 'hidden';
    }
    
    // Force header container to stay within viewport
    const headerContainer = document.querySelector('.header-container');
    if (headerContainer) {
        headerContainer.style.maxWidth = '100%';
        headerContainer.style.overflowX = 'hidden';
        headerContainer.style.overflowY = 'hidden';
    }
}

// Run immediately
fixHorizontalOverflow();

// Run on DOM ready
document.addEventListener('DOMContentLoaded', function() {
    fixHorizontalOverflow();
    
    // Run after a short delay to catch any late-rendering elements
    setTimeout(fixHorizontalOverflow, 100);
    setTimeout(fixHorizontalOverflow, 500);
    
    // Run on window load
    window.addEventListener('load', fixHorizontalOverflow);
    
    // Run on resize
    window.addEventListener('resize', fixHorizontalOverflow);
    
    // Fixed Services Navigation - Always pinned at top
    const servicesNav = document.querySelector('.services-nav');
    const servicesHbMenu = document.getElementById('safaHbMenu');
    
    if (servicesNav && servicesHbMenu) {
        // Navigation is now fixed, no need for scroll detection
        // Just ensure it's always visible
        servicesNav.style.position = 'fixed';
        servicesNav.style.top = '70px';
        servicesNav.style.left = '0';
        servicesNav.style.right = '0';
        servicesNav.style.width = '100%';
        
        // Hide services-nav when mobile menu is open
        function toggleServicesNav() {
            const isMenuOpen = servicesHbMenu.getAttribute('aria-hidden') === 'false';
            if (isMenuOpen) {
                servicesNav.classList.add('menu-open-hidden');
            } else {
                servicesNav.classList.remove('menu-open-hidden');
            }
        }
        
        // Watch for menu state changes
        const servicesMenuObserver = new MutationObserver(toggleServicesNav);
        servicesMenuObserver.observe(servicesHbMenu, {
            attributes: true,
            attributeFilter: ['aria-hidden']
        });
        
        // Initial check
        toggleServicesNav();
    }
    
    AOS.init({
        duration: 800,
        easing: 'ease-out-cubic',
        once: true,
        offset: 120,
        delay: 0,
        disable: false,
        startEvent: 'DOMContentLoaded',
        animatedClassName: 'aos-animate',
        useClassNames: false,
        disableMutationObserver: false,
        debounceDelay: 50,
        throttleDelay: 99
    });

    // ========== HEADER SCROLL EFFECT ==========
    const headerWrapper = document.getElementById('headerWrapper');
    let lastScroll = 0;
    let ticking = false;

    function updateHeader() {
        const currentScroll = window.pageYOffset;
        
        if (currentScroll > 100) {
            headerWrapper.classList.add('scrolled');
        } else {
            headerWrapper.classList.remove('scrolled');
        }
        
        lastScroll = currentScroll;
        ticking = false;
    }

    window.addEventListener('scroll', function() {
        if (!ticking) {
            window.requestAnimationFrame(updateHeader);
            ticking = true;
        }
        // Fix overflow on scroll (in case it appears)
        fixHorizontalOverflow();
    }, { passive: true });

    // ========== MOBILE HAMBURGER MENU - NAMESPACED (.safa-hb-*) ==========
    const safaHbToggle = document.getElementById('safaHbToggle');
    const safaHbMenu = document.getElementById('safaHbMenu');
    const safaHbBackdrop = document.getElementById('safaHbBackdrop');
    const safaHbLinks = document.querySelectorAll('.safa-hb-link');
    const body = document.body;

    // Focus trap elements
    let focusableElements = [];
    let firstFocusableElement = null;
    let lastFocusableElement = null;

    function getFocusableElements() {
        if (!safaHbMenu) return [];
        const focusable = safaHbMenu.querySelectorAll(
            'a[href], button:not([disabled]), [tabindex]:not([tabindex="-1"])'
        );
        return Array.from(focusable);
    }

    function trapFocus(e) {
        if (!safaHbMenu || safaHbMenu.getAttribute('aria-hidden') === 'true') return;

        if (e.key === 'Tab') {
            if (focusableElements.length === 0) {
                focusableElements = getFocusableElements();
                firstFocusableElement = focusableElements[0];
                lastFocusableElement = focusableElements[focusableElements.length - 1];
            }

            if (e.shiftKey) {
                if (document.activeElement === firstFocusableElement) {
                    e.preventDefault();
                    lastFocusableElement.focus();
                }
            } else {
                if (document.activeElement === lastFocusableElement) {
                    e.preventDefault();
                    firstFocusableElement.focus();
                }
            }
        }
    }

    function openMobileMenu() {
        if (!safaHbToggle || !safaHbMenu || !safaHbBackdrop) return;
        
        // Only work on mobile (≤768px)
        if (window.innerWidth > 768) return;

        safaHbToggle.setAttribute('aria-expanded', 'true');
        safaHbMenu.setAttribute('aria-hidden', 'false');
        safaHbBackdrop.setAttribute('aria-hidden', 'false');
        body.style.overflow = 'hidden';

        // Get focusable elements for trap
        focusableElements = getFocusableElements();
        if (focusableElements.length > 0) {
            firstFocusableElement = focusableElements[0];
            lastFocusableElement = focusableElements[focusableElements.length - 1];
            // Focus first element immediately (no delay for instant feel)
            if (firstFocusableElement) firstFocusableElement.focus();
        }

        // Add focus trap listener
        document.addEventListener('keydown', trapFocus);
    }

    function closeMobileMenu() {
        if (!safaHbToggle || !safaHbMenu || !safaHbBackdrop) return;
        
        // Only work on mobile (≤768px)
        if (window.innerWidth > 768) return;

        safaHbToggle.setAttribute('aria-expanded', 'false');
        safaHbMenu.setAttribute('aria-hidden', 'true');
        safaHbBackdrop.setAttribute('aria-hidden', 'true');
        body.style.overflow = '';

        // Remove focus trap listener
        document.removeEventListener('keydown', trapFocus);

        // Return focus to toggle button immediately (no delay)
        if (safaHbToggle) safaHbToggle.focus();
    }

    // Toggle menu on button click
    if (safaHbToggle) {
        safaHbToggle.addEventListener('click', function() {
            const isExpanded = safaHbToggle.getAttribute('aria-expanded') === 'true';
            if (isExpanded) {
                closeMobileMenu();
            } else {
                openMobileMenu();
            }
        });
    }

    // Close menu when clicking backdrop
    if (safaHbBackdrop) {
        safaHbBackdrop.addEventListener('click', closeMobileMenu);
    }

    // Close menu when clicking a link
    safaHbLinks.forEach(link => {
        link.addEventListener('click', function() {
            closeMobileMenu();
        });
    });

    // Close menu on escape key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && safaHbMenu && safaHbMenu.getAttribute('aria-hidden') === 'false') {
            closeMobileMenu();
        }
    });

    // ========== ACTIVE LINK HIGHLIGHTING ==========
    // Get current page filename only
    const pathParts = window.location.pathname.split('/');
    let currentPage = pathParts[pathParts.length - 1];
    
    // Handle root/homepage case
    if (currentPage === '' || !currentPage || currentPage === window.location.hostname) {
        currentPage = 'index.php';
    }
    
    const desktopNavLinks = document.querySelectorAll('.desktop-nav-link');
    const mobileNavLinks = document.querySelectorAll('.safa-hb-link');
    const allNavLinks = [...desktopNavLinks, ...mobileNavLinks];
    
    allNavLinks.forEach(link => {
        // Remove active class first - important!
        link.classList.remove('active');
        
        const linkHref = link.getAttribute('href');
        const linkPageName = linkHref.split('/').pop();
        
        // Only highlight if exact match with current page
        if (linkPageName === currentPage) {
            link.classList.add('active');
        }
    });

    // ========== BOOTSTRAP SCROLL TO TOP BUTTON ==========
    const scrollTopBtn = document.getElementById('scrollTopBtn');
    if (scrollTopBtn) {
        let scrollTopTicking = false;
        
        function updateScrollTop() {
            if (window.pageYOffset > 300) {
                scrollTopBtn.classList.add('show');
            } else {
                scrollTopBtn.classList.remove('show');
            }
            scrollTopTicking = false;
        }

        window.addEventListener('scroll', function() {
            if (!scrollTopTicking) {
                window.requestAnimationFrame(updateScrollTop);
                scrollTopTicking = true;
            }
        }, { passive: true });

        scrollTopBtn.addEventListener('click', function() {
            window.scrollTo({
                top: 0,
                behavior: 'smooth'
            });
        });
    }

    // Lazy load images
    if ('IntersectionObserver' in window) {
        const imageObserver = new IntersectionObserver((entries, observer) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const img = entry.target;
                    if (img.dataset.src) {
                        img.src = img.dataset.src;
                        img.removeAttribute('data-src');
                        observer.unobserve(img);
                    }
                }
            });
        });

        document.querySelectorAll('img[data-src]').forEach(img => {
            imageObserver.observe(img);
        });
    }

    // Initialize GLightbox
    if (typeof GLightbox !== 'undefined') {
        const lightbox = GLightbox({
            selector: '.glightbox',
            touchNavigation: true,
            loop: true,
            autoplayVideos: true
        });
    }

    // Animated Counter for Statistics
    const animateCounter = (element) => {
        const target = parseInt(element.getAttribute('data-target'));
        const suffix = element.getAttribute('data-suffix') || '';
        const duration = 2000; // 2 seconds
        const increment = target / (duration / 16); // 60fps
        let current = 0;

        const updateCounter = () => {
            current += increment;
            if (current < target) {
                element.textContent = Math.floor(current) + suffix;
                requestAnimationFrame(updateCounter);
            } else {
                element.textContent = target + suffix;
            }
        };

        updateCounter();
    };

    // Observe stat numbers for animation
    const statNumbers = document.querySelectorAll('.stat-number');
    if (statNumbers.length > 0 && 'IntersectionObserver' in window) {
        const counterObserver = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting && !entry.target.classList.contains('animated')) {
                    entry.target.classList.add('animated');
                    animateCounter(entry.target);
                }
            });
        }, { threshold: 0.5 });

        statNumbers.forEach(stat => {
            counterObserver.observe(stat);
        });
    }

    // ========== FLOATING BOX CURSOR PROMPT (Ctrl or Drag) ==========
    (function initFloatingDndHint(){
        const hint = document.createElement('div');
        hint.id = 'cursorDndHint';
        hint.textContent = 'Drag & Drop Photos Here';
        document.body.appendChild(hint);

        let isVisible = false;
        let targetX = 0, targetY = 0;
        let currentX = 0, currentY = 0;
        let rafId = null;

        function setVisible(v) {
            if (v === isVisible) return;
            isVisible = v;
            hint.style.opacity = v ? '1' : '0';
            hint.style.transform = `translate3d(${currentX}px, ${currentY}px, 0)`;
            hint.style.pointerEvents = 'none';
        }

        function animate() {
            currentX += (targetX - currentX) * 0.18;
            currentY += (targetY - currentY) * 0.18;
            hint.style.transform = `translate3d(${currentX}px, ${currentY}px, 0)`;
            rafId = requestAnimationFrame(animate);
        }

        function updateTarget(e) {
            targetX = e.clientX + 16;
            targetY = e.clientY + 16;
        }

        // Mouse move to follow cursor when visible
        document.addEventListener('mousemove', function(e){
            if (!isVisible) return;
            updateTarget(e);
            if (rafId === null) rafId = requestAnimationFrame(animate);
        }, { passive: true });

        // Ctrl key shows/hides
        document.addEventListener('keydown', function(e){
            if (e.key === 'Control') {
                setVisible(true);
            }
        });
        document.addEventListener('keyup', function(e){
            if (e.key === 'Control') {
                setVisible(false);
            }
        });

        // Drag events
        window.addEventListener('dragenter', function(e){
            setVisible(true);
            if (e.clientX !== 0 || e.clientY !== 0) updateTarget(e);
        });
        window.addEventListener('dragover', function(e){
            setVisible(true);
            updateTarget(e);
            e.preventDefault();
        });
        window.addEventListener('dragleave', function(){
            setVisible(false);
        });
        window.addEventListener('drop', function(){
            setVisible(false);
        });
    })();
});

// Form validation helper
function validateForm(form) {
    let isValid = true;
    const inputs = form.querySelectorAll('input[required], textarea[required], select[required]');

    inputs.forEach(input => {
        const group = input.closest('.form-group');
        if (!input.value.trim()) {
            group.classList.add('error');
            isValid = false;
        } else {
            group.classList.remove('error');
        }

        // Email validation
        if (input.type === 'email' && input.value) {
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailRegex.test(input.value)) {
                group.classList.add('error');
                isValid = false;
            }
        }
    });

    return isValid;
}

// AJAX form submission helper
function submitForm(form, url, successCallback) {
    const formData = new FormData(form);
    const submitBtn = form.querySelector('button[type="submit"]');
    const originalText = submitBtn.textContent;

    submitBtn.disabled = true;
    submitBtn.innerHTML = '<span class="loading"></span> Sending...';

    fetch(url, {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        submitBtn.disabled = false;
        submitBtn.textContent = originalText;

        if (data.success) {
            if (successCallback) successCallback(data);
        } else {
            alert(data.message || 'An error occurred. Please try again.');
        }
    })
    .catch(error => {
        submitBtn.disabled = false;
        submitBtn.textContent = originalText;
        console.error('Error:', error);
        alert('An error occurred. Please try again.');
    });
}

// ========== OPTIMIZED GOOGLE MAPS LAZY LOADING ==========
function initMapLazyLoading() {
    // Function to load map when it's about to be visible
    function loadMap(iframe, placeholder) {
        if (!iframe) return;
        
        if (iframe.dataset.src && !iframe.src) {
            // Add a small delay to ensure smooth transition
            setTimeout(() => {
                iframe.src = iframe.dataset.src;
                
                // Hide placeholder after map loads
                iframe.addEventListener('load', function() {
                    if (placeholder) {
                        placeholder.style.opacity = '0';
                        setTimeout(() => {
                            placeholder.style.display = 'none';
                        }, 300);
                    }
                }, { once: true });
                
                // Fallback: hide placeholder after 5 seconds even if load event doesn't fire
                setTimeout(() => {
                    if (placeholder && placeholder.style.opacity !== '0') {
                        placeholder.style.opacity = '0';
                        setTimeout(() => {
                            placeholder.style.display = 'none';
                        }, 300);
                    }
                }, 5000);
            }, 100);
        }
    }

    // Contact page map
    const contactMapIframe = document.getElementById('contactMapIframe');
    const contactMapPlaceholder = document.getElementById('contactMapPlaceholder');
    const contactMapWrapper = document.querySelector('.safa-contact-map-wrapper');
    
    // Footer map
    const footerMapIframe = document.getElementById('footerMapIframe');
    const footerMapPlaceholder = document.getElementById('footerMapPlaceholder');
    const footerMapContainer = document.querySelector('.location-section .map-container');

    // Use Intersection Observer for better performance
    if ('IntersectionObserver' in window) {
        const mapObserver = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const container = entry.target;
                    let iframe = null;
                    let placeholder = null;
                    
                    if (container === contactMapWrapper || container.classList.contains('safa-contact-map-wrapper')) {
                        iframe = contactMapIframe;
                        placeholder = contactMapPlaceholder;
                    } else if (container === footerMapContainer || container.classList.contains('map-container')) {
                        iframe = footerMapIframe;
                        placeholder = footerMapPlaceholder;
                    }
                    
                    if (iframe) {
                        loadMap(iframe, placeholder);
                        mapObserver.unobserve(container);
                    }
                }
            });
        }, {
            rootMargin: '50px' // Start loading 50px before map enters viewport
        });

        // Observe the wrapper/container instead of the iframe
        if (contactMapWrapper) {
            mapObserver.observe(contactMapWrapper);
        }
        
        if (footerMapContainer) {
            mapObserver.observe(footerMapContainer);
        }
        
        // Also check if maps are already visible on page load
        setTimeout(() => {
            if (contactMapIframe && contactMapWrapper) {
                const rect = contactMapWrapper.getBoundingClientRect();
                const isVisible = rect.top < window.innerHeight + 50 && rect.bottom > -50;
                if (isVisible && !contactMapIframe.src) {
                    loadMap(contactMapIframe, contactMapPlaceholder);
                    mapObserver.unobserve(contactMapWrapper);
                }
            }
            
            if (footerMapIframe && footerMapContainer) {
                const rect = footerMapContainer.getBoundingClientRect();
                const isVisible = rect.top < window.innerHeight + 50 && rect.bottom > -50;
                if (isVisible && !footerMapIframe.src) {
                    loadMap(footerMapIframe, footerMapPlaceholder);
                    mapObserver.unobserve(footerMapContainer);
                }
            }
        }, 500);
    } else {
        // Fallback for older browsers - load after page load
        window.addEventListener('load', () => {
            setTimeout(() => {
                if (contactMapIframe) {
                    loadMap(contactMapIframe, contactMapPlaceholder);
                }
                if (footerMapIframe) {
                    loadMap(footerMapIframe, footerMapPlaceholder);
                }
            }, 1000);
        });
    }
}

// Initialize map lazy loading on DOM ready
document.addEventListener('DOMContentLoaded', initMapLazyLoading);

