<?php
// public/index.php
declare(strict_types=1);

require __DIR__ . '/../includes/security.php';

start_secure_session();
send_security_headers();
$token = csrf_token();
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- CSRF token for JavaScript requests, escaped before output -->
    <meta name="csrf-token" content="<?= e($token) ?>">
    <title>Comments</title>
    <!-- No inline scripts: the Content-Security-Policy only allows
         scripts loaded from this site. -->
    <script src="js/app.js" defer></script>
</head>
<body>
    <h1>Comments</h1>

    <form id="comment-form" novalidate>
        <label for="author">Name</label>
        <input id="author" name="author" type="text" maxlength="50" required>

        <label for="body">Comment</label>
        <textarea id="body" name="body" maxlength="1000" required></textarea>

        <button type="submit">Post</button>
    </form>

    <p id="status" role="status" aria-live="polite"></p>
    <ul id="comment-list"></ul>
</body>
</html>
