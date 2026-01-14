<?php
/**
 * About Page - Company Information
 * 
 * Displays comprehensive information about Safa Formwork including company history,
 * team members, featured projects, and company values.
 * 
 * @package SafaFormwork
 * @version 1.0
 */
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Safa Formwork - A family-owned construction company delivering precision formwork, steel fixing, concrete, and scaffolding services across NSW and Sydney with 20+ years of excellence.">
    <title>About Us - Safa Formwork & Scaffolding</title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet">
    
    <!-- CSS -->
    <link rel="stylesheet" href="css/global.css">
    <link rel="stylesheet" href="css/footer.css">
    <link rel="stylesheet" href="css/about.css">
    
    <!-- AOS CSS -->
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <!-- Main Header -->
    <?php include 'includes/header.php'; ?>

<main>
    <!-- Hero Section - Cinematic Intro -->
    <section class="safa-about-hero">
        <div class="safa-about-hero-image" style="background-image: url('assets/about-main.jpg');"></div>
        <div class="safa-about-hero-overlay"></div>
        <div class="safa-about-hero-background-effects"></div>
        <div class="safa-about-hero-content">
            <div class="safa-about-hero-badge">About Safa Formwork</div>
            <h1 class="safa-about-hero-title">Precision in Every Pour</h1>
            <p class="safa-about-hero-subtitle">A family-owned construction company delivering excellence in formwork, steel fixing, concrete, and scaffolding across NSW and Sydney</p>
            <div class="safa-about-hero-buttons">
                <a href="projects" class="safa-about-btn safa-about-btn-primary">View Projects</a>
                <a href="contact" class="safa-about-btn safa-about-btn-secondary">Contact Us</a>
            </div>
        </div>
       
    </section>

    <!-- Intro Paragraph -->
    <section class="safa-about-intro">
        <div class="container">
            <p class="safa-about-intro-text" data-safa-animate="fade-up" data-aos="fade-up">Safa Formwork is a family owned and run construction business specializing in professional formwork, steel fixing, concrete, and scaffolding services across NSW and the Sydney metropolitan area. With over 20 years of experience, we deliver precision engineering and uncompromising quality on every project, from residential developments to major civil infrastructure.</p>
        </div>
    </section>

    <!-- Our Story - Two Column -->
    <section class="safa-about-story">
        <div class="container">
            <div class="safa-about-story-grid">
                <div class="safa-about-story-content" data-safa-animate="fade-right" data-aos="fade-right">
                    <h2 class="safa-about-section-title">Our Story</h2>
                    <p>Safa Formwork was founded with a vision to deliver exceptional construction services that combine traditional craftsmanship with modern engineering precision. As a family-owned business, we bring personal commitment and care to every project, ensuring that each client receives the same level of attention and quality that we would expect for our own projects.</p>
                    <p>Our expertise spans formwork, steel fixing, concrete placement, and scaffolding services, serving clients across Commercial, Residential, Civil, Aged Care, and Educational sectors. We have successfully completed diverse projects including OSD tanks, capping beams, site access roads, educational facilities, and complex structural formwork throughout NSW and Sydney.</p>
                    <p>Safety and quality are at the core of everything we do. We value our employees and ensure a safe, positive working environment while maintaining the highest standards of workmanship. Our experienced and friendly site and office teams work proactively to meet your needs, with your productivity as our priority.</p>
                    <p>We build long-term relationships through demonstrated capability, competitive pricing, and a commitment to excellence. Safa Formwork welcomes the opportunity to provide our services through tendering for projects requiring formwork, reinforcement, and concrete packages, ensuring high-quality standards on every delivery.</p>
                </div>
                <div class="safa-about-story-images" data-safa-animate="fade-left" data-aos="fade-left">
                    <div class="safa-about-image-card safa-about-image-card-1" data-aos="fade-right" data-aos-delay="100" data-aos-duration="800">
                        <img src="assets/card.jpg" alt="Safa Formwork Company Vehicle" loading="lazy" onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                        <div class="safa-about-image-placeholder" style="display: none;">
                            <i class="bi bi-image"></i>
                            <p>Upload card.jpg</p>
                        </div>
                    </div>
                    <div class="safa-about-image-card safa-about-image-card-2" data-aos="fade-right" data-aos-delay="200" data-aos-duration="800">
                        <img src="assets/site-1.jpg" alt="Safa Formwork Construction Site" loading="lazy" onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                        <div class="safa-about-image-placeholder" style="display: none;">
                            <i class="bi bi-image"></i>
                            <p>Upload site-1.jpg</p>
                        </div>
                    </div>
                    <div class="safa-about-image-card safa-about-image-card-3" data-aos="fade-right" data-aos-delay="300" data-aos-duration="800">
                        <img src="assets/site-2.jpg" alt="Safa Formwork Project Site" loading="lazy" onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                        <div class="safa-about-image-placeholder" style="display: none;">
                            <i class="bi bi-image"></i>
                            <p>Upload site-2.jpg</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Cinematic Photo Showcase - Field Operations -->
    <section class="safa-about-showcase">
        <div class="safa-about-showcase-container">
            <!-- Large Hero Image - Field -->
            <div class="safa-about-showcase-hero" data-aos="fade-up" data-aos-duration="1000">
                <div class="safa-about-showcase-image-wrapper" data-aos="fade-right" data-aos-delay="100" data-aos-duration="1000">
                    <img src="assets/field.jpg" alt="Safa Formwork Construction Field" class="safa-about-showcase-image" loading="lazy" onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                    <div class="safa-about-image-placeholder" style="display: none;">
                        <i class="bi bi-image"></i>
                        <p>Upload field.jpg</p>
                    </div>
                    <div class="safa-about-showcase-overlay"></div>
                </div>
                <div class="safa-about-showcase-content" data-aos="fade-left" data-aos-delay="200" data-aos-duration="1000">
                    <div class="safa-about-swipe-text">
                        <span class="safa-about-swipe-text-inner">On-Site Excellence</span>
                    </div>
                    <h2 class="safa-about-showcase-title">Where Precision Meets Performance</h2>
                    <p class="safa-about-showcase-desc">Every project begins with meticulous planning and ends with flawless execution. Our field operations represent the heart of Safa Formwork where skilled professionals transform blueprints into reality.</p>
                </div>
            </div>

            <!-- Two Column Layout - Car & Content -->
            <div class="safa-about-showcase-grid">
                <div class="safa-about-showcase-item safa-about-showcase-item-image" data-aos="fade-right" data-aos-delay="100" data-aos-duration="1000">
                    <div class="safa-about-showcase-item-wrapper">
                        <img src="assets/car-parking.jpg" alt="Safa Formwork Company Vehicle" class="safa-about-showcase-item-img" loading="lazy" onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                        <div class="safa-about-image-placeholder" style="display: none;">
                            <i class="bi bi-image"></i>
                            <p>Upload car-parking.jpg</p>
                        </div>
                        <div class="safa-about-showcase-item-overlay"></div>
                        <div class="safa-about-showcase-item-content">
                            <div class="safa-about-swipe-text safa-about-swipe-text-small">
                                <span class="safa-about-swipe-text-inner">Our Fleet</span>
                            </div>
                            <h3 class="safa-about-showcase-item-title">Ready for Every Project</h3>
                        </div>
                    </div>
                </div>
                <div class="safa-about-showcase-item safa-about-showcase-item-text" data-aos="fade-left" data-aos-delay="200" data-aos-duration="1000">
                    <div class="safa-about-showcase-text-content">
                        <div class="safa-about-swipe-text safa-about-swipe-text-small">
                            <span class="safa-about-swipe-text-inner">Commitment</span>
                        </div>
                        <h3 class="safa-about-showcase-text-title">Always On The Move</h3>
                        <p class="safa-about-showcase-text-desc">Our fleet of vehicles ensures we're always ready to respond to your project needs. From site inspections to material delivery, we maintain a professional presence across NSW and Sydney, bringing expertise and equipment where they're needed most.</p>
                        <div class="safa-about-showcase-features">
                            <div class="safa-about-showcase-feature" data-aos="fade-left" data-aos-delay="300" data-aos-duration="600">
                                <i class="bi bi-check-circle-fill"></i>
                                <span>Fully Equipped Vehicles</span>
                            </div>
                            <div class="safa-about-showcase-feature" data-aos="fade-left" data-aos-delay="350" data-aos-duration="600">
                                <i class="bi bi-check-circle-fill"></i>
                                <span>Rapid Response Time</span>
                            </div>
                            <div class="safa-about-showcase-feature" data-aos="fade-left" data-aos-delay="400" data-aos-duration="600">
                                <i class="bi bi-check-circle-fill"></i>
                                <span>Professional Standards</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Concrete Truck Feature -->
            <div class="safa-about-showcase-featured" data-aos="fade-up" data-aos-duration="1000">
                <div class="safa-about-showcase-featured-wrapper">
                    <div class="safa-about-showcase-featured-image" data-aos="fade-right" data-aos-delay="100" data-aos-duration="1000">
                        <img src="assets/concrete-truck.jpg" alt="Safa Formwork Concrete Truck" class="safa-about-showcase-featured-img" loading="lazy" onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                        <div class="safa-about-image-placeholder" style="display: none;">
                            <i class="bi bi-image"></i>
                            <p>Upload concrete-truck.jpg</p>
                        </div>
                        <div class="safa-about-showcase-featured-overlay"></div>
                    </div>
                    <div class="safa-about-showcase-featured-content" data-aos="fade-left" data-aos-delay="200" data-aos-duration="1000">
                        <div class="safa-about-swipe-text">
                            <span class="safa-about-swipe-text-inner">Concrete Excellence</span>
                        </div>
                        <h2 class="safa-about-showcase-featured-title">Delivering Quality, Pour by Pour</h2>
                        <p class="safa-about-showcase-featured-desc">Our concrete operations represent the culmination of years of expertise. Every pour is carefully planned, precisely executed, and meticulously finished. From residential foundations to major civil infrastructure, we deliver concrete solutions that stand the test of time.</p>
                        <div class="safa-about-showcase-featured-stats">
                            <div class="safa-about-showcase-stat" data-aos="fade-up" data-aos-delay="300" data-aos-duration="600">
                                <div class="safa-about-showcase-stat-number">1480+</div>
                                <div class="safa-about-showcase-stat-label">Projects</div>
                            </div>
                            <div class="safa-about-showcase-stat" data-aos="fade-up" data-aos-delay="350" data-aos-duration="600">
                                <div class="safa-about-showcase-stat-number">20+</div>
                                <div class="safa-about-showcase-stat-label">Years</div>
                            </div>
                            <div class="safa-about-showcase-stat" data-aos="fade-up" data-aos-delay="400" data-aos-duration="600">
                                <div class="safa-about-showcase-stat-number">100%</div>
                                <div class="safa-about-showcase-stat-label">Quality</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Creative Projects Showcase - Cinematic Design -->
    <section class="safa-about-projects-creative">
        <div class="safa-about-projects-creative-bg"></div>
        <div class="container">
            <div class="safa-about-projects-creative-header" data-safa-animate="fade-up" data-aos="fade-up">
                <div class="safa-about-swipe-text">
                    <span class="safa-about-swipe-text-inner">Our Legacy</span>
                </div>
                <h2 class="safa-about-section-title safa-about-section-title-center">Projects That Define Excellence</h2>
                <p class="safa-about-section-subtitle">Each project tells a story of precision, dedication, and uncompromising quality</p>
            </div>
            
            <!-- Main Featured Project - Large -->
            <div class="safa-about-project-featured" data-safa-animate="fade-up" data-aos="fade-up" data-aos-delay="100">
                <div class="safa-about-project-featured-image">
                    <div class="safa-about-project-featured-bg"></div>
                    <div class="safa-about-project-featured-overlay"></div>
                    <div class="safa-about-project-featured-content">
                        <div class="safa-about-project-featured-badge">
                            <i class="bi bi-star-fill"></i>
                            <span>Featured Project</span>
                        </div>
                        <h3 class="safa-about-project-featured-title">Oran Park Project</h3>
                        <p class="safa-about-project-featured-type">Complex Multi-Level Structural Construction</p>
                        <p class="safa-about-project-featured-desc">A testament to our technical expertise, this project involved intricate multi-level concrete construction including slab on ground with strategic 1.2 metre pour breaks, suspended slabs supported by columns and walls, integrated structural elements with three sets of stairs, and a connecting bridge to the adjoining building.</p>
                        <div class="safa-about-project-featured-highlights">
                            <div class="safa-about-project-highlight">
                                <i class="bi bi-check-circle"></i>
                                <span>Strategic Pour Breaks</span>
                            </div>
                            <div class="safa-about-project-highlight">
                                <i class="bi bi-check-circle"></i>
                                <span>Suspended Slabs</span>
                            </div>
                            <div class="safa-about-project-highlight">
                                <i class="bi bi-check-circle"></i>
                                <span>Bridge Integration</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Cinematic Photo Gallery with Explanations -->
            <div class="safa-about-photo-gallery">
                <!-- Photo Item 1 - Trinity College -->
                <div class="safa-about-photo-item" data-aos="fade-up" data-aos-delay="100" data-aos-duration="800">
                    <div class="safa-about-photo-wrapper">
                        <img src="assets/trinity-college-regents-park-stage-2-phase-2-5.jpg" alt="Trinity College Regents Park Stage 2 Phase 2" class="safa-about-photo-img" loading="lazy" onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                        <div class="safa-about-image-placeholder" style="display: none;">
                            <i class="bi bi-image"></i>
                            <p>Upload trinity-college-regents-park-stage-2-phase-2-5.jpg</p>
                        </div>
                        <div class="safa-about-photo-overlay"></div>
                    </div>
                    <div class="safa-about-photo-content">
                        <div class="safa-about-swipe-text safa-about-swipe-text-small">
                            <span class="safa-about-swipe-text-inner">Trinity College</span>
                        </div>
                        <h3 class="safa-about-photo-title">Regents Park Stage 2 Phase 2</h3>
                        <p class="safa-about-photo-desc">Multi-phase educational facility construction showcasing our expertise in complex formwork and concrete solutions. This project demonstrates our ability to handle large-scale institutional developments with precision and attention to detail.</p>
                    </div>
                </div>

                <!-- Photo Item 2 - Capping Beam -->
                <div class="safa-about-photo-item" data-aos="fade-up" data-aos-delay="200" data-aos-duration="800">
                    <div class="safa-about-photo-wrapper">
                        <img src="assets/Capping Beam.jpg" alt="Capping Beam Construction" class="safa-about-photo-img" loading="lazy" onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                        <div class="safa-about-image-placeholder" style="display: none;">
                            <i class="bi bi-image"></i>
                            <p>Upload Capping Beam.jpg</p>
                        </div>
                        <div class="safa-about-photo-overlay"></div>
                    </div>
                    <div class="safa-about-photo-content">
                        <div class="safa-about-swipe-text safa-about-swipe-text-small">
                            <span class="safa-about-swipe-text-inner">Structural Engineering</span>
                        </div>
                        <h3 class="safa-about-photo-title">Precision Capping Beams</h3>
                        <p class="safa-about-photo-desc">Expert formwork and concrete placement for capping beams requiring exact dimensional accuracy and structural integrity. Our precision engineering ensures every beam meets the highest standards for load-bearing capacity and durability.</p>
                    </div>
                </div>

                <!-- Photo Item 3 - Crane Bases -->
                <div class="safa-about-photo-item" data-aos="fade-up" data-aos-delay="300" data-aos-duration="800">
                    <div class="safa-about-photo-wrapper">
                        <img src="assets/Crane Bases.jpg" alt="Crane Bases Construction" class="safa-about-photo-img" loading="lazy" onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                        <div class="safa-about-image-placeholder" style="display: none;">
                            <i class="bi bi-image"></i>
                            <p>Upload Crane Bases.jpg</p>
                        </div>
                        <div class="safa-about-photo-overlay"></div>
                    </div>
                    <div class="safa-about-photo-content">
                        <div class="safa-about-swipe-text safa-about-swipe-text-small">
                            <span class="safa-about-swipe-text-inner">Heavy Construction</span>
                        </div>
                        <h3 class="safa-about-photo-title">Crane Base Foundations</h3>
                        <p class="safa-about-photo-desc">Specialized concrete foundations for crane bases requiring exceptional strength and stability. Our expertise in heavy-duty formwork and reinforced concrete ensures safe and reliable crane operations throughout the construction phase.</p>
                    </div>
                </div>

                <!-- Photo Item 4 - OSD Tanks -->
                <div class="safa-about-photo-item" data-aos="fade-up" data-aos-delay="400" data-aos-duration="800">
                    <div class="safa-about-photo-wrapper">
                        <img src="assets/OSD Tanks.jpg" alt="OSD Tanks Construction" class="safa-about-photo-img" loading="lazy" onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                        <div class="safa-about-image-placeholder" style="display: none;">
                            <i class="bi bi-image"></i>
                            <p>Upload OSD Tanks.jpg</p>
                        </div>
                        <div class="safa-about-photo-overlay"></div>
                    </div>
                    <div class="safa-about-photo-content">
                        <div class="safa-about-swipe-text safa-about-swipe-text-small">
                            <span class="safa-about-swipe-text-inner">Civil Infrastructure</span>
                        </div>
                        <h3 class="safa-about-photo-title">OSD Tank Construction</h3>
                        <p class="safa-about-photo-desc">On-Site Detention (OSD) tank construction requiring watertight formwork and precise concrete placement. Our specialized expertise in civil infrastructure ensures reliable stormwater management systems that meet environmental compliance standards.</p>
                    </div>
                </div>

                <!-- Photo Item 5 - Kerb Gutter Footpath -->
                <div class="safa-about-photo-item" data-aos="fade-up" data-aos-delay="500" data-aos-duration="800">
                    <div class="safa-about-photo-wrapper">
                        <img src="assets/kerb.jpg" alt="Kerb Gutter Footpath Construction" class="safa-about-photo-img" loading="lazy" onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                        <div class="safa-about-image-placeholder" style="display: none;">
                            <i class="bi bi-image"></i>
                            <p>Upload kerb.jpg</p>
                        </div>
                        <div class="safa-about-photo-overlay"></div>
                    </div>
                    <div class="safa-about-photo-content">
                        <div class="safa-about-swipe-text safa-about-swipe-text-small">
                            <span class="safa-about-swipe-text-inner">Site Works</span>
                        </div>
                        <h3 class="safa-about-photo-title">Kerb, Gutter & Footpath</h3>
                        <p class="safa-about-photo-desc">Comprehensive site works including kerb, gutter, and footpath construction with precision formwork and quality concrete finishes. Our attention to detail ensures functional and aesthetically pleasing site infrastructure that enhances project completion.</p>
                    </div>
                </div>

                <!-- Photo Item 6 - Stormwater Culverts -->
                <div class="safa-about-photo-item" data-aos="fade-up" data-aos-delay="600" data-aos-duration="800">
                    <div class="safa-about-photo-wrapper">
                        <img src="assets/Stormwater Culverts.jpg" alt="Stormwater Culverts Construction" class="safa-about-photo-img" loading="lazy" onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                        <div class="safa-about-image-placeholder" style="display: none;">
                            <i class="bi bi-image"></i>
                            <p>Upload Stormwater Culverts.jpg</p>
                        </div>
                        <div class="safa-about-photo-overlay"></div>
                    </div>
                    <div class="safa-about-photo-content">
                        <div class="safa-about-swipe-text safa-about-swipe-text-small">
                            <span class="safa-about-swipe-text-inner">Drainage Systems</span>
                        </div>
                        <h3 class="safa-about-photo-title">Stormwater Culverts</h3>
                        <p class="safa-about-photo-desc">Specialized formwork and concrete construction for stormwater culverts ensuring proper water flow and structural durability. Our expertise in drainage infrastructure delivers reliable solutions that manage stormwater effectively while maintaining long-term performance.</p>
                    </div>
                </div>
            </div>
        </div>
        <div class="safa-about-projects-creative-particles">
            <div class="safa-about-particle"></div>
            <div class="safa-about-particle"></div>
            <div class="safa-about-particle"></div>
            <div class="safa-about-particle"></div>
            <div class="safa-about-particle"></div>
        </div>
    </section>

    <!-- Team Snapshot -->
    <section class="safa-about-team">
        <div class="container">
            <h2 class="safa-about-section-title safa-about-section-title-center" data-safa-animate="fade-up" data-aos="fade-up">Our Team</h2>
            <p class="safa-about-section-subtitle" data-safa-animate="fade-up" data-aos="fade-up" data-aos-delay="100">Experienced professionals dedicated to excellence</p>
            <div class="safa-about-team-grid">
                <div class="safa-about-team-card" data-safa-animate="fade-up" data-aos="fade-up" data-aos-delay="100">
                    <div class="safa-about-team-image">
                        <img src="assets/team-1.jpg" alt="Site Manager" loading="lazy" onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                        <div class="safa-about-image-placeholder" style="display: none;">
                            <i class="bi bi-person"></i>
                        </div>
                    </div>
                    <h4 class="safa-about-team-name">Site Management</h4>
                    <p class="safa-about-team-role">Project Coordination</p>
                    <p class="safa-about-team-skill">Expert in complex formwork and concrete placement</p>
                </div>
                <div class="safa-about-team-card" data-safa-animate="fade-up" data-aos="fade-up" data-aos-delay="200">
                    <div class="safa-about-team-image">
                        <img src="assets/team-2.jpg" alt="Steel Fixing Specialist" loading="lazy" onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                        <div class="safa-about-image-placeholder" style="display: none;">
                            <i class="bi bi-person"></i>
                        </div>
                    </div>
                    <h4 class="safa-about-team-name">Steel Fixing Team</h4>
                    <p class="safa-about-team-role">Reinforcement Specialists</p>
                    <p class="safa-about-team-skill">Precision rebar placement and structural integrity</p>
                </div>
                <div class="safa-about-team-card" data-safa-animate="fade-up" data-aos="fade-up" data-aos-delay="300">
                    <div class="safa-about-team-image">
                        <img src="assets/team-3.jpg" alt="Concrete Specialist" loading="lazy" onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                        <div class="safa-about-image-placeholder" style="display: none;">
                            <i class="bi bi-person"></i>
                        </div>
                    </div>
                    <h4 class="safa-about-team-name">Concrete Team</h4>
                    <p class="safa-about-team-role">Placement & Finishing</p>
                    <p class="safa-about-team-skill">Expert concrete pouring and surface finishing</p>
                </div>
                <div class="safa-about-team-card" data-safa-animate="fade-up" data-aos="fade-up" data-aos-delay="400">
                    <div class="safa-about-team-image">
                        <img src="assets/team-4.jpg" alt="Scaffolding Specialist" loading="lazy" onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                        <div class="safa-about-image-placeholder" style="display: none;">
                            <i class="bi bi-person"></i>
                        </div>
                    </div>
                    <h4 class="safa-about-team-name">Scaffolding Team</h4>
                    <p class="safa-about-team-role">Access Systems</p>
                    <p class="safa-about-team-skill">Safe and reliable scaffolding installation</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Main Person Section - Side Animation -->
    <section class="safa-about-person">
        <div class="container">
            <div class="safa-about-person-grid">
                <div class="safa-about-person-content" data-safa-animate="fade-right" data-aos="fade-right" data-aos-duration="1000">
                    <div class="safa-about-person-badge" data-aos="fade-down" data-aos-delay="100" data-aos-duration="600">Leadership</div>
                    <h2 class="safa-about-section-title" data-aos="fade-up" data-aos-delay="200" data-aos-duration="800">Meet Our Founder</h2>
                    <p class="safa-about-person-intro" data-aos="fade-up" data-aos-delay="300" data-aos-duration="800">At the heart of Safa Formwork is a commitment to excellence that comes from years of hands-on experience and a deep understanding of the construction industry.</p>
                    <div class="safa-about-person-details">
                        <div class="safa-about-person-detail-item" data-aos="fade-left" data-aos-delay="400" data-aos-duration="600">
                            <i class="bi bi-award-fill"></i>
                            <div>
                                <h4>20+ Years Experience</h4>
                                <p>Extensive expertise in formwork, concrete, and construction management</p>
                            </div>
                        </div>
                        <div class="safa-about-person-detail-item" data-aos="fade-left" data-aos-delay="500" data-aos-duration="600">
                            <i class="bi bi-building-fill"></i>
                            <div>
                                <h4>Family Values</h4>
                                <p>Building relationships and delivering quality with personal commitment</p>
                            </div>
                        </div>
                        <div class="safa-about-person-detail-item" data-aos="fade-left" data-aos-delay="600" data-aos-duration="600">
                            <i class="bi bi-shield-check-fill"></i>
                            <div>
                                <h4>Safety First</h4>
                                <p>Uncompromising dedication to safety standards and team wellbeing</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="safa-about-person-image-wrapper" data-safa-animate="fade-left" data-aos="fade-left">
                    <div class="safa-about-person-image-bg"></div>
                    <div class="safa-about-person-image-frame">
                        <img src="assets/main-person.jpg" alt="Safa Formwork Founder" class="safa-about-person-image" loading="lazy" onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                        <div class="safa-about-image-placeholder" style="display: none;">
                            <i class="bi bi-person-circle"></i>
                            <p>Upload main-person.jpg</p>
                        </div>
                    </div>
                    <div class="safa-about-person-decoration safa-about-person-decoration-1"></div>
                    <div class="safa-about-person-decoration safa-about-person-decoration-2"></div>
                    <div class="safa-about-person-decoration safa-about-person-decoration-3"></div>
                </div>
            </div>
        </div>
    </section>


    <!-- Call to Action -->
    <section class="safa-about-cta">
        <div class="safa-about-cta-bg"></div>
        <div class="safa-about-cta-overlay"></div>
        <div class="container">
            <div class="safa-about-cta-content" data-safa-animate="fade-up" data-aos="fade-up">
                <h2 class="safa-about-cta-title">Start Your Project</h2>
                <p class="safa-about-cta-desc">Ready to work with a construction partner that delivers precision, quality, and reliability? Get in touch with Safa Formwork today.</p>
                <a href="contact" class="safa-about-btn safa-about-btn-cta">Get in Touch</a>
            </div>
        </div>
    </section>
</main>

    <!-- Footer -->
    <?php include 'footer.php'; ?>

    <!-- AOS JS -->
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    
    <!-- Bootstrap 5 JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Main JS -->
    <script src="js/main.js"></script>
    
    <!-- About Page JS -->
    <script src="js/about.js"></script>
</body>
</html>
