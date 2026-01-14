<?php
header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . '/../includes/db_connect.php';

// Ensure tables exist (safe no-op if already created)
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

try {
	$projectsStmt = $pdo->query("SELECT id, title, category, location, description, cover_image, created_at FROM projects ORDER BY created_at DESC");
	$projects = $projectsStmt->fetchAll();
	$result = [];
	if ($projects) {
		$ids = array_map(fn($p) => (int)$p['id'], $projects);
		if (!empty($ids)) {
			$placeholders = implode(',', array_fill(0, count($ids), '?'));
			$imgStmt = $pdo->prepare("SELECT project_id, image_path FROM project_images WHERE project_id IN ($placeholders) ORDER BY id ASC");
			$imgStmt->execute($ids);
			$imagesByProject = [];
			while ($row = $imgStmt->fetch()) {
				$pid = (int)$row['project_id'];
				if (!isset($imagesByProject[$pid])) $imagesByProject[$pid] = [];
				$imagesByProject[$pid][] = $row['image_path'];
			}
		} else {
			$imagesByProject = [];
		}
		foreach ($projects as $p) {
			$pid = (int)$p['id'];
			$result[] = [
				'id' => $pid,
				'title' => $p['title'],
				'category' => $p['category'],
				'status' => $p['category'], // for frontend compatibility
				'location' => $p['location'],
				'description' => $p['description'],
				'cover_image' => $p['cover_image'],
				'images' => $imagesByProject[$pid] ?? [],
				'created_at' => $p['created_at'],
			];
		}
	}
	echo json_encode(['ok' => true, 'projects' => $result], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
} catch (Throwable $e) {
	http_response_code(500);
	echo json_encode(['ok' => false, 'error' => 'Failed to load projects.']);
}


