<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="View our current, completed, and past construction projects.">
    <title>Projects - Safa Formwork & Scaffolding</title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet">
    
    <!-- CSS -->
    <link rel="stylesheet" href="css/global.css">
    <link rel="stylesheet" href="css/footer.css">
    <link rel="stylesheet" href="css/projects.css">
    <link rel="stylesheet" href="css/projects-inline.css">
    
    <!-- AOS CSS -->
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    
    <!-- GLightbox CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/glightbox/dist/css/glightbox.min.css">
    
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <!-- Main Header -->
    <?php include 'includes/header.php'; ?>

<?php
/**
 * Projects Page - Portfolio Display
 * 
 * Displays all projects with filtering by status (all, current, completed, past).
 * Projects are loaded dynamically via AJAX from the API.
 * 
 * @package SafaFormwork
 * @version 1.0
 */

// Get filter status from URL
$status = isset($_GET['status']) ? $_GET['status'] : 'all';
if (!in_array($status, ['all', 'current', 'completed', 'past'])) {
    $status = 'all';
}

// Projects Data - Empty array, will be populated from admin dashboard later
// Projects are loaded dynamically via JavaScript from API
$projects = [];

// Filter projects based on status (for initial server-side render)
$filteredProjects = [];
if ($status === 'all') {
    $filteredProjects = $projects;
} else {
    $filteredProjects = array_filter($projects, function($project) use ($status) {
        return $project['status'] === $status;
    });
}

// Hero background image
$heroImage = 'assets/service-formwork.jpg';
?>

