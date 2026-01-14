<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Professional formwork, steel fixing, concrete, and scaffolding services by Safa Formwork & Scaffolding.">
    <title>Services - Safa Formwork & Scaffolding</title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet">
    
    <!-- CSS -->
    <link rel="stylesheet" href="css/global.css">
    <link rel="stylesheet" href="css/footer.css">
    <link rel="stylesheet" href="css/services.css">
    
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

<?php
/**
 * Services Page - Service Information
 * 
 * Displays detailed information about all services offered by Safa Formwork.
 * Services include: Formwork, Steel Fixing, Concrete, and Scaffolding.
 * 
 * @package SafaFormwork
 * @version 1.0
 */

// Service data structure
$services = [
    'formwork' => [
        'title' => 'Formwork',
        'subtitle' => 'Precision Engineering for Concrete Excellence',
        'description' => 'Professional formwork solutions for OSD tanks, capping beams, site access roads, and structural elements. Our experienced team delivers cost-effective, timely solutions ensuring flawless concrete finishes on every project.',
        'extended' => 'At Safa Formwork, we deliver architectural precision and structural integrity on every project, from major educational facilities to complex civil infrastructure. Our completion of the Greystanes OLQP project demonstrates our expertise in constructing new halls with integrated external footpaths, stairs, and accessible ramps that seamlessly link existing and new structures. We ensure full compliance with accessibility standards while maintaining the highest quality finishes and structural integrity throughout. Our commitment to on-time delivery and meticulous attention to detail means every formwork installation meets both engineering specifications and architectural vision. From the initial planning stages through to final concrete placement, Safa Formwork brings the craftsmanship and precision that defines exceptional construction outcomes.',
        'benefits' => [
            'Architectural precision ensuring structural integrity and flawless concrete finishes',
            'Expert formwork for educational facilities, halls, and complex building structures',
            'Full compliance with accessibility standards for ramps, stairs, and pathways',
            'Seamless integration of new structures with existing building infrastructure',
            'On-time delivery with meticulous attention to detail at every stage',
            'Experienced teams delivering cost-effective solutions without compromising quality'
        ],
        'process' => [
            'Planning & Design' => 'Detailed assessment and custom formwork design tailored to your project requirements',
            'Material Selection' => 'Choosing the right materials and systems for optimal performance and cost efficiency',
            'Installation' => 'Professional installation by experienced technicians ensuring precision and safety',
            'Quality Control' => 'Rigorous inspection at every stage to ensure compliance and excellence',
            'Removal & Cleanup' => 'Safe and efficient formwork removal with minimal disruption to your timeline'
        ],
        'why' => 'As demonstrated in projects like St Leonards Capping Beam and Norwest OSD tanks, we have the expertise to handle complex formwork requirements. Our family-owned business ensures personal commitment to quality, safety, and meeting your project deadlines.',
        'stats' => ['650+', 'Projects', '98%', 'Client Satisfaction']
    ],
    'steel-fixing' => [
        'title' => 'Steel Fixing',
        'subtitle' => 'Expert Reinforcement with Uncompromising Quality',
        'description' => 'Professional reinforcement services for commercial, residential, civil, aged care, and educational projects. Our skilled steel fixers ensure every reinforcement bar is placed with precision, from OSD tanks to site access roads.',
        'extended' => 'At Safa Formwork, excellence isn\'t measured by project size alone. Whether reinforcing a major commercial development or crafting the intricate steel framework for a custom plunge pool in a residential backyard, our commitment to precision remains unwavering. Our experienced teams bring meticulous attention to detail, ensuring each rebar is cut, bent, and positioned with exacting accuracy. This dedication to quality ensures every client receives the same professional standard of workmanship, regardless of project scale.',
        'benefits' => [
            'Precision steel fixing with meticulous attention to structural accuracy',
            'Expert reinforcement for projects of all scales, from custom residential pools to major infrastructure',
            'Superior structural strength and durability through precise rebar placement',
            'Skilled craftsmanship ensuring every reinforcement bar meets exact specifications',
            'Comprehensive quality assurance with thorough inspection at every stage',
            'Dedicated commitment to customer satisfaction on every project, large or small'
        ],
        'process' => [
            'Design Review' => 'Understanding project specifications and requirements for optimal reinforcement planning',
            'Material Preparation' => 'Cutting and bending reinforcement bars to exact specifications with precision',
            'Installation' => 'Precise placement and tying of reinforcement following engineering drawings',
            'Inspection' => 'Quality checks and compliance verification at every stage',
            'Documentation' => 'Complete records and certifications for project handover and compliance'
        ],
        'why' => 'Our experienced reinforcement teams have worked on projects ranging from educational facilities to civil infrastructure. We ensure a safe and positive working environment while delivering high-quality reinforcement work that meets your project requirements.',
        'stats' => ['987+', 'Tons Installed', '100%', 'Safety Record']
    ],
    'concrete' => [
        'title' => 'Concrete',
        'subtitle' => 'Durability Meets Excellence in Every Pour',
        'description' => 'High-quality concrete solutions for OSD tanks, site access roads, educational facilities, and infrastructure projects. We deliver great concrete finishes that showcase our commitment to quality and precision.',
        'extended' => 'At Safa Formwork, we deliver complex structural concrete solutions that demonstrate our technical precision and engineering expertise. Our completion of the Oran Park Project showcases our capability in managing intricate multi-level concrete construction, including slab on ground with strategic 1.2 metre pour breaks, suspended slabs supported by columns and walls, and integrated structural elements including three sets of stairs and a connecting bridge to the adjoining building. This project exemplifies our team\'s coordination and technical skill in executing complex formwork and concreting requirements. We ensure every pour meets engineering specifications while maintaining structural integrity and finish quality throughout. From initial planning to final placement, Safa Formwork brings the expertise and precision required for demanding structural construction projects.',
        'benefits' => [
            'Expert execution of complex structural concrete including slab on ground and suspended slabs',
            'Strategic pour break management ensuring optimal concrete placement and structural integrity',
            'Precision formwork and concreting for multi-level structures with columns, walls, and stairs',
            'Seamless integration of structural elements including bridges connecting adjoining buildings',
            'Technical coordination and team expertise for large-scale structural construction projects',
            'Quality assurance and engineering compliance throughout every stage of the concrete process'
        ],
        'process' => [
            'Mix Design' => 'Custom concrete mix design tailored to your project\'s specific requirements and conditions',
            'Quality Control' => 'Rigorous testing and certification of materials before placement',
            'Placement' => 'Expert concrete placement using industry best practices and modern equipment',
            'Finishing' => 'Professional finishing techniques for desired surface quality and aesthetics',
            'Curing' => 'Proper curing protocols for optimal strength development and durability'
        ],
        'why' => 'As demonstrated in projects like Holy Spirit Catholic College Lakemba and Norwest OSD tanks, we deliver great concrete finishes. Our family-owned business ensures personal commitment to quality, and we aim to meet or better your requirements daily.',
        'stats' => ['50,000+', 'Cubic Meters', '99.8%', 'Quality Rate']
    ],
    'scaffolding' => [
        'title' => 'Scaffolding',
        'subtitle' => 'Safety Engineered for Optimal Access',
        'description' => 'Safe and reliable scaffolding systems designed for optimal access and worker safety on every project. We provide comprehensive scaffolding solutions for construction, maintenance, and renovation projects.',
        'extended' => 'At Safa Formwork, our scaffolding and structural formwork expertise enables complex reinforced concrete construction with precision and safety. Our completion of the Regents Park Project demonstrates our capability in managing intricate vertical construction, including the successful pouring of 12 bridge columns requiring exact vertical precision and structural integrity. The project also involved lift shaft construction and lid installation, showcasing our team\'s coordination and technical skill in executing complex formwork and concrete placement. Our temporary access systems ensure safe and efficient work platforms for all structural elements, from ground level to elevated positions. Through meticulous planning and professional execution, Safa Formwork delivers the structural integrity and safety standards required for demanding bridge and high-rise construction projects.',
        'benefits' => [
            'Expert scaffolding and formwork systems for complex vertical construction including bridge columns',
            'Precision formwork and concrete placement ensuring structural integrity and vertical accuracy',
            'Professional lift shaft construction with integrated formwork and reinforced concrete solutions',
            'Temporary access systems providing safe and efficient work platforms for elevated structures',
            'Team coordination and technical expertise managing multiple structural elements simultaneously',
            'Comprehensive safety protocols and quality assurance throughout all construction phases'
        ],
        'process' => [
            'Site Assessment' => 'Evaluating project requirements and site conditions for optimal scaffolding design',
            'Design & Planning' => 'Custom scaffolding design ensuring optimal access and safety compliance',
            'Installation' => 'Professional installation by certified scaffolders following safety protocols',
            'Safety Inspection' => 'Regular inspections and maintenance to ensure ongoing safety compliance',
            'Dismantling' => 'Safe and efficient removal after project completion with minimal disruption'
        ],
        'why' => 'Safety is our top priority. Our scaffolding systems are designed and installed by certified professionals who understand the critical importance of worker safety. We ensure that every scaffold meets or exceeds safety regulations.',
        'stats' => ['854+', 'Projects', '98%', 'Client Satisfaction']
    ]
];
?>

