<?php
// Server-side safety settings - MUST be before any output
@set_time_limit(600);
@ini_set("memory_limit", "1024M");
@ini_set("upload_max_filesize", "512M");
@ini_set("post_max_size", "512M");
@ini_set("max_file_uploads", 500);
@ini_set("max_input_time", "600");
@ini_set("default_socket_timeout", "600");

// Start output buffering early to prevent connection timeout
if (!ob_get_level()) {
	ob_start();
}

// Disable output compression for progress updates
if (function_exists('apache_setenv')) {
	@apache_setenv('no-gzip', 1);
}
@ini_set('zlib.output_compression', 0);

// Security protection - MUST be first
require_once __DIR__ . '/../includes/security.php';
checkAdminLogin();

require_once __DIR__ . '/../includes/db_connect.php';

// Ensure tables exist
$pdo->exec("CREATE TABLE IF NOT EXISTS projects (
	id INT AUTO_INCREMENT PRIMARY KEY,
	title VARCHAR(255) NOT NULL,
	category ENUM('current','completed','past') NOT NULL DEFAULT 'current',
	location VARCHAR(255) NOT NULL,
	description TEXT NOT NULL,
	cover_image VARCHAR(255) NOT NULL,
	created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;");

$pdo->exec("CREATE TABLE IF NOT EXISTS project_images (
	id INT AUTO_INCREMENT PRIMARY KEY,
	project_id INT NOT NULL,
	image_path VARCHAR(255) NOT NULL,
	created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
	FOREIGN KEY (project_id) REFERENCES projects(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;");


// Create upload directories with proper error handling
$rootDir = realpath(__DIR__ . '/..');
if ($rootDir === false) {
	$rootDir = dirname(__DIR__);
}
$uploadsRoot = $rootDir . DIRECTORY_SEPARATOR . 'uploads';
if (!is_dir($uploadsRoot)) {
	if (!@mkdir($uploadsRoot, 0755, true)) {
		$errorMessage = 'Failed to create uploads directory. Please check folder permissions.';
	}
}
if (!is_writable($uploadsRoot)) {
	$errorMessage = 'Uploads directory is not writable. Please check folder permissions.';
}
$projectsDir = $uploadsRoot . DIRECTORY_SEPARATOR . 'projects';
if (!is_dir($projectsDir)) {
	if (!@mkdir($projectsDir, 0755, true)) {
		$errorMessage = 'Failed to create projects directory. Please check folder permissions.';
	}
}

// Helper functions
function process_single_image(array $file, string $uploadsDir, int $maxSize = 25): ?string {
	if (!isset($file['error']) || is_array($file['error']) || $file['error'] !== UPLOAD_ERR_OK) {
		return null;
	}
	
	if (empty($file['tmp_name']) || !is_uploaded_file($file['tmp_name'])) {
		return null;
	}
	
	if ($file['size'] > $maxSize * 1024 * 1024 || $file['size'] === 0) {
		return null;
	}
	
	if (!is_writable($uploadsDir)) {
		return null;
	}
	
	// Get MIME type
	$mime = null;
	if (class_exists('finfo')) {
		try {
			$finfo = new finfo(FILEINFO_MIME_TYPE);
			$mime = $finfo->file($file['tmp_name']);
		} catch (Exception $e) {
			$mime = null;
		}
	}
	
	// Fallback to extension-based detection
	if (!$mime) {
		$ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
		$mimeMap = ['jpg' => 'image/jpeg', 'jpeg' => 'image/jpeg', 'png' => 'image/png', 'webp' => 'image/webp'];
		$mime = $mimeMap[$ext] ?? null;
	}
	
	$allowed = ['image/jpeg' => 'jpg', 'image/jpg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp'];
	if (!isset($allowed[$mime])) {
		return null;
	}
	
	$ext = $allowed[$mime];
	$uniqueName = time() . '_' . bin2hex(random_bytes(8)) . '.' . $ext;
	$targetPath = $uploadsDir . DIRECTORY_SEPARATOR . $uniqueName;
	
	if (!move_uploaded_file($file['tmp_name'], $targetPath)) {
		return null;
	}
	
	// Compress image if JPG or PNG
	if (in_array($ext, ['jpg', 'jpeg', 'png']) && extension_loaded('gd')) {
		compress_image_file($targetPath, $ext);
	}
	
	$dirName = basename($uploadsDir);
	return 'uploads/' . $dirName . '/' . $uniqueName;
}

function compress_image_file(string $filePath, string $ext, int $quality = 80): bool {
	if (!extension_loaded('gd') || !file_exists($filePath)) {
		return false;
	}
	
	try {
		$image = null;
		switch (strtolower($ext)) {
			case 'jpg':
			case 'jpeg':
				$image = @imagecreatefromjpeg($filePath);
				if ($image !== false) {
					@imagejpeg($image, $filePath, $quality);
					@imagedestroy($image);
					return true;
				}
				break;
			case 'png':
				$image = @imagecreatefrompng($filePath);
				if ($image !== false) {
					@imagealphablending($image, false);
					@imagesavealpha($image, true);
					$pngQuality = (int)(9 - ($quality / 100) * 9);
					@imagepng($image, $filePath, $pngQuality);
					@imagedestroy($image);
					return true;
				}
				break;
		}
		return false;
	} catch (Exception $e) {
		return false;
	}
}

$successMessage = '';
$errorMessage = '';

// Handle new project creation
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['create_project'])) {
	try {
		$title = trim($_POST['title'] ?? '');
		$category = trim($_POST['category'] ?? 'current');
		$location = trim($_POST['location'] ?? '');
		$description = trim($_POST['description'] ?? '');

		if ($title === '' || $location === '' || $description === '') {
			$errorMessage = 'Please fill in all required fields.';
		} else {
			// Process cover image
			$coverPath = null;
			if (isset($_FILES['cover']) && is_uploaded_file($_FILES['cover']['tmp_name'])) {
				$coverFile = [
					'name' => $_FILES['cover']['name'] ?? '',
					'type' => $_FILES['cover']['type'] ?? '',
					'tmp_name' => $_FILES['cover']['tmp_name'] ?? '',
					'error' => $_FILES['cover']['error'] ?? UPLOAD_ERR_NO_FILE,
					'size' => $_FILES['cover']['size'] ?? 0,
				];
				$coverPath = process_single_image($coverFile, $projectsDir, 25);
			}
			
			if (!$coverPath) {
				$errorMessage = 'Cover photo is required and must be a JPG/PNG/WEBP up to 25MB.';
			} else {
				// Insert project
				$stmt = $pdo->prepare("INSERT INTO projects (title, category, location, description, cover_image) VALUES (?, ?, ?, ?, ?)");
				$stmt->execute([$title, $category, $location, $description, $coverPath]);
				$projectId = (int)$pdo->lastInsertId();

				// Process multiple images one-by-one with connection keep-alive
				$uploadedPaths = [];
				$failedFiles = [];
				
				if (isset($_FILES['project_images']) && is_array($_FILES['project_images']['name'])) {
					$totalFiles = count($_FILES['project_images']['name']);
					
					// Send initial progress to keep connection alive
					if (!headers_sent()) {
						header('Content-Type: text/html; charset=utf-8');
						header('Cache-Control: no-cache');
						header('Connection: keep-alive');
					}
					
					// Send initial buffer to establish connection
					echo str_repeat(' ', 1024); // Send 1KB to start connection
					if (ob_get_level()) {
						@ob_flush();
					}
					@flush();
					
					// Process each file one-by-one (streaming-safe)
					for ($i = 0; $i < $totalFiles; $i++) {
						// Reset execution time limit every 50 files
						if ($i > 0 && $i % 50 === 0) {
							@set_time_limit(600);
						}
						
						$file = [
							'name' => $_FILES['project_images']['name'][$i] ?? '',
							'type' => $_FILES['project_images']['type'][$i] ?? '',
							'tmp_name' => $_FILES['project_images']['tmp_name'][$i] ?? '',
							'error' => $_FILES['project_images']['error'][$i] ?? UPLOAD_ERR_NO_FILE,
							'size' => $_FILES['project_images']['size'][$i] ?? 0,
						];
						
						// Skip invalid or empty files
						if ($file['error'] !== UPLOAD_ERR_OK || empty($file['tmp_name']) || !is_uploaded_file($file['tmp_name'])) {
							$failedFiles[] = $file['name'] ?: "File #" . ($i + 1);
							continue;
						}
						
						// Process single image (streaming-safe move_uploaded_file)
						$imgPath = process_single_image($file, $projectsDir, 25);
						
						if ($imgPath) {
							$uploadedPaths[] = $imgPath;
						} else {
							$failedFiles[] = $file['name'] ?: "File #" . ($i + 1);
						}
						
						// Keep connection alive - send progress every 5 files
						if (($i + 1) % 5 === 0) {
							// Send minimal output to keep connection alive
							echo ' ';
							if (ob_get_level()) {
								@ob_flush();
							}
							@flush();
						}
						
						// Clear any memory used
						if (($i + 1) % 20 === 0 && function_exists('gc_collect_cycles')) {
							@gc_collect_cycles();
						}
					}
					
					// Final flush before database operations
					if (ob_get_level()) {
						@ob_end_clean();
					}
				}
				
				// Bulk insert all image paths using single prepared statement (prevent MySQL timeout)
				if (!empty($uploadedPaths)) {
					try {
						// Set MySQL timeout for long operations
						$pdo->setAttribute(PDO::ATTR_TIMEOUT, 600);
						$pdo->exec("SET SESSION wait_timeout = 600");
						$pdo->exec("SET SESSION interactive_timeout = 600");
						
						$pdo->beginTransaction();
						$stmtImg = $pdo->prepare("INSERT INTO project_images (project_id, image_path) VALUES (?, ?)");
						
						// Insert in chunks to prevent memory issues
						$chunkSize = 100;
						$chunks = array_chunk($uploadedPaths, $chunkSize);
						
						foreach ($chunks as $chunk) {
							foreach ($chunk as $imgPath) {
								$stmtImg->execute([$projectId, $imgPath]);
							}
							// Small delay between chunks
							usleep(10000); // 0.01 seconds
						}
						
						$pdo->commit();
					} catch (PDOException $e) {
						if ($pdo->inTransaction()) {
							$pdo->rollBack();
						}
						$failedFiles = array_merge($failedFiles, array_map(function($p) {
							return basename($p);
						}, $uploadedPaths));
						$uploadedPaths = [];
						error_log("Database error during bulk insert: " . $e->getMessage());
					}
				}
				
				$uploadedCount = count($uploadedPaths);
				$failedCount = count($failedFiles);
				
				$successMessage = 'Project created successfully with ' . $uploadedCount . ' image(s) uploaded.';
				if ($failedCount > 0) {
					$successMessage .= ' ' . $failedCount . ' image(s) failed: ' . implode(', ', array_slice($failedFiles, 0, 5));
					if ($failedCount > 5) {
						$successMessage .= ' and ' . ($failedCount - 5) . ' more.';
					}
				}
			}
		}
	} catch (Exception $e) {
		$errorMessage = 'An error occurred while creating the project: ' . htmlspecialchars($e->getMessage());
	}
}


// Refresh statistics after form submission
if ($successMessage) {
	// Use JavaScript redirect with loading overlay to prevent blank screen
	// This keeps the upload modal visible during redirect
	echo '<!DOCTYPE html><html><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0"><title>Processing...</title>';
	echo '<style>';
	echo '*{margin:0;padding:0;box-sizing:border-box;}';
	echo 'body{background:linear-gradient(135deg, rgba(102, 126, 234, 0.95) 0%, rgba(118, 75, 162, 0.95) 100%);min-height:100vh;display:flex;align-items:center;justify-content:center;font-family:-apple-system,BlinkMacSystemFont,"Segoe UI",Roboto,sans-serif;}';
	echo '.redirect-loader{text-align:center;color:#fff;}';
	echo '.redirect-spinner{width:60px;height:60px;border:4px solid rgba(255,255,255,0.3);border-top-color:#fff;border-radius:50%;animation:spin 0.8s linear infinite;margin:0 auto 20px;}';
	echo '.redirect-text{font-size:1.2rem;font-weight:600;margin-bottom:8px;}';
	echo '.redirect-subtext{font-size:0.9rem;opacity:0.9;}';
	echo '@keyframes spin{to{transform:rotate(360deg);}}';
	echo '</style>';
	echo '</head><body>';
	echo '<div class="redirect-loader">';
	echo '<div class="redirect-spinner"></div>';
	echo '<div class="redirect-text">Processing Upload...</div>';
	echo '<div class="redirect-subtext">Please wait while we redirect...</div>';
	echo '</div>';
	echo '<script>';
	echo 'setTimeout(function(){';
	echo '  window.location.href = "xtztragiikz.php?success=' . urlencode($successMessage) . '";';
	echo '}, 100);';
	echo '</script>';
	echo '</body></html>';
	exit;
}

// Get statistics
try {
	$totalProjects = $pdo->query("SELECT COUNT(*) FROM projects")->fetchColumn() ?: 0;
} catch (Exception $e) {
	$totalProjects = 0;
}


try {
	$totalInquiries = $pdo->query("SELECT COUNT(*) FROM inquiries")->fetchColumn() ?: 0;
} catch (Exception $e) {
	$totalInquiries = 0;
}

try {
	$unreadInquiries = $pdo->query("SELECT COUNT(*) FROM inquiries WHERE status = 'unread'")->fetchColumn() ?: 0;
} catch (Exception $e) {
	$unreadInquiries = 0;
}

$displaySuccess = isset($_GET['success']) ? htmlspecialchars($_GET['success']) : '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Safa Formwork Admin - Dashboard</title>
	<link rel="stylesheet" href="../css/global.css">
	<style>
		* {
			margin: 0;
			padding: 0;
			box-sizing: border-box;
		}

		body {
			background: #f4f5f7;
			font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
			color: #1a1a1a;
			line-height: 1.6;
		}

		/* ========== PAGE LOADER (Only shows during initial page load) ========== */
		.page-loader {
			position: fixed;
			top: 0;
			left: 0;
			width: 100%;
			height: 100%;
			background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
			display: flex;
			flex-direction: column;
			align-items: center;
			justify-content: center;
			z-index: 9999;
			transition: opacity 0.5s ease, visibility 0.5s ease;
		}

		.page-loader.hidden {
			opacity: 0;
			visibility: hidden;
		}

		.loader-content {
			text-align: center;
			color: #fff;
		}

		.loader-logo {
			width: 80px;
			height: 80px;
			margin: 0 auto 24px;
			background: rgba(255, 255, 255, 0.2);
			border-radius: 20px;
			display: flex;
			align-items: center;
			justify-content: center;
			font-size: 40px;
			backdrop-filter: blur(10px);
			box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
		}

		.loader-spinner {
			width: 50px;
			height: 50px;
			border: 4px solid rgba(255, 255, 255, 0.3);
			border-top-color: #fff;
			border-radius: 50%;
			animation: spin 0.8s linear infinite;
			margin: 0 auto 20px;
		}

		@keyframes spin {
			to { transform: rotate(360deg); }
		}

		.loader-text {
			font-size: 1.1rem;
			font-weight: 600;
			margin-bottom: 8px;
			letter-spacing: 0.5px;
		}

		.loader-subtext {
			font-size: 0.9rem;
			opacity: 0.9;
			font-weight: 400;
		}

		.wrapper {
			min-height: 100vh;
			padding: 20px;
			max-width: 1400px;
			margin: 0 auto;
		}

		/* ========== HEADER/TOPBAR ========== */
		.topbar {
			background: #fff;
			border-radius: 16px;
			box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
			padding: 20px 28px;
			margin-bottom: 24px;
			display: flex;
			align-items: center;
			justify-content: space-between;
			flex-wrap: wrap;
			gap: 16px;
			position: relative;
		}

		.brand {
			display: flex;
			align-items: center;
			gap: 12px;
		}

		.brand img {
			height: 40px;
			width: auto;
			display: block;
		}

		.brand strong {
			font-size: 1.4rem;
			font-weight: 700;
			color: #c2925f;
			letter-spacing: -0.3px;
		}

		.menu {
			display: flex;
			align-items: center;
			gap: 8px;
			flex-wrap: wrap;
		}

		/* Hamburger Menu Button */
		.hamburger {
			display: none;
			flex-direction: column;
			justify-content: space-around;
			width: 30px;
			height: 30px;
			background: transparent;
			border: none;
			cursor: pointer;
			padding: 0;
			z-index: 1001;
			position: relative;
		}

		.hamburger span {
			width: 100%;
			height: 3px;
			background: #1a1a1a;
			border-radius: 3px;
			transition: all 0.3s ease;
			transform-origin: center;
			display: block;
		}

		.hamburger.active span:nth-child(1) {
			transform: rotate(45deg) translate(8px, 8px);
		}

		.hamburger.active span:nth-child(2) {
			opacity: 0;
		}

		.hamburger.active span:nth-child(3) {
			transform: rotate(-45deg) translate(7px, -7px);
		}

		/* Mobile Menu Backdrop */
		.menu-backdrop {
			display: none;
			position: fixed;
			top: 0;
			left: 0;
			width: 100%;
			height: 100%;
			background: rgba(0, 0, 0, 0.5);
			z-index: 999;
			opacity: 0;
			transition: opacity 0.3s ease;
		}

		.menu-backdrop.active {
			display: block;
			opacity: 1;
		}

		.menu a {
			display: inline-block;
			padding: 12px 20px;
			background: #fff;
			border: 2px solid #e0e0e0;
			border-radius: 10px;
			text-decoration: none;
			color: #1a1a1a;
			font-weight: 500;
			transition: all 0.2s ease;
		}

		.menu a.active {
			background: #c2925f;
			color: #fff;
			border-color: #c2925f;
			box-shadow: 0 4px 12px rgba(194, 146, 95, 0.3);
		}

		.menu a:not(.active):hover {
			border-color: #c2925f;
			color: #c2925f;
		}

		.menu a.logout {
			color: #d32f2f;
			border-color: #ffebee;
		}

		.menu a.logout:hover {
			background: #ffebee;
			border-color: #d32f2f;
		}

		.welcome-msg {
			text-align: right;
			color: #666;
			font-size: 0.95rem;
			margin-top: 8px;
			width: 100%;
		}

		/* ========== MESSAGES ========== */
		.message {
			max-width: 1100px;
			margin: 0 auto 20px;
			padding: 16px 20px;
			border-radius: 12px;
			font-weight: 500;
		}

		.message.success {
			background: #e8f5e9;
			color: #2e7d32;
			border: 1px solid #c8e6c9;
		}

		.message.error {
			background: #ffebee;
			color: #c62828;
			border: 1px solid #ffcdd2;
		}

		/* ========== DASHBOARD TITLE ========== */
		.dashboard-title {
			font-size: 2rem;
			font-weight: 700;
			color: #1a1a1a;
			margin-bottom: 28px;
			text-align: center;
		}

		/* ========== STATISTICS CARDS ========== */
		.stats-grid {
			display: grid;
			grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
			gap: 20px;
			margin-bottom: 32px;
		}

		.stat-card {
			background: #fff;
			border-radius: 16px;
			padding: 24px;
			box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
			display: flex;
			align-items: center;
			gap: 16px;
			transition: transform 0.2s ease, box-shadow 0.2s ease;
		}

		.stat-card:hover {
			transform: translateY(-2px);
			box-shadow: 0 8px 30px rgba(0, 0, 0, 0.12);
		}

		.stat-card.highlight {
			border: 2px solid #c2925f;
		}

		.stat-icon {
			width: 60px;
			height: 60px;
			border-radius: 12px;
			display: flex;
			align-items: center;
			justify-content: center;
			flex-shrink: 0;
			font-size: 28px;
		}

		.stat-icon.folder {
			background: #fff3cd;
			color: #c2925f;
		}


		.stat-icon.inquiries {
			background: #f3e5f5;
			color: #9c27b0;
		}

		.stat-icon.bell {
			background: #fff3cd;
			color: #c2925f;
		}

		.stat-content {
			flex: 1;
		}

		.stat-label {
			font-size: 0.9rem;
			color: #666;
			margin-bottom: 4px;
		}

		.stat-value {
			font-size: 2rem;
			font-weight: 700;
			color: #1a1a1a;
		}

		/* ========== QUICK ACTIONS SECTION ========== */
		.quick-actions-title {
			font-size: 1.5rem;
			font-weight: 700;
			color: #1a1a1a;
			margin-bottom: 20px;
		}

		.actions-grid {
			display: grid;
			grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
			gap: 20px;
		}

		.action-card {
			background: #fff;
			border-radius: 16px;
			padding: 28px;
			box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
			text-align: center;
			transition: transform 0.2s ease, box-shadow 0.2s ease;
			cursor: pointer;
		}

		.action-card:hover {
			transform: translateY(-4px);
			box-shadow: 0 8px 30px rgba(0, 0, 0, 0.12);
		}

		.action-icon {
			width: 80px;
			height: 80px;
			border-radius: 50%;
			margin: 0 auto 20px;
			display: flex;
			align-items: center;
			justify-content: center;
			font-size: 36px;
		}

		.action-icon.plus {
			background: #f3e5f5;
			color: #9c27b0;
		}


		.action-icon.inquiries {
			background: #f3e5f5;
			color: #9c27b0;
		}

		.action-card h3 {
			font-size: 1.2rem;
			font-weight: 600;
			color: #1a1a1a;
			margin-bottom: 8px;
		}

		.action-card p {
			font-size: 0.9rem;
			color: #666;
			margin: 0;
		}

		/* ========== MODAL OVERLAY ========== */
		.modal-overlay {
			display: none;
			position: fixed;
			top: 0;
			left: 0;
			width: 100%;
			height: 100%;
			background: rgba(0, 0, 0, 0.5);
			z-index: 1000;
			backdrop-filter: blur(4px);
			align-items: center;
			justify-content: center;
			padding: 20px;
			overflow-y: auto;
		}

		.modal-overlay.active {
			display: flex;
		}

		.modal-content {
			background: #fff;
			border-radius: 20px;
			padding: 32px;
			max-width: 600px;
			width: 100%;
			max-height: 90vh;
			overflow-y: auto;
			box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
			position: relative;
		}

		.modal-header {
			display: flex;
			align-items: center;
			justify-content: space-between;
			margin-bottom: 24px;
		}

		.modal-header h2 {
			font-size: 1.5rem;
			font-weight: 700;
			color: #1a1a1a;
		}

		.close-btn {
			background: none;
			border: none;
			font-size: 28px;
			color: #666;
			cursor: pointer;
			width: 32px;
			height: 32px;
			display: flex;
			align-items: center;
			justify-content: center;
			border-radius: 8px;
			transition: all 0.2s ease;
		}

		.close-btn:hover {
			background: #f5f5f5;
			color: #1a1a1a;
		}

		.form-group {
			margin-bottom: 20px;
		}

		.form-group label {
			display: block;
			font-weight: 600;
			color: #333;
			margin-bottom: 8px;
			font-size: 0.95rem;
		}

		.form-group input[type="text"],
		.form-group textarea,
		.form-group select {
			width: 100%;
			padding: 12px 16px;
			border: 2px solid #e0e0e0;
			border-radius: 10px;
			font-size: 1rem;
			outline: none;
			transition: border-color 0.2s ease;
			font-family: inherit;
		}

		.form-group input[type="text"]:focus,
		.form-group textarea:focus,
		.form-group select:focus {
			border-color: #c2925f;
		}

		.form-group textarea {
			min-height: 120px;
			resize: vertical;
		}

		.form-group input[type="file"] {
			width: 100%;
			padding: 10px;
			border: 2px dashed #e0e0e0;
			border-radius: 10px;
			background: #fafafa;
			cursor: pointer;
			transition: border-color 0.2s ease;
		}

		.form-group input[type="file"]:hover {
			border-color: #c2925f;
		}

		.file-count {
			margin-top: 8px;
			font-size: 0.9rem;
			color: #666;
		}

		.file-count.success {
			color: #2e7d32;
		}

		.file-count.warning {
			color: #f57c00;
		}

		.form-actions {
			display: flex;
			gap: 12px;
			margin-top: 24px;
		}

		.btn {
			padding: 12px 24px;
			border: none;
			border-radius: 10px;
			font-size: 1rem;
			font-weight: 600;
			cursor: pointer;
			transition: all 0.2s ease;
			font-family: inherit;
		}

		.btn-primary {
			background: linear-gradient(135deg, #E0B27C, #C89A60);
			color: #fff;
			box-shadow: 0 4px 12px rgba(200, 154, 96, 0.3);
		}

		.btn-primary:hover {
			background: linear-gradient(135deg, #D4A574, #B88950);
			box-shadow: 0 6px 20px rgba(200, 154, 96, 0.4);
		}

		.btn-secondary {
			background: #f5f5f5;
			color: #666;
		}

		.btn-secondary:hover {
			background: #e0e0e0;
		}

		.note {
			font-size: 0.85rem;
			color: #666;
			margin-top: 8px;
		}

		/* ========== CONFIRMATION MODAL ========== */
		.confirm-modal-overlay {
			display: none;
			position: fixed;
			top: 0;
			left: 0;
			width: 100%;
			height: 100%;
			background: rgba(0, 0, 0, 0.6);
			backdrop-filter: blur(4px);
			z-index: 2000;
			align-items: center;
			justify-content: center;
			padding: 20px;
			opacity: 0;
			transition: opacity 0.3s ease;
		}

		.confirm-modal-overlay.active {
			display: flex;
			opacity: 1;
		}

		.confirm-modal {
			background: #fff;
			border-radius: 20px;
			padding: 0;
			max-width: 480px;
			width: 100%;
			box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
			transform: scale(0.9);
			transition: transform 0.3s ease;
			overflow: hidden;
		}

		.confirm-modal-overlay.active .confirm-modal {
			transform: scale(1);
		}

		.confirm-modal-header {
			padding: 28px 32px 20px;
			border-bottom: 1px solid #f0f0f0;
		}

		.confirm-modal-icon {
			width: 64px;
			height: 64px;
			border-radius: 50%;
			margin: 0 auto 20px;
			display: flex;
			align-items: center;
			justify-content: center;
			font-size: 32px;
		}

		.confirm-modal-icon.warning {
			background: linear-gradient(135deg, #fff3cd, #ffe082);
			color: #856404;
		}

		.confirm-modal-icon.danger {
			background: linear-gradient(135deg, #ffebee, #ffcdd2);
			color: #c62828;
		}

		.confirm-modal-icon.info {
			background: linear-gradient(135deg, #e3f2fd, #bbdefb);
			color: #1565c0;
		}

		.confirm-modal-title {
			font-size: 1.5rem;
			font-weight: 700;
			color: #1a1a1a;
			text-align: center;
			margin-bottom: 12px;
		}

		.confirm-modal-message {
			font-size: 1rem;
			color: #666;
			text-align: center;
			line-height: 1.6;
			margin: 0;
		}

		.confirm-modal-actions {
			padding: 24px 32px 32px;
			display: flex;
			gap: 12px;
			justify-content: center;
		}

		.confirm-btn {
			padding: 14px 32px;
			border: none;
			border-radius: 12px;
			font-size: 1rem;
			font-weight: 600;
			cursor: pointer;
			transition: all 0.2s ease;
			min-width: 120px;
		}

		.confirm-btn-yes {
			background: linear-gradient(135deg, #E0B27C, #C89A60);
			color: #fff;
			box-shadow: 0 4px 12px rgba(200, 154, 96, 0.3);
		}

		.confirm-btn-yes:hover {
			background: linear-gradient(135deg, #D4A574, #B88950);
			box-shadow: 0 6px 20px rgba(200, 154, 96, 0.4);
			transform: translateY(-2px);
		}

		.confirm-btn-no {
			background: #f5f5f5;
			color: #666;
			border: 2px solid #e0e0e0;
		}

		.confirm-btn-no:hover {
			background: #e0e0e0;
			border-color: #d0d0d0;
		}

		.confirm-btn-danger {
			background: linear-gradient(135deg, #f44336, #d32f2f);
			color: #fff;
			box-shadow: 0 4px 12px rgba(244, 67, 54, 0.3);
		}

		.confirm-btn-danger:hover {
			background: linear-gradient(135deg, #e53935, #c62828);
			box-shadow: 0 6px 20px rgba(244, 67, 54, 0.4);
			transform: translateY(-2px);
		}

		/* ========== UPLOAD PROGRESS MODAL - ENHANCED ========== */
		.upload-progress-overlay {
			display: none;
			position: fixed;
			top: 0;
			left: 0;
			width: 100%;
			height: 100%;
			background: linear-gradient(135deg, rgba(102, 126, 234, 0.95) 0%, rgba(118, 75, 162, 0.95) 100%);
			backdrop-filter: blur(8px);
			-webkit-backdrop-filter: blur(8px);
			z-index: 99999;
			align-items: center;
			justify-content: center;
			padding: 20px;
			opacity: 0;
			transition: opacity 0.4s cubic-bezier(0.4, 0, 0.2, 1);
		}

		.upload-progress-overlay.active {
			display: flex !important;
			opacity: 1;
		}

		.upload-progress-modal {
			background: #ffffff;
			border-radius: 24px;
			padding: 0;
			max-width: 560px;
			width: 100%;
			box-shadow: 0 25px 80px rgba(0, 0, 0, 0.3), 0 0 0 1px rgba(255, 255, 255, 0.1);
			transform: scale(0.95) translateY(20px);
			transition: all 0.4s cubic-bezier(0.34, 1.56, 0.64, 1);
			overflow: hidden;
			position: relative;
		}

		.upload-progress-overlay.active .upload-progress-modal {
			transform: scale(1) translateY(0);
		}

		.upload-progress-modal::before {
			content: '';
			position: absolute;
			top: 0;
			left: 0;
			right: 0;
			height: 4px;
			background: linear-gradient(90deg, #667eea 0%, #764ba2 50%, #667eea 100%);
			background-size: 200% 100%;
			animation: progress-line 2s linear infinite;
		}

		@keyframes progress-line {
			0% { background-position: 200% 0; }
			100% { background-position: -200% 0; }
		}

		.upload-progress-header {
			padding: 40px 40px 28px;
			background: linear-gradient(135deg, #f8f9ff 0%, #ffffff 100%);
			border-bottom: 1px solid rgba(102, 126, 234, 0.1);
			text-align: center;
			position: relative;
		}

		.upload-progress-icon {
			width: 100px;
			height: 100px;
			border-radius: 50%;
			margin: 0 auto 24px;
			display: flex;
			align-items: center;
			justify-content: center;
			font-size: 48px;
			background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
			box-shadow: 0 12px 40px rgba(102, 126, 234, 0.4), 
			            0 0 0 8px rgba(102, 126, 234, 0.1),
			            inset 0 2px 4px rgba(255, 255, 255, 0.2);
			position: relative;
			animation: icon-pulse 2s ease-in-out infinite;
		}

		@keyframes icon-pulse {
			0%, 100% { 
				transform: scale(1);
				box-shadow: 0 12px 40px rgba(102, 126, 234, 0.4), 
				            0 0 0 8px rgba(102, 126, 234, 0.1),
				            inset 0 2px 4px rgba(255, 255, 255, 0.2);
			}
			50% { 
				transform: scale(1.05);
				box-shadow: 0 16px 50px rgba(102, 126, 234, 0.5), 
				            0 0 0 12px rgba(102, 126, 234, 0.15),
				            inset 0 2px 4px rgba(255, 255, 255, 0.2);
			}
		}

		.upload-progress-icon::before {
			content: '';
			position: absolute;
			width: 100%;
			height: 100%;
			border-radius: 50%;
			border: 4px solid transparent;
			border-top-color: rgba(255, 255, 255, 0.9);
			border-right-color: rgba(255, 255, 255, 0.5);
			animation: spin-icon 1.2s linear infinite;
		}

		@keyframes spin-icon {
			to { transform: rotate(360deg); }
		}

		.upload-progress-icon.success {
			background: linear-gradient(135deg, #10b981 0%, #059669 100%);
			box-shadow: 0 12px 40px rgba(16, 185, 129, 0.4);
			animation: success-bounce 0.6s ease-out;
		}

		.upload-progress-icon.success::before {
			display: none;
		}

		@keyframes success-bounce {
			0% { transform: scale(0.8); }
			50% { transform: scale(1.15); }
			100% { transform: scale(1); }
		}

		.upload-progress-title {
			font-size: 1.75rem;
			font-weight: 700;
			color: #1a1a1a;
			margin-bottom: 12px;
			letter-spacing: -0.5px;
		}

		.upload-progress-message {
			font-size: 1rem;
			color: #4b5563;
			line-height: 1.7;
			margin: 0;
			font-weight: 500;
		}

		.upload-progress-content {
			padding: 36px 40px;
			background: #ffffff;
		}

		.progress-bar-container {
			margin-bottom: 24px;
		}

		.progress-bar {
			width: 100%;
			height: 16px;
			background: #e5e7eb;
			border-radius: 12px;
			overflow: hidden;
			margin-bottom: 16px;
			box-shadow: inset 0 2px 4px rgba(0, 0, 0, 0.06);
			position: relative;
		}

		.progress-bar-fill {
			height: 100%;
			background: linear-gradient(90deg, #667eea 0%, #764ba2 50%, #667eea 100%);
			background-size: 200% 100%;
			border-radius: 12px;
			width: 0%;
			transition: width 0.5s cubic-bezier(0.4, 0, 0.2, 1);
			position: relative;
			box-shadow: 0 2px 12px rgba(102, 126, 234, 0.4),
			            inset 0 1px 0 rgba(255, 255, 255, 0.3);
			animation: progress-gradient 2s linear infinite;
		}

		@keyframes progress-gradient {
			0% { background-position: 200% 0; }
			100% { background-position: -200% 0; }
		}

		.progress-bar-fill.complete {
			background: linear-gradient(90deg, #10b981 0%, #059669 100%);
			animation: none;
		}

		.progress-bar-fill::after {
			content: '';
			position: absolute;
			top: 0;
			left: 0;
			bottom: 0;
			right: 0;
			background: linear-gradient(90deg, transparent, rgba(255,255,255,0.3), transparent);
			animation: shimmer 2s infinite;
		}

		@keyframes shimmer {
			0% { transform: translateX(-100%); }
			100% { transform: translateX(100%); }
		}

		.progress-text {
			text-align: center;
			font-size: 1.25rem;
			font-weight: 700;
			color: #667eea;
			letter-spacing: 0.5px;
		}

		.progress-text.complete {
			color: #10b981;
		}

		.upload-stats {
			display: grid;
			grid-template-columns: 1fr;
			gap: 16px;
			margin-top: 24px;
		}

		.upload-stat-item {
			display: flex;
			justify-content: space-between;
			align-items: center;
			padding: 12px 16px;
			background: #f8f9fa;
			border-radius: 8px;
		}

		.stat-label {
			font-size: 0.9rem;
			color: #666;
		}

		.stat-value {
			font-size: 1rem;
			font-weight: 600;
			color: #1a1a1a;
		}

		.upload-progress-note {
			padding: 24px 40px;
			background: linear-gradient(135deg, #f8f9ff 0%, #f0f4ff 100%);
			border-top: 1px solid rgba(102, 126, 234, 0.1);
			text-align: center;
		}

		.upload-progress-note p {
			font-size: 0.95rem;
			color: #4b5563;
			margin: 0;
			line-height: 1.6;
			font-weight: 500;
		}

		.upload-progress-note.success {
			background: linear-gradient(135deg, #ecfdf5 0%, #d1fae5 100%);
			border-top-color: rgba(16, 185, 129, 0.2);
		}

		.upload-progress-note.success p {
			color: #059669;
		}

		/* ========== RESPONSIVE DESIGN ========== */
		@media (max-width: 768px) {
			.wrapper {
				padding: 12px;
			}

			.topbar {
				padding: 16px;
				flex-wrap: nowrap;
			}

			.brand {
				flex: 1;
			}

			.brand strong {
				font-size: 1.2rem;
			}

			.hamburger {
				display: flex;
			}

			.menu {
				position: fixed;
				top: 0;
				right: -100%;
				width: 280px;
				height: 100vh;
				background: #fff;
				box-shadow: -4px 0 20px rgba(0, 0, 0, 0.15);
				flex-direction: column;
				align-items: stretch;
				padding: 80px 20px 20px;
				gap: 12px;
				transition: right 0.3s ease;
				z-index: 1000;
				overflow-y: auto;
			}

			.menu.active {
				right: 0;
			}

			.menu a {
				padding: 14px 18px;
				font-size: 1rem;
				width: 100%;
				text-align: left;
				border-radius: 10px;
				margin: 0;
				background: #fff;
				border: 2px solid #e0e0e0;
				display: block;
			}

			.menu a.active {
				background: #c2925f;
				color: #fff;
				border-color: #c2925f;
			}

			.welcome-msg {
				display: none;
			}

			.dashboard-title {
				font-size: 1.5rem;
				margin-bottom: 20px;
			}

			.stats-grid {
				grid-template-columns: 1fr;
				gap: 16px;
				margin-bottom: 24px;
			}

			.stat-card {
				padding: 20px;
			}

			.stat-icon {
				width: 50px;
				height: 50px;
				font-size: 24px;
			}

			.stat-value {
				font-size: 1.75rem;
			}

			.quick-actions-title {
				font-size: 1.3rem;
				margin-bottom: 16px;
			}

			.actions-grid {
				grid-template-columns: 1fr;
				gap: 16px;
			}

			.action-card {
				padding: 24px;
			}

			.action-icon {
				width: 70px;
				height: 70px;
				font-size: 32px;
			}

			.modal-content {
				padding: 24px;
				margin: 10px;
			}

			.confirm-modal {
				max-width: 100%;
				margin: 10px;
			}

			.confirm-modal-header {
				padding: 24px 20px 16px;
			}

			.confirm-modal-icon {
				width: 56px;
				height: 56px;
				font-size: 28px;
				margin-bottom: 16px;
			}

			.confirm-modal-title {
				font-size: 1.3rem;
			}

			.confirm-modal-message {
				font-size: 0.95rem;
			}

			.confirm-modal-actions {
				padding: 20px;
				flex-direction: column;
			}

			.confirm-btn {
				width: 100%;
				min-width: auto;
			}
		}

		@media (max-width: 480px) {
			.brand strong {
				font-size: 1rem;
			}

			.menu {
				width: 280px;
			}

			.menu a {
				padding: 14px 18px;
				font-size: 1rem;
			}

			.dashboard-title {
				font-size: 1.3rem;
			}

			.stat-card {
				padding: 16px;
			}

			.action-card {
				padding: 20px;
			}

			.modal-content {
				padding: 20px;
			}
		}
	</style>
</head>
<body>
	<!-- Page Loader (Only shows during initial page load) -->
	<div class="page-loader" id="pageLoader">
		<div class="loader-content">
			<div class="loader-logo">
				<img src="../assets/logo.jpg" alt="Logo" style="width: 60px; height: auto; border-radius: 10px;" onerror="this.style.display='none'; this.parentElement.innerHTML='🔐';">
			</div>
			<div class="loader-spinner"></div>
			<div class="loader-text">Loading Dashboard</div>
			<div class="loader-subtext">Authenticating session...</div>
		</div>
	</div>

	<div class="wrapper">
		<!-- Header/Topbar -->
		<div class="topbar">
			<div class="brand">
				<img src="../assets/logo.jpg" alt="Logo" onerror="this.style.display='none'">
				<strong>Safa Formwork Admin</strong>
			</div>
			<button class="hamburger" id="hamburgerBtn" aria-label="Toggle menu">
				<span></span>
				<span></span>
				<span></span>
			</button>
			<div class="menu" id="mobileMenu">
				<a href="xtztragiikz.php" class="active">Dashboard</a>
				<a href="projects.php">Manage Projects</a>
				<a href="logout.php" class="logout">Logout</a>
			</div>
			<div class="welcome-msg">Welcome back, admin!</div>
		</div>
		<div class="menu-backdrop" id="menuBackdrop"></div>

		<?php if ($displaySuccess): ?>
			<div class="message success"><?php echo $displaySuccess; ?></div>
		<?php endif; ?>

		<?php if ($errorMessage): ?>
			<div class="message error"><?php echo htmlspecialchars($errorMessage); ?></div>
		<?php endif; ?>

		<!-- Dashboard Title -->
		<h1 class="dashboard-title">Dashboard</h1>

		<!-- Statistics Cards -->
		<div class="stats-grid">
			<div class="stat-card">
				<div class="stat-icon folder">📁</div>
				<div class="stat-content">
					<div class="stat-label">Total Projects</div>
					<div class="stat-value"><?php echo htmlspecialchars($totalProjects); ?></div>
				</div>
			</div>


			<div class="stat-card">
				<div class="stat-icon inquiries">💬</div>
				<div class="stat-content">
					<div class="stat-label">Total Inquiries</div>
					<div class="stat-value"><?php echo htmlspecialchars($totalInquiries); ?></div>
				</div>
			</div>

			<div class="stat-card highlight">
				<div class="stat-icon bell">🔔</div>
				<div class="stat-content">
					<div class="stat-label">Unread Inquiries</div>
					<div class="stat-value"><?php echo htmlspecialchars($unreadInquiries); ?></div>
				</div>
			</div>
		</div>

		<!-- Quick Actions Section -->
		<h2 class="quick-actions-title">Quick Actions</h2>
		<div class="actions-grid">
			<div class="action-card" onclick="openProjectForm()">
				<div class="action-icon plus">➕</div>
				<h3>Add New Project</h3>
				<p>Create a new project with images</p>
			</div>


			<a href="inquiries.php" style="text-decoration: none; color: inherit;">
				<div class="action-card">
					<div class="action-icon inquiries">💬</div>
					<h3>View Inquiries</h3>
					<p>Manage contact form submissions</p>
				</div>
			</a>
		</div>
	</div>

	<!-- Project Form Modal -->
	<div class="modal-overlay" id="projectModal">
		<div class="modal-content">
			<div class="modal-header">
				<h2>Add New Project</h2>
				<button class="close-btn" onclick="closeProjectForm()">&times;</button>
			</div>
			<form method="post" action="" enctype="multipart/form-data" id="projectForm">
				<div class="form-group">
					<label for="project_title">Title *</label>
					<input type="text" id="project_title" name="title" required>
				</div>
				<div class="form-group">
					<label for="project_category">Category *</label>
					<select id="project_category" name="category" required>
						<option value="current">Current</option>
						<option value="completed">Completed</option>
						<option value="past">Past</option>
					</select>
				</div>
				<div class="form-group">
					<label for="project_location">Location *</label>
					<input type="text" id="project_location" name="location" required>
				</div>
				<div class="form-group">
					<label for="project_description">Description *</label>
					<textarea id="project_description" name="description" required></textarea>
				</div>
				<div class="form-group">
					<label for="project_cover">Cover Photo *</label>
					<input type="file" id="project_cover" name="cover" accept="image/jpeg,image/png,image/webp" required>
					<div class="note">JPG/PNG/WEBP, max 25MB</div>
				</div>
				<div class="form-group">
					<label for="project_images">Additional Images</label>
					<input type="file" id="project_images" name="project_images[]" accept="image/jpeg,image/png,image/webp" multiple>
					<div class="file-count" id="projectFileCount"></div>
					<div class="note">JPG/PNG/WEBP, max 25MB each. You can select multiple images.</div>
				</div>
				<input type="hidden" name="create_project" value="1">
				<div class="form-actions">
					<button type="submit" class="btn btn-primary">Create Project</button>
					<button type="button" class="btn btn-secondary" onclick="closeProjectForm()">Cancel</button>
				</div>
			</form>
		</div>
	</div>


	<!-- Confirmation Modal -->
	<div class="confirm-modal-overlay" id="confirmModal">
		<div class="confirm-modal">
			<div class="confirm-modal-header">
				<div class="confirm-modal-icon" id="confirmIcon">⚠️</div>
				<h3 class="confirm-modal-title" id="confirmTitle">Confirm Action</h3>
				<p class="confirm-modal-message" id="confirmMessage">Are you sure you want to proceed?</p>
			</div>
			<div class="confirm-modal-actions">
				<button class="confirm-btn confirm-btn-no" id="confirmNo">No, Cancel</button>
				<button class="confirm-btn confirm-btn-yes" id="confirmYes">Yes, Continue</button>
			</div>
		</div>
	</div>

	<!-- Upload Progress Modal -->
	<div class="upload-progress-overlay" id="uploadProgressModal">
		<div class="upload-progress-modal">
			<div class="upload-progress-header">
				<div class="upload-progress-icon">📤</div>
				<h3 class="upload-progress-title">Uploading Files</h3>
				<p class="upload-progress-message" id="uploadProgressMessage">Please wait while your files are being uploaded...</p>
			</div>
			<div class="upload-progress-content">
				<div class="progress-bar-container">
					<div class="progress-bar" id="uploadProgressBar">
						<div class="progress-bar-fill" id="uploadProgressFill"></div>
					</div>
					<div class="progress-text" id="uploadProgressText">0%</div>
				</div>
				<div class="upload-stats" id="uploadStats">
					<div class="upload-stat-item">
						<span class="stat-label">Files Selected:</span>
						<span class="stat-value" id="totalFiles">0</span>
					</div>
					<div class="upload-stat-item">
						<span class="stat-label">Uploading:</span>
						<span class="stat-value" id="currentFile">-</span>
					</div>
					<div class="upload-stat-item">
						<span class="stat-label">Time Elapsed:</span>
						<span class="stat-value" id="elapsedTime">0s</span>
					</div>
				</div>
			</div>
			<div class="upload-progress-note">
				<p>⏳ This may take a few minutes for large uploads. Please do not close this window.</p>
			</div>
		</div>
	</div>

	<script>
		// Confirmation Modal System
		let confirmCallback = null;
		let confirmType = 'warning';

		function showConfirm(title, message, type, callback) {
			confirmType = type || 'warning';
			confirmCallback = callback;
			
			document.getElementById('confirmTitle').textContent = title;
			document.getElementById('confirmMessage').textContent = message;
			
			const icon = document.getElementById('confirmIcon');
			const yesBtn = document.getElementById('confirmYes');
			
			// Set icon and button style based on type
			if (type === 'danger') {
				icon.className = 'confirm-modal-icon danger';
				icon.textContent = '🗑️';
				yesBtn.className = 'confirm-btn confirm-btn-danger';
			} else if (type === 'info') {
				icon.className = 'confirm-modal-icon info';
				icon.textContent = 'ℹ️';
				yesBtn.className = 'confirm-btn confirm-btn-yes';
			} else {
				icon.className = 'confirm-modal-icon warning';
				icon.textContent = '⚠️';
				yesBtn.className = 'confirm-btn confirm-btn-yes';
			}
			
			document.getElementById('confirmModal').classList.add('active');
			document.body.style.overflow = 'hidden';
		}

		function hideConfirm() {
			document.getElementById('confirmModal').classList.remove('active');
			document.body.style.overflow = '';
			confirmCallback = null;
		}

		document.getElementById('confirmYes').addEventListener('click', function() {
			if (confirmCallback) {
				confirmCallback();
			}
			hideConfirm();
		});

		document.getElementById('confirmNo').addEventListener('click', hideConfirm);
		document.getElementById('confirmModal').addEventListener('click', function(e) {
			if (e.target === this) {
				hideConfirm();
			}
		});

		// Replace all confirm() calls
		document.addEventListener('DOMContentLoaded', function() {
			// Logout links
			const logoutLinks = document.querySelectorAll('a.logout');
			logoutLinks.forEach(link => {
				link.addEventListener('click', function(e) {
					e.preventDefault();
					const href = this.getAttribute('href');
					showConfirm('Logout', 'Are you sure you want to log out?', 'warning', function() {
						window.location.href = href;
					});
				});
			});

			// Upload Progress System
			/* ===== BEGIN REPLACED UPLOAD PROGRESS & SUBMIT HANDLER ===== */
			(function() {
				// Lightweight upload UI helpers (replace heavy simulated progress)
				function showLightweightUploadModal(totalFiles) {
					// Only show if files are actually being uploaded
					const projectForm = document.getElementById('projectForm');
					const activeForm = projectForm;
					
					if (!activeForm) return;
					
					const fileInputs = activeForm.querySelectorAll('input[type="file"]');
					let hasFiles = false;
					fileInputs.forEach(input => {
						if (input.files && input.files.length > 0) {
							hasFiles = true;
						}
					});
					
					// Only show progress modal if files are actually selected
					if (!hasFiles) {
						return; // Don't show progress if no files to upload
					}
					
					// Store upload state in sessionStorage to persist across redirects
					sessionStorage.setItem('uploadInProgress', 'true');
					sessionStorage.setItem('uploadTotalFiles', totalFiles.toString());
					
					document.getElementById('totalFiles').textContent = totalFiles;
					document.getElementById('currentFile').textContent = 'Processing...';
					document.getElementById('elapsedTime').textContent = '0s';
					const fill = document.getElementById('uploadProgressFill');
					fill.style.width = '0%';
					document.getElementById('uploadProgressText').textContent = '0%';
					document.getElementById('uploadProgressMessage').textContent = totalFiles > 1 
						? `Uploading ${totalFiles} files to server... This may take a few minutes.` 
						: 'Uploading file to server...';
					document.getElementById('uploadProgressModal').classList.add('active');
					document.body.style.overflow = 'hidden';
				}

				function showUploadSuccess() {
					const icon = document.querySelector('.upload-progress-icon');
					const title = document.querySelector('.upload-progress-title');
					const message = document.getElementById('uploadProgressMessage');
					const fill = document.getElementById('uploadProgressFill');
					const text = document.getElementById('uploadProgressText');
					
					if (icon) {
						icon.classList.add('success');
						icon.innerHTML = '✓';
					}
					if (title) title.textContent = 'Upload Complete!';
					if (message) message.textContent = 'All files have been successfully uploaded. Redirecting...';
					if (fill) {
						fill.style.width = '100%';
						fill.classList.add('complete');
					}
					if (text) {
						text.textContent = '100%';
						text.classList.add('complete');
					}
				}

				function hideLightweightUploadModal() {
					// Clear session storage
					sessionStorage.removeItem('uploadInProgress');
					sessionStorage.removeItem('uploadTotalFiles');
					
					setTimeout(() => {
						const modal = document.getElementById('uploadProgressModal');
						if (modal) {
							modal.classList.remove('active');
						}
						document.body.style.overflow = '';
						// Reset progress
						const fill = document.getElementById('uploadProgressFill');
						const text = document.getElementById('uploadProgressText');
						if (fill) {
							fill.style.width = '0%';
							fill.classList.remove('complete');
						}
						if (text) {
							text.textContent = '0%';
							text.classList.remove('complete');
						}
					}, 2000);
				}

				// Realistic progress simulation - smooth and accurate
				let smallProgressTimer = null;
				function startSmallProgressSimulation() {
					let percent = 0;
					const fill = document.getElementById('uploadProgressFill');
					const text = document.getElementById('uploadProgressText');
					if (smallProgressTimer) clearInterval(smallProgressTimer);
					
					// More realistic progress - starts slow, speeds up, slows near end
					smallProgressTimer = setInterval(() => {
						let increment;
						if (percent < 20) {
							increment = 1 + Math.random() * 1.5; // Very slow start
						} else if (percent < 50) {
							increment = 2.5 + Math.random() * 2.5; // Medium speed
						} else if (percent < 85) {
							increment = 3.5 + Math.random() * 3; // Faster middle
						} else {
							increment = 0.5 + Math.random() * 1; // Very slow near end
						}
						percent = Math.min(percent + increment, 95); // Cap at 95% until real completion
						if (fill) fill.style.width = Math.round(percent) + '%';
						if (text) text.textContent = Math.round(percent) + '%';
					}, 400);
				}
				function stopSmallProgressSimulation() {
					if (smallProgressTimer) {
						clearInterval(smallProgressTimer);
						smallProgressTimer = null;
					}
				}

				// Elapsed time updater (light)
				let uploadStartTime = null;
				let elapsedTimer = null;
				function startElapsedTimer() {
					uploadStartTime = Date.now();
					elapsedTimer = setInterval(() => {
						const elapsed = Math.floor((Date.now() - uploadStartTime) / 1000);
						const m = Math.floor(elapsed / 60);
						const s = elapsed % 60;
						document.getElementById('elapsedTime').textContent = m > 0 ? `${m}m ${s}s` : `${s}s`;
					}, 1000);
				}
				function stopElapsedTimer() {
					if (elapsedTimer) clearInterval(elapsedTimer);
					uploadStartTime = null;
				}

				// Replace form submit logic for project forms
				function setupSafeFormSubmit(formId) {
					const form = document.getElementById(formId);
					if (!form) return;
					form.addEventListener('submit', function(e) {
						const fileInput = form.querySelector('input[type="file"][multiple]');
						const fileCount = fileInput ? fileInput.files.length : 0;
						const coverInput = form.querySelector('input[type="file"]:not([multiple])');
						const coverFileCount = coverInput && coverInput.files.length > 0 ? 1 : 0;
						const totalFiles = fileCount + coverFileCount;
						
						// Get modal references for closing later
						const projectModal = document.getElementById('projectModal');
						
						// Function to close form modals
						function closeFormModals() {
							if (projectModal && projectModal.classList.contains('active')) {
								projectModal.classList.remove('active');
								document.body.style.overflow = '';
							}
						}
						
						// If large batch, show modal and submit
						if (fileCount > 50) {
							e.preventDefault();
							closeFormModals();
							showLightweightUploadModal(totalFiles);
							startSmallProgressSimulation();
							startElapsedTimer();
							setTimeout(() => {
								// Keep progress running during upload
								form.submit();
								// Progress will continue until page reloads
							}, 500);
							return;
						}

						// Small batch flow (<=50): show confirm, then realistic progress
						const createInput = form.querySelector('input[name="create_project"]');
						if (createInput) {
							e.preventDefault();
							
							// Only show confirmation if files are being uploaded
							if (totalFiles > 0) {
								const formType = 'project';
								const formTypeTitle = 'Project';
								const albumCount = fileCount > 0 ? `\n\n${fileCount} album image(s) will be uploaded.` : '';
								const coverText = coverFileCount > 0 ? (fileCount > 0 ? 'Cover photo + ' : 'Cover photo will be uploaded.') : '';
								
								showConfirm(
									`Create New ${formTypeTitle}`,
									`Are you sure you want to create this new ${formType}?${coverText}${albumCount}`,
									'info',
									function() {
										// Close form modal before showing upload progress
										closeFormModals();
										showLightweightUploadModal(totalFiles);
										startSmallProgressSimulation();
										startElapsedTimer();
										setTimeout(() => {
											// Don't stop progress - let it continue during upload
											form.submit();
											// Modal stays visible until page reloads with success
										}, 500);
									}
								);
							} else {
								// No files, just submit normally
								form.submit();
							}
						} else {
							// Not a create form, check if files are being uploaded
							if (totalFiles > 0) {
								e.preventDefault();
								closeFormModals();
								showLightweightUploadModal(totalFiles);
								startSmallProgressSimulation();
								startElapsedTimer();
								setTimeout(() => {
									// Keep progress running during upload
									form.submit();
									// Modal stays visible until page reloads
								}, 500);
							}
						}
					});
				}

				// Initialize safe submitters for both forms
				setupSafeFormSubmit('projectForm');

				// Check if page loaded with success message (only show modal if success in URL)
				const urlParams = new URLSearchParams(window.location.search);
				const successParam = urlParams.get('success');
				const uploadInProgress = sessionStorage.getItem('uploadInProgress');
				
				// Only show modal if there's a success message in URL (not on every refresh)
				if (successParam && (successParam.includes('uploaded') || successParam.includes('created'))) {
					// Clear sessionStorage since upload is complete
					sessionStorage.removeItem('uploadInProgress');
					sessionStorage.removeItem('uploadTotalFiles');
					
					// Show success state briefly
					const modal = document.getElementById('uploadProgressModal');
					if (modal) {
						modal.classList.add('active');
						document.body.style.overflow = 'hidden';
						showUploadSuccess();
						const note = document.querySelector('.upload-progress-note');
						if (note) note.classList.add('success');
						
						// Remove success parameter from URL to prevent showing on refresh
						const newUrl = window.location.pathname;
						window.history.replaceState({}, document.title, newUrl);
						
						// Hide after showing success
						setTimeout(() => {
							hideLightweightUploadModal();
						}, 2000);
					}
				} else {
					// No success message - clear any stale sessionStorage
					sessionStorage.removeItem('uploadInProgress');
					sessionStorage.removeItem('uploadTotalFiles');
				}
				
				// Only show progress if upload is actually in progress (not just on refresh)
				if (uploadInProgress === 'true' && !successParam) {
					// Upload was in progress but no success yet - continue showing progress
					const modal = document.getElementById('uploadProgressModal');
					if (modal) {
						modal.classList.add('active');
						document.body.style.overflow = 'hidden';
						const totalFiles = parseInt(sessionStorage.getItem('uploadTotalFiles') || '0');
						if (totalFiles > 0) {
							document.getElementById('totalFiles').textContent = totalFiles;
						}
						startSmallProgressSimulation();
						startElapsedTimer();
					}
				}

				// Keep modal visible during page unload/redirect
				let isNavigating = false;
				window.addEventListener('beforeunload', function() {
					const modal = document.getElementById('uploadProgressModal');
					if (modal && modal.classList.contains('active')) {
						isNavigating = true;
						// Keep modal visible - don't hide it
					}
				});

				// Also handle page visibility to keep modal visible
				document.addEventListener('visibilitychange', function() {
					if (document.hidden && uploadInProgress === 'true') {
						// Page is being hidden (redirecting) - keep modal visible
						const modal = document.getElementById('uploadProgressModal');
						if (modal) {
							modal.classList.add('active');
						}
					}
				});
			})();
			/* ===== END REPLACED UPLOAD PROGRESS & SUBMIT HANDLER ===== */
		});
	</script>

	<script>
		function openProjectForm() {
			document.getElementById('projectModal').classList.add('active');
			document.body.style.overflow = 'hidden';
		}

		function closeProjectForm() {
			document.getElementById('projectModal').classList.remove('active');
			document.body.style.overflow = '';
			document.getElementById('projectForm').reset();
			document.getElementById('projectFileCount').textContent = '';
		}

		// Close modal on overlay click
		document.getElementById('projectModal').addEventListener('click', function(e) {
			if (e.target === this) {
				closeProjectForm();
			}
		});

		// File count display for projects
		document.getElementById('project_images').addEventListener('change', function() {
			const count = this.files.length;
			const fileCount = document.getElementById('projectFileCount');
			if (count > 0) {
				fileCount.textContent = '✓ ' + count + ' image(s) selected';
				fileCount.className = 'file-count success';
			} else {
				fileCount.textContent = '';
			}
		});


		// Close on Escape key
		document.addEventListener('keydown', function(e) {
			if (e.key === 'Escape') {
				closeProjectForm();
				closeMobileMenu();
			}
		});

		// Hamburger Menu Toggle
		const hamburgerBtn = document.getElementById('hamburgerBtn');
		const mobileMenu = document.getElementById('mobileMenu');
		const menuBackdrop = document.getElementById('menuBackdrop');

		function toggleMobileMenu() {
			hamburgerBtn.classList.toggle('active');
			mobileMenu.classList.toggle('active');
			menuBackdrop.classList.toggle('active');
			document.body.style.overflow = mobileMenu.classList.contains('active') ? 'hidden' : '';
		}

		function closeMobileMenu() {
			hamburgerBtn.classList.remove('active');
			mobileMenu.classList.remove('active');
			menuBackdrop.classList.remove('active');
			document.body.style.overflow = '';
		}

		hamburgerBtn.addEventListener('click', toggleMobileMenu);
		menuBackdrop.addEventListener('click', closeMobileMenu);

		// Close menu when clicking on a menu link
		const menuLinks = mobileMenu.querySelectorAll('a');
		menuLinks.forEach(link => {
			link.addEventListener('click', function() {
				// Don't close if it's the logout link (needs confirmation)
				if (!this.classList.contains('logout')) {
					closeMobileMenu();
				}
			});
		});
		// Hide page loader when page is fully loaded
		window.addEventListener('load', function() {
			const pageLoader = document.getElementById('pageLoader');
			if (pageLoader) {
				setTimeout(function() {
					pageLoader.classList.add('hidden');
					setTimeout(function() {
						pageLoader.style.display = 'none';
					}, 500);
				}, 300);
			}
		});

		// Also hide loader if DOM is already loaded
		if (document.readyState === 'complete') {
			const pageLoader = document.getElementById('pageLoader');
			if (pageLoader) {
				pageLoader.classList.add('hidden');
				setTimeout(function() {
					pageLoader.style.display = 'none';
				}, 500);
			}
		}
	</script>
</body>
</html>
