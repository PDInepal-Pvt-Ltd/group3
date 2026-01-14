<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Get in touch with Safa Formwork & Scaffolding for your construction needs. Let's build something great together.">
    <title>Contact Us - Safa Formwork & Scaffolding</title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet">
    
    <!-- CSS -->
    <link rel="stylesheet" href="css/global.css">
    <link rel="stylesheet" href="css/footer.css">
    <link rel="stylesheet" href="css/contact.css">
    
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
 * Contact Page - Contact Form Handler
 * 
 * Handles contact form submissions and displays success/error messages.
 * Form validation is performed both client-side and server-side.
 * 
 * @package SafaFormwork
 * @version 1.0
 */

// Handle form submission
$formMessage = '';
$formSuccess = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['name'])) {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $subject = trim($_POST['subject'] ?? '');
    $message = trim($_POST['message'] ?? '');
    
    if (empty($name) || empty($email) || empty($subject) || empty($message)) {
        $formMessage = 'Please fill in all required fields.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $formMessage = 'Please enter a valid email address.';
    } elseif (strlen($message) > 400) {
        $formMessage = 'Message must be 400 characters or less.';
    } else {
        // Simple success message (no database)
        $formSuccess = true;
        $formMessage = '✅ Thank you! We\'ll get back to you soon.';
    }
}
?>

