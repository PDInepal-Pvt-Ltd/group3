<?php
// Security protection - MUST be first
require_once __DIR__ . '/../includes/security.php';
checkAdminLogin();

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

$message = '';
$messageType = '';

// Handle actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
	$action = $_POST['action'] ?? '';
	$id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
	if ($id > 0) {
		if ($action === 'read') {
			$stmt = $pdo->prepare("UPDATE inquiries SET status='read' WHERE id=?");
			$stmt->execute([$id]);
			$message = 'Inquiry marked as read.';
			$messageType = 'success';
			// Redirect to refresh and show inquiry in read section, switch to read tab
			header('Location: inquiries.php?tab=read&read=' . $id);
			exit;
		} elseif ($action === 'delete') {
			$stmt = $pdo->prepare("DELETE FROM inquiries WHERE id=?");
			$stmt->execute([$id]);
			$message = 'Inquiry deleted successfully.';
			$messageType = 'success';
		}
	}
}

// Fetch inquiries - separate unread and read
try {
	// First, get total count
	$countStmt = $pdo->query("SELECT COUNT(*) as total FROM inquiries");
	$totalCount = $countStmt->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;
	
	// Fetch unread inquiries
	$unreadStmt = $pdo->query("SELECT id, name, email, phone, project_type, subject, message, status, created_at FROM inquiries WHERE status='unread' ORDER BY created_at DESC");
	$unreadInquiries = $unreadStmt->fetchAll(PDO::FETCH_ASSOC);
	
	// Fetch read inquiries
	$readStmt = $pdo->query("SELECT id, name, email, phone, project_type, subject, message, status, created_at FROM inquiries WHERE status='read' ORDER BY created_at DESC");
	$readInquiries = $readStmt->fetchAll(PDO::FETCH_ASSOC);
	
	$unreadCount = count($unreadInquiries);
	$readCount = count($readInquiries);
} catch (Exception $e) {
	$unreadInquiries = [];
	$readInquiries = [];
	$totalCount = 0;
	$unreadCount = 0;
	$readCount = 0;
	$message = 'Error fetching inquiries: ' . $e->getMessage();
	$messageType = 'error';
}