<main>
    <!-- Hero Section - Cinematic Intro -->
    <section class="safa-projects-hero">
        <div class="safa-projects-hero-image" style="background-image: url('<?php echo $heroImage; ?>');"></div>
        <div class="safa-projects-hero-overlay"></div>
        <div class="safa-projects-hero-background-effects"></div>
        <div class="safa-projects-hero-content">
            <div class="safa-projects-hero-badge">Our Portfolio</div>
            <h1 class="safa-projects-hero-title">Excellence in Construction</h1>
            <p class="safa-projects-hero-subtitle">Discover our portfolio of successful projects across Commercial, Residential, Civil, Educational, and Aged Care sectors</p>
        </div>
    </section>

    <!-- Projects Filter Section -->
    <section class="safa-projects-filter" id="projectsFilter">
        <div class="container">
            <div class="safa-projects-filter-wrapper">
                <button type="button"
                   class="safa-filter-btn <?php echo $status === 'all' ? 'active' : ''; ?>" 
                   data-status="all"
                   data-aos="fade-up" 
                   data-aos-delay="0">
                    <span class="safa-filter-icon"><i class="bi bi-grid-3x3-gap"></i></span>
                    <span class="safa-filter-text">All Projects</span>
                </button>
                <button type="button"
                   class="safa-filter-btn <?php echo $status === 'current' ? 'active' : ''; ?>" 
                   data-status="current"
                   data-aos="fade-up" 
                   data-aos-delay="100">
                    <span class="safa-filter-icon"><i class="bi bi-play-circle"></i></span>
                    <span class="safa-filter-text">Current</span>
                </button>
                <button type="button"
                   class="safa-filter-btn <?php echo $status === 'completed' ? 'active' : ''; ?>" 
                   data-status="completed"
                   data-aos="fade-up" 
                   data-aos-delay="200">
                    <span class="safa-filter-icon"><i class="bi bi-check-circle"></i></span>
                    <span class="safa-filter-text">Completed</span>
                </button>
                <button type="button"
                   class="safa-filter-btn <?php echo $status === 'past' ? 'active' : ''; ?>" 
                   data-status="past"
                   data-aos="fade-up" 
                   data-aos-delay="300">
                    <span class="safa-filter-icon"><i class="bi bi-archive"></i></span>
                    <span class="safa-filter-text">Past Projects</span>
                </button>
            </div>
        </div>
    </section>

    <!-- Projects Grid Section -->
    <section class="safa-projects-section" id="projectsSection">
        <div class="container">
            <div class="proj-toolbar">
                <div class="proj-search">
                    <input type="search" id="projectSearch" placeholder="Search projects by title or location...">
                </div>
                <div class="view-toggle" id="viewToggle" role="group" aria-label="Switch view">
                    <button class="view-btn active" data-view="grid">Images</button>
                    <button class="view-btn" data-view="timeline">Timeline</button>
                </div>
            </div>
            <div id="projectsContent">
                <?php if (!empty($filteredProjects)): ?>
                    <div class="safa-projects-grid">
                        <?php 
                        $delay = 0;
                        foreach ($filteredProjects as $index => $project): 
                            $delay += 100;
                        ?>
                            <div class="safa-project-card" 
                                 data-aos="fade-up" 
                                 data-aos-delay="<?php echo $delay; ?>"
                                 data-status="<?php echo $project['status']; ?>">
                                <div class="safa-project-card-image-wrapper">
                                    <img src="<?php echo htmlspecialchars($project['cover_image']); ?>" 
                                         alt="<?php echo htmlspecialchars($project['title']); ?>" 
                                         class="safa-project-card-image" 
                                         loading="lazy"
                                         onerror="this.src='assets/placeholder.jpg';">
                                    <div class="safa-project-card-overlay">
                                        <div class="safa-project-card-badge">
                                            <?php echo htmlspecialchars($project['category']); ?>
                                        </div>
                                    </div>
                                    <div class="safa-project-card-border"></div>
                                </div>
                                <div class="safa-project-card-content">
                                    <h3 class="safa-project-card-title"><?php echo htmlspecialchars($project['title']); ?></h3>
                                    <p class="safa-project-card-description"><?php echo htmlspecialchars($project['description']); ?></p>
                                    <div class="safa-project-card-meta">
                                        <div class="safa-project-location">
                                            <i class="bi bi-geo-alt-fill"></i>
                                            <span><?php echo htmlspecialchars($project['location']); ?></span>
                                        </div>
                                        <div class="safa-project-status safa-project-status-<?php echo $project['status']; ?>">
                                            <?php 
                                            $statusLabels = [
                                                'current' => 'In Progress',
                                                'completed' => 'Completed',
                                                'past' => 'Past Project'
                                            ];
                                            echo $statusLabels[$project['status']] ?? ucfirst($project['status']);
                                            ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <div class="safa-projects-empty" data-aos="fade-up">
                        <div class="safa-projects-empty-icon">
                            <i class="bi bi-folder-plus"></i>
                        </div>
                        <h3 class="safa-projects-empty-title">Projects Coming Soon</h3>
                        <p class="safa-projects-empty-text" id="emptyStateMessage">
                            <?php 
                            $statusMessages = [
                                'all' => 'Our project portfolio will be available here soon. Check back to see our completed and ongoing construction projects.',
                                'current' => 'No current projects at the moment. New projects will appear here once they are added.',
                                'completed' => 'No completed projects to display yet. Completed projects will be showcased here.',
                                'past' => 'Past projects will be archived here. Check back soon to view our project history.'
                            ];
                            echo $statusMessages[$status] ?? 'Projects will be available here soon.';
                            ?>
                        </p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </section>
</main>

    <!-- Footer -->
    <?php include 'footer.php'; ?>

    <!-- AOS JS -->
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    
    <!-- GLightbox JS -->
    <script src="https://cdn.jsdelivr.net/npm/glightbox/dist/js/glightbox.min.js"></script>
    
    <!-- Bootstrap 5 JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Main JS -->
    <script src="js/main.js"></script>
    
    <!-- Initialize AOS -->
    <script>
        AOS.init({
            duration: 800,
            easing: 'ease-in-out',
            once: true,
            offset: 100
        });
    </script>
    
    <!-- Projects Page JavaScript -->
    <script src="js/projects.js"></script>
</body>
</html>
