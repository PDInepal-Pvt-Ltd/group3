/**
 * Project Detail Page JavaScript
 * 
 * Handles loading and displaying individual project details,
 * including project information, images, and lightbox gallery.
 * 
 * @package SafaFormwork
 * @version 1.0
 */

(function() {
    'use strict';
    
    // Get project ID from URL
    const params = new URLSearchParams(window.location.search);
    const idParam = params.get('id');
    
    // DOM elements
    const headTitle = document.getElementById('headTitle');
    const headStatus = document.getElementById('headStatus');
    const headLoc = document.getElementById('headLoc');
    const projDesc = document.getElementById('projDesc');
    const projGrid = document.getElementById('projGrid');
    const projBg = document.getElementById('projBg');
    const projNoteCard = document.getElementById('projNoteCard');
    
    /**
     * Escape HTML to prevent XSS attacks
     * @param {string} text - Text to escape
     * @returns {string} Escaped HTML
     */
    function esc(text) {
        const d = document.createElement('div');
        d.textContent = text || '';
        return d.innerHTML;
    }
    
    /**
     * Build API URL relative to current page
     * @param {string} path - API path
     * @returns {string} Full URL
     */
    function apiUrl(path) {
        const parts = window.location.pathname.split('/');
        parts.pop(); // drop project.php
        const base = parts.join('/') || '/';
        const fullPath = base.replace(/\/+$/, '') + '/' + path.replace(/^\/+/, '');
        return window.location.origin + fullPath;
    }
    
    /**
     * Set fallback content when project cannot be loaded
     */
    function setHeroFallback() {
        headTitle.textContent = 'Project';
        headStatus.textContent = '';
        headLoc.textContent = '';
        projDesc.textContent = 'This project could not be loaded right now.';
        projBg.style.background = 'linear-gradient(180deg,#f5f5f5,#e9e9e9)';
    }
    
    /**
     * Format text with line breaks
     * @param {string} text - Text to format
     * @returns {string} Formatted HTML
     */
    function formatText(text) {
        return esc(text).replace(/\n/g, '<br>');
    }
    
    /**
     * Load project data from API
     */
    async function loadProject() {
        try {
            const res = await fetch(apiUrl('api/get_projects.php'), { cache: 'no-store' });
            if (!res.ok) throw new Error('HTTP ' + res.status);
            const data = await res.json();
            if (!data || !data.ok || !Array.isArray(data.projects)) throw new Error('Bad JSON');
            
            const pid = parseInt(idParam, 10);
            let proj = data.projects.find(p => parseInt(p.id, 10) === pid);
            if (!proj) proj = data.projects[0];
            if (!proj) {
                setHeroFallback();
                return;
            }
            
            // Update project title
            headTitle.innerHTML = esc(proj.title || 'Project');
            
            // Update status
            const statusMap = {
                current: 'In Progress',
                completed: 'Completed',
                past: 'Past Project'
            };
            const readable = statusMap[proj.status] || (proj.status ? proj.status.toString() : '');
            headStatus.textContent = readable;
            headStatus.classList.remove('chip-success', 'chip-warning', 'chip-secondary');
            if (proj.status === 'completed') {
                headStatus.classList.add('chip-success');
            } else if (proj.status === 'current') {
                headStatus.classList.add('chip-warning');
            } else {
                headStatus.classList.add('chip-secondary');
            }
            
            // Update location
            headLoc.innerHTML = esc(proj.location || '');
            
            // Update description
            if (proj.description && proj.description.trim() !== '') {
                projNoteCard.style.display = 'block';
                projDesc.innerHTML = formatText(proj.description);
            }
            
            // Update background image
            if (proj.cover_image) {
                projBg.style.backgroundImage = "url('" + esc(proj.cover_image) + "')";
            }
            
            // Load images
            const images = Array.isArray(proj.images) ? proj.images : [];
            // Exclude cover in the visible grid, but include it in the lightbox group (hidden trigger)
            const all = images.filter(u => u && u !== proj.cover_image);
            const group = 'p-' + (proj.id || 'x');
            
            if (all.length === 0) {
                projGrid.innerHTML = '<div class="text-center" style="grid-column:1/-1;color:#777;">No images available for this project.</div>';
            } else {
                // Hidden cover link for full set navigation in lightbox
                const hiddenCover = proj.cover_image ? `<a href="${esc(proj.cover_image)}" class="glightbox" data-gallery="${group}" style="display:none" aria-hidden="true"></a>` : '';
                projGrid.innerHTML = hiddenCover + all.map(u => {
                    const url = esc(u);
                    return `<a href="${url}" class="glightbox" data-gallery="${group}" aria-label="Open image"><img src="${url}" alt="${esc(proj.title || 'Project')}" loading="lazy" onerror="this.style.display='none'"></a>`;
                }).join('');
                
                // Initialize GLightbox if available
                if (typeof GLightbox !== 'undefined') {
                    GLightbox({
                        selector: '.glightbox',
                        touchNavigation: true,
                        loop: true
                    });
                }
            }
        } catch (e) {
            console.error('Error loading project:', e);
            setHeroFallback();
        }
    }
    
    // Initialize on page load
    loadProject();
})();



