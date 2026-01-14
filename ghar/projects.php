<?php
// Increase upload limits for large multi-file uploads
ini_set('upload_max_filesize', '256M');
ini_set('post_max_size', '512M');
ini_set('max_file_uploads', '200');
ini_set('max_execution_time', '300');
ini_set('memory_limit', '512M');

// Security protection - MUST be first
require_once __DIR__ . '/../includes/security.php';
checkAdminLogin();

require_once __DIR__ . '/../includes/db_connect.php';

// Ensure tables exist
$pdo->exec("CREATE TABLE IF NOT EXISTS projects (
	id INT AUTO_INCREMENT PRIMARY KEY,
	title VARCHAR(255) NOT NULL,
	category ENUM('current','completed','past') NOT NULL DEFAULT 'current',
	project_type VARCHAR(100) DEFAULT NULL,
	location VARCHAR(255) NOT NULL,
	description TEXT NOT NULL,
	cover_image VARCHAR(255) NOT NULL,
	created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;");

// Add project_type column if it doesn't exist
try {
	$pdo->exec("ALTER TABLE projects ADD COLUMN project_type VARCHAR(100) DEFAULT NULL");
} catch (Exception $e) {
	// Column already exists, ignore
}

$pdo->exec("CREATE TABLE IF NOT EXISTS project_images (
	id INT AUTO_INCREMENT PRIMARY KEY,
	project_id INT NOT NULL,
	image_path VARCHAR(255) NOT NULL,
	created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
	FOREIGN KEY (project_id) REFERENCES projects(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;");

// Create uploads/projects directory if missing with proper error handling
$rootDir = realpath(__DIR__ . '/..');
if ($rootDir === false) {
	$rootDir = dirname(__DIR__);
}
$uploadsRoot = $rootDir . DIRECTORY_SEPARATOR . 'uploads';
if (!is_dir($uploadsRoot)) {
	if (!@mkdir($uploadsRoot, 0775, true)) {
		$message = 'Failed to create uploads directory. Please check folder permissions.';
		$messageType = 'error';
	}
}
if (!is_writable($uploadsRoot)) {
	$message = 'Uploads directory is not writable. Please check folder permissions.';
	$messageType = 'error';
}
$uploadsDir = $uploadsRoot . DIRECTORY_SEPARATOR . 'projects';
if (!is_dir($uploadsDir)) {
	if (!@mkdir($uploadsDir, 0775, true)) {
		$message = 'Failed to create projects directory. Please check folder permissions.';
		$messageType = 'error';
	}
}

// Helper function for image upload
function save_uploaded_image(array $file, string $uploadsDir, int $maxSize = 25): ?string {
	try {
		if (!isset($file['error']) || is_array($file['error'])) {
			return null;
		}
		if ($file['error'] !== UPLOAD_ERR_OK) {
			return null;
		}
		if ($file['size'] > $maxSize * 1024 * 1024) {
			return null;
		}
		if (!is_uploaded_file($file['tmp_name'])) {
			return null;
		}
		
		// Check if directory is writable
		if (!is_writable($uploadsDir)) {
			return null;
		}
		
		// Get MIME type with fallback
		$mime = null;
		if (class_exists('finfo')) {
			try {
				$finfo = new finfo(FILEINFO_MIME_TYPE);
				$mime = $finfo->file($file['tmp_name']);
			} catch (Exception $e) {
				// Fallback to extension-based detection
			}
		}
		
		// Fallback to extension-based MIME detection
		if (!$mime) {
			$ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
			$mimeMap = [
				'jpg' => 'image/jpeg',
				'jpeg' => 'image/jpeg',
				'png' => 'image/png',
				'webp' => 'image/webp',
			];
			$mime = $mimeMap[$ext] ?? null;
		}
		
		$allowed = [
			'image/jpeg' => 'jpg',
			'image/jpg'  => 'jpg',
			'image/png'  => 'png',
			'image/webp' => 'webp',
		];
		if (!isset($allowed[$mime])) {
			return null;
		}
		$ext = $allowed[$mime];
		$basename = bin2hex(random_bytes(8)) . '_' . uniqid('', true);
		$filename = $basename . '.' . $ext;
		$targetPath = $uploadsDir . DIRECTORY_SEPARATOR . $filename;
		if (!move_uploaded_file($file['tmp_name'], $targetPath)) {
			return null;
		}
		$dirName = basename($uploadsDir);
		$relativePath = 'uploads/' . $dirName . '/' . $filename;
		return $relativePath;
	} catch (Exception $e) {
		return null;
	}
}

$message = '';
$messageType = '';

// Handle project update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_project'])) {
	try {
		$projectId = (int)($_POST['project_id'] ?? 0);
		$title = trim($_POST['title'] ?? '');
		$description = trim($_POST['description'] ?? '');
		$category = trim($_POST['category'] ?? 'current');
		
		// Validate category
		if (!in_array($category, ['current', 'completed', 'past'])) {
			$category = 'current';
		}
		
		if ($projectId > 0 && $title !== '' && $description !== '') {
			// Handle cover image upload
			$coverImagePath = null;
			if (isset($_FILES['cover_image']) && is_uploaded_file($_FILES['cover_image']['tmp_name'])) {
				$coverImagePath = save_uploaded_image($_FILES['cover_image'], $uploadsDir, 25);
				if ($coverImagePath) {
					// Get old cover image path to delete it
					$stmt = $pdo->prepare("SELECT cover_image FROM projects WHERE id = ?");
					$stmt->execute([$projectId]);
					$oldCover = $stmt->fetch();
					if ($oldCover && !empty($oldCover['cover_image'])) {
						$oldCoverPath = $rootDir . DIRECTORY_SEPARATOR . $oldCover['cover_image'];
						if (file_exists($oldCoverPath)) {
							@unlink($oldCoverPath);
						}
					}
				}
			}
			
			// Update project with or without new cover image
			if ($coverImagePath) {
				$stmt = $pdo->prepare("UPDATE projects SET title = ?, description = ?, category = ?, cover_image = ? WHERE id = ?");
				$stmt->execute([$title, $description, $category, $coverImagePath, $projectId]);
			} else {
				$stmt = $pdo->prepare("UPDATE projects SET title = ?, description = ?, category = ? WHERE id = ?");
				$stmt->execute([$title, $description, $category, $projectId]);
			}
		
		// Handle image deletions
		$deletedCount = 0;
		if (isset($_POST['delete_images']) && is_array($_POST['delete_images'])) {
			foreach ($_POST['delete_images'] as $imageId) {
				$imageId = (int)$imageId;
				if ($imageId > 0) {
					// Get image path
					$stmt = $pdo->prepare("SELECT image_path FROM project_images WHERE id = ? AND project_id = ?");
					$stmt->execute([$imageId, $projectId]);
					$image = $stmt->fetch();
					
					if ($image) {
						// Delete from database
						$stmt = $pdo->prepare("DELETE FROM project_images WHERE id = ? AND project_id = ?");
						$stmt->execute([$imageId, $projectId]);
						
						// Delete file
						$imagePath = $rootDir . DIRECTORY_SEPARATOR . $image['image_path'];
						if (file_exists($imagePath)) {
							@unlink($imagePath);
						}
						$deletedCount++;
					}
				}
			}
		}
		
			// Build success message
			$message = 'Project updated successfully.';
			if ($coverImagePath) {
				$message .= ' Cover photo updated.';
			}
			if ($deletedCount > 0) {
				$message .= ' ' . $deletedCount . ' image(s) deleted.';
			}
			$messageType = 'success';
		} else {
			$message = 'Please provide a valid title and description.';
			$messageType = 'error';
		}
	} catch (Exception $e) {
		$message = 'An error occurred while updating the project: ' . htmlspecialchars($e->getMessage());
		$messageType = 'error';
	}
}

// Image deletion is now handled in the update_project section

// Fetch projects with all images
$projects = $pdo->query("SELECT id, title, category, project_type, location, description, cover_image, created_at FROM projects ORDER BY created_at DESC")->fetchAll();

// Preload images for all projects
$imagesByProject = [];
if (!empty($projects)) {
	$ids = array_map(function($p){ return (int)$p['id']; }, $projects);
	if (!empty($ids)) {
		$placeholders = implode(',', array_fill(0, count($ids), '?'));
		$stmtImgs = $pdo->prepare("SELECT id, project_id, image_path FROM project_images WHERE project_id IN ($placeholders) ORDER BY id DESC");
		$stmtImgs->execute($ids);
		while ($row = $stmtImgs->fetch()) {
			$pid = (int)$row['project_id'];
			if (!isset($imagesByProject[$pid])) $imagesByProject[$pid] = [];
			$imagesByProject[$pid][] = $row;
		}
	}
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Projects Management</title>
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

		.container-admin {
			max-width: 1400px;
			margin: 24px auto;
			padding: 0 20px;
		}

		.top {
			display: flex;
			align-items: center;
			justify-content: space-between;
			margin-bottom: 24px;
			flex-wrap: wrap;
			gap: 16px;
		}

		.top h1 {
			font-size: 2rem;
			font-weight: 700;
			color: #1a1a1a;
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

		.top .actions {
			display: flex;
			gap: 12px;
			flex-wrap: wrap;
		}

		.top .actions a {
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

		.top .actions a:hover {
			border-color: #c2925f;
			color: #c2925f;
		}

		.top .actions a.logout {
			color: #d32f2f;
			border-color: #ffebee;
		}

		.top .actions a.logout:hover {
			background: #ffebee;
			border-color: #d32f2f;
		}

		.top .actions a.active {
			background: #c2925f;
			color: #fff;
			border-color: #c2925f;
		}

		.message {
			padding: 16px 20px;
			border-radius: 12px;
			margin-bottom: 24px;
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

		.projects-grid {
			display: grid;
			grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
			gap: 24px;
		}

		.project-card {
			background: #fff;
			border-radius: 16px;
			box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
			overflow: hidden;
			transition: transform 0.2s ease, box-shadow 0.2s ease;
		}

		.project-card:hover {
			transform: translateY(-4px);
			box-shadow: 0 8px 30px rgba(0, 0, 0, 0.12);
		}

		.project-cover {
			width: 100%;
			height: 200px;
			object-fit: cover;
			display: block;
		}

		.project-info {
			padding: 20px;
		}

		.project-badge {
			display: inline-block;
			padding: 6px 12px;
			border-radius: 20px;
			font-size: 0.85rem;
			font-weight: 600;
			margin-bottom: 12px;
			text-transform: capitalize;
		}

		.project-badge.current {
			background: #fff3cd;
			color: #856404;
		}

		.project-badge.completed {
			background: #d1ecf1;
			color: #0c5460;
		}

		.project-badge.past {
			background: #f8d7da;
			color: #721c24;
		}

		.project-badge.project-type-badge {
			background: #e3f2fd;
			color: #1565c0;
		}

		.project-title {
			font-size: 1.3rem;
			font-weight: 700;
			color: #1a1a1a;
			margin-bottom: 8px;
		}

		.project-location {
			color: #666;
			font-size: 0.95rem;
			margin-bottom: 16px;
		}

		.project-images-preview {
			display: flex;
			gap: 8px;
			flex-wrap: wrap;
			margin-bottom: 16px;
		}

		.image-thumb {
			width: 56px;
			height: 56px;
			border-radius: 8px;
			overflow: hidden;
			background: #f2f2f2;
			box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
		}

		.image-thumb img {
			width: 100%;
			height: 100%;
			object-fit: cover;
		}

		.image-thumb.more {
			display: flex;
			align-items: center;
			justify-content: center;
			background: #fff;
			border: 2px solid #e0e0e0;
			color: #666;
			font-size: 0.85rem;
			font-weight: 600;
		}

		.project-actions {
			display: flex;
			gap: 10px;
		}

		.btn {
			padding: 10px 20px;
			border: none;
			border-radius: 10px;
			font-size: 0.95rem;
			font-weight: 600;
			cursor: pointer;
			transition: all 0.2s ease;
			flex: 1;
			text-align: center;
		}

		.btn-edit {
			background: linear-gradient(135deg, #E0B27C, #C89A60);
			color: #fff;
			box-shadow: 0 4px 12px rgba(200, 154, 96, 0.3);
		}

		.btn-edit:hover {
			background: linear-gradient(135deg, #D4A574, #B88950);
			box-shadow: 0 6px 20px rgba(200, 154, 96, 0.4);
		}

		.btn-delete {
			background: #fff;
			border: 2px solid #f3d0cf;
			color: #a33b2e;
		}

		.btn-delete:hover {
			background: #ffebee;
			border-color: #e6b6b3;
			box-shadow: 0 4px 12px rgba(163, 59, 46, 0.15);
		}

		/* Modal Styles */
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
			max-width: 900px;
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
			padding-bottom: 16px;
			border-bottom: 2px solid #f0f0f0;
		}

		.modal-header h2 {
			font-size: 1.75rem;
			font-weight: 700;
			color: #1a1a1a;
		}

		.close-btn {
			background: none;
			border: none;
			font-size: 32px;
			color: #666;
			cursor: pointer;
			width: 40px;
			height: 40px;
			display: flex;
			align-items: center;
			justify-content: center;
			border-radius: 10px;
			transition: all 0.2s ease;
		}

		.close-btn:hover {
			background: #f5f5f5;
			color: #1a1a1a;
		}

		.form-group {
			margin-bottom: 24px;
		}

		.form-group label {
			display: block;
			font-weight: 600;
			color: #333;
			margin-bottom: 8px;
			font-size: 1rem;
		}

		.form-group input[type="text"] {
			width: 100%;
			padding: 14px 18px;
			border: 2px solid #e0e0e0;
			border-radius: 12px;
			font-size: 1rem;
			outline: none;
			transition: border-color 0.2s ease;
			font-family: inherit;
		}

		.form-group input[type="text"]:focus {
			border-color: #c2925f;
		}

		.form-group textarea {
			width: 100%;
			padding: 14px 18px;
			border: 2px solid #e0e0e0;
			border-radius: 12px;
			font-size: 1rem;
			outline: none;
			transition: border-color 0.2s ease;
			font-family: inherit;
			resize: vertical;
			min-height: 120px;
		}

		.form-group textarea:focus {
			border-color: #c2925f;
		}

		.form-group select {
			width: 100%;
			padding: 14px 18px;
			border: 2px solid #e0e0e0;
			border-radius: 12px;
			font-size: 1rem;
			outline: none;
			transition: border-color 0.2s ease;
			font-family: inherit;
			background: #fff;
			cursor: pointer;
		}

		.form-group select:focus {
			border-color: #c2925f;
		}

		.form-group input[type="file"] {
			width: 100%;
			padding: 14px 18px;
			border: 2px solid #e0e0e0;
			border-radius: 12px;
			font-size: 1rem;
			outline: none;
			transition: border-color 0.2s ease;
			font-family: inherit;
			background: #fff;
			cursor: pointer;
		}

		.form-group input[type="file"]:focus {
			border-color: #c2925f;
		}

		.cover-image-preview {
			margin-bottom: 12px;
		}

		.cover-image-preview img {
			border: 2px solid #e0e0e0;
			box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
		}

		.images-section {
			margin-top: 32px;
		}

		.images-section h3 {
			font-size: 1.3rem;
			font-weight: 700;
			color: #1a1a1a;
			margin-bottom: 20px;
		}

		.images-grid {
			display: grid;
			grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
			gap: 16px;
		}

		.image-item {
			position: relative;
			border-radius: 12px;
			overflow: hidden;
			box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
			transition: transform 0.2s ease, opacity 0.2s ease, border 0.2s ease;
		}

		.image-item:hover {
			transform: scale(1.05);
		}

		.image-item.marked-for-deletion {
			opacity: 0.5;
			border: 2px solid #d32f2f;
		}

		.image-item.marked-for-deletion img {
			filter: grayscale(100%);
		}

		.image-item img {
			width: 100%;
			height: 150px;
			object-fit: cover;
			display: block;
		}

		.image-delete-btn {
			position: absolute;
			top: 8px;
			right: 8px;
			background: rgba(211, 47, 47, 0.95);
			color: #fff;
			border: none;
			border-radius: 8px;
			width: 40px;
			height: 40px;
			cursor: pointer;
			display: flex;
			align-items: center;
			justify-content: center;
			transition: all 0.2s ease;
			box-shadow: 0 2px 8px rgba(0, 0, 0, 0.25);
			padding: 0;
		}

		.image-delete-btn:hover {
			background: rgba(211, 47, 47, 1);
			transform: scale(1.05);
			box-shadow: 0 4px 12px rgba(211, 47, 47, 0.4);
		}

		.image-delete-btn svg {
			width: 18px;
			height: 18px;
			fill: currentColor;
		}

		.modal-actions {
			display: flex;
			gap: 12px;
			margin-top: 32px;
			padding-top: 24px;
			border-top: 2px solid #f0f0f0;
		}

		.btn-save {
			background: linear-gradient(135deg, #E0B27C, #C89A60);
			color: #fff;
			box-shadow: 0 4px 12px rgba(200, 154, 96, 0.3);
			padding: 14px 28px;
			border: none;
			border-radius: 12px;
			font-size: 1rem;
			font-weight: 600;
			cursor: pointer;
			transition: all 0.2s ease;
			flex: 1;
		}

		.btn-save:hover {
			background: linear-gradient(135deg, #D4A574, #B88950);
			box-shadow: 0 6px 20px rgba(200, 154, 96, 0.4);
		}

		.btn-cancel {
			background: #f5f5f5;
			color: #666;
			padding: 14px 28px;
			border: none;
			border-radius: 12px;
			font-size: 1rem;
			font-weight: 600;
			cursor: pointer;
			transition: all 0.2s ease;
		}

		.btn-cancel:hover {
			background: #e0e0e0;
		}

		.empty-state {
			text-align: center;
			padding: 60px 20px;
			background: #fff;
			border-radius: 16px;
			box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
		}

		.empty-state h2 {
			font-size: 1.5rem;
			color: #666;
			margin-bottom: 12px;
		}

		.empty-state p {
			color: #999;
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

		/* Responsive */
		@media (max-width: 768px) {
			.container-admin {
				padding: 0 16px;
			}

			.top h1 {
				font-size: 1.5rem;
			}

			.hamburger {
				display: flex;
			}

			.top .actions {
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

			.top .actions.active {
				right: 0;
			}

			.top .actions a {
				padding: 14px 18px;
				font-size: 1rem;
				width: 100%;
				text-align: left;
				border-radius: 10px;
				margin: 0;
				background: #fff;
				border: 2px solid #e0e0e0;
			}

			.top .actions a.active {
				background: #c2925f;
				color: #fff;
				border-color: #c2925f;
			}

			.projects-grid {
				grid-template-columns: 1fr;
				gap: 20px;
			}

			.modal-content {
				padding: 24px;
				margin: 10px;
			}

			.images-grid {
				grid-template-columns: repeat(auto-fill, minmax(120px, 1fr));
				gap: 12px;
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
			.top {
				flex-wrap: nowrap;
			}

			.modal-content {
				padding: 20px;
			}

			.images-grid {
				grid-template-columns: repeat(2, 1fr);
			}
		}
	</style>
</head>
<body>
	<div class="container-admin">
		<div class="top">
			<h1>Projects Management</h1>
			<button class="hamburger" id="hamburgerBtn" aria-label="Toggle menu">
				<span></span>
				<span></span>
				<span></span>
			</button>
			<div class="actions" id="mobileMenu">
				<a href="xtztragiikz.php">Dashboard</a>
				<a href="projects.php" class="active">Manage Projects</a>
				<a href="logout.php" class="logout">Logout</a>
			</div>
		</div>
		<div class="menu-backdrop" id="menuBackdrop"></div>

		<?php if ($message): ?>
			<div class="message <?php echo $messageType; ?>"><?php echo htmlspecialchars($message); ?></div>
		<?php endif; ?>

		<?php if (empty($projects)): ?>
			<div class="empty-state">
				<h2>No Projects Yet</h2>
				<p>Create your first project from the dashboard.</p>
			</div>
		<?php else: ?>
			<div class="projects-grid">
				<?php foreach ($projects as $p): ?>
					<div class="project-card">
						<?php 
						$imgSrc = (string)$p['cover_image'];
						if ($imgSrc !== '' && strpos($imgSrc, '../') !== 0 && strpos($imgSrc, '/') !== 0) {
							$imgSrc = '../' . $imgSrc;
						}
						?>
						<img src="<?php echo htmlspecialchars($imgSrc); ?>" alt="<?php echo htmlspecialchars($p['title']); ?>" class="project-cover">
						<div class="project-info">
							<div style="display: flex; gap: 8px; flex-wrap: wrap; margin-bottom: 12px;">
								<span class="project-badge <?php echo htmlspecialchars($p['category']); ?>">
									<?php echo htmlspecialchars(ucfirst($p['category'])); ?>
								</span>
							</div>
							<h3 class="project-title"><?php echo htmlspecialchars($p['title']); ?></h3>
							<div class="project-location">📍 <?php echo htmlspecialchars($p['location']); ?></div>
							
							<?php 
							$pid = (int)$p['id'];
							$thumbs = $imagesByProject[$pid] ?? [];
							if (!empty($thumbs)):
								$limit = min(count($thumbs), 6);
							?>
								<div class="project-images-preview">
									<?php for ($i=0; $i<$limit; $i++):
										$t = $thumbs[$i];
										$src = (strpos($t['image_path'], '../')===0 || strpos($t['image_path'], '/')===0) ? $t['image_path'] : ('../' . $t['image_path']);
									?>
										<div class="image-thumb">
											<img src="<?php echo htmlspecialchars($src); ?>" alt="">
										</div>
									<?php endfor; ?>
									<?php if (count($thumbs) > $limit): ?>
										<div class="image-thumb more">+<?php echo (int)(count($thumbs)-$limit); ?></div>
									<?php endif; ?>
								</div>
							<?php endif; ?>
							
							<div class="project-actions">
								<button class="btn btn-edit" onclick="openEditModal(<?php echo $p['id']; ?>, '<?php echo htmlspecialchars(addslashes($p['title'])); ?>', <?php echo htmlspecialchars(json_encode($p['description'])); ?>, '<?php echo htmlspecialchars($p['category']); ?>', <?php echo htmlspecialchars(json_encode($imagesByProject[$pid] ?? [])); ?>, '<?php echo htmlspecialchars(addslashes($p['cover_image'] ?? '')); ?>')">
									Edit
								</button>
								<a href="project_delete.php?id=<?php echo (int)$p['id']; ?>" class="btn btn-delete" data-delete-url="project_delete.php?id=<?php echo (int)$p['id']; ?>">
									Delete
								</a>
							</div>
						</div>
					</div>
				<?php endforeach; ?>
			</div>
		<?php endif; ?>
	</div>

	<!-- Edit Modal -->
	<div class="modal-overlay" id="editModal">
		<div class="modal-content">
			<div class="modal-header">
				<h2>Edit Project</h2>
				<button class="close-btn" onclick="closeEditModal()">&times;</button>
			</div>
			<form method="post" action="" id="editForm" enctype="multipart/form-data">
				<input type="hidden" name="project_id" id="edit_project_id">
				<input type="hidden" name="update_project" value="1">
				
				<div class="form-group">
					<label for="edit_cover_image">Cover Photo</label>
					<div class="cover-image-preview" id="coverImagePreview">
						<img id="coverImagePreviewImg" src="" alt="Cover preview" style="display: none; max-width: 100%; max-height: 200px; border-radius: 8px; margin-bottom: 10px;">
					</div>
					<input type="file" id="edit_cover_image" name="cover_image" accept="image/jpeg,image/jpg,image/png,image/webp" onchange="previewCoverImage(this)">
					<small style="color: #666; display: block; margin-top: 5px;">Leave empty to keep current cover photo. Max 25MB. JPG, PNG, or WEBP.</small>
				</div>
				
				<div class="form-group">
					<label for="edit_title">Project Title</label>
					<input type="text" id="edit_title" name="title" required>
				</div>

				<div class="form-group">
					<label for="edit_category">Category</label>
					<select id="edit_category" name="category" required>
						<option value="current">Current</option>
						<option value="completed">Completed</option>
						<option value="past">Past Projects</option>
					</select>
				</div>

				<div class="form-group">
					<label for="edit_description">Description</label>
					<textarea id="edit_description" name="description" required></textarea>
				</div>

				<div class="images-section">
					<h3>Project Images</h3>
					<div class="images-grid" id="imagesGrid">
						<!-- Images will be inserted here by JavaScript -->
					</div>
				</div>

				<div class="modal-actions">
					<button type="submit" class="btn-save">Save Changes</button>
					<button type="button" class="btn-cancel" onclick="closeEditModal()">Cancel</button>
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

	<script>
		let currentProjectId = 0;
		let currentImages = [];
		let imagesToDelete = [];

		function openEditModal(projectId, title, description, category, images, coverImage) {
			currentProjectId = projectId;
			currentImages = images || [];
			imagesToDelete = []; // Reset deletion list
			
			// Decode description if it's a JSON string
			let descValue = description;
			if (typeof description === 'string' && description.startsWith('"')) {
				try {
					descValue = JSON.parse(description);
				} catch(e) {
					descValue = description;
				}
			}
			
			document.getElementById('edit_project_id').value = projectId;
			document.getElementById('edit_title').value = title;
			document.getElementById('edit_description').value = descValue || '';
			document.getElementById('edit_category').value = category || 'current';
			
			// Set cover image preview
			const coverPreview = document.getElementById('coverImagePreviewImg');
			const coverInput = document.getElementById('edit_cover_image');
			if (coverImage) {
				let coverSrc = coverImage;
				if (coverSrc && !coverSrc.startsWith('../') && !coverSrc.startsWith('/')) {
					coverSrc = '../' + coverSrc;
				}
				coverPreview.src = coverSrc;
				coverPreview.style.display = 'block';
				coverInput.setAttribute('data-original-cover', coverImage);
			} else {
				coverPreview.style.display = 'none';
				coverInput.removeAttribute('data-original-cover');
			}
			coverInput.value = ''; // Reset file input
			
			renderImages();
			
			document.getElementById('editModal').classList.add('active');
			document.body.style.overflow = 'hidden';
		}

		function renderImages() {
			const imagesGrid = document.getElementById('imagesGrid');
			imagesGrid.innerHTML = '';
			
			if (currentImages.length === 0) {
				imagesGrid.innerHTML = '<p style="grid-column: 1/-1; text-align: center; color: #999; padding: 20px;">No images in this project.</p>';
			} else {
				currentImages.forEach(function(image) {
					const isMarked = imagesToDelete.indexOf(image.id) !== -1;
					const imageItem = document.createElement('div');
					imageItem.className = 'image-item' + (isMarked ? ' marked-for-deletion' : '');
					imageItem.id = 'image-item-' + image.id;
					
					const imgSrc = (image.image_path.indexOf('../') === 0 || image.image_path.indexOf('/') === 0) 
						? image.image_path 
						: '../' + image.image_path;
					
					imageItem.innerHTML = `
						<img src="${imgSrc.replace(/"/g, '&quot;')}" alt="Project Image">
						<button type="button" class="image-delete-btn" onclick="markImageForDeletion(${image.id})" title="${isMarked ? 'Undo Delete' : 'Delete Image'}">
							<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
								<polyline points="3 6 5 6 21 6"></polyline>
								<path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
								<line x1="10" y1="11" x2="10" y2="17"></line>
								<line x1="14" y1="11" x2="14" y2="17"></line>
							</svg>
						</button>
					`;
					
					imagesGrid.appendChild(imageItem);
				});
			}
		}

		function previewCoverImage(input) {
			const preview = document.getElementById('coverImagePreviewImg');
			if (input.files && input.files[0]) {
				const reader = new FileReader();
				reader.onload = function(e) {
					preview.src = e.target.result;
					preview.style.display = 'block';
				};
				reader.readAsDataURL(input.files[0]);
			} else {
				// If no file selected, show original cover image if it exists
				const coverInput = document.getElementById('edit_cover_image');
				const originalCover = coverInput.getAttribute('data-original-cover');
				if (originalCover) {
					let coverSrc = originalCover;
					if (coverSrc && !coverSrc.startsWith('../') && !coverSrc.startsWith('/')) {
						coverSrc = '../' + coverSrc;
					}
					preview.src = coverSrc;
					preview.style.display = 'block';
				} else {
					preview.style.display = 'none';
				}
			}
		}

		function closeEditModal() {
			document.getElementById('editModal').classList.remove('active');
			document.body.style.overflow = '';
			document.getElementById('editForm').reset();
			currentProjectId = 0;
			currentImages = [];
			imagesToDelete = [];
			// Reset cover image preview
			const coverPreview = document.getElementById('coverImagePreviewImg');
			coverPreview.style.display = 'none';
			coverPreview.src = '';
		}

		function markImageForDeletion(imageId) {
			const index = imagesToDelete.indexOf(imageId);
			if (index === -1) {
				// Mark for deletion
				imagesToDelete.push(imageId);
			} else {
				// Unmark
				imagesToDelete.splice(index, 1);
			}
			renderImages();
		}

		// Handle form submission - add delete_images to form
		document.getElementById('editForm').addEventListener('submit', function(e) {
			// Add hidden inputs for images to delete
			imagesToDelete.forEach(function(imageId) {
				const input = document.createElement('input');
				input.type = 'hidden';
				input.name = 'delete_images[]';
				input.value = imageId;
				this.appendChild(input);
			}, this);
		});

		// Close modal on overlay click
		document.getElementById('editModal').addEventListener('click', function(e) {
			if (e.target === this) {
				closeEditModal();
			}
		});

		// Close on Escape key
		document.addEventListener('keydown', function(e) {
			if (e.key === 'Escape') {
				closeEditModal();
				closeMobileMenu();
				hideConfirm();
			}
		});

		// Confirmation Modal System
		let confirmCallback = null;

		function showConfirm(title, message, type, callback) {
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

		// Replace logout confirm and delete links
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

			// Delete project links
			const deleteLinks = document.querySelectorAll('a.btn-delete[data-delete-url]');
			deleteLinks.forEach(link => {
				link.addEventListener('click', function(e) {
					e.preventDefault();
					const deleteUrl = this.getAttribute('data-delete-url');
					showConfirm('Delete Project', 'Are you sure you want to delete this project and all its images? This action cannot be undone.', 'danger', function() {
						window.location.href = deleteUrl;
					});
				});
			});

			// Edit form submission confirmation
			const editForm = document.getElementById('editForm');
			if (editForm) {
				editForm.addEventListener('submit', function(e) {
					e.preventDefault();
					showConfirm('Save Changes', 'Are you sure you want to save these changes?', 'info', function() {
						editForm.submit();
					});
				});
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
				if (!this.classList.contains('logout')) {
					closeMobileMenu();
				}
			});
		});
	</script>
</body>
</html>
