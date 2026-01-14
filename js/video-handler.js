/**
 * Video Handler for Hero Section
 * 
 * Manages the cinematic video background system with dual format support (WebM/MP4),
 * smooth fade-in transitions, and fallback image handling.
 * 
 * @package SafaFormwork
 * @version 1.0
 */

(function() {
    'use strict';
    
    /**
     * Initialize video playback system
     * Starts video decoding during document.readyState = "interactive"
     */
    function initVideoSystem() {
        const heroVideo = document.getElementById('heroVideo');
        const heroShimmer = document.getElementById('heroShimmer');
        const heroPosterImage = document.getElementById('heroPosterImage');
        const heroFallbackImage = document.getElementById('heroFallbackImage');
        
        if (!heroVideo) return;
        
        /**
         * Start video playback immediately
         */
        function startVideoPlayback() {
            if (!heroVideo) return;
            
            // Check if video has error
            if (heroVideo.error) {
                console.error('Video has error:', heroVideo.error);
                return;
            }
            
            // Set video to start decoding (only if not already loading)
            if (heroVideo.networkState === 0 || heroVideo.networkState === 1) {
                heroVideo.load();
            }
            
            // Attempt to play immediately
            const playPromise = heroVideo.play();
            
            if (playPromise !== undefined) {
                playPromise
                    .then(function() {
                        handleVideoPlaying();
                    })
                    .catch(function(error) {
                        // Autoplay was prevented - retry silently
                        setTimeout(function() {
                            heroVideo.play()
                                .then(function() {
                                    handleVideoPlaying();
                                })
                                .catch(function(retryError) {
                                    // Only show fallback after longer delay if still not playing
                                    setTimeout(function() {
                                        if (heroVideo.paused && heroVideo.readyState < 2 && heroVideo.networkState === 3) {
                                            showFallback();
                                        }
                                    }, 3000);
                                });
                        }, 300);
                    });
            } else {
                // Fallback for older browsers
                try {
                    heroVideo.play();
                    handleVideoPlaying();
                } catch (e) {
                    console.error('Direct play failed:', e);
                }
            }
        }
        
        /**
         * Handle video playing event - smooth fade-in
         */
        function handleVideoPlaying() {
            // Fade out poster image
            if (heroPosterImage) {
                heroPosterImage.style.transition = 'opacity 0.5s ease-in-out';
                heroPosterImage.style.opacity = '0';
                setTimeout(function() {
                    if (heroPosterImage) {
                        heroPosterImage.style.display = 'none';
                    }
                }, 500);
            }
            
            // Fade in video smoothly
            heroVideo.classList.add('video-loaded');
            heroVideo.style.transition = 'opacity 0.5s ease-in-out';
            heroVideo.style.opacity = '1';
            
            // Remove shimmer effect
            if (heroShimmer) {
                heroShimmer.style.transition = 'opacity 0.8s ease-out';
                heroShimmer.style.opacity = '0';
                setTimeout(function() {
                    if (heroShimmer) {
                        heroShimmer.style.display = 'none';
                    }
                }, 800);
            }
        }
        
        /**
         * Show fallback image if video fails
         */
        function showFallback() {
            if (heroVideo) {
                heroVideo.style.display = 'none';
            }
            if (heroShimmer) {
                heroShimmer.style.display = 'none';
            }
            if (heroFallbackImage) {
                heroFallbackImage.style.display = 'block';
                heroFallbackImage.style.opacity = '1';
            }
        }
        
        // Listen for video events
        heroVideo.addEventListener('playing', handleVideoPlaying);
        
        // Video can play - start playback
        heroVideo.addEventListener('canplay', function() {
            if (heroVideo.paused) {
                heroVideo.play().catch(function(error) {
                    console.log('Video play error:', error);
                });
            } else {
                handleVideoPlaying();
            }
        });
        
        // Video can start playing (has enough data)
        heroVideo.addEventListener('canplaythrough', function() {
            if (heroVideo.paused) {
                heroVideo.play().catch(function(error) {
                    console.log('Video playthrough error:', error);
                });
            }
        });
        
        // Handle video errors
        heroVideo.addEventListener('error', function(e) {
            // If WebM fails but MP4 exists, try to continue (browser will auto-fallback)
            // Only show fallback if both formats fail
            if (heroVideo.readyState === 0 && heroVideo.networkState === 3) {
                showFallback();
            }
        });
        
        // START VIDEO DECODING DURING readystatechange = "interactive"
        // This is the key optimization - starts before full page load
        document.addEventListener("readystatechange", function() {
            if (document.readyState === "interactive") {
                // Start video loading immediately
                heroVideo.load();
                // Attempt to play as soon as possible
                startVideoPlayback();
            }
        });
        
        // Also try immediately if already interactive
        if (document.readyState === "interactive" || document.readyState === "complete") {
            heroVideo.load();
            startVideoPlayback();
        }
        
        // Retry autoplay if browser blocks it (after a short delay)
        setTimeout(function() {
            if (heroVideo.paused) {
                heroVideo.play().catch(function() {});
            }
        }, 300);
        
        // Register Service Worker for caching
        if ('serviceWorker' in navigator) {
            window.addEventListener('load', function() {
                navigator.serviceWorker.register('/sw.js').then(function(registration) {
                    console.log('✅ Service Worker registered — video caching active');
                }).catch(function(error) {
                    console.log('Service Worker registration failed:', error);
                });
            });
        }
    }
    
    // Initialize when DOM is ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initVideoSystem);
    } else {
        initVideoSystem();
    }
})();



