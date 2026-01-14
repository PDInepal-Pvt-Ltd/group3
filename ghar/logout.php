<?php
// Security protection
require_once __DIR__ . '/../includes/security.php';

// Log logout event if admin was logged in
if (isset($_SESSION['admin_id'])) {
	logSecurityEvent('LOGOUT', 'Admin logged out: ' . ($_SESSION['admin_user'] ?? 'Unknown'));
}

// Destroy session
$_SESSION = [];
if (ini_get("session.use_cookies")) {
	$params = session_get_cookie_params();
	setcookie(session_name(), '', time() - 42000,
		$params["path"], $params["domain"],
		$params["secure"], $params["httponly"]
	);
}
session_destroy();
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Logging Out - Safa Formwork</title>
	<style>
		* {
			margin: 0;
			padding: 0;
			box-sizing: border-box;
		}

		body {
			background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
			min-height: 100vh;
			display: flex;
			align-items: center;
			justify-content: center;
			padding: 20px;
			font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
		}

		.logout-modal {
			background: #fff;
			border-radius: 24px;
			padding: 48px 40px;
			max-width: 400px;
			width: 100%;
			box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
			text-align: center;
			animation: fadeIn 0.5s ease;
		}

		@keyframes fadeIn {
			from {
				opacity: 0;
				transform: translateY(-20px);
			}
			to {
				opacity: 1;
				transform: translateY(0);
			}
		}

		.logout-icon {
			width: 80px;
			height: 80px;
			margin: 0 auto 24px;
			background: linear-gradient(135deg, #ff9800, #f57c00);
			border-radius: 50%;
			display: flex;
			align-items: center;
			justify-content: center;
			font-size: 40px;
			animation: pulse 2s ease-in-out infinite;
		}

		@keyframes pulse {
			0%, 100% {
				transform: scale(1);
			}
			50% {
				transform: scale(1.05);
			}
		}

		.logout-title {
			font-size: 1.5rem;
			font-weight: 700;
			color: #1a1a1a;
			margin-bottom: 12px;
		}

		.logout-message {
			font-size: 1rem;
			color: #666;
			line-height: 1.6;
		}

		@media (max-width: 480px) {
			.logout-modal {
				padding: 36px 28px;
			}

			.logout-icon {
				width: 70px;
				height: 70px;
				font-size: 36px;
			}

			.logout-title {
				font-size: 1.3rem;
			}
		}
	</style>
</head>
<body>
	<div class="logout-modal">
		<div class="logout-icon">👋</div>
		<h2 class="logout-title">Logging Out</h2>
		<p class="logout-message">You are being logged out. Redirecting to login page...</p>
	</div>

	<script>
		// Destroy session and redirect after showing message
		setTimeout(function() {
			window.location.href = 'xtzprabin12.php?logout=1';
		}, 1500);
	</script>
</body>
</html>
