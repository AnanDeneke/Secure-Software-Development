# Secure Comments

A small comments page that demonstrates secure software development practices in a website that uses JavaScript on the front end and PHP with a MySQL database on the back end.

Visitors can post a name and a comment, and the page shows the 50 most recent comments.

## Project Structure

```
secure-comments/
├── includes/              # Server-only code, never publicly served
│   ├── db.php             # Database connection (PDO)
│   └── security.php       # Sessions, CSRF, headers, output escaping
├── public/                # Web root: point the web server here
│   ├── index.php          # Page and comment form
│   ├── api/
│   │   └── comments.php   # JSON API: GET lists, POST adds
│   └── js/
│       └── app.js         # Front-end logic
├── database/
│   └── schema.sql         # Table and least-privilege database user
├── .env.example           # Required variables (no real values)
├── .gitignore
└── README.md
```

## Security Controls

| Threat | Control | Where |
|---|---|---|
| SQL injection | Parameterized queries with real (non-emulated) prepared statements | `db.php`, `comments.php` |
| Cross-site scripting (XSS) | Output rendered with `textContent`, server-side HTML escaping, Content Security Policy | `app.js`, `security.php`, `index.php` |
| Cross-site request forgery (CSRF) | Random per-session token checked with a constant-time comparison | `security.php`, `comments.php` |
| Session hijacking | Cookies set to `Secure`, `HttpOnly`, `SameSite=Strict`; strict session mode | `security.php` |
| Invalid or malicious input | Server-side validation of type and length; request size limit | `comments.php` |
| Abuse and spam | Basic per-session rate limit | `comments.php` |
| Information leakage | Errors logged privately; users see a generic message | `comments.php` |
| Credential exposure | Credentials in environment variables, not in source code or Git | `db.php`, `.gitignore` |
| Excessive privileges | Database account limited to `SELECT` and `INSERT` on one table | `schema.sql` |
| Downgrade to HTTP | HTTP Strict Transport Security (HSTS) header | `security.php` |

## Requirements

- PHP 8.1 or newer, with the `pdo_mysql` and `mbstring` extensions
- MySQL 5.7+ or MariaDB 10.3+
- A web server such as Apache or Nginx, served over HTTPS (`localhost` works for local testing)

## Setup

1. **Create the database and user.** Open `database/schema.sql`, change `appdb` to your database name if needed, and run it. Replace the placeholder password with a strong random one (for example, `openssl rand -base64 24`) when you run it. Do not commit the real password.

2. **Set the environment variables.** Copy `.env.example` to `.env` for your own reference and fill in the values. PHP does not read `.env` files automatically, so make the variables available to PHP through your server configuration (for example, `SetEnv` in Apache or `env[...]` in a PHP-FPM pool), or load them with a library such as `vlucas/phpdotenv`.

   | Variable | Description |
   |---|---|
   | `DB_HOST` | Database host (default: `localhost`) |
   | `DB_NAME` | Database name |
   | `DB_USER` | Database user (`comments_app`) |
   | `DB_PASS` | That user's password |

3. **Point the web server at `public/`.** The document root must be the `public/` folder, not the repository root. This keeps `includes/`, `database/` and `.env` unreachable from a browser.

4. **Open the site** at your configured address, for example `https://localhost/`.

## Notes

- The placeholder password in `schema.sql` is intentional. Real credentials must never be committed to version control.
- Client-side validation in `app.js` exists only for usability. All security checks are enforced on the server.

## Reference

Souppaya, M., Scarfone, K., & Dodson, D. (2022). *Secure software development framework (SSDF) version 1.1: Recommendations for mitigating the risk of software vulnerabilities* (NIST Special Publication 800-218). National Institute of Standards and Technology. https://doi.org/10.6028/NIST.SP.800-218

## AI Assistance Disclosure

This project was developed with the assistance of an AI tool (Claude, by Anthropic), which helped draft the code and documentation. All code was reviewed by the author, who is responsible for its content.
