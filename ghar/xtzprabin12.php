<?php
// Security protection
require_once __DIR__ . '/../includes/security.php';

// If already logged in, redirect to dashboard
if (isset($_SESSION['admin_id'])) {
	header('Location: xtztragiikz.php');
	exit;
}

require_once __DIR__ . '/../includes/db_connect.php';

// Create a default admin if none exists (first-time setup)
try {
	$pdo->exec("CREATE TABLE IF NOT EXISTS admins (
		id INT AUTO_INCREMENT PRIMARY KEY,
		username VARCHAR(100) NOT NULL UNIQUE,
		password_hash VARCHAR(255) NOT NULL,
		created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
	) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;");

	$count = (int)$pdo->query("SELECT COUNT(*) FROM admins")->fetchColumn();
	if ($count === 0) {
		$defaultUser = 'admin';
		$defaultPass = 'admin123';
		$hash = password_hash($defaultPass, PASSWORD_DEFAULT);
		$stmt = $pdo->prepare("INSERT INTO admins (username, password_hash) VALUES (?, ?)");
		$stmt->execute([$defaultUser, $hash]);
	}
} catch (Throwable $e) {
	// Ignore seeding errors; login page will still render
}

$error = '';
$showSuccess = false;
$csrfToken = generateCSRFToken();

// Allow manual reset of login attempts via URL parameter
$resetMessage = '';
if (isset($_GET['reset_attempts']) && $_GET['reset_attempts'] === 'admin') {
    resetLoginAttempts();
    $resetMessage = 'Login attempts have been reset. You can now try logging in again.';
    $error = ''; // Clear any error
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
	// Verify CSRF token
	if (!isset($_POST['csrf_token']) || !verifyCSRFToken($_POST['csrf_token'])) {
		$error = 'Security token mismatch. Please try again.';
		logSecurityEvent('CSRF_TOKEN_FAILED', 'Login attempt with invalid token');
	} else {
		// Check rate limiting
		if (!checkLoginRateLimit()) {
			$error = 'Too many login attempts. Please wait 5 minutes before trying again, or add ?reset_attempts=admin to the URL to reset immediately.';
			logSecurityEvent('RATE_LIMIT_EXCEEDED', 'Too many login attempts from IP: ' . $_SERVER['REMOTE_ADDR']);
		} else {
			$username = sanitizeInput($_POST['username'] ?? '');
			$password = $_POST['password'] ?? '';

			if ($username === '' || $password === '') {
				$error = 'Please enter your username and password.';
			} else {
				$stmt = $pdo->prepare("SELECT id, username, password_hash FROM admins WHERE username = ?");
				$stmt->execute([$username]);
				$user = $stmt->fetch();
				if ($user && password_verify($password, $user['password_hash'])) {
					// Successful login
					$_SESSION['admin_id'] = (int)$user['id'];
					$_SESSION['admin_user'] = $user['username'];
					$_SESSION['created'] = time();
					$_SESSION['last_activity'] = time();
					$_SESSION['ip_address'] = $_SERVER['REMOTE_ADDR']; // Store IP for validation
					resetLoginAttempts();
					logSecurityEvent('LOGIN_SUCCESS', 'Admin logged in: ' . $username);
					$showSuccess = true;
				} else {
					$error = 'Invalid username or password.';
					incrementLoginAttempts();
					logSecurityEvent('LOGIN_FAILED', 'Failed login attempt for username: ' . $username);
				}
			}
		}
	}
	// Regenerate CSRF token after POST
	$csrfToken = generateCSRFToken();
}

