<?php
header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
	echo json_encode(['ok' => false, 'error' => 'Invalid method']);
	exit;
}

require_once __DIR__ . '/../includes/db_connect.php';

// Ensure table exists with all fields
$pdo->exec("CREATE TABLE IF NOT EXISTS inquiries (
	id INT AUTO_INCREMENT PRIMARY KEY,
	name VARCHAR(100) NOT NULL,
	email VARCHAR(255) NOT NULL,
	phone VARCHAR(50) DEFAULT NULL,
	project_type VARCHAR(100) DEFAULT NULL,
	subject VARCHAR(255) DEFAULT NULL,
	message TEXT NOT NULL,
	status ENUM('unread','read') NOT NULL DEFAULT 'unread',
	created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;");

// Add new columns if they don't exist (for existing tables)
try {
	$columns = $pdo->query("SHOW COLUMNS FROM inquiries LIKE 'phone'")->fetchAll();
	if (empty($columns)) {
		$pdo->exec("ALTER TABLE inquiries ADD COLUMN phone VARCHAR(50) DEFAULT NULL AFTER email");
	}
} catch (Exception $e) {
	// Column might already exist, ignore
}
try {
	$columns = $pdo->query("SHOW COLUMNS FROM inquiries LIKE 'project_type'")->fetchAll();
	if (empty($columns)) {
		$pdo->exec("ALTER TABLE inquiries ADD COLUMN project_type VARCHAR(100) DEFAULT NULL AFTER phone");
	}
} catch (Exception $e) {
	// Column might already exist, ignore
}
try {
	$columns = $pdo->query("SHOW COLUMNS FROM inquiries LIKE 'subject'")->fetchAll();
	if (empty($columns)) {
		$pdo->exec("ALTER TABLE inquiries ADD COLUMN subject VARCHAR(255) DEFAULT NULL AFTER project_type");
	}
} catch (Exception $e) {
	// Column might already exist, ignore
}

$name = trim($_POST['name'] ?? '');
$email = trim($_POST['email'] ?? '');
$phone = trim($_POST['phone'] ?? '');
$projectType = trim($_POST['project_type'] ?? '');
$subject = trim($_POST['subject'] ?? '');
$message = trim($_POST['message'] ?? '');

if ($name === '' || $email === '' || $subject === '' || $message === '') {
	echo json_encode(['ok' => false, 'error' => 'Missing required fields']);
	exit;
}
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
	echo json_encode(['ok' => false, 'error' => 'Invalid email']);
	exit;
}
if (mb_strlen($message) > 400) {
	echo json_encode(['ok' => false, 'error' => 'Message too long']);
	exit;
}

try {
	$stmt = $pdo->prepare("INSERT INTO inquiries (name, email, phone, project_type, subject, message, status) VALUES (?, ?, ?, ?, ?, ?, 'unread')");
	$stmt->execute([$name, $email, $phone ?: null, $projectType ?: null, $subject, $message]);
	
	// Send email notification via SMTP
	require_once __DIR__ . '/../includes/send_mail.php';
	$emailResult = sendContactInquiryEmail([
		'name' => $name,
		'email' => $email,
		'phone' => $phone,
		'project_type' => $projectType,
		'subject' => $subject,
		'message' => $message
	]);
	
	// Log email result (optional - for debugging)
	if (!$emailResult['success']) {
		error_log('SMTP Email failed: ' . $emailResult['message']);
	}
	
	echo json_encode(['ok' => true]);
} catch (Throwable $e) {
	http_response_code(500);
	echo json_encode(['ok' => false, 'error' => 'Failed to submit inquiry']);
}


