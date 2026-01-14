<?php
// Security protection - MUST be first
require_once __DIR__ . '/../includes/security.php';
checkAdminLogin();

require_once __DIR__ . '/../includes/db_connect.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id > 0) {
	// Collect image paths before deletion
	$stmt = $pdo->prepare("SELECT cover_image FROM projects WHERE id=?");
	$stmt->execute([$id]);
	$project = $stmt->fetch();

	$stmt = $pdo->prepare("SELECT image_path FROM project_images WHERE project_id=?");
	$stmt->execute([$id]);
	$images = $stmt->fetchAll();

	// Delete DB rows (images via cascade)
	$del = $pdo->prepare("DELETE FROM projects WHERE id=?");
	$del->execute([$id]);

	// Delete files
	$paths = [];
	if ($project && !empty($project['cover_image'])) {
		$paths[] = $project['cover_image'];
	}
	foreach ($images as $img) {
		if (!empty($img['image_path'])) {
			$paths[] = $img['image_path'];
		}
	}
	foreach ($paths as $rel) {
		$full = realpath(__DIR__ . '/..' . DIRECTORY_SEPARATOR . $rel);
		// Fallback if realpath fails (e.g., file missing)
		if ($full === false) {
			$full = __DIR__ . '/..' . DIRECTORY_SEPARATOR . $rel;
		}
		if (is_file($full)) {
			@unlink($full);
		}
	}
}
header('Location: projects.php');
exit;


