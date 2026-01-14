<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Safa Formwork & Scaffolding - Professional construction services specializing in formwork, concrete, and scaffolding.">
    <title>Home - Safa Formwork & Scaffolding</title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet">
    
    <!-- CSS -->
    <link rel="stylesheet" href="css/global.css">
    <link rel="stylesheet" href="css/footer.css">
    <link rel="stylesheet" href="css/index.css">
    
    <!-- Swiper CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css">
    
    <!-- AOS CSS -->
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    
    <!-- GLightbox CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/glightbox/dist/css/glightbox.min.css">
    
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Performance Optimization - Video Preload (Priority Loading) -->
    <link rel="preload" as="video" href="assets/Formwork-Bg.webm" type="video/webm">
    <link rel="preload" as="video" href="assets/formwork-bg.mp4" type="video/mp4">
    <link rel="preconnect" href="https://fonts.googleapis.com" crossorigin>
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <meta http-equiv="x-dns-prefetch-control" content="on">
</head>
<body>
    <!-- Main Header -->
    <?php include 'includes/header.php'; ?>

<?php
/**
 * Index Page - Home Page Configuration
 * 
 * This file contains all image paths and configuration for the home page.
 * Update the paths below to point to your actual image files in the assets folder.
 * 
 * @package SafaFormwork
 * @version 1.0
 */

// ============================================================================
// HERO SECTION - Cinematic Video Background
// ============================================================================
$heroVideoWebm = 'assets/Formwork-Bg.webm';  // Primary video format (WebM)
$heroVideoMp4 = 'assets/formwork-bg.mp4';      // Fallback video format (MP4)
$heroPoster = 'assets/formwork-thumb.jpg';   // Poster image for instant display
$heroImage = 'assets/hero-image.jpg';         // Fallback static image

// ============================================================================
// SECTION IMAGES
// ============================================================================
$aboutImage = 'assets/about-preview.jpg';
$valueImage = 'assets/value-proposition.jpg';

// ============================================================================
// SERVICE IMAGES
// ============================================================================
$formworkImage = 'assets/service-formwork.jpg';
$steelImage = 'assets/service-steel.jpg';
$concreteImage = 'assets/service-concrete.jpg';
$scaffoldingImage = 'assets/service-scaffolding.jpg';

// ============================================================================
// FEATURE IMAGES - Why Choose Us Section
// ============================================================================
// Upload your photos to: assets/ folder with these exact names:
// - feature-1.jpg (Main large image - recommended: 1200x800px)
// - feature-2.jpg to feature-5.jpg (Small grid images - recommended: 600x400px)
$feature1 = 'assets/feature-1.jpg';
$feature2 = 'assets/feature-2.jpg';
$feature3 = 'assets/feature-3.jpg';
$feature4 = 'assets/feature-4.jpg';
$feature5 = 'assets/feature-5.jpg';
$feature6 = 'assets/feature-6.jpg'; // Optional: reserved for future use

// ============================================================================
// GALLERY IMAGES
// ============================================================================
// Slider/Gallery images - add your images here
$sliderImages = [
    // Uncomment and add your slider images:
    // 'assets/slider-1.jpg',
    // 'assets/slider-2.jpg',
    // 'assets/slider-3.jpg',
];

// Gallery Sliding images for slow slideshow
$gallerySlidingImages = [
    'assets/Gallery Sliding/02.jpg',
    'assets/Gallery Sliding/03.jpg',
    'assets/Gallery Sliding/06.jpg',
    'assets/Gallery Sliding/07.jpg',
    'assets/Gallery Sliding/08.jpg',
    'assets/Gallery Sliding/09.jpg',
    'assets/Gallery Sliding/11.jpg',
    'assets/Gallery Sliding/12.jpg',
    'assets/Gallery Sliding/16.jpg',
    'assets/Gallery Sliding/17.jpg',
    'assets/Gallery Sliding/20.jpg',
    'assets/Gallery Sliding/21.jpg',
    'assets/Gallery Sliding/38.jpg',
    'assets/Gallery Sliding/39.jpg',
    'assets/Gallery Sliding/40.jpg',
    'assets/Gallery Sliding/43.jpg',
    'assets/Gallery Sliding/scafolding.jpg',
];