<main>
    <!-- Hero Section - Cinematic Intro -->
    <section class="safa-services-hero">
        <div class="safa-services-hero-image" style="background-image: url('assets/services-main.jpg');"></div>
        <div class="safa-services-hero-overlay"></div>
        <div class="safa-services-hero-background-effects"></div>
        <div class="safa-services-hero-content">
            <div class="safa-services-hero-badge">Our Expertise</div>
            <h1 class="safa-services-hero-title">Professional Construction Services</h1>
            <p class="safa-services-hero-subtitle">Delivering excellence in formwork, steel fixing, concrete, and scaffolding with precision, safety, and reliability</p>
        </div>
    </section>

    <!-- Services Navigation -->
    <section class="services-nav">
        <div class="container">
            <div class="nav-tabs" data-aos="fade-up">
                <a href="#formwork" class="nav-tab active" data-service="formwork">
                    <span class="tab-icon">🏗️</span>
                    <span class="tab-text">Formwork</span>
                </a>
                <a href="#steel-fixing" class="nav-tab" data-service="steel-fixing">
                    <span class="tab-icon">🔩</span>
                    <span class="tab-text">Steel Fixing</span>
                </a>
                <a href="#concrete" class="nav-tab" data-service="concrete">
                    <span class="tab-icon">🏛️</span>
                    <span class="tab-text">Concrete</span>
                </a>
                <a href="#scaffolding" class="nav-tab" data-service="scaffolding">
                    <span class="tab-icon">🪜</span>
                    <span class="tab-text">Scaffolding</span>
                </a>
            </div>
        </div>
    </section>

    <?php foreach ($services as $key => $service): ?>
    <!-- <?php echo ucfirst($service['title']); ?> Service Section -->
    <section id="<?php echo $key; ?>" class="service-section">
        <div class="container">
            <!-- Service Header -->
            <div class="service-header" data-aos="fade-up">
                <div class="service-badge"><?php echo $service['title']; ?></div>
                <h2 class="service-title"><?php echo $service['subtitle']; ?></h2>
                <p class="service-intro"><?php echo $service['description']; ?></p>
            </div>

            <!-- Main Content Grid -->
            <div class="service-main-grid">
                <!-- Image Section -->
                <div class="service-image-wrapper" data-aos="fade-right" data-aos-delay="100">
                    <?php 
                    $serviceImg = 'assets/' . $key . '-service.jpg';
                    if (file_exists($serviceImg)): ?>
                        <div class="service-image-container">
                            <img src="<?php echo $serviceImg; ?>" alt="<?php echo $service['title']; ?>" loading="lazy" class="service-image" onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                            <div class="image-overlay"></div>
                            <div class="empty-image-placeholder" style="display: none;">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                                    <rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect>
                                    <circle cx="8.5" cy="8.5" r="1.5"></circle>
                                    <polyline points="21 15 16 10 5 21"></polyline>
                                </svg>
                            </div>
                        </div>
                    <?php else: ?>
                        <div class="service-image-container placeholder">
                            <div class="empty-image-placeholder">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                                    <rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect>
                                    <circle cx="8.5" cy="8.5" r="1.5"></circle>
                                    <polyline points="21 15 16 10 5 21"></polyline>
                                </svg>
                                <p style="margin-top: 15px; color: #999; font-size: 0.9rem;">Upload <?php echo $key; ?>-service.jpg to assets/ folder</p>
                            </div>
                        </div>
                    <?php endif; ?>
                    
                    <!-- Service Stats -->
                    <div class="service-stats-card" data-aos="fade-up" data-aos-delay="300">
                        <div class="stat-box">
                            <span class="stat-value"><?php echo $service['stats'][0]; ?></span>
                            <span class="stat-desc"><?php echo $service['stats'][1]; ?></span>
                        </div>
                        <div class="stat-divider"></div>
                        <div class="stat-box">
                            <span class="stat-value"><?php echo $service['stats'][2]; ?></span>
                            <span class="stat-desc"><?php echo $service['stats'][3]; ?></span>
                        </div>
                    </div>
                </div>

                <!-- Content Section -->
                <div class="service-content-wrapper" data-aos="fade-left" data-aos-delay="200">
                    <!-- Extended Description -->
                    <div class="content-block">
                        <p class="service-extended"><?php echo $service['extended']; ?></p>
                    </div>

                    <!-- Key Benefits -->
                    <div class="content-block benefits-block">
                        <h3 class="block-title">
                            <span class="title-icon">✓</span>
                            Key Benefits
                        </h3>
                        <ul class="benefits-list">
                            <?php foreach ($service['benefits'] as $benefit): ?>
                            <li data-aos="fade-left" data-aos-delay="<?php echo array_search($benefit, $service['benefits']) * 50; ?>">
                                <span class="benefit-check"></span>
                                <span class="benefit-text"><?php echo $benefit; ?></span>
                            </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Process Timeline -->
            <div class="process-section" data-aos="fade-up" data-aos-delay="300">
                <div class="section-label">
                    <span class="label-line"></span>
                    <span class="label-text">Our Process</span>
                    <span class="label-line"></span>
                </div>
                <div class="process-timeline">
                    <?php 
                    $stepNum = 1;
                    foreach ($service['process'] as $stepTitle => $stepDesc): 
                    ?>
                    <div class="process-step" data-aos="fade-up" data-aos-delay="<?php echo ($stepNum - 1) * 100; ?>">
                        <div class="step-number"><?php echo $stepNum; ?></div>
                        <div class="step-content">
                            <h4 class="step-title"><?php echo $stepTitle; ?></h4>
                            <p class="step-description"><?php echo $stepDesc; ?></p>
                        </div>
                        <?php if ($stepNum < count($service['process'])): ?>
                        <div class="step-connector"></div>
                        <?php endif; ?>
                    </div>
                    <?php 
                    $stepNum++;
                    endforeach; 
                    ?>
                </div>
            </div>
        </div>
    </section>
    <?php endforeach; ?>

    <!-- Modern CTA Section with Smooth Floating Animation -->
    <section class="modern-cta-section">
        <div class="cta-background-overlay"></div>
        <div class="container">
            <div class="modern-cta-content">
                <div class="cta-badge-wrapper" data-aos="fade-down" data-aos-delay="100">
                    <span class="cta-badge">Let's Build Together</span>
                </div>
                <h2 class="cta-title" data-aos="fade-up" data-aos-delay="200">Ready to Start Your Project?</h2>
                <p class="cta-description" data-aos="fade-up" data-aos-delay="300">Get in touch with our team to discuss your construction needs and discover how we can help bring your vision to life.</p>
                <div class="modern-cta-buttons" data-aos="fade-up" data-aos-delay="400">
                    <a href="contact" class="cta-btn cta-btn-primary">
                        <span class="btn-text">Contact Us😀</span>
                        
                    </a>
                    <a href="projects" class="cta-btn cta-btn-secondary">
                        <span class="btn-text">Projects👀</span>
                    </a>
                </div>
            </div>
        </div>
        <div class="cta-floating-elements">
            <div class="floating-circle circle-1"></div>
            <div class="floating-circle circle-2"></div>
            <div class="floating-circle circle-3"></div>
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
    
    <!-- Services Page JavaScript -->
    <script src="js/services.js"></script>