<main class="safa-contact-main">
    <!-- Floating Particles Background -->
    <div class="safa-contact-particles">
        <div class="particle"></div>
        <div class="particle"></div>
        <div class="particle"></div>
        <div class="particle"></div>
        <div class="particle"></div>
        <div class="particle"></div>
        <div class="particle"></div>
        <div class="particle"></div>
    </div>

    <!-- Hero Section -->
    <section class="safa-contact-hero">
        <div class="safa-contact-hero-image" style="background-image: url('assets/contact.jpg');"></div>
        <div class="safa-contact-hero-overlay"></div>
        <div class="safa-contact-hero-background"></div>
        <div class="safa-contact-hero-content">
            <div class="safa-contact-hero-badge">Get in Touch</div>
            <h1 class="safa-contact-hero-title">Let's Build Something Great Together</h1>
            <p class="safa-contact-hero-subtitle">We'd love to hear about your project. Fill out the form and our team will get back to you shortly.</p>
        </div>
       
    </section>

    <!-- Contact Section -->
    <section class="safa-contact-section">
        <div class="container">
            <div class="safa-contact-container">
                <!-- Contact Form - Left Side -->
                <div class="safa-contact-form-wrapper" data-aos="fade-right">
                    <div class="safa-contact-form-glass">
                        <h2 class="safa-contact-form-title">Send Us a Message</h2>
                        
                        <!-- Success/Error Message -->
                        <?php if ($formMessage): ?>
                            <div class="safa-contact-form-message <?php echo $formSuccess ? 'safa-contact-success' : 'safa-contact-error'; ?>" id="safaContactFormMessage">
                                <?php echo htmlspecialchars($formMessage); ?>
                            </div>
                        <?php else: ?>
                            <div class="safa-contact-form-message safa-contact-success" id="safaContactFormMessage" style="display: none;">
                                ✅ Thank you! We'll get back to you soon.
                            </div>
                        <?php endif; ?>
                        
                        <form id="safaContactForm" method="POST" action="" class="safa-contact-form">
                            <!-- Full Name -->
                            <div class="safa-contact-form-group">
                                <div class="safa-contact-input-wrapper">
                                    <i class="bi bi-person-fill safa-contact-input-icon safa-contact-icon-person"></i>
                                    <input type="text" id="safaContactName" name="name" class="safa-contact-input" placeholder=" " required>
                                    <label for="safaContactName" class="safa-contact-label">Full Name *</label>
                                </div>
                                <span class="safa-contact-error-message">Please enter your name</span>
                            </div>

                            <!-- Email -->
                            <div class="safa-contact-form-group">
                                <div class="safa-contact-input-wrapper">
                                    <i class="bi bi-envelope-fill safa-contact-input-icon safa-contact-icon-email"></i>
                                    <input type="email" id="safaContactEmail" name="email" class="safa-contact-input" placeholder=" " required>
                                    <label for="safaContactEmail" class="safa-contact-label">Email *</label>
                                </div>
                                <span class="safa-contact-error-message">Please enter a valid email</span>
                            </div>

                            <!-- Phone Number -->
                            <div class="safa-contact-form-group">
                                <div class="safa-contact-input-wrapper">
                                    <i class="bi bi-telephone-fill safa-contact-input-icon safa-contact-icon-phone"></i>
                                    <input type="tel" id="safaContactPhone" name="phone" class="safa-contact-input" placeholder=" ">
                                    <label for="safaContactPhone" class="safa-contact-label">Phone Number</label>
                                </div>
                            </div>

                            <!-- Project Type -->
                            <div class="safa-contact-form-group">
                                <div class="safa-contact-input-wrapper">
                                    <i class="bi bi-briefcase-fill safa-contact-input-icon safa-contact-icon-briefcase"></i>
                                    <select id="safaContactProjectType" name="project_type" class="safa-contact-input safa-contact-select">
                                        <option value="">Select Project Type</option>
                                        <option value="Formwork">Formwork</option>
                                        <option value="Steel Fixing">Steel Fixing</option>
                                        <option value="Concrete">Concrete</option>
                                        <option value="Scaffolding">Scaffolding</option>
                                        <option value="General Inquiry">General Inquiry</option>
                                    </select>
                                    <label for="safaContactProjectType" class="safa-contact-label safa-contact-label-select">Project Type</label>
                                </div>
                            </div>

                            <!-- Subject -->
                            <div class="safa-contact-form-group">
                                <div class="safa-contact-input-wrapper">
                                    <i class="bi bi-tag-fill safa-contact-input-icon safa-contact-icon-tag"></i>
                                    <input type="text" id="safaContactSubject" name="subject" class="safa-contact-input" placeholder=" " required>
                                    <label for="safaContactSubject" class="safa-contact-label">Subject *</label>
                                </div>
                                <span class="safa-contact-error-message">Please enter a subject</span>
                            </div>

                            <!-- Description -->
                            <div class="safa-contact-form-group safa-contact-textarea-group">
                                <div class="safa-contact-input-wrapper">
                                    <i class="bi bi-chat-dots-fill safa-contact-input-icon safa-contact-icon-chat safa-contact-textarea-icon"></i>
                                    <textarea id="safaContactMessage" name="message" class="safa-contact-input safa-contact-textarea" placeholder=" " required maxlength="400"></textarea>
                                    <label for="safaContactMessage" class="safa-contact-label">Description *</label>
                                </div>
                                <div class="safa-contact-char-count">
                                    <span id="safaContactCharCount">0</span>/400 characters
                                </div>
                                <span class="safa-contact-error-message">Please enter your message</span>
                            </div>

                            <!-- Submit Button -->
                            <button type="submit" class="safa-contact-submit-btn">
                                <span class="safa-contact-btn-text">Send Message</span>
                                <span class="safa-contact-btn-ripple"></span>
                            </button>
                        </form>
                    </div>
                </div>

                <!-- Contact Information - Right Side -->
                <div class="safa-contact-info-wrapper" data-aos="fade-left">
                    <div class="safa-contact-info-glass">
                        <h2 class="safa-contact-info-title">Get in Touch</h2>
                        
                        <!-- Call Us -->
                        <div class="safa-contact-info-card">
                            <div class="safa-contact-info-icon safa-contact-icon-call">
                                <i class="bi bi-telephone-fill"></i>
                            </div>
                            <div class="safa-contact-info-content">
                                <h4>Call Us</h4>
                                <p><a href="tel:+61297294550">02 9729 4550</a></p>
                            </div>
                        </div>

                        <!-- Email Us -->
                        <div class="safa-contact-info-card">
                            <div class="safa-contact-info-icon safa-contact-icon-email-card">
                                <i class="bi bi-envelope-fill"></i>
                            </div>
                            <div class="safa-contact-info-content">
                                <h4>Email Us</h4>
                                <p><a href="mailto:info@safaformwork.com">info@safaformwork.com</a></p>
                            </div>
                        </div>

                        <!-- Find Us -->
                        <div class="safa-contact-info-card">
                            <div class="safa-contact-info-icon safa-contact-icon-location">
                                <i class="bi bi-geo-alt-fill"></i>
                            </div>
                            <div class="safa-contact-info-content">
                                <h4>Find Us</h4>
                                <p>215 Thirteenth Avenue, Austral, 2179, NSW</p>
                            </div>
                        </div>

                        <!-- Follow Us -->
                        <div class="safa-contact-info-card">
                            <div class="safa-contact-info-icon safa-contact-icon-globe">
                                <i class="bi bi-globe"></i>
                            </div>
                            <div class="safa-contact-info-content">
                                <h4>Follow Us</h4>
                                <div class="safa-contact-social-icons">
                                    <a href="https://www.facebook.com/p/Safaformwork-and-Scaffolding-100057090025059/" target="_blank" rel="noopener noreferrer" class="safa-contact-social-icon safa-contact-social-facebook" aria-label="Facebook" title="Facebook">
                                        <i class="bi bi-facebook"></i>
                                    </a>
                                    <a href="https://www.instagram.com/safaformwork/?hl=en" target="_blank" rel="noopener noreferrer" class="safa-contact-social-icon safa-contact-social-instagram" aria-label="Instagram" title="Instagram">
                                        <i class="bi bi-instagram"></i>
                                    </a>
                                    <a href="https://au.linkedin.com/in/safa-formwork-and-scaffolding-46b21718b?trk=public_post_feed-actor-name" target="_blank" rel="noopener noreferrer" class="safa-contact-social-icon safa-contact-social-linkedin" aria-label="LinkedIn" title="LinkedIn">
                                        <i class="bi bi-linkedin"></i>
                                    </a>
                                    <a href="#" class="safa-contact-social-icon safa-contact-social-tiktok" aria-label="TikTok" title="TikTok">
                                        <i class="bi bi-tiktok"></i>
                                    </a>
                                    <a href="#" class="safa-contact-social-icon safa-contact-social-whatsapp" aria-label="WhatsApp" title="WhatsApp">
                                        <i class="bi bi-whatsapp"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Google Map -->
            <div class="safa-contact-map-wrapper" data-aos="fade-up">
                <div class="safa-contact-map-glass">
                    <div class="map-placeholder" id="contactMapPlaceholder">
                        <div class="map-loading-spinner"></div>
                        <p>Loading map...</p>
                    </div>
                    <iframe 
                        data-src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3313.123456789012!2d150.8333333!3d-33.9333333!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x6b1297b1c8b8b8b8%3A0x8b8b8b8b8b8b8b8b!2s215%20Thirteenth%20Avenue%2C%20Austral%20NSW%202179%2C%20Australia!5e0!3m2!1sen!2sau!4v1234567890123!5m2!1sen!2sau"
                        width="100%" 
                        height="100%" 
                        style="border:0; border-radius: 20px;" 
                        allowfullscreen="" 
                        loading="lazy" 
                        referrerpolicy="no-referrer-when-downgrade"
                        title="Safa Formwork Location - 215 Thirteenth Avenue, Austral, 2179, NSW"
                        id="contactMapIframe">
                    </iframe>
                </div>
            </div>
        </div>
    </section>
</main>

<?php include 'footer.php'; ?>

    <!-- Bootstrap 5 JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- AOS JS -->
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    
    <!-- Main JS -->
    <script src="js/main.js"></script>
    
    <!-- Contact JS -->
    <script src="js/contact.js"></script>
</body>
</html>
