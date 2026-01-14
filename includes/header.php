<?php
/**
 * Header Include File
 * 
 * This file contains the common header navigation used across all pages.
 * It includes the logo, desktop navigation, and mobile hamburger menu.
 * 
 * @package SafaFormwork
 * @version 1.0
 */

// Determine current page for active navigation highlighting
$currentPage = basename($_SERVER['PHP_SELF'], '.php');
if ($currentPage === 'index') {
    $currentPage = 'index';
}
?>
<!-- Main Header -->
<header class="header-wrapper" id="headerWrapper">
    <div class="header-container">
        <!-- Logo Section - Left Side -->
        <div class="header-logo-section">
            <a href="index" class="header-logo-link">
                <span class="header-brand-text">Safa Formwork</span>
            </a>
        </div>
        
        <!-- Desktop Navigation - Right Side (Hidden on Mobile) -->
        <nav class="desktop-nav" id="desktopNav">
            <ul class="desktop-nav-list">
                <li class="desktop-nav-item">
                    <a class="desktop-nav-link <?php echo ($currentPage === 'index') ? 'active' : ''; ?>" href="index">Home</a>
                </li>
                <li class="desktop-nav-item">
                    <a class="desktop-nav-link <?php echo ($currentPage === 'services') ? 'active' : ''; ?>" href="services">Services</a>
                </li>
                <li class="desktop-nav-item">
                    <a class="desktop-nav-link <?php echo ($currentPage === 'projects' || $currentPage === 'project') ? 'active' : ''; ?>" href="projects">Projects</a>
                </li>
                <li class="desktop-nav-item">
                    <a class="desktop-nav-link <?php echo ($currentPage === 'about') ? 'active' : ''; ?>" href="about">About</a>
                </li>
                <li class="desktop-nav-item">
                    <a class="desktop-nav-link <?php echo ($currentPage === 'contact') ? 'active' : ''; ?>" href="contact">Contact</a>
                </li>
            </ul>
        </nav>
        
        <!-- Mobile Hamburger Toggle -->
        <button class="safa-hb-toggle" 
                id="safaHbToggle" 
                role="button" 
                aria-expanded="false" 
                aria-controls="safaHbMenu" 
                aria-label="Toggle mobile navigation menu">
            <span class="safa-hb-bar"></span>
            <span class="safa-hb-bar"></span>
            <span class="safa-hb-bar"></span>
        </button>
    </div>
    
    <!-- Mobile Menu Backdrop -->
    <div class="safa-hb-backdrop" id="safaHbBackdrop" aria-hidden="true"></div>
    
    <!-- Mobile Menu - Slides from Right -->
    <nav class="safa-hb-menu" id="safaHbMenu" role="navigation" aria-label="Mobile navigation">
        <ul class="safa-hb-menu-list">
            <li class="safa-hb-menu-item">
                <a class="safa-hb-link" href="index">Home</a>
            </li>
            <li class="safa-hb-menu-item">
                <a class="safa-hb-link" href="services">Services</a>
            </li>
            <li class="safa-hb-menu-item">
                <a class="safa-hb-link" href="projects">Projects</a>
            </li>
            <li class="safa-hb-menu-item">
                <a class="safa-hb-link" href="about">About</a>
            </li>
            <li class="safa-hb-menu-item">
                <a class="safa-hb-link" href="contact">Contact</a>
            </li>
        </ul>
    </nav>
</header>