// Helper function to format date in Australia timezone
function formatAustraliaDate($dateString) {
	try {
		$date = new DateTime($dateString, new DateTimeZone('UTC'));
		$date->setTimezone(new DateTimeZone('Australia/Sydney'));
		return $date->format('M d, Y g:i A');
	} catch (Exception $e) {
		// Fallback to original format if conversion fails
		return date('M d, Y g:i A', strtotime($dateString));
	}
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Inquiries Management</title>
	<link rel="stylesheet" href="../css/global.css">
	<style>
		* {
			margin: 0;
			padding: 0;
			box-sizing: border-box;
		}

		body {
			background: #f5f3f0;
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
			font-size: 2.5rem;
			font-weight: 900;
			color: #1a1a1a;
			letter-spacing: -0.03em;
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

		.tabs-container {
			background: #fff;
			border-radius: 12px;
			padding: 8px;
			margin-bottom: 24px;
			box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
			display: flex;
			gap: 8px;
		}

		.tab-button {
			flex: 1;
			padding: 12px 20px;
			border: none;
			background: transparent;
			border-radius: 8px;
			font-size: 1rem;
			font-weight: 600;
			color: #666;
			cursor: pointer;
			transition: all 0.2s ease;
			display: flex;
			align-items: center;
			justify-content: center;
			gap: 8px;
			position: relative;
		}

		.tab-button:hover {
			background: #f5f5f5;
			color: #333;
		}

		.tab-button.active {
			background: linear-gradient(135deg, #E0B27C, #C89A60);
			color: #fff;
			box-shadow: 0 2px 8px rgba(200, 154, 96, 0.3);
		}

		.tab-button.active .tab-count {
			background: rgba(255, 255, 255, 0.3);
			color: #fff;
		}

		.tab-count {
			display: inline-block;
			padding: 2px 8px;
			border-radius: 12px;
			font-size: 0.85rem;
			font-weight: 700;
			background: #f0f0f0;
			color: #666;
		}

		.inquiries-section {
			margin-bottom: 40px;
			display: none;
		}

		.inquiries-section.active {
			display: block;
		}

		.section-header {
			display: flex;
			align-items: center;
			justify-content: space-between;
			margin-bottom: 20px;
			padding: 12px 16px;
			background: #fff;
			border-radius: 12px;
			box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
		}

		.section-title {
			font-size: 1.5rem;
			font-weight: 700;
			color: #1a1a1a;
			display: flex;
			align-items: center;
			gap: 12px;
		}

		.section-count {
			display: inline-block;
			padding: 4px 12px;
			background: #e3f2fd;
			color: #1565c0;
			border-radius: 20px;
			font-size: 0.9rem;
			font-weight: 600;
		}

		.section-count.unread {
			background: #fff3cd;
			color: #856404;
		}

		.section-count.read {
			background: #e8f5e9;
			color: #2e7d32;
		}

		/* ========== ORIGINAL SIMPLE INQUIRY DESIGN ========== */
		.inquiries-grid {
			display: grid;
			grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
			gap: 20px;
		}

		.inquiry-card {
			background: #ffffff;
			border: 1px solid #e0e0e0;
			border-radius: 8px;
			padding: 20px;
			box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
			transition: box-shadow 0.3s ease;
		}

		.inquiry-card:hover {
			box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
		}

		.inquiry-card.unread {
			border-left: 4px solid #C89A60;
		}

		.inquiry-header {
			display: flex;
			justify-content: space-between;
			align-items: center;
			margin-bottom: 15px;
			padding-bottom: 15px;
			border-bottom: 1px solid #e0e0e0;
		}

		.inquiry-name {
			font-size: 1.2rem;
			font-weight: 700;
			color: #222;
			margin: 0;
		}

		.inquiry-badge {
			display: inline-block;
			padding: 4px 10px;
			border-radius: 4px;
			font-size: 0.75rem;
			font-weight: 600;
			text-transform: uppercase;
		}

		.inquiry-badge.unread {
			background: #C89A60;
			color: #fff;
		}

		.inquiry-badge.read {
			background: #e0e0e0;
			color: #666;
		}

		.inquiry-info {
			margin-bottom: 15px;
		}

		.inquiry-info-item {
			display: flex;
			align-items: center;
			margin-bottom: 12px;
			font-size: 0.95rem;
			color: #555;
		}

		.inquiry-info-item-icon {
			width: 24px;
			height: 24px;
			margin-right: 12px;
			flex-shrink: 0;
			display: flex;
			align-items: center;
			justify-content: center;
			background: linear-gradient(135deg, #C89A60, #E0B27C);
			border-radius: 6px;
			padding: 4px;
		}

		.inquiry-info-item-icon svg {
			width: 16px;
			height: 16px;
			fill: #ffffff;
		}

		.inquiry-info-item strong {
			min-width: 120px;
			color: #333;
			margin-right: 10px;
			font-weight: 700;
			font-size: 0.9rem;
		}

		.inquiry-info-item-value {
			font-weight: 600;
			color: #222;
		}

		.inquiry-info-item a {
			color: #C89A60;
			text-decoration: none;
			font-weight: 600;
		}

		.inquiry-info-item a:hover {
			text-decoration: underline;
		}

		.inquiry-message {
			background: #f5f5f5;
			padding: 15px;
			border-radius: 4px;
			margin-bottom: 15px;
			font-size: 0.95rem;
			line-height: 1.6;
			color: #222;
			white-space: pre-wrap;
			word-wrap: break-word;
			text-align: left;
			font-weight: 500;
		}

		.inquiry-footer {
			display: flex;
			justify-content: space-between;
			align-items: center;
			padding-top: 15px;
			border-top: 1px solid #e0e0e0;
		}

		.inquiry-date {
			font-size: 0.9rem;
			color: #666;
			font-weight: 600;
			display: flex;
			align-items: center;
			gap: 6px;
		}

		.inquiry-date-icon {
			width: 16px;
			height: 16px;
			fill: #C89A60;
		}

		.inquiry-actions {
			display: flex;
			gap: 10px;
		}

		.btn {
			padding: 8px 16px;
			border: none;
			border-radius: 4px;
			font-size: 0.875rem;
			font-weight: 600;
			cursor: pointer;
			transition: background-color 0.3s ease;
			text-decoration: none;
			display: inline-block;
		}

		.btn-mark-read {
			background: #C89A60;
			color: #fff;
		}

		.btn-mark-read:hover {
			background: #B88950;
		}

		.btn-delete {
			background: #dc3545;
			color: #fff;
		}

		.btn-delete:hover {
			background: #c82333;
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

		/* ========== MOBILE RESPONSIVE ========== */
		@media (max-width: 768px) {
			.container-admin {
				padding: 0 16px;
			}

			.top h1 {
				font-size: 2rem;
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

			.inquiries-grid {
				grid-template-columns: 1fr;
			}

			.inquiry-card {
				padding: 15px;
			}

			.inquiry-header {
				flex-direction: column;
				align-items: flex-start;
				gap: 10px;
			}

			.inquiry-footer {
				flex-direction: column;
				align-items: flex-start;
				gap: 10px;
			}

			.inquiry-actions {
				width: 100%;
			}

			.btn {
				width: 100%;
			}
		}

		@media (max-width: 480px) {
			.top {
				flex-wrap: nowrap;
			}

			.top h1 {
				font-size: 1.75rem;
			}

			.inquiry-card {
				padding: 12px;
			}

			.inquiry-name {
				font-size: 1rem;
			}

			.inquiry-info-item {
				font-size: 0.85rem;
			}

			.inquiry-message {
				font-size: 0.85rem;
				padding: 12px;
			}

			.inquiry-date {
				font-size: 0.8rem;
			}

			.btn {
				padding: 6px 12px;
				font-size: 0.8rem;
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
	</style>
</head>
<body>
	<div class="container-admin">
		<div class="top">
			<h1>Inquiries Management</h1>
			<button class="hamburger" id="hamburgerBtn" aria-label="Toggle menu">
				<span></span>
				<span></span>
				<span></span>
			</button>
			<div class="actions" id="mobileMenu">
				<a href="xtztragiikz.php">Dashboard</a>
				<a href="projects.php">Manage Projects</a>
				<a href="inquiries.php" class="active">View Inquiries</a>
				<a href="logout.php" class="logout">Logout</a>
			</div>
		</div>
		<div class="menu-backdrop" id="menuBackdrop"></div>

		<?php if ($message): ?>
			<div class="message <?php echo $messageType; ?>"><?php echo htmlspecialchars($message); ?></div>
		<?php endif; ?>

		<!-- Tabs Navigation -->
		<div class="tabs-container">
			<button class="tab-button active" data-tab="unread" id="tabUnread">
				Unread Inquiries
				<span class="tab-count unread"><?php echo $unreadCount; ?></span>
			</button>
			<button class="tab-button" data-tab="read" id="tabRead">
				Read Inquiries
				<span class="tab-count read"><?php echo $readCount; ?></span>
			</button>
		</div>

		<!-- Unread Inquiries Section -->
		<div class="inquiries-section active" id="sectionUnread">
			<div class="section-header">
				<h2 class="section-title">
					Unread Inquiries
					<span class="section-count unread"><?php echo $unreadCount; ?></span>
				</h2>
			</div>
			<?php if (empty($unreadInquiries)): ?>
				<div class="empty-state">
					<h2>No Unread Inquiries</h2>
					<p>All inquiries have been read.</p>
				</div>
			<?php else: ?>
				<div class="inquiries-grid">
					<?php foreach ($unreadInquiries as $inq): 
					// Ensure all fields exist
					$inq = array_merge([
						'id' => 0,
						'name' => '',
						'email' => '',
						'phone' => '',
						'project_type' => '',
						'subject' => '',
						'message' => '',
						'status' => 'unread',
						'created_at' => date('Y-m-d H:i:s')
					], $inq);
				?>
					<div class="inquiry-card <?php echo $inq['status'] === 'unread' ? 'unread' : 'read'; ?>">
						<div class="inquiry-header">
							<h3 class="inquiry-name"><?php echo htmlspecialchars($inq['name']); ?></h3>
							<span class="inquiry-badge <?php echo $inq['status']; ?>">
								<?php echo $inq['status'] === 'unread' ? 'NEW' : 'READ'; ?>
							</span>
						</div>

						<div class="inquiry-info">
							<?php if (!empty($inq['email'])): ?>
							<div class="inquiry-info-item">
								<div class="inquiry-info-item-icon">
									<svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
										<path d="M20 4H4c-1.1 0-1.99.9-1.99 2L2 18c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 4l-8 5-8-5V6l8 5 8-5v2z"/>
									</svg>
								</div>
								<strong>Email:</strong>
								<span class="inquiry-info-item-value">
									<a href="mailto:<?php echo htmlspecialchars($inq['email']); ?>"><?php echo htmlspecialchars($inq['email']); ?></a>
								</span>
							</div>
							<?php endif; ?>
							<?php if (!empty($inq['phone'])): ?>
							<div class="inquiry-info-item">
								<div class="inquiry-info-item-icon">
									<svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
										<path d="M6.62 10.79c1.44 2.83 3.76 5.14 6.59 6.59l2.2-2.2c.27-.27.67-.36 1.02-.24 1.12.37 2.33.57 3.57.57.55 0 1 .45 1 1V20c0 .55-.45 1-1 1-9.39 0-17-7.61-17-17 0-.55.45-1 1-1h3.5c.55 0 1 .45 1 1 0 1.25.2 2.45.57 3.57.11.35.03.74-.25 1.02l-2.2 2.2z"/>
									</svg>
								</div>
								<strong>Phone:</strong>
								<span class="inquiry-info-item-value">
									<a href="tel:<?php echo htmlspecialchars($inq['phone']); ?>"><?php echo htmlspecialchars($inq['phone']); ?></a>
								</span>
							</div>
							<?php endif; ?>
							<?php if (!empty($inq['project_type'])): ?>
							<div class="inquiry-info-item">
								<div class="inquiry-info-item-icon">
									<svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
										<path d="M20 6h-4V4c0-1.11-.89-2-2-2h-4c-1.11 0-2 .89-2 2v2H4c-1.11 0-1.99.89-1.99 2L2 19c0 1.11.89 2 2 2h16c1.11 0 2-.89 2-2V8c0-1.11-.89-2-2-2zm-6 0h-4V4h4v2z"/>
									</svg>
								</div>
								<strong>Project Type:</strong>
								<span class="inquiry-info-item-value"><?php echo htmlspecialchars($inq['project_type']); ?></span>
							</div>
							<?php endif; ?>
							<?php if (!empty($inq['subject'])): ?>
							<div class="inquiry-info-item">
								<div class="inquiry-info-item-icon">
									<svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
										<path d="M20 4H4c-1.1 0-1.99.9-1.99 2L2 18c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm-2 12H6v-2h12v2zm0-4H6v-2h12v2zm0-4H6V6h12v2z"/>
									</svg>
								</div>
								<strong>Subject:</strong>
								<span class="inquiry-info-item-value"><?php echo htmlspecialchars($inq['subject']); ?></span>
							</div>
							<?php endif; ?>
						</div>

						<?php if (!empty($inq['message'])): ?>
						<div class="inquiry-message"><?php echo nl2br(htmlspecialchars($inq['message'])); ?></div>
						<?php endif; ?>

						<div class="inquiry-footer">
							<div class="inquiry-date">
								<svg class="inquiry-date-icon" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
									<path d="M19 3h-1V1h-2v2H8V1H6v2H5c-1.11 0-1.99.9-1.99 2L3 19c0 1.1.89 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm0 16H5V8h14v11zM7 10h5v5H7z"/>
								</svg>
								<span><?php echo formatAustraliaDate($inq['created_at']); ?></span>
							</div>
							<div class="inquiry-actions">
								<?php if ($inq['status'] === 'unread'): ?>
									<form method="post" action="" class="mark-read-form" style="display: inline;">
										<input type="hidden" name="id" value="<?php echo (int)$inq['id']; ?>">
										<input type="hidden" name="action" value="read">
										<button type="submit" class="btn btn-mark-read">Mark as Read</button>
									</form>
								<?php endif; ?>
								<form method="post" action="" class="delete-inquiry-form" style="display: inline;">
									<input type="hidden" name="id" value="<?php echo (int)$inq['id']; ?>">
									<input type="hidden" name="action" value="delete">
									<button type="submit" class="btn btn-delete">Delete</button>
								</form>
							</div>
						</div>
					</div>
					<?php endforeach; ?>
				</div>
			<?php endif; ?>
		</div>

		<!-- Read Inquiries Section -->
		<div class="inquiries-section" id="sectionRead">
			<div class="section-header">
				<h2 class="section-title">
					Read Inquiries
					<span class="section-count read"><?php echo $readCount; ?></span>
				</h2>
			</div>
			<?php if (empty($readInquiries)): ?>
				<div class="empty-state">
					<h2>No Read Inquiries</h2>
					<p>Inquiries marked as read will appear here.</p>
				</div>
			<?php else: ?>
				<div class="inquiries-grid">
					<?php foreach ($readInquiries as $inq): 
					// Ensure all fields exist
					$inq = array_merge([
						'id' => 0,
						'name' => '',
						'email' => '',
						'phone' => '',
						'project_type' => '',
						'subject' => '',
						'message' => '',
						'status' => 'read',
						'created_at' => date('Y-m-d H:i:s')
					], $inq);
				?>
					<div class="inquiry-card <?php echo $inq['status'] === 'unread' ? 'unread' : 'read'; ?>">
						<div class="inquiry-header">
							<h3 class="inquiry-name"><?php echo htmlspecialchars($inq['name']); ?></h3>
							<span class="inquiry-badge <?php echo $inq['status']; ?>">
								<?php echo $inq['status'] === 'unread' ? 'NEW' : 'READ'; ?>
							</span>
						</div>

						<div class="inquiry-info">
							<?php if (!empty($inq['email'])): ?>
							<div class="inquiry-info-item">
								<div class="inquiry-info-item-icon">
									<svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
										<path d="M20 4H4c-1.1 0-1.99.9-1.99 2L2 18c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 4l-8 5-8-5V6l8 5 8-5v2z"/>
									</svg>
								</div>
								<strong>Email:</strong>
								<span class="inquiry-info-item-value">
									<a href="mailto:<?php echo htmlspecialchars($inq['email']); ?>"><?php echo htmlspecialchars($inq['email']); ?></a>
								</span>
							</div>
							<?php endif; ?>
							<?php if (!empty($inq['phone'])): ?>
							<div class="inquiry-info-item">
								<div class="inquiry-info-item-icon">
									<svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
										<path d="M6.62 10.79c1.44 2.83 3.76 5.14 6.59 6.59l2.2-2.2c.27-.27.67-.36 1.02-.24 1.12.37 2.33.57 3.57.57.55 0 1 .45 1 1V20c0 .55-.45 1-1 1-9.39 0-17-7.61-17-17 0-.55.45-1 1-1h3.5c.55 0 1 .45 1 1 0 1.25.2 2.45.57 3.57.11.35.03.74-.25 1.02l-2.2 2.2z"/>
									</svg>
								</div>
								<strong>Phone:</strong>
								<span class="inquiry-info-item-value">
									<a href="tel:<?php echo htmlspecialchars($inq['phone']); ?>"><?php echo htmlspecialchars($inq['phone']); ?></a>
								</span>
							</div>
							<?php endif; ?>
							<?php if (!empty($inq['project_type'])): ?>
							<div class="inquiry-info-item">
								<div class="inquiry-info-item-icon">
									<svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
										<path d="M20 6h-4V4c0-1.11-.89-2-2-2h-4c-1.11 0-2 .89-2 2v2H4c-1.11 0-1.99.89-1.99 2L2 19c0 1.11.89 2 2 2h16c1.11 0 2-.89 2-2V8c0-1.11-.89-2-2-2zm-6 0h-4V4h4v2z"/>
									</svg>
								</div>
								<strong>Project Type:</strong>
								<span class="inquiry-info-item-value"><?php echo htmlspecialchars($inq['project_type']); ?></span>
							</div>
							<?php endif; ?>
							<?php if (!empty($inq['subject'])): ?>
							<div class="inquiry-info-item">
								<div class="inquiry-info-item-icon">
									<svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
										<path d="M20 4H4c-1.1 0-1.99.9-1.99 2L2 18c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm-2 12H6v-2h12v2zm0-4H6v-2h12v2zm0-4H6V6h12v2z"/>
									</svg>
								</div>
								<strong>Subject:</strong>
								<span class="inquiry-info-item-value"><?php echo htmlspecialchars($inq['subject']); ?></span>
							</div>
							<?php endif; ?>
						</div>

						<?php if (!empty($inq['message'])): ?>
						<div class="inquiry-message"><?php echo nl2br(htmlspecialchars($inq['message'])); ?></div>
						<?php endif; ?>

						<div class="inquiry-footer">
							<div class="inquiry-date">
								<svg class="inquiry-date-icon" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
									<path d="M19 3h-1V1h-2v2H8V1H6v2H5c-1.11 0-1.99.9-1.99 2L3 19c0 1.1.89 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm0 16H5V8h14v11zM7 10h5v5H7z"/>
								</svg>
								<span><?php echo formatAustraliaDate($inq['created_at']); ?></span>
							</div>
							<div class="inquiry-actions">
								<form method="post" action="" class="delete-inquiry-form" style="display: inline;">
									<input type="hidden" name="id" value="<?php echo (int)$inq['id']; ?>">
									<input type="hidden" name="action" value="delete">
									<button type="submit" class="btn btn-delete">Delete</button>
								</form>
							</div>
						</div>
					</div>
					<?php endforeach; ?>
				</div>
			<?php endif; ?>
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

		// Close on Escape key
		document.addEventListener('keydown', function(e) {
			if (e.key === 'Escape') {
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

		// Replace all confirmations
		document.addEventListener('DOMContentLoaded', function() {
			// Tab Switching Functionality
			function switchTab(tabName) {
				// Remove active class from all tabs and sections
				document.querySelectorAll('.tab-button').forEach(btn => {
					btn.classList.remove('active');
				});
				document.querySelectorAll('.inquiries-section').forEach(section => {
					section.classList.remove('active');
				});

				// Add active class to selected tab and section
				const selectedTab = document.querySelector(`[data-tab="${tabName}"]`);
				const selectedSection = document.getElementById(`section${tabName.charAt(0).toUpperCase() + tabName.slice(1)}`);
				
				if (selectedTab) {
					selectedTab.classList.add('active');
				}
				if (selectedSection) {
					selectedSection.classList.add('active');
				}

				// Store active tab in sessionStorage
				sessionStorage.setItem('activeInquiryTab', tabName);
			}

			// Tab button event listeners
			document.querySelectorAll('.tab-button').forEach(button => {
				button.addEventListener('click', function() {
					const tabName = this.getAttribute('data-tab');
					switchTab(tabName);
				});
			});

			// Restore active tab from URL parameter or sessionStorage on page load
			const urlParams = new URLSearchParams(window.location.search);
			const tabParam = urlParams.get('tab');
			const savedTab = sessionStorage.getItem('activeInquiryTab');
			
			if (tabParam && (tabParam === 'unread' || tabParam === 'read')) {
				switchTab(tabParam);
			} else if (savedTab && (savedTab === 'unread' || savedTab === 'read')) {
				switchTab(savedTab);
			}
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

			// Mark as read forms
			const markReadForms = document.querySelectorAll('form.mark-read-form');
			markReadForms.forEach(form => {
				form.addEventListener('submit', function(e) {
					e.preventDefault();
					showConfirm('Mark as Read', 'Mark this inquiry as read?', 'info', function() {
						form.submit();
					});
				});
			});

			// Delete inquiry forms
			const deleteForms = document.querySelectorAll('form.delete-inquiry-form');
			deleteForms.forEach(form => {
				form.addEventListener('submit', function(e) {
					e.preventDefault();
					showConfirm('Delete Inquiry', 'Are you sure you want to delete this inquiry permanently? This action cannot be undone.', 'danger', function() {
						form.submit();
					});
				});
			});
		});
	</script>
</body>
</html>
