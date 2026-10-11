<?php
// includes/security.php
// Shared security helpers. Keep OUTSIDE the public web root.
declare(strict_types=1);

// Session cookie that is HTTPS-only, hidden from JavaScript,
// and not sent on cross-site requests.
function start_secure_session(): void
{
    if (session_status() === PHP_SESSION_ACTIVE) {
        return;
    }
    ini_set('session.use_strict_mode', '1');
    session_set_cookie_params([
        'lifetime' => 0,
        'path'     => '/',
        'secure'   => true,
        'httponly' => true,
        'samesite' => 'Strict',
    ]);
    session_start();
}

// HTTP headers that limit what the browser will run and load.
function send_security_headers(): void
{
    header("Content-Security-Policy: default-src 'self'; script-src 'self'; "
        . "object-src 'none'; base-uri 'self'; frame-ancestors 'none'");
    header('X-Content-Type-Options: nosniff');
    header('Referrer-Policy: strict-origin-when-cross-origin');
    header('Strict-Transport-Security: max-age=31536000; includeSubDomains');
}

// Cross-Site Request Forgery (CSRF) protection.
function csrf_token(): string
{
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function verify_csrf(?string $token): bool
{
    return is_string($token)
        && isset($_SESSION['csrf_token'])
        // Constant-time comparison prevents timing attacks.
        && hash_equals($_SESSION['csrf_token'], $token);
}

// Escape any value before placing it in HTML (prevents XSS).
function e(string $value): string
{
    return htmlspecialchars($value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

function json_response(int $status, array $data): never
{
    http_response_code($status);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($data);
    exit;
}
