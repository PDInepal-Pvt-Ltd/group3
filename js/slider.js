// Slider JavaScript

document.addEventListener('DOMContentLoaded', function() {
    // Initialize Swiper for gallery slider
    const gallerySwiper = document.querySelector('.gallery-swiper');
    if (gallerySwiper) {
        new Swiper('.gallery-swiper', {
            slidesPerView: 1,
            spaceBetween: 30,
            loop: true,
            autoplay: {
                delay: 5000,
                disableOnInteraction: false,
            },
            pagination: {
                el: '.swiper-pagination',
                clickable: true,
            },
            navigation: {
                nextEl: '.swiper-button-next',
                prevEl: '.swiper-button-prev',
            },
            breakpoints: {
                768: {
                    slidesPerView: 2,
                },
                1024: {
                    slidesPerView: 3,
                }
            }
        });
    }

    // Initialize Swiper for certificates carousel
    const certSwiper = document.querySelector('.certificates-swiper');
    if (certSwiper) {
        new Swiper('.certificates-swiper', {
            slidesPerView: 2,
            spaceBetween: 30,
            loop: true,
            autoplay: {
                delay: 3000,
                disableOnInteraction: false,
            },
            breakpoints: {
                768: {
                    slidesPerView: 3,
                },
                1024: {
                    slidesPerView: 4,
                }
            }
        });
    }

    // Initialize Swiper for Gallery Sliding - Optimized Fast Swiping
    const gallerySlidingSwiper = document.querySelector('.gallery-sliding-swiper');
    if (gallerySlidingSwiper) {
        // Preload all images to prevent black screen
        const slides = gallerySlidingSwiper.querySelectorAll('.swiper-slide img');
        let loadedCount = 0;
        const totalImages = slides.length;
        
        slides.forEach(function(img) {
            if (img.complete && img.naturalHeight !== 0) {
                // Image already loaded
                img.classList.add('loaded');
                loadedCount++;
            } else {
                // Wait for image to load
                img.addEventListener('load', function() {
                    this.classList.add('loaded');
                    loadedCount++;
                }, { once: true });
                
                // Preload image
                const preloadImg = new Image();
                preloadImg.onload = function() {
                    img.classList.add('loaded');
                };
                preloadImg.src = img.src;
            }
        });

        new Swiper('.gallery-sliding-swiper', {
            slidesPerView: 1,
            spaceBetween: 0,
            loop: true,
            autoplay: {
                delay: 5000,
                disableOnInteraction: false,
                pauseOnMouseEnter: true,
            },
            speed: 800, // Faster transition (0.8 seconds instead of 2)
            effect: 'fade',
            fadeEffect: {
                crossFade: true
            },
            // Faster swipe sensitivity
            touchRatio: 1.5,
            touchAngle: 45,
            simulateTouch: true,
            allowTouchMove: true,
            // Prevent black screen during transitions
            preloadImages: true,
            updateOnImagesReady: true,
            watchSlidesProgress: true,
            watchSlidesVisibility: true,
            pagination: {
                el: '.gallery-sliding-swiper .swiper-pagination',
                clickable: true,
                dynamicBullets: true,
                dynamicMainBullets: 3,
            },
            navigation: {
                nextEl: '.gallery-sliding-swiper .swiper-button-next',
                prevEl: '.gallery-sliding-swiper .swiper-button-prev',
            },
            grabCursor: true,
            keyboard: {
                enabled: true,
            },
            on: {
                init: function() {
                    // Smooth initialization
                    this.el.style.opacity = '1';
                    // Mark active slide image as loaded immediately
                    const activeSlide = this.slides[this.activeIndex];
                    if (activeSlide) {
                        const activeImg = activeSlide.querySelector('img');
                        if (activeImg) {
                            activeImg.classList.add('loaded');
                        }
                    }
                    // Ensure images are loaded
                    this.update();
                },
                slideChangeTransitionStart: function() {
                    // Preload and mark next/prev images as loaded
                    const nextIndex = (this.activeIndex + 1) % this.slides.length;
                    const prevIndex = (this.activeIndex - 1 + this.slides.length) % this.slides.length;
                    
                    // Preload next slide
                    const nextSlide = this.slides[nextIndex];
                    if (nextSlide) {
                        const nextImg = nextSlide.querySelector('img');
                        if (nextImg) {
                            if (!nextImg.complete) {
                                const preload = new Image();
                                preload.onload = function() {
                                    nextImg.classList.add('loaded');
                                };
                                preload.src = nextImg.src;
                            } else {
                                nextImg.classList.add('loaded');
                            }
                        }
                    }
                    
                    // Preload prev slide
                    const prevSlide = this.slides[prevIndex];
                    if (prevSlide) {
                        const prevImg = prevSlide.querySelector('img');
                        if (prevImg) {
                            if (!prevImg.complete) {
                                const preload = new Image();
                                preload.onload = function() {
                                    prevImg.classList.add('loaded');
                                };
                                preload.src = prevImg.src;
                            } else {
                                prevImg.classList.add('loaded');
                            }
                        }
                    }
                },
                slideChangeTransitionEnd: function() {
                    // Ensure active slide image is visible
                    const activeSlide = this.slides[this.activeIndex];
                    if (activeSlide) {
                        const activeImg = activeSlide.querySelector('img');
                        if (activeImg) {
                            activeImg.classList.add('loaded');
                        }
                    }
                }
            }
        });
    }
});

