<?php
/**
 * SMTP Mail Sending Function
 * Uses SimpleSMTP class for Gmail SMTP support
 */

require_once __DIR__ . '/smtp_config.php';
require_once __DIR__ . '/smtp_mail.php';

/**
 * Send email using SMTP
 * Uses SimpleSMTP class for Gmail SMTP support
 * 
 * @param string $to Recipient email
 * @param string $subject Email subject
 * @param string $message Email body (HTML)
 * @param string $fromEmail Sender email
 * @param string $fromName Sender name
 * @return array ['success' => bool, 'message' => string]
 */
function sendSMTPMail($to, $subject, $message, $fromEmail = null, $fromName = null) {
    $config = require __DIR__ . '/smtp_config.php';
    
    // Use config defaults if not provided
    $fromEmail = $fromEmail ?: $config['from_email'];
    $fromName = $fromName ?: $config['from_name'];
    
    // Check if password is set
    if (empty($config['smtp_password'])) {
        return ['success' => false, 'message' => 'SMTP password not configured'];
    }
    
    // Use SimpleSMTP for Gmail
    try {
        $smtp = new SimpleSMTP(
            $config['smtp_host'],
            $config['smtp_port'],
            $config['smtp_secure'],
            $config['smtp_username'],
            $config['smtp_password']
        );
        
        if (!$smtp->connect()) {
            return ['success' => false, 'message' => 'Failed to connect to SMTP server'];
        }
        
        if (!$smtp->authenticate()) {
            $smtp->close();
            return ['success' => false, 'message' => 'SMTP authentication failed'];
        }
        
        $sent = $smtp->send($fromEmail, $fromName, $to, $subject, $message);
        $smtp->close();
        
        if ($sent) {
            return ['success' => true, 'message' => 'Email sent successfully via SMTP'];
        } else {
            return ['success' => false, 'message' => 'Failed to send email'];
        }
    } catch (Exception $e) {
        return ['success' => false, 'message' => 'SMTP Error: ' . $e->getMessage()];
    }
}


/**
 * Send contact form inquiry email
 * 
 * @param array $data Contact form data (name, email, phone, project_type, subject, message)
 * @return array ['success' => bool, 'message' => string]
 */
