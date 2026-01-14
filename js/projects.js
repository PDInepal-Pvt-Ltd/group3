/**
 * Projects Page JavaScript
 * 
 * Handles project filtering, search functionality, view toggling (grid/timeline),
 * and smooth AJAX-based filtering without page refresh.
 * 
 * @package SafaFormwork
 * @version 1.0
 */

(function() {
    'use strict';
    
    // Status messages for empty state
    const statusMessages = {
        'all': 'Our project portfolio will be available here soon. Check back to see our completed and ongoing construction projects.',
        'current': 'No current projects at the moment. New projects will appear here once they are added.',
        'completed': 'No completed projects to display yet. Completed projects will be showcased here.',
        'past': 'Past projects will be archived here. Check back soon to view our project history.'
    };
    
    // Status labels for display
    const statusLabels = {
        'current': 'In Progress',
        'completed': 'Completed',
        'past': 'Past Project'
    };
    
    // Global state
    let allProjects = [];
    let currentStatus = 'all';
    let viewMode = 'grid';
    let searchTerm = '';
    
    // DOM elements
    const filterButtons = document.querySelectorAll('.safa-filter-btn');
    const projectsContent = document.getElementById('projectsContent');
    const projectsSection = document.getElementById('projectsSection');
    const searchInput = document.getElementById('projectSearch');
    const viewToggle = document.getElementById('viewToggle');
    
    /**
     * Filter projects by status
     * @param {string} status - The status to filter by ('all', 'current', 'completed', 'past')
     * @returns {Array} Filtered projects array
     */
    function filterProjects(status) {
        if (status === 'all') {
            return allProjects;
        }
        return allProjects.filter(function(project) {
            return project.status === status;
        });
    }
    
    /**
     * Escape HTML to prevent XSS attacks
     * @param {string} text - Text to escape
     * @returns {string} Escaped HTML
     */
    function escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }
    
    /**
     * Check if project matches search term
     * @param {Object} project - Project object
     * @returns {boolean} True if matches search
     */
    function matchesSearch(project) {
        if (!searchTerm) return true;
        const txt = ((project.title || '') + ' ' + (project.location || '') + ' ' + (project.description || '')).toLowerCase();
        return txt.includes(searchTerm);
    }
    
    /**
     * Render projects in grid view
     * @param {Array} projects - Projects array to render
     * @returns {string} HTML string
     */
    function renderProjects(projects) {
        if (projects.length === 0) {
            return `
                <div class="safa-projects-empty safa-projects-empty-fade-in">
                    <div class="safa-projects-empty-icon">
                        <i class="bi bi-folder-plus"></i>
                    </div>
                    <h3 class="safa-projects-empty-title">Projects Coming Soon</h3>
                    <p class="safa-projects-empty-text">${statusMessages[currentStatus] || 'Projects will be available here soon.'}</p>
                </div>
            `;
        }
        
        let delay = 0;
        return `
            <div class="safa-projects-grid safa-projects-grid-fade-in">
                ${projects.map(function(project) {
                    delay += 100;
                    const pid = project.id || Math.random().toString(36).slice(2);
                    const safeTitle = escapeHtml(project.title || 'Project');
                    const bottomBlock = `
                        <div class="proj-v2-bottom">
                            <h3 class="proj-v2-title">${safeTitle}</h3>
                            <div class="proj-v2-location">
                                <i class="bi bi-geo-alt-fill"></i>
                                <span>${escapeHtml(project.location || '')}</span>
                            </div>
                        </div>`;
                    return `
                        <div class="proj-card-v2 safa-project-card-fade-in" style="animation-delay:${delay}ms" data-status="${project.status}">
                            <a href="project?id=${pid}" class="proj-v2-cover" style="background-image:url('${escapeHtml(project.cover_image)}')" aria-label="${safeTitle}">
                                <div class="proj-v2-overlay">
                                    <div class="proj-v2-top">
                                        <span class="proj-v2-badge">${statusLabels[project.status] || (project.status ? project.status.charAt(0).toUpperCase() + project.status.slice(1) : '')}</span>
                                    </div>
                                    ${bottomBlock}
                                </div>
                            </a>
                        </div>
                    `;
                }).join('')}
            </div>
        `;
    }
    
    /**
     * Render projects in timeline view
     * @param {Array} projects - Projects array to render
     * @returns {string} HTML string
     */
    function renderTimeline(projects) {
        if (projects.length === 0) {
            return `
                <div class="safa-projects-empty safa-projects-empty-fade-in">
                    <div class="safa-projects-empty-icon">
                        <i class="bi bi-folder-plus"></i>
                    </div>
                    <h3 class="safa-projects-empty-title">Projects Coming Soon</h3>
                    <p class="safa-projects-empty-text">${statusMessages[currentStatus] || 'Projects will be available here soon.'}</p>
                </div>
            `;
        }
        return `
            <div class="proj-timeline">
                ${projects.map(function(project) {
                    const pid = project.id || Math.random().toString(36).slice(2);
                    const safeTitle = escapeHtml(project.title || 'Project');
                    return `
                        <div class="tl-item">
                            <div class="tl-dot"></div>
                            <div class="tl-card">
                                <img class="tl-thumb" src="${escapeHtml(project.cover_image)}" alt="${safeTitle}" onerror="this.src='assets/placeholder.jpg'">
                                <div>
                                    <div class="tl-title"><a href="project?id=${pid}" style="text-decoration:none;color:#1a1a1a">${safeTitle}</a></div>
                                    <div class="tl-meta">${escapeHtml(project.location || '')} • ${statusLabels[project.status] || project.status}</div>
                                </div>
                            </div>
                        </div>
                    `;
                }).join('')}
            </div>
        `;
    }
    
    /**
     * Render view based on current mode (grid or timeline)
     * @param {Array} list - Projects list to render
     * @returns {string} HTML string
     */
    function renderView(list) {
        const items = list.filter(matchesSearch);
        return viewMode === 'timeline' ? renderTimeline(items) : renderProjects(items);
    }
    
    /**
     * Update filter buttons active state
     * @param {string} activeStatus - The active status
     */
    function updateFilterButtons(activeStatus) {
        filterButtons.forEach(function(btn) {
            if (btn.getAttribute('data-status') === activeStatus) {
                btn.classList.add('active');
            } else {
                btn.classList.remove('active');
            }
        });
    }
    
    /**
     * Switch filter with smooth transition
     * @param {string} newStatus - New status to filter by
     */
    function switchFilter(newStatus) {
        if (newStatus === currentStatus) {
            return; // Already on this filter
        }
        
        // Update current status
        currentStatus = newStatus;
        
        // Update URL without page reload
        const newUrl = newStatus === 'all' 
            ? 'projects.php' 
            : `projects.php?status=${newStatus}`;
        window.history.pushState({ status: newStatus }, '', newUrl);
        
        // Update filter buttons
        updateFilterButtons(newStatus);
        
        // Get filtered projects
        const filteredProjects = filterProjects(newStatus);
        
        // Add fade-out class
        projectsContent.classList.add('safa-projects-content-fade-out');
        
        // After fade-out, update content and fade-in
        setTimeout(function() {
            projectsContent.innerHTML = renderView(filteredProjects);
            projectsContent.classList.remove('safa-projects-content-fade-out');
            projectsContent.classList.add('safa-projects-content-fade-in');
            
            // Remove fade-in class after animation
            setTimeout(function() {
                projectsContent.classList.remove('safa-projects-content-fade-in');
            }, 400);
            
            // Add magnetic hover effect to new cards
            addMagneticEffect();
        }, 300);
    }
    
    /**
     * Add magnetic hover effect to project cards
     */
    function addMagneticEffect() {
        const cards = document.querySelectorAll('.proj-card-v2');
        cards.forEach(function(card) {
            let rafId = 0;
            
            function reset() {
                card.style.transform = 'translate3d(0,0,0)';
            }
            
            card.addEventListener('mousemove', function(e) {
                const rect = card.getBoundingClientRect();
                const x = e.clientX - rect.left;
                const y = e.clientY - rect.top;
                const moveX = ((x / rect.width) - 0.5) * 16;   // px
                const moveY = ((y / rect.height) - 0.5) * 16;  // px
                // pass mouse coordinates for radial flash center
                card.style.setProperty('--mx', x + 'px');
                card.style.setProperty('--my', y + 'px');
                cancelAnimationFrame(rafId);
                rafId = requestAnimationFrame(() => {
                    card.style.transform = 'translate3d(' + moveX + 'px,' + moveY + 'px,0)';
                });
            }, { passive: true });
            
            card.addEventListener('mouseleave', function() {
                reset();
            });
            
            // click/press flash
            card.addEventListener('click', function() {
                card.classList.remove('flash');
                // restart class
                void card.offsetWidth;
                card.classList.add('flash');
                setTimeout(() => card.classList.remove('flash'), 450);
            });
        });
        
        // Ensure anchor click still navigates inside the card
        cards.forEach(function(card) {
            card.addEventListener('click', function(e) {
                const link = card.querySelector('a.proj-v2-cover');
                if (link && e.currentTarget === card) {
                    window.location.href = link.getAttribute('href');
                }
            });
        });
    }
    
    /**
     * Load projects from API
     */
    function loadProjects() {
        fetch('api/get_projects.php', { cache: 'no-store' })
            .then(res => res.json())
            .then(data => {
                if (data && data.ok && Array.isArray(data.projects)) {
                    allProjects = data.projects;
                } else {
                    allProjects = [];
                }
                updateFilterButtons(currentStatus);
                const filtered = filterProjects(currentStatus);
                projectsContent.innerHTML = renderView(filtered);
                addMagneticEffect();
            })
            .catch(() => {
                updateFilterButtons(currentStatus);
                projectsContent.innerHTML = renderView([]);
            });
    }
    
    // Event listeners for filter buttons
    filterButtons.forEach(function(button) {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            const status = this.getAttribute('data-status');
            switchFilter(status);
        });
    });
    
    // Handle browser back/forward buttons
    window.addEventListener('popstate', function(e) {
        const status = e.state ? e.state.status : 'all';
        currentStatus = status;
        updateFilterButtons(status);
        const filteredProjects = filterProjects(status);
        projectsContent.innerHTML = renderView(filteredProjects);
    });
    
    // Search input listener
    if (searchInput) {
        searchInput.addEventListener('input', function() {
            searchTerm = this.value.trim().toLowerCase();
            const filtered = filterProjects(currentStatus);
            projectsContent.innerHTML = renderView(filtered);
            addMagneticEffect();
        });
    }
    
    // View toggle listener
    if (viewToggle) {
        viewToggle.addEventListener('click', function(e) {
            const btn = e.target.closest('.view-btn');
            if (!btn) return;
            viewMode = btn.getAttribute('data-view');
            this.querySelectorAll('.view-btn').forEach(b => b.classList.remove('active'));
            btn.classList.add('active');
            const filtered = filterProjects(currentStatus);
            projectsContent.innerHTML = renderView(filtered);
            addMagneticEffect();
        });
    }
    
    // Initialize on page load
    loadProjects();
})();