// Show logout message if redirected from logout
$logoutMessage = isset($_GET['logout']) ? 'You have been successfully logged out.' : '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Admin Login - Safa Formwork</title>
	<link rel="stylesheet" href="../css/global.css">
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

		.login-wrapper {
			width: 100%;
			max-width: 480px;
		}

		.login-card {
			background: #ffffff;
			border-radius: 24px;
			box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
			padding: 48px 40px;
			position: relative;
			overflow: hidden;
		}

		.login-card::before {
			content: '';
			position: absolute;
			top: 0;
			left: 0;
			right: 0;
			height: 4px;
			background: linear-gradient(90deg, #E0B27C, #C89A60, #E0B27C);
			background-size: 200% 100%;
			animation: shimmer 3s ease-in-out infinite;
		}

		@keyframes shimmer {
			0%, 100% { background-position: 0% 50%; }
			50% { background-position: 100% 50%; }
		}

		.login-header {
			text-align: center;
			margin-bottom: 40px;
		}

		.login-logo {
			width: 80px;
			height: 80px;
			margin: 0 auto 24px;
			background: linear-gradient(135deg, #E0B27C, #C89A60);
			border-radius: 20px;
			display: flex;
			align-items: center;
			justify-content: center;
			font-size: 40px;
			box-shadow: 0 8px 24px rgba(200, 154, 96, 0.3);
		}

		.login-logo img {
			width: 60px;
			height: 60px;
			object-fit: contain;
			display: block;
			border-radius: 12px;
			background: transparent;
		}

		.login-title {
			font-size: 2rem;
			font-weight: 700;
			color: #1a1a1a;
			margin-bottom: 8px;
			letter-spacing: -0.5px;
		}

		.login-subtitle {
			font-size: 1rem;
			color: #666;
			font-weight: 400;
		}

		.form-group {
			margin-bottom: 24px;
			position: relative;
		}

		.form-label {
			display: block;
			margin-bottom: 8px;
			color: #333;
			font-weight: 600;
			font-size: 0.95rem;
		}

		.input-wrapper {
			position: relative;
		}

		.form-input {
			width: 100%;
			padding: 16px 50px 16px 18px;
			border: 2px solid #e0e0e0;
			border-radius: 12px;
			font-size: 1rem;
			outline: none;
			transition: all 0.3s ease;
			background: #fff;
			color: #1a1a1a;
			font-family: inherit;
		}

		.form-input:focus {
			border-color: #c2925f;
			box-shadow: 0 0 0 4px rgba(194, 146, 95, 0.1);
			transform: translateY(-2px);
		}

		.password-toggle {
			position: absolute;
			right: 12px;
			top: 50%;
			transform: translateY(-50%);
			background: rgba(255, 255, 255, 0.9);
			border: 1px solid #e0e0e0;
			border-radius: 6px;
			cursor: pointer;
			padding: 6px 8px;
			color: #666;
			display: flex;
			align-items: center;
			justify-content: center;
			transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
			z-index: 10;
			min-width: 36px;
			min-height: 36px;
			box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
		}

		.password-toggle:hover {
			color: #c2925f;
			background: rgba(255, 255, 255, 1);
			border-color: #c2925f;
			box-shadow: 0 2px 6px rgba(194, 146, 95, 0.2);
			transform: translateY(-50%) scale(1.05);
		}

		.password-toggle:active {
			transform: translateY(-50%) scale(0.95);
		}

		.password-toggle svg {
			width: 20px;
			height: 20px;
			transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
		}

		.password-toggle:hover svg {
			transform: scale(1.1);
		}

		.toggle-icon {
			transition: opacity 0.15s ease, transform 0.15s ease;
		}

		.btn-primary {
			width: 100%;
			padding: 16px 24px;
			border-radius: 12px;
			background: linear-gradient(135deg, #E0B27C, #C89A60);
			border: none;
			color: #fff;
			font-weight: 700;
			font-size: 1rem;
			cursor: pointer;
			box-shadow: 0 8px 24px rgba(200, 154, 96, 0.35);
			transition: all 0.3s ease;
			margin-top: 8px;
			position: relative;
			overflow: hidden;
		}

		.btn-primary::before {
			content: '';
			position: absolute;
			top: 50%;
			left: 50%;
			width: 0;
			height: 0;
			border-radius: 50%;
			background: rgba(255, 255, 255, 0.3);
			transform: translate(-50%, -50%);
			transition: width 0.6s, height 0.6s;
		}

		.btn-primary:hover::before {
			width: 300px;
			height: 300px;
		}

		.btn-primary:hover {
			background: linear-gradient(135deg, #D4A574, #B88950);
			box-shadow: 0 12px 32px rgba(200, 154, 96, 0.45);
			transform: translateY(-2px);
		}

		.btn-primary:active {
			transform: translateY(0);
		}

		/* Secondary button */
		.btn-secondary {
			width: 100%;
			display: inline-block;
			text-align: center;
			padding: 14px 24px;
			border-radius: 12px;
			background: #f5f5f5;
			border: 2px solid #e0e0e0;
			color: #333;
			font-weight: 700;
			font-size: 1rem;
			cursor: pointer;
			transition: all 0.3s ease;
			margin-top: 12px;
			text-decoration: none;
		}

		.btn-secondary:hover {
			background: #eaeaea;
			border-color: #d8d8d8;
			transform: translateY(-1px);
		}

		.error-message {
			background: linear-gradient(135deg, #ffebee, #ffcdd2);
			color: #c62828;
			border: 2px solid #ef5350;
			border-radius: 12px;
			padding: 14px 18px;
			margin-bottom: 24px;
			font-weight: 500;
			display: flex;
			align-items: center;
			gap: 10px;
			animation: shake 0.5s ease;
		}

		@keyframes shake {
			0%, 100% { transform: translateX(0); }
			25% { transform: translateX(-10px); }
			75% { transform: translateX(10px); }
		}

		.error-message::before {
			content: '⚠️';
			font-size: 20px;
		}

		.success-message-box {
			background: linear-gradient(135deg, #e8f5e9, #c8e6c9);
			color: #2e7d32;
			border: 2px solid #4caf50;
			border-radius: 12px;
			padding: 14px 18px;
			margin-bottom: 24px;
			font-weight: 500;
			display: flex;
			align-items: center;
			gap: 10px;
			animation: slideDown 0.5s ease;
		}

		@keyframes slideDown {
			from {
				opacity: 0;
				transform: translateY(-10px);
			}
			to {
				opacity: 1;
				transform: translateY(0);
			}
		}

		.success-message-box::before {
			content: '✓';
			font-size: 20px;
		}

		.footer-note {
			margin-top: 32px;
			text-align: center;
			color: #999;
			font-size: 0.9rem;
		}

		/* Success Modal */
		.success-modal-overlay {
			display: none;
			position: fixed;
			top: 0;
			left: 0;
			width: 100%;
			height: 100%;
			background: rgba(0, 0, 0, 0.6);
			backdrop-filter: blur(4px);
			z-index: 3000;
			align-items: center;
			justify-content: center;
			padding: 20px;
			opacity: 0;
			transition: opacity 0.3s ease;
		}

		.success-modal-overlay.active {
			display: flex;
			opacity: 1;
		}

		.success-modal {
			background: #fff;
			border-radius: 24px;
			padding: 48px 40px;
			max-width: 400px;
			width: 100%;
			box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
			transform: scale(0.9);
			transition: transform 0.3s ease;
			text-align: center;
		}

		.success-modal-overlay.active .success-modal {
			transform: scale(1);
		}

		.success-icon {
			width: 80px;
			height: 80px;
			margin: 0 auto 24px;
			background: linear-gradient(135deg, #4caf50, #388e3c);
			border-radius: 50%;
			display: flex;
			align-items: center;
			justify-content: center;
			font-size: 40px;
			animation: scaleIn 0.5s ease;
		}

		@keyframes scaleIn {
			0% {
				transform: scale(0);
			}
			50% {
				transform: scale(1.1);
			}
			100% {
				transform: scale(1);
			}
		}

		.success-title {
			font-size: 1.5rem;
			font-weight: 700;
			color: #1a1a1a;
			margin-bottom: 12px;
		}

		.success-message {
			font-size: 1rem;
			color: #666;
			line-height: 1.6;
		}

		/* Responsive */
		@media (max-width: 768px) {
			.login-card {
				padding: 36px 28px;
				border-radius: 20px;
			}

			.login-title {
				font-size: 1.75rem;
			}

			.login-logo {
				width: 70px;
				height: 70px;
				font-size: 36px;
			}

			.form-input {
				padding: 14px 48px 14px 16px;
			}
		}

		@media (max-width: 480px) {
			.login-card {
				padding: 32px 24px;
			}

			.login-title {
				font-size: 1.5rem;
			}

			.login-logo {
				width: 60px;
				height: 60px;
				font-size: 32px;
				margin-bottom: 20px;
			}
		}
	</style>
</head>
<body>
	<div class="login-wrapper">
		<div class="login-card">
			<div class="login-header">
				<div class="login-logo"><img src="../assets/login-logo.jpg" alt="Safa Logo"></div>
				<h1 class="login-title">Admin Login</h1>
				<p class="login-subtitle">Access the Safa Formwork admin panel</p>
			</div>

			<?php if ($logoutMessage): ?>
				<div class="success-message-box">
					<?php echo htmlspecialchars($logoutMessage); ?>
				</div>
			<?php endif; ?>

			<?php if ($resetMessage): ?>
				<div class="success-message-box">
					<?php echo htmlspecialchars($resetMessage); ?>
				</div>
			<?php endif; ?>

			<?php if ($error): ?>
				<div class="error-message"><?php echo htmlspecialchars($error); ?></div>
			<?php endif; ?>

			<form method="post" action="" id="loginForm">
				<!-- CSRF Protection Token -->
				<input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrfToken); ?>">
				
				<div class="form-group">
					<label for="username" class="form-label">Username</label>
					<div class="input-wrapper">
						<input type="text" id="username" name="username" class="form-input" required autocomplete="username" placeholder="Enter your username">
					</div>
				</div>

				<div class="form-group">
					<label for="password" class="form-label">Password</label>
					<div class="input-wrapper">
						<input type="password" id="password" name="password" class="form-input" required autocomplete="current-password" placeholder="Enter your password">
						<button type="button" class="password-toggle" id="passwordToggle" aria-label="Toggle password visibility" title="Show/Hide Password">
							<svg id="eyeIcon" class="toggle-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
								<path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
								<circle cx="12" cy="12" r="3"></circle>
							</svg>
							<svg id="eyeOffIcon" class="toggle-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="display: none;">
								<path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"></path>
								<line x1="1" y1="1" x2="23" y2="23"></line>
							</svg>
						</button>
					</div>
				</div>

				<button type="submit" class="btn-primary">
					<span style="position: relative; z-index: 1;">Sign In</span>
				</button>
				<a href="../index.php" class="btn-secondary">Back to Home</a>
			</form>

			<div class="footer-note">© <?php echo date('Y'); ?> Safa Formwork. All rights reserved.</div>
		</div>
	</div>

	<!-- Success Modal -->
	<?php if ($showSuccess): ?>
	<div class="success-modal-overlay active" id="successModal">
		<div class="success-modal">
			<div class="success-icon">✓</div>
			<h2 class="success-title">Login Successful!</h2>
			<p class="success-message">Welcome back! Redirecting to dashboard...</p>
		</div>
	</div>
	<script>
		setTimeout(function() {
			window.location.href = 'xtztragiikz.php';
		}, 1500);
	</script>
	<?php endif; ?>

	<script>
		// Enhanced Password Toggle with smooth animations
		const passwordToggle = document.getElementById('passwordToggle');
		const passwordInput = document.getElementById('password');
		const eyeIcon = document.getElementById('eyeIcon');
		const eyeOffIcon = document.getElementById('eyeOffIcon');

		function togglePassword() {
			if (passwordInput.type === 'password') {
				passwordInput.type = 'text';
				eyeIcon.style.opacity = '0';
				eyeIcon.style.transform = 'scale(0.8)';
				setTimeout(() => {
					eyeIcon.style.display = 'none';
					eyeOffIcon.style.display = 'block';
					eyeOffIcon.style.opacity = '0';
					eyeOffIcon.style.transform = 'scale(0.8)';
					setTimeout(() => {
						eyeOffIcon.style.opacity = '1';
						eyeOffIcon.style.transform = 'scale(1)';
					}, 10);
				}, 150);
				passwordToggle.setAttribute('aria-label', 'Hide password');
				passwordToggle.setAttribute('title', 'Hide Password');
			} else {
				passwordInput.type = 'password';
				eyeOffIcon.style.opacity = '0';
				eyeOffIcon.style.transform = 'scale(0.8)';
				setTimeout(() => {
					eyeOffIcon.style.display = 'none';
					eyeIcon.style.display = 'block';
					eyeIcon.style.opacity = '0';
					eyeIcon.style.transform = 'scale(0.8)';
					setTimeout(() => {
						eyeIcon.style.opacity = '1';
						eyeIcon.style.transform = 'scale(1)';
					}, 10);
				}, 150);
				passwordToggle.setAttribute('aria-label', 'Show password');
				passwordToggle.setAttribute('title', 'Show Password');
			}
		}

		passwordToggle.addEventListener('click', togglePassword);
		
		// Add keyboard support
		passwordToggle.addEventListener('keydown', function(e) {
			if (e.key === 'Enter' || e.key === ' ') {
				e.preventDefault();
				togglePassword();
			}
		});

		// Form validation enhancement
		const loginForm = document.getElementById('loginForm');
		loginForm.addEventListener('submit', function(e) {
			const username = document.getElementById('username').value.trim();
			const password = document.getElementById('password').value.trim();
			
			if (!username || !password) {
				e.preventDefault();
				return false;
			}
		});
	</script>
</body>
</html>
