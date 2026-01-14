<?php
/**
 * Website ko security protect garna yo file use huncha.
 */

if (session_status() === PHP_SESSION_NONE) {
    ini_set('session.cookie_httponly', 1);
    ini_set('session.cookie_secure', 0);
    ini_set('session.use_only_cookies', 1);
    ini_set('session.cookie_samesite', 'Strict');
    ini_set('session.gc_maxlifetime', 3600);
    session_start();
}

if (!isset($_SESSION['created'])) {
    $_SESSION['created'] = time();
} else if (time() - $_SESSION['created'] > 1800) {
    session_regenerate_id(true);
    $_SESSION['created'] = time();
}

header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: DENY');
header('X-XSS-Protection: 1; mode=block');
header('Referrer-Policy: strict-origin-when-cross-origin');
header('Permissions-Policy: geolocation=(), microphone=(), camera=()');
header("Content-Security-Policy: default-src 'self'; script-src 'self' 'unsafe-inline' 'unsafe-eval' https://cdn.jsdelivr.net https://unpkg.com; style-src 'self' 'unsafe-inline' https://cdn.jsdelivr.net https://fonts.googleapis.com; font-src 'self' https://fonts.gstatic.com; img-src 'self' data: https:; connect-src 'self';");

function checkAdminLogin()
{
    if (!isset($_SESSION['admin_id'])) {
        logSecurityEvent('UNAUTHORIZED_ACCESS', 'Attempted access to protected page: ' . ($_SERVER['REQUEST_URI'] ?? 'unknown'));
        header('HTTP/1.1 403 Forbidden');
        header('Location: xtzprabin12.php?error=unauthorized');
        exit;
    }

    if (!isset($_SESSION['admin_user']) || !isset($_SESSION['admin_id'])) {
        session_destroy();
        header('Location: xtzprabin12.php?error=session_expired');
        exit;
    }

    if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity'] > 3600)) {
        logSecurityEvent('SESSION_TIMEOUT', 'Session expired for admin: ' . ($_SESSION['admin_user'] ?? 'unknown'));
        session_destroy();
        header('Location: xtzprabin12.php?error=session_expired');
        exit;
    }

    $_SESSION['last_activity'] = time();
}

function generateCSRFToken()
{
    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function verifyCSRFToken($token)
{
    if (!isset($_SESSION['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $token)) {
        error_log('CSRF token mismatch from IP: ' . $_SERVER['REMOTE_ADDR']);
        return false;
    }
    return true;
}

function checkLoginRateLimit()
{
    $ip = $_SERVER['REMOTE_ADDR'];
    $key = 'login_attempts_' . $ip;

    if (isset($_GET['reset_attempts']) && $_GET['reset_attempts'] === 'admin') {
        unset($_SESSION[$key]);
    }

    if (!isset($_SESSION[$key])) {
        $_SESSION[$key] = ['count' => 0, 'time' => time()];
    }

    $attempts = &$_SESSION[$key];

    if (time() - $attempts['time'] > 300) {
        $attempts = ['count' => 0, 'time' => time()];
    }

    if ($attempts['count'] >= 5) {
        error_log('Login rate limit exceeded for IP: ' . $ip);
        return false;
    }

    return true;
}

function incrementLoginAttempts()
{
    $ip = $_SERVER['REMOTE_ADDR'];
    $key = 'login_attempts_' . $ip;

    if (!isset($_SESSION[$key])) {
        $_SESSION[$key] = ['count' => 0, 'time' => time()];
    }

    $_SESSION[$key]['count']++;
    $_SESSION[$key]['time'] = time();
}

function resetLoginAttempts()
{
    $ip = $_SERVER['REMOTE_ADDR'];
    $key = 'login_attempts_' . $ip;
    unset($_SESSION[$key]);
}

function sanitizeInput($data)
{
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
    return $data;
}

function validateFileUpload($file, $allowedTypes = ['image/jpeg', 'image/png', 'image/jpg', 'image/gif', 'image/webp'])
{
    if (!isset($file['tmp_name']) || !is_uploaded_file($file['tmp_name'])) {
        return ['valid' => false, 'error' => 'Invalid file upload'];
    }

    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mimeType = finfo_file($finfo, $file['tmp_name']);
    finfo_close($finfo);

    if (!in_array($mimeType, $allowedTypes)) {
        return ['valid' => false, 'error' => 'Invalid file type'];
    }

    if ($file['size'] > 10 * 1024 * 1024) {
        return ['valid' => false, 'error' => 'File too large'];
    }

    return ['valid' => true];
}

function logSecurityEvent($event, $details = '')
{
    $ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
    $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? 'unknown';
    $uri = $_SERVER['REQUEST_URI'] ?? 'unknown';
    $log = date('Y-m-d H:i:s') . ' | ' . $event . ' | IP: ' . $ip . ' | URI: ' . $uri . ' | ' . $details . ' | UA: ' . substr($userAgent, 0, 100) . PHP_EOL;
    $logFile = __DIR__ . '/../logs/security.log';
    $logDir = dirname($logFile);

    if (!is_dir($logDir)) {
        @mkdir($logDir, 0755, true);
    }

    @file_put_contents($logFile, $log, FILE_APPEND | LOCK_EX);
}

function validatePasswordStrength($password)
{
    $errors = [];

    if (strlen($password) < 8) {
        $errors[] = 'Password must be at least 8 characters long';
    }

    if (!preg_match('/[A-Z]/', $password)) {
        $errors[] = 'Password must contain at least one uppercase letter';
    }

    if (!preg_match('/[a-z]/', $password)) {
        $errors[] = 'Password must contain at least one lowercase letter';
    }

    if (!preg_match('/[0-9]/', $password)) {
        $errors[] = 'Password must contain at least one number';
    }

    return $errors;
}

function escapeOutput($data)
{
    return htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
}