// Satisfaction photos - add your images here (all in assets folder)
$satisfactionPhotos = [
    // Uncomment and add your satisfaction photos:
    // 'assets/satisfaction-1.jpg',
    // 'assets/satisfaction-2.jpg',
    // 'assets/satisfaction-3.jpg',
    // 'assets/satisfaction-4.jpg',
    // 'assets/satisfaction-5.jpg',
    // 'assets/satisfaction-6.jpg',
];

// ============================================================================
// PROJECTS DATA
// ============================================================================
// Simple projects array - can be manually updated or loaded from database
$currentProjects = [];
?>

<main>
    <!-- Premium Hero Section - Cinematic Video Background -->
    <section class="hero">
        <?php 
        // Cinematic video background with dual format support
        if (!empty($heroVideoWebm) || !empty($heroVideoMp4)): ?>
            <!-- Poster Image - Shows Instantly Before Video -->
            <?php if (!empty($heroPoster)): ?>
            <div class="hero-background hero-poster-image" 
                 id="heroPosterImage" 
                 style="background-image: url('<?php echo htmlspecialchars($heroPoster); ?>'); opacity: 1; z-index: 1; display: block;"></div>
            <?php endif; ?>
            
            <!-- Gold Shimmer Loading Effect -->
            <div class="hero-shimmer" id="heroShimmer">
                <div class="shimmer-overlay"></div>
                <div class="shimmer-glow"></div>
            </div>
            
            <!-- Video Background - Ultra-Smooth Playback -->
            <video class="hero-background hero-video" 
                   id="heroVideo"
                   autoplay 
                   loop
                   muted 
                   playsinline 
                   preload="auto"
                   poster="<?php echo !empty($heroPoster) ? htmlspecialchars($heroPoster) : ''; ?>"
                   style="opacity: 0; z-index: 2;"
                   webkit-playsinline="true"
                   x-webkit-airplay="allow">
                <?php if (!empty($heroVideoWebm)): ?>
                    <source src="<?php echo htmlspecialchars($heroVideoWebm); ?>" type="video/webm">
                <?php endif; ?>
                <?php if (!empty($heroVideoMp4)): ?>
                    <source src="<?php echo htmlspecialchars($heroVideoMp4); ?>" type="video/mp4">
                <?php endif; ?>
                Your browser does not support the video tag.
            </video>
            
            <!-- Fallback image if video fails to load -->
            <div class="hero-background hero-image hero-fallback-image" 
                 id="heroFallbackImage" 
                 style="display: none; background-image: url('<?php echo !empty($heroImage) ? $heroImage : 'assets/hero-image.jpg'; ?>'); z-index: 1;"></div>
        <?php elseif (!empty($heroImage)): ?>
            <div class="hero-background hero-image" style="background-image: url('<?php echo $heroImage; ?>');"></div>
        <?php elseif (!empty($sliderImages)): ?>
            <div class="hero-background hero-image" style="background-image: url('<?php echo $sliderImages[0]; ?>');"></div>
        <?php else: ?>
            <div class="hero-background" style="background: linear-gradient(135deg, #1E1E1E 0%, #2C2C2C 100%);"></div>
        <?php endif; ?>
        
        <div class="hero-overlay"></div>
        <div class="hero-background-effects"></div>
        <div class="hero-content">
            <h1 class="hero-title">Building Excellence in Formwork & Scaffolding</h1>
            <p class="hero-subtitle">Precision, Strength, and Reliability in Every Structure</p>
            <div class="hero-buttons">
                <a href="projects" class="btn btn-primary">View Projects</a>
                <a href="contact" class="btn btn-secondary">Get in Touch</a>
            </div>
        </div>
        
    </section>
    
    <?php if (!empty($heroVideoWebm) || !empty($heroVideoMp4)): ?>
    <!-- Video Handler Script -->
    <script src="js/video-handler.js"></script>
    <?php endif; ?>

    <!-- About Preview -->
    <section class="about-preview section">
        <div class="container">
            <div class="about-preview-content">
                <div class="about-preview-text" data-aos="fade-right">
                    <span class="section-badge">Our Story</span>
                    <h2>Family Owned Formwork Excellence</h2><p></p>
                    <p class="lead">SAFA Formwork is a family-owned and run business, specializing in professional formwork, reinforcement, and concrete solutions across NSW and Sydney metropolitan area.</p>
                    <p>We value our employees and ensure we offer a safe and positive working environment. Our experienced and friendly site and office teams work proactively in meeting your needs. We have undertaken a diverse range of projects in Commercial, Residential, Civil, Aged Care, and Educational fields.</p>
                    <p>As a family-oriented business, we wish to build long-term relationships through showcasing our demonstrated capability. We provide competitive pricing for formwork, reinforcement, and concrete packages while ensuring a high-quality standard of work.</p>
                    <a href="about" class="btn btn-primary">Learn More About Us</a>
                </div>
                <div class="about-preview-image" data-aos="fade-left">
                    <img src="<?php echo $aboutImage; ?>" alt="SAFA Formwork Team" loading="lazy" onerror="this.style.display='none'; this.nextElementSibling.style.display='block';">
                    <div class="empty-image-placeholder" style="display: none;">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                            <rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect>
                            <circle cx="8.5" cy="8.5" r="1.5"></circle>
                            <polyline points="21 15 16 10 5 21"></polyline>
                        </svg>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Services Overview -->
    <section class="services-overview section">
        <div class="container">
            <div class="section-title text-center mb-5" data-aos="fade-up">
                <span class="section-badge">What We Do</span>
                <h2 class="mt-3 mb-3">Our Services</h2>
                <p class="lead mx-auto" style="max-width: 700px; font-weight: 700;">Comprehensive construction solutions tailored to your project needs</p>
            </div>
            <div class="row g-4 g-lg-5 justify-content-center">
                <div class="col-12 col-sm-6 col-lg-3 mb-4 mb-lg-0" data-aos="fade-up" data-aos-delay="100">
                    <div class="service-card h-100 d-flex flex-column shadow-sm">
                        <div class="service-icon position-relative overflow-hidden">
                            <img src="<?php echo $formworkImage; ?>" alt="Formwork Service" class="w-100 h-100" style="object-fit: cover;" loading="lazy" onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                            <div class="empty-image-placeholder position-absolute top-0 start-0 w-100 h-100 d-none align-items-center justify-content-center" style="background: #f5f5f5;">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" style="width: 80px; height: 80px; opacity: 0.3;">
                                    <rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect>
                                    <circle cx="8.5" cy="8.5" r="1.5"></circle>
                                    <polyline points="21 15 16 10 5 21"></polyline>
                                </svg>
                            </div>
                        </div>
                        <div class="service-card-content d-flex flex-column flex-grow-1">
                            <h3 class="fw-bold">Formwork</h3>
                            <p class="text-muted flex-grow-1 mb-4">Professional formwork solutions ensuring concrete precision and structural integrity for your projects. We provide custom design services, precision installation with expert craftsmanship, and comprehensive quality assurance to meet the highest industry standards.</p>
                            <a href="services#formwork" class="btn btn-primary btn-service mt-auto">Learn More</a>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-sm-6 col-lg-3 mb-4 mb-lg-0" data-aos="fade-up" data-aos-delay="200">
                    <div class="service-card h-100 d-flex flex-column shadow-sm">
                        <div class="service-icon position-relative overflow-hidden">
                            <img src="<?php echo $steelImage; ?>" alt="Steel Fixing Service" class="w-100 h-100" style="object-fit: cover;" loading="lazy" onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                            <div class="empty-image-placeholder position-absolute top-0 start-0 w-100 h-100 d-none align-items-center justify-content-center" style="background: #f5f5f5;">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" style="width: 80px; height: 80px; opacity: 0.3;">
                                    <rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect>
                                    <circle cx="8.5" cy="8.5" r="1.5"></circle>
                                    <polyline points="21 15 16 10 5 21"></polyline>
                                </svg>
                            </div>
                        </div>
                        <div class="service-card-content p-4 d-flex flex-column flex-grow-1">
                            <h3 class="mb-3 fw-bold">Steel Fixing</h3>
                            <p class="text-muted mb-4 flex-grow-1">Expert steel reinforcement services with focus on safety, quality, and adherence to international standards. Our team specializes in rebar fabrication, expert placement techniques, and ensuring complete safety compliance on every construction site.</p>
                            <a href="services#steel-fixing" class="btn btn-primary btn-service mt-auto">Learn More</a>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-sm-6 col-lg-3 mb-4 mb-lg-0" data-aos="fade-up" data-aos-delay="300">
                    <div class="service-card h-100 d-flex flex-column shadow-sm">
                        <div class="service-icon position-relative overflow-hidden">
                            <img src="<?php echo $concreteImage; ?>" alt="Concrete Service" class="w-100 h-100" style="object-fit: cover;" loading="lazy" onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                            <div class="empty-image-placeholder position-absolute top-0 start-0 w-100 h-100 d-none align-items-center justify-content-center" style="background: #f5f5f5;">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" style="width: 80px; height: 80px; opacity: 0.3;">
                                    <rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect>
                                    <circle cx="8.5" cy="8.5" r="1.5"></circle>
                                    <polyline points="21 15 16 10 5 21"></polyline>
                                </svg>
                            </div>
                        </div>
                        <div class="service-card-content p-4 d-flex flex-column flex-grow-1">
                            <h3 class="mb-3 fw-bold">Concrete</h3>
                            <p class="text-muted mb-4 flex-grow-1">High-quality concrete solutions emphasizing durability, finish quality, and long-term performance. We deliver expert concrete pouring services, professional surface finishing, and rigorous quality control processes to ensure exceptional results.</p>
                            <a href="services#concrete" class="btn btn-primary btn-service mt-auto">Learn More</a>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-sm-6 col-lg-3 mb-4 mb-lg-0" data-aos="fade-up" data-aos-delay="400">
                    <div class="service-card h-100 d-flex flex-column shadow-sm">
                        <div class="service-icon position-relative overflow-hidden">
                            <img src="<?php echo $scaffoldingImage; ?>" alt="Scaffolding Service" class="w-100 h-100" style="object-fit: cover;" loading="lazy" onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                            <div class="empty-image-placeholder position-absolute top-0 start-0 w-100 h-100 d-none align-items-center justify-content-center" style="background: #f5f5f5;">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" style="width: 80px; height: 80px; opacity: 0.3;">
                                    <rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect>
                                    <circle cx="8.5" cy="8.5" r="1.5"></circle>
                                    <polyline points="21 15 16 10 5 21"></polyline>
                                </svg>
                            </div>
                        </div>
                        <div class="service-card-content p-4 d-flex flex-column flex-grow-1">
                            <h3 class="mb-3 fw-bold">Scaffolding</h3>
                            <p class="text-muted mb-4 flex-grow-1">Safe and reliable scaffolding systems designed for optimal access and worker safety on every project. We provide safe installation services, ensure proper worker access, and conduct regular maintenance to maintain the highest safety standards.</p>
                            <a href="services#scaffolding" class="btn btn-primary btn-service mt-auto">Learn More</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Current Projects Preview -->
    <?php if (!empty($currentProjects)): ?>
    <section class="projects-preview section">
        <div class="container">
            <div class="section-title" data-aos="fade-up">
                <span class="section-badge">Our Work</span>
                <h2>Current Projects</h2>
                <p>See what we're building right now</p>
            </div>
            <div class="projects-grid">
                <?php foreach ($currentProjects as $index => $project): ?>
                <div class="project-card" data-aos="fade-up" data-aos-delay="<?php echo $index * 100; ?>">
                    <div class="project-image-wrapper">
                        <?php 
                        $coverImage = $project['cover_image'] ?? 'assets/placeholder.jpg';
                        ?>
                        <img src="<?php echo htmlspecialchars($coverImage); ?>" 
                             alt="<?php echo htmlspecialchars($project['title'] ?? 'Project'); ?>" 
                             class="project-card-image" 
                             loading="lazy"
                             style="object-fit: cover; width: 100%; height: 100%;"
                             onerror="this.src='assets/placeholder.jpg';">
                        <div class="project-overlay">
                            <a href="projects?status=current" class="view-project-btn">View Details</a>
                        </div>
                    </div>
                    <div class="project-card-content">
                        <h3><?php echo htmlspecialchars($project['title'] ?? 'Project'); ?></h3>
                        <p><?php echo htmlspecialchars(substr($project['description'] ?? '', 0, 100)) . '...'; ?></p>
                        <div class="location">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path>
                                <circle cx="12" cy="10" r="3"></circle>
                            </svg>
                            <?php echo htmlspecialchars($project['location'] ?? 'Location'); ?>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            <div class="text-center" style="margin-top: 50px;" data-aos="fade-up">
                <a href="projects?status=current" class="btn btn-primary">View All Projects</a>
            </div>
        </div>
    </section>
    <?php endif; ?>


    <!-- Why Choose Us Section - Professional Content Rich -->
    <section class="why-choose-us section">
        <div class="container">
            <div class="section-title" data-aos="fade-up">
                <span class="section-badge">Why Choose Us</span>
                <h2>Excellence That Speaks for Itself</h2>
                <p>What sets us apart in the construction industry</p>
            </div>
            
            <!-- Main Content Grid -->
            <div class="why-choose-main-grid">
                <!-- Left Side - Image Gallery -->
                <!-- 
                    IMAGE UPLOAD INSTRUCTIONS:
                    1. Upload your photos to: assets/ folder
                    2. Name them exactly as: feature-1.jpg, feature-2.jpg, feature-3.jpg, feature-4.jpg, feature-5.jpg
                    3. Recommended sizes:
                       - feature-1.jpg: 1200x800px (Main large image)
                       - feature-2.jpg to feature-5.jpg: 600x400px (Small grid images)
                    4. Supported formats: JPG, JPEG, PNG
                -->
                <div class="why-choose-image-wrapper">
                    <div class="why-choose-image-container" data-aos="slide-right" data-aos-delay="100">
                        <img src="<?php echo $feature1; ?>" alt="SAFA Formwork Excellence - 20+ Years Experience" loading="lazy" onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                        <div class="empty-image-placeholder" style="display: none;">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                                <rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect>
                                <circle cx="8.5" cy="8.5" r="1.5"></circle>
                                <polyline points="21 15 16 10 5 21"></polyline>
                            </svg>
                            <p style="margin-top: 15px; color: #999; font-size: 0.9rem;">Upload feature-1.jpg to assets/ folder</p>
                        </div>
                    </div>
                    
                    <!-- Secondary Images Grid -->
                    <div class="why-choose-images-grid">
                        <div class="why-choose-small-image" data-aos="slide-up" data-aos-delay="100">
                            <img src="<?php echo $feature2; ?>" alt="Quality Construction - SAFA Formwork" loading="lazy" onerror="this.style.display='none'; this.parentElement.innerHTML='<div style=\'padding:20px;text-align:center;color:#999;font-size:0.8rem;\'>Upload feature-2.jpg</div>';">
                        </div>
                        <div class="why-choose-small-image" data-aos="slide-up" data-aos-delay="150">
                            <img src="<?php echo $feature3; ?>" alt="Expert Team - SAFA Formwork" loading="lazy" onerror="this.style.display='none'; this.parentElement.innerHTML='<div style=\'padding:20px;text-align:center;color:#999;font-size:0.8rem;\'>Upload feature-3.jpg</div>';">
                        </div>
                        <div class="why-choose-small-image" data-aos="slide-up" data-aos-delay="200">
                            <img src="<?php echo $feature4; ?>" alt="Safety Standards - SAFA Formwork" loading="lazy" onerror="this.style.display='none'; this.parentElement.innerHTML='<div style=\'padding:20px;text-align:center;color:#999;font-size:0.8rem;\'>Upload feature-4.jpg</div>';">
                        </div>
                        <div class="why-choose-small-image" data-aos="slide-up" data-aos-delay="250">
                            <img src="<?php echo $feature5; ?>" alt="Premium Materials - SAFA Formwork" loading="lazy" onerror="this.style.display='none'; this.parentElement.innerHTML='<div style=\'padding:20px;text-align:center;color:#999;font-size:0.8rem;\'>Upload feature-5.jpg</div>';">
                        </div>
                    </div>
                </div>
                
                <!-- Right Side - Content Blocks -->
                <div class="why-choose-content-wrapper">
                    <!-- Experience Block -->
                    <div class="why-choose-content-block" data-aos="slide-left" data-aos-delay="100">
                        <div class="content-block-header">
                            <div class="content-block-icon">🏆</div>
                            <h3>20+ Years of Proven Excellence</h3>
                        </div>
                        <p class="content-block-text">SAFA Formwork brings over two decades of proven expertise in construction excellence across NSW and Sydney. Our extensive experience spans Commercial, Residential, Civil, Aged Care, and Educational projects. We have successfully completed hundreds of projects including OSD tanks, capping beams, site access roads, and structural formwork, demonstrating our commitment to precision, quality, and timely delivery.</p>
                    </div>
                    
                    <!-- Quality Block -->
                    <div class="why-choose-content-block" data-aos="slide-left" data-aos-delay="200">
                        <div class="content-block-header">
                            <div class="content-block-icon">✅</div>
                            <h3>Uncompromising Quality Standards</h3>
                        </div>
                        <p class="content-block-text">We stand behind every project with our commitment to uncompromising quality. Our experienced team ensures all formwork, reinforcement, and concrete solutions meet the highest industry standards. We implement rigorous quality control at every stage, use only premium materials, and maintain strict compliance with Australian building codes. This commitment has earned us a reputation for delivering durable, reliable construction solutions.</p>
                    </div>
                    
                    <!-- Service Excellence Block -->
                    <div class="why-choose-content-block" data-aos="slide-left" data-aos-delay="250">
                        <div class="content-block-header">
                            <div class="content-block-icon">🎯</div>
                            <h3>Comprehensive Service Portfolio</h3>
                        </div>
                        <p class="content-block-text">SAFA Formwork offers a complete range of construction services. Our expertise includes professional formwork for OSD tanks, capping beams, and site access roads. We provide expert steel fixing, high-quality concrete services, and safe scaffolding systems. This comprehensive portfolio means you can rely on a single trusted partner for all your formwork, reinforcement, and concrete requirements.</p>
                    </div>
                    
                    <!-- Benefits List -->
                    <div class="why-choose-benefits-block" data-aos="slide-left" data-aos-delay="300">
                        <h4 class="benefits-title">Why SAFA Formwork Stands Out</h4>
                        <ul class="why-choose-benefits-list">
                            <li data-aos="fade-right" data-aos-delay="350">
                                <span class="benefit-check benefit-check-1">
                                    <i class="bi bi-check-circle-fill"></i>
                                </span>
                                <span class="benefit-text">Family-owned business with personal commitment and dedicated service</span>
                            </li>
                            <li data-aos="fade-right" data-aos-delay="400">
                                <span class="benefit-check benefit-check-2">
                                    <i class="bi bi-shield-check"></i>
                                </span>
                                <span class="benefit-text">Safe and positive working environment with rigorous safety protocols</span>
                            </li>
                            <li data-aos="fade-right" data-aos-delay="450">
                                <span class="benefit-check benefit-check-3">
                                    <i class="bi bi-people-fill"></i>
                                </span>
                                <span class="benefit-text">Experienced and friendly site and office teams with decades of expertise</span>
                            </li>
                            <li data-aos="fade-right" data-aos-delay="500">
                                <span class="benefit-check benefit-check-4">
                                    <i class="bi bi-award-fill"></i>
                                </span>
                                <span class="benefit-text">Competitive pricing without compromising on quality standards</span>
                            </li>
                            <li data-aos="fade-right" data-aos-delay="550">
                                <span class="benefit-check benefit-check-5">
                                    <i class="bi bi-handshake-fill"></i>
                                </span>
                                <span class="benefit-text">Long-term relationship building through demonstrated capability</span>
                            </li>
                            <li data-aos="fade-right" data-aos-delay="600">
                                <span class="benefit-check benefit-check-6">
                                    <i class="bi bi-lightning-charge-fill"></i>
                                </span>
                                <span class="benefit-text">Proactive approach to meeting client needs and deadlines</span>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
            
            <!-- Gallery Sliding Title Section -->
            <?php if (!empty($gallerySlidingImages)): ?>
            <div class="gallery-sliding-title-section" data-aos="fade-up" data-aos-delay="50">
                <div class="gallery-sliding-title-wrapper">
                    <span class="gallery-sliding-badge">SAFA Excellence</span>
                    <h2 class="gallery-sliding-title"><span>Building Excellence Across NSW</span></h2>
                    <p class="gallery-sliding-subtitle">20+ years of proven expertise in formwork, scaffolding, and concrete solutions. From Commercial to Residential, Civil to Educational projects - SAFA Formwork delivers precision, quality, and reliability that stands the test of time.</p>
                </div>
            </div>
            
            <!-- Gallery Sliding Slideshow -->
            <div class="gallery-sliding-slider-wrapper" data-aos="fade-up" data-aos-delay="100">
                <div class="swiper gallery-sliding-swiper">
                    <div class="swiper-wrapper">
                        <?php foreach ($gallerySlidingImages as $img): ?>
                        <div class="swiper-slide">
                            <img src="<?php echo $img; ?>" alt="SAFA Formwork Gallery" loading="eager" onload="this.classList.add('loaded');" onerror="this.style.display='none';">
                        </div>
                        <?php endforeach; ?>
                    </div>
                    <div class="swiper-pagination"></div>
                    <div class="swiper-button-next"></div>
                    <div class="swiper-button-prev"></div>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </section>

    <!-- Value Proposition Section -->
    <section class="value-proposition section">
        <div class="container">
            <div class="value-content">
                <div class="value-text" data-aos="fade-up">
                    <div class="value-text-header">
                        <span class="section-badge">Our Value</span>
                        <h2>Delivering Value That Matters</h2>
                        <p class="lead">At Safa Formwork & Scaffolding, we don't just build structures we build trust, reliability, and lasting partnerships.</p>
                    </div>
                    <div class="value-points">
                        <div class="value-point" data-aos="fade-right" data-aos-delay="100">
                            <div class="value-icon value-icon-1">
                                <i class="bi bi-currency-dollar"></i>
                            </div>
                            <div class="value-point-content">
                                <h4>Cost-Effective Solutions</h4>
                                <p>Optimized processes and efficient resource management ensure maximum value for your investment</p>
                            </div>
                        </div>
                        <div class="value-point" data-aos="fade-right" data-aos-delay="200">
                            <div class="value-icon value-icon-2">
                                <i class="bi bi-bullseye"></i>
                            </div>
                            <div class="value-point-content">
                                <h4>Precision Engineering</h4>
                                <p>Every detail matters. Our meticulous approach ensures flawless execution from start to finish</p>
                            </div>
                        </div>
                        <div class="value-point" data-aos="fade-right" data-aos-delay="300">
                            <div class="value-icon value-icon-3">
                                <i class="bi bi-rocket-takeoff"></i>
                            </div>
                            <div class="value-point-content">
                                <h4>Innovation & Technology</h4>
                                <p>Staying ahead with the latest construction techniques and modern equipment</p>
                            </div>
                        </div>
                        <div class="value-point" data-aos="fade-right" data-aos-delay="400">
                            <div class="value-icon value-icon-4">
                                <i class="bi bi-clock"></i>
                            </div>
                            <div class="value-point-content">
                                <h4>24/7 Support</h4>
                                <p>Round-the-clock assistance ensuring your project never faces unnecessary delays</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="value-image" data-aos="fade-up" data-aos-delay="200">
                    <img src="<?php echo $valueImage; ?>" alt="Construction Excellence" loading="lazy" onerror="this.style.display='none'; this.nextElementSibling.style.display='block';">
                    <div class="empty-image-placeholder" style="display: none;">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                            <rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect>
                            <circle cx="8.5" cy="8.5" r="1.5"></circle>
                            <polyline points="21 15 16 10 5 21"></polyline>
                        </svg>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Our Journey Section - Cinematic Storytelling -->
    <section class="journey-section">
        <!-- Journey Header -->
        <div class="journey-header" data-aos="fade-up">
            <div class="container">
                <span class="journey-badge">Our Journey</span>
                <h2 class="journey-title">Building Excellence Through Time</h2>
                <p class="journey-subtitle">Two decades of precision, dedication, and unwavering commitment to construction excellence across NSW</p>
            </div>
        </div>

        <!-- Journey Block 1 - Formwork Excellence -->
        <div class="journey-block journey-block-1" data-aos="fade-in" data-aos-duration="1200">
            <div class="journey-background" style="background-image: url('assets/service-formwork.jpg');"></div>
            <div class="journey-overlay"></div>
            <div class="journey-content">
                <div class="container">
                    <div class="journey-text-wrapper">
                        <span class="journey-number">01</span>
                        <h3 class="journey-headline">Precision Built on Experience</h3>
                        <p class="journey-description">With over 20 years of expertise, we've mastered the art of formwork construction. Every project reflects our commitment to precision, quality, and structural integrity.</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Journey Block 2 - Steel & Strength -->
        <div class="journey-block journey-block-2" data-aos="fade-in" data-aos-duration="1200">
            <div class="journey-background" style="background-image: url('assets/service-steel.jpg');"></div>
            <div class="journey-overlay"></div>
            <div class="journey-content">
                <div class="container">
                    <div class="journey-text-wrapper">
                        <span class="journey-number">02</span>
                        <h3 class="journey-headline">Engineering with Integrity</h3>
                        <p class="journey-description">Our steel fixing expertise ensures every reinforcement meets the highest standards. We build structures that stand strong, safe, and sustainable for generations.</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Journey Block 3 - Scaffolding & Safety -->
        <div class="journey-block journey-block-3" data-aos="fade-in" data-aos-duration="1200">
            <div class="journey-background" style="background-image: url('assets/service-scaffolding.jpg');"></div>
            <div class="journey-overlay"></div>
            <div class="journey-content">
                <div class="container">
                    <div class="journey-text-wrapper">
                        <span class="journey-number">03</span>
                        <h3 class="journey-headline">Safety First, Always</h3>
                        <p class="journey-description">Rigorous safety protocols and modern scaffolding systems define our approach. We create secure work environments where excellence thrives and teams flourish.</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Journey Block 4 - Concrete Excellence -->
        <div class="journey-block journey-block-4" data-aos="fade-in" data-aos-duration="1200">
            <div class="journey-background" style="background-image: url('assets/service-concrete.jpg');"></div>
            <div class="journey-overlay"></div>
            <div class="journey-content">
                <div class="container">
                    <div class="journey-text-wrapper">
                        <span class="journey-number">04</span>
                        <h3 class="journey-headline">Foundations of Trust</h3>
                        <p class="journey-description">From concrete pouring to surface finishing, we deliver solutions that exceed expectations. Every pour, every finish, every detail crafted with care and expertise.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

</main>

    <!-- Footer -->
    <?php include 'footer.php'; ?>

    <!-- Swiper JS -->
    <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
    
    <!-- AOS JS -->
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    
    <!-- GLightbox JS -->
    <script src="https://cdn.jsdelivr.net/npm/glightbox/dist/js/glightbox.min.js"></script>
    
    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Main JS -->
    <script src="js/main.js"></script>
    <script src="js/slider.js"></script>
</body>
</html>
