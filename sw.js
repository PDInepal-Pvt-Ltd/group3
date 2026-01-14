// Service Worker for Safa Formwork - Video Caching System
// Caches video files for instant replay on subsequent visits

const CACHE_NAME = 'safa-video-cache-v1';
const VIDEO_ASSETS = [
  '/assets/Formwork-Bg.webm',
  '/assets/formwork-bg.mp4',
  '/assets/formwork-thumb.jpg'
];

// Install event - Cache video assets immediately
self.addEventListener('install', event => {
  console.log('Service Worker: Installing...');
  event.waitUntil(
    caches.open(CACHE_NAME).then(cache => {
      console.log('Service Worker: Caching video assets');
      return cache.addAll(VIDEO_ASSETS).catch(error => {
        console.log('Service Worker: Cache addAll failed (some files may not exist):', error);
        // Continue even if some files fail to cache
        return Promise.resolve();
      });
    })
  );
  // Force the waiting service worker to become the active service worker
  self.skipWaiting();
});

// Activate event - Clean up old caches
self.addEventListener('activate', event => {
  console.log('Service Worker: Activating...');
  event.waitUntil(
    caches.keys().then(cacheNames => {
      return Promise.all(
        cacheNames.map(cacheName => {
          if (cacheName !== CACHE_NAME) {
            console.log('Service Worker: Deleting old cache:', cacheName);
            return caches.delete(cacheName);
          }
        })
      );
    })
  );
  // Take control of all pages immediately
  return self.clients.claim();
});

// Fetch event - Serve from cache, fallback to network
self.addEventListener('fetch', event => {
  const requestUrl = new URL(event.request.url);
  
  // Only cache video and image assets
  if (requestUrl.pathname.match(/\.(mp4|webm|jpg|jpeg|png|gif)$/i)) {
    event.respondWith(
      caches.match(event.request).then(response => {
        // Return cached version if available
        if (response) {
          console.log('Service Worker: Serving from cache:', requestUrl.pathname);
          return response;
        }
        
        // Otherwise fetch from network and cache it
        return fetch(event.request).then(response => {
          // Don't cache if not a valid response
          if (!response || response.status !== 200 || response.type !== 'basic') {
            return response;
          }
          
          // Clone the response
          const responseToCache = response.clone();
          
          caches.open(CACHE_NAME).then(cache => {
            cache.put(event.request, responseToCache);
            console.log('Service Worker: Cached new video asset:', requestUrl.pathname);
          });
          
          return response;
        }).catch(error => {
          console.log('Service Worker: Fetch failed:', error);
          // Return a fallback or error response
          return new Response('Video not available', { status: 404 });
        });
      })
    );
  }
});

