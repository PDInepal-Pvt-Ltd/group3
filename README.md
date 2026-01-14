
# SAFA Formwork Web Platform

A comprehensive PHP-based web application for SAFA Formwork, designed to showcase construction projects, company services, and facilitate client engagement through inquiries and a dynamic gallery. This project is ideal for construction businesses seeking a professional online presence with robust backend management.

---

## Table of Contents

- [Project Overview](#project-overview)
- [Key Features](#key-features)
- [Technology Stack](#technology-stack)
- [Installation & Setup](#installation--setup)
- [Configuration](#configuration)
- [Folder Structure](#folder-structure)
- [API Endpoints](#api-endpoints)
- [Usage Guide](#usage-guide)
- [Screenshots](#screenshots)
- [Contributing](#contributing)
- [License](#license)

---

## Project Overview

SAFA Formwork Web Platform is a full-featured website for a construction company, built with PHP and MySQL. It provides:

- A public-facing site for clients to view company information, services, and completed projects
- A secure backend for managing projects, gallery images, and client inquiries
- Automated email notifications for inquiries
- Responsive design for desktop and mobile users

---

## Key Features

- **Multi-Page Website:** Home, About, Services, Projects, and Contact pages
- **Dynamic Project Gallery:** Add, edit, and display project images and details
- **Inquiry Management:** Clients can submit inquiries via forms; admins receive email notifications
- **Admin Panel:** Secure area for managing projects and viewing inquiries
- **Custom Styling:** Responsive layouts using custom CSS
- **Database Integration:** All data stored and managed in MySQL
- **Email Integration:** SMTP-based email notifications for inquiries
- **Security:** Includes basic security features for authentication and data protection

---

## Technology Stack

- **Frontend:** HTML5, CSS3, JavaScript
- **Backend:** PHP 7+
- **Database:** MySQL (see `safa_formwork.sql`)
- **Email:** SMTP (see `includes/smtp_config.php`)

---

## Installation & Setup

### 1. Clone the Repository

```sh
git clone https://github.com/amritsapkotadev/Internship.git
```

### 2. Database Setup

- Import the `safa_formwork.sql` file into your MySQL server to create the required tables and sample data.
- Update database credentials in `includes/db_connect.php` as per your environment.

### 3. PHP Configuration

- Ensure your server supports PHP 7 or higher.
- Update `php.ini` for file uploads, email, and other settings as needed.

### 4. SMTP Email Setup

- Edit `includes/smtp_config.php` with your SMTP server details for email notifications.

### 5. Run the Application

- Host the project on a local or remote PHP server (e.g., XAMPP, MAMP, LAMP).
- Access the site via `http://localhost/your-folder/` or your server's domain.

---

## Configuration

- **Database:**
  - Update `includes/db_connect.php` with your MySQL host, username, password, and database name.
- **Email:**
  - Configure SMTP settings in `includes/smtp_config.php` for outgoing emails.
- **Uploads:**
  - Ensure `uploads/` directory is writable for image and project uploads.

---

## Folder Structure

```
├── about.php                # About page
├── contact.php              # Contact page with inquiry form
├── footer.php               # Footer include
├── index.php                # Home page
├── php.ini                  # PHP configuration
├── project.php              # Single project details
├── projects.php             # Projects listing page
├── safa_formwork.sql        # MySQL database schema
├── services.php             # Services page
├── sw.js                    # Service worker (if used)
├── api/
│   ├── get_projects.php     # API endpoint for fetching projects
│   └── send_inquiry.php     # API endpoint for submitting inquiries
├── assets/
│   └── Gallery Sliding/     # Gallery assets
├── css/
│   ├── about.css
│   ├── contact.css
│   ├── footer.css
│   ├── global.css
│   ├── index.css
│   ├── project-detail-inline.css
│   ├── projects-inline.css
│   ├── projects.css
│   └── services.css
├── ghar/
│   ├── inquiries.php        # Admin inquiries management
│   ├── logout.php           # Admin logout
│   ├── project_delete.php   # Project deletion
│   ├── projects.php         # Admin projects management
│   ├── xtzprabin12.php      # (Custom/admin scripts)
│   └── xtztragiikz.php      # (Custom/admin scripts)
├── includes/
│   ├── db_connect.php       # Database connection
│   ├── header.php           # Header include
│   ├── security.php         # Security/authentication
│   ├── send_mail.php        # Email sending logic
│   ├── smtp_config.php      # SMTP configuration
│   └── smtp_mail.php        # SMTP mail logic
├── js/
│   ├── about.js
│   ├── contact.js
│   ├── main.js
│   ├── project-detail.js
│   ├── projects.js
│   ├── services.js
│   ├── slider.js
│   └── video-handler.js
├── logs/
│   └── README.txt           # Log documentation
├── uploads/
│   ├── index.html
│   ├── gallery/             # Uploaded gallery images
│   └── projects/            # Uploaded project images
```

---

## API Endpoints

- `api/get_projects.php` — Returns JSON data for all projects
- `api/send_inquiry.php` — Accepts POST requests for inquiry submissions

---

## Usage Guide

### For Visitors
- Browse company information, services, and completed projects
- View project gallery with images and details
- Submit inquiries via the contact form

### For Admins
- Log in to the admin panel (see `ghar/` directory)
- Add, edit, or delete projects
- View and respond to client inquiries
- Manage gallery uploads

---

## Screenshots

_Add screenshots of the homepage, project gallery, admin panel, and inquiry form here to showcase the UI and features._

---

## Contributing

Contributions are welcome! To contribute:

1. Fork the repository
2. Create a new branch (`git checkout -b feature-branch`)
3. Make your changes
4. Commit and push (`git commit -m 'Add new feature'`)
5. Open a pull request

For major changes, please open an issue first to discuss your ideas.

---

## License

This project is licensed under the MIT License. See the LICENSE file for details.
