<?php
// includes/db.php
// Keep this file OUTSIDE the public web root so it can never be served.
declare(strict_types=1);

function db(): PDO
{
    static $pdo = null;
    if ($pdo instanceof PDO) {
        return $pdo;
    }

    // Credentials come from environment variables, never hard-coded
    // in source code or committed to version control.
    $host = getenv('DB_HOST') ?: 'localhost';
    $name = getenv('DB_NAME');
    $user = getenv('DB_USER');
    $pass = getenv('DB_PASS');

    if (!$name || !$user) {
        throw new RuntimeException('Database configuration is missing.');
    }

    $dsn = "mysql:host={$host};dbname={$name};charset=utf8mb4";

    $pdo = new PDO($dsn, $user, (string) $pass, [
        // Throw exceptions on errors instead of failing silently.
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        // Use real server-side prepared statements, so user input is
        // always sent separately from the SQL and cannot change the query.
        PDO::ATTR_EMULATE_PREPARES   => false,
        // If the database is on another server, also enable TLS here
        // (e.g. the MySQL SSL CA option) so data is encrypted in transit.
    ]);

    return $pdo;
}
