<?php
// public/api/comments.php
// JSON API: GET lists comments, POST adds one.
declare(strict_types=1);

require __DIR__ . '/../../includes/security.php';
require __DIR__ . '/../../includes/db.php';

start_secure_session();
send_security_headers();

try {
    switch ($_SERVER['REQUEST_METHOD']) {

        case 'GET':
            $stmt = db()->prepare(
                'SELECT id, author, body, created_at
                   FROM comments
               ORDER BY created_at DESC
                  LIMIT 50'
            );
            $stmt->execute();
            json_response(200, ['comments' => $stmt->fetchAll()]);

        case 'POST':
            // 1. Reject forged requests from other sites.
            if (!verify_csrf($_SERVER['HTTP_X_CSRF_TOKEN'] ?? null)) {
                json_response(403, ['error' => 'Invalid request token.']);
            }

            // 2. Reject oversized request bodies.
            if ((int) ($_SERVER['CONTENT_LENGTH'] ?? 0) > 10_000) {
                json_response(413, ['error' => 'Request too large.']);
            }

            // 3. Basic rate limit: one post every 10 seconds per session.
            $now = time();
            if (isset($_SESSION['last_post']) && $now - $_SESSION['last_post'] < 10) {
                json_response(429, ['error' => 'Please wait before posting again.']);
            }

            // 4. Validate input on the server. Client-side checks in
            //    JavaScript are a convenience only and can be bypassed.
            $input = json_decode((string) file_get_contents('php://input'), true);
            if (!is_array($input)) {
                json_response(400, ['error' => 'Invalid request format.']);
            }

            $author = trim((string) ($input['author'] ?? ''));
            $body   = trim((string) ($input['body'] ?? ''));

            if ($author === '' || mb_strlen($author) > 50) {
                json_response(422, ['error' => 'Name must be 1-50 characters.']);
            }
            if ($body === '' || mb_strlen($body) > 1000) {
                json_response(422, ['error' => 'Comment must be 1-1000 characters.']);
            }

            // 5. Parameterized query: input is bound as data, never
            //    concatenated into the SQL string (prevents SQL injection).
            $stmt = db()->prepare(
                'INSERT INTO comments (author, body) VALUES (:author, :body)'
            );
            $stmt->execute([':author' => $author, ':body' => $body]);

            $_SESSION['last_post'] = $now;
            json_response(201, ['id' => (int) db()->lastInsertId()]);

        default:
            header('Allow: GET, POST');
            json_response(405, ['error' => 'Method not allowed.']);
    }
} catch (Throwable $ex) {
    // Log the details privately; show the user a generic message so
    // database names, queries, and file paths are never leaked.
    error_log('comments API error: ' . $ex->getMessage());
    json_response(500, ['error' => 'Something went wrong. Please try again.']);
}