function sendContactInquiryEmail($data) {
    $config = require __DIR__ . '/smtp_config.php';
    
    $name = htmlspecialchars($data['name'] ?? '');
    $email = htmlspecialchars($data['email'] ?? '');
    $phone = htmlspecialchars($data['phone'] ?? 'N/A');
    $projectType = htmlspecialchars($data['project_type'] ?? 'N/A');
    $subject = htmlspecialchars($data['subject'] ?? '');
    $message = htmlspecialchars($data['message'] ?? '');
    
    // Email subject
    $emailSubject = 'New Contact Form Inquiry: ' . $subject;
    
    // Email body (HTML format) - Matching the golden-brown card design
    $emailBody = '
    <!DOCTYPE html>
    <html>
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <style>
            * { margin: 0; padding: 0; box-sizing: border-box; }
            body { 
                font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif; 
                line-height: 1.6; 
                color: #333; 
                background-color: #f5f5f5;
                padding: 20px;
            }
            .container { 
                max-width: 600px; 
                margin: 0 auto; 
                background: #ffffff;
            }
            .banner { 
                background: linear-gradient(135deg, #C89A60 0%, #E0B27C 100%); 
                color: white; 
                padding: 20px 24px;
                border-radius: 12px 12px 0 0;
                display: flex;
                align-items: center;
                gap: 12px;
            }
            .banner-dot {
                width: 12px;
                height: 12px;
                background: #ffffff;
                border-radius: 50%;
                flex-shrink: 0;
            }
            .banner-text {
                font-size: 1.1rem;
                font-weight: 700;
                letter-spacing: 0.5px;
            }
            .content { 
                background: #ffffff; 
                padding: 24px;
            }
            .info-card {
                background: #fffef9;
                border-radius: 12px;
                padding: 16px 20px;
                margin-bottom: 12px;
                border-left: 4px solid #C89A60;
            }
            .info-label {
                font-size: 0.7rem;
                font-weight: 700;
                color: #666;
                text-transform: uppercase;
                letter-spacing: 1px;
                margin-bottom: 8px;
                padding-left: 20px;
            }
            .info-value {
                font-size: 0.95rem;
                font-weight: 600;
                color: #222;
                padding-left: 20px;
                word-break: break-word;
            }
            .info-value a {
                color: #C89A60;
                text-decoration: none;
                font-weight: 600;
            }
            .info-value a:hover {
                text-decoration: underline;
            }
            .status-badge {
                display: inline-block;
                background: linear-gradient(135deg, #C89A60, #E0B27C);
                color: #ffffff;
                padding: 4px 10px;
                border-radius: 12px;
                font-size: 0.7rem;
                font-weight: 700;
                text-transform: uppercase;
                letter-spacing: 0.5px;
                margin-left: 8px;
            }
            .message-card {
                background: #fffef9;
                border-radius: 12px;
                padding: 16px 20px;
                margin-top: 12px;
                border-left: 4px solid #C89A60;
            }
            .message-label {
                font-size: 0.7rem;
                font-weight: 700;
                color: #666;
                text-transform: uppercase;
                letter-spacing: 1px;
                margin-bottom: 12px;
                padding-left: 20px;
            }
            .message-value {
                font-size: 0.9rem;
                font-weight: 500;
                color: #222;
                line-height: 1.6;
                white-space: pre-wrap;
                word-wrap: break-word;
                text-align: left;
                padding-left: 20px;
            }
            .footer { 
                text-align: center; 
                margin-top: 24px; 
                padding: 20px;
                background: #f5f5f5;
                border-radius: 0 0 12px 12px;
                color: #999; 
                font-size: 0.75rem;
            }
            @media only screen and (max-width: 600px) {
                body { padding: 10px; }
                .content { padding: 16px; }
                .info-card, .message-card { padding: 12px 16px; }
            }
        </style>
    </head>
    <body>
        <div class="container">
            <div class="banner">
                <div class="banner-dot"></div>
                <div class="banner-text">New Project Inquiry</div>
            </div>
            <div class="content">
                <div class="info-card">
                    <div class="info-label">CLIENT NAME</div>
                    <div class="info-value">' . $name . '</div>
                </div>
                <div class="info-card">
                    <div class="info-label">EMAIL ADDRESS</div>
                    <div class="info-value"><a href="mailto:' . $email . '">' . $email . '</a></div>
                </div>
                <div class="info-card">
                    <div class="info-label">PHONE NUMBER</div>
                    <div class="info-value">' . $phone . '</div>
                </div>
                <div class="info-card">
                    <div class="info-label">PROJECT TYPE</div>
                    <div class="info-value">' . $projectType . ' <span class="status-badge">NEW INQUIRY</span></div>
                </div>
                <div class="info-card">
                    <div class="info-label">INQUIRY TIME</div>
                    <div class="info-value">' . (new DateTime('now', new DateTimeZone('Australia/Sydney')))->format('F j, Y @ g:i A') . '</div>
                </div>
                <div class="message-card">
                    <div class="message-label">PROJECT MESSAGE</div>
                    <div class="message-value">' . nl2br($message) . '</div>
                </div>
            </div>
            <div class="footer">
                <p>This email was sent from the Safa Formwork & Scaffolding website contact form.</p>
                <p>© ' . date('Y') . ' Safa Formwork & Scaffolding</p>
            </div>
        </div>
    </body>
    </html>
    ';
    
    // Send email to admin
    $result = sendSMTPMail(
        $config['to_email'],
        $emailSubject,
        $emailBody,
        $config['from_email'],
        $config['from_name']
    );
    
    // If admin email sent successfully, send auto-reply to user
    if ($result['success']) {
        $autoReplyResult = sendAutoReplyEmail([
            'name' => $name,
            'email' => $email,
            'subject' => $subject,
            'project_type' => $projectType
        ]);
        
        // Log auto-reply result (optional - for debugging)
        if (!$autoReplyResult['success']) {
            error_log('Auto-reply email failed: ' . $autoReplyResult['message']);
        }
    }
    
    return $result;
}

/**
 * Send auto-reply email to user who submitted inquiry
 * 
 * @param array $data User data (name, email, subject)
 * @return array ['success' => bool, 'message' => string]
 */
function sendAutoReplyEmail($data) {
    $config = require __DIR__ . '/smtp_config.php';
    
    $name = htmlspecialchars($data['name'] ?? '');
    $email = htmlspecialchars($data['email'] ?? '');
    $subject = htmlspecialchars($data['subject'] ?? 'Your Inquiry');
    
    // Email subject
    $emailSubject = 'Thank You for Contacting Safa Formwork & Scaffolding';
    
    // Get project type from data if available
    $projectType = htmlspecialchars($data['project_type'] ?? 'your inquiry');
    if ($projectType === 'N/A' || empty($projectType)) {
        $projectType = 'your inquiry';
    }
    
    // Email body (HTML format) - Simple, gentle, professional auto-reply
    $emailBody = '
    <!DOCTYPE html>
    <html>
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <style>
            * { margin: 0; padding: 0; box-sizing: border-box; }
            body { 
                font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif; 
                line-height: 1.6; 
                color: #333; 
                background-color: #f5f5f5;
                padding: 20px;
            }
            .container { 
                max-width: 600px; 
                margin: 0 auto; 
                background: #ffffff;
            }
            .banner { 
                background: linear-gradient(135deg, #C89A60 0%, #E0B27C 100%); 
                color: white; 
                padding: 50px 30px;
                text-align: center;
            }
            .banner-text {
                font-size: 2.5rem;
                font-weight: 700;
                letter-spacing: 1px;
            }
            .content { 
                background: #ffffff; 
                padding: 40px 30px;
            }
            .greeting {
                font-size: 1.1rem;
                color: #222;
                margin-bottom: 20px;
                line-height: 1.8;
            }
            .message-text {
                font-size: 1rem;
                color: #444;
                line-height: 1.8;
                margin-bottom: 0;
            }
            @media only screen and (max-width: 600px) {
                body { padding: 10px; }
                .content { padding: 30px 20px; }
                .banner { padding: 40px 20px; }
                .banner-text { font-size: 2rem; }
            }
        </style>
    </head>
    <body>
        <div class="container">
            <div class="banner">
                <div class="banner-text">Thank You</div>
            </div>
            <div class="content">
                <div class="greeting">
                    Dear ' . $name . ',<br><br>
                </div>
                
                <div class="message-text">
                    Thank you for reaching out to Safa Formwork & Scaffolding. We have received your inquiry regarding ' . $projectType . ' and truly appreciate your interest in our services.
                </div>
                
                <div class="message-text" style="margin-top: 20px;">
                    Our team is currently reviewing your inquiry and will get back to you within 24 hours during business days. We are committed to providing you with the best service and solutions for your construction needs.
                </div>
                
                <div class="message-text" style="margin-top: 20px;">
                    We look forward to the opportunity of working with you and helping bring your project to life.
                </div>
                
                <div class="message-text" style="margin-top: 30px; padding-top: 20px; border-top: 1px solid #e0e0e0;">
                    Best regards,<br>
                    <strong>The Safa Formwork & Scaffolding Team</strong>
                </div>
            </div>
        </div>
    </body>
    </html>
    ';
    
    // Send auto-reply email to user
    return sendSMTPMail(
        $email,
        $emailSubject,
        $emailBody,
        $config['from_email'],
        $config['from_name']
    );
}
