<?php
/**
 * Yo file le database connectivity handle garcha.
 */

if (session_status() === PHP_SESSION_NONE) {
	session_start();
}

$dbConfig = [
	'host' => 'localhost',
	'dbname' => 'safa_formwork',
	'user' => 'root',
	'pass' => '',
	'charset' => 'utf8mb4',
];

$dsn = sprintf(
	"mysql:host=%s;dbname=%s;charset=%s",
	$dbConfig['host'],
	$dbConfig['dbname'],
	$dbConfig['charset']
);

try {
	$options = [
		PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
		PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
		PDO::ATTR_EMULATE_PREPARES => false,
	];
	$pdo = new PDO($dsn, $dbConfig['user'], $dbConfig['pass'], $options);
} catch (PDOException $e) {
	http_response_code(503);
	echo '<!DOCTYPE html><html lang="en"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0"><title>Service Unavailable</title><style>body{font-family:Arial,system-ui,-apple-system,Segoe UI,Roboto,Helvetica,Ubuntu,sans-serif;background:#faf9f6;color:#1e1e1e;margin:0;padding:40px;} .card{max-width:680px;margin:40px auto;background:#fff;border-radius:12px;box-shadow:0 10px 30px rgba(0,0,0,.08);padding:28px;} h1{font-size:1.4rem;margin:0 0 8px;} p{margin:0;color:#444;line-height:1.6;} .muted{color:#777;margin-top:10px;font-size:.95rem}</style></head><body><div class="card"><h1>We&#39;re experiencing a temporary issue</h1><p>Our database connection could not be established right now. Please try again shortly.</p><p class="muted">If this issue persists, contact the site administrator.</p></div></body></html>';
	exit;
}
