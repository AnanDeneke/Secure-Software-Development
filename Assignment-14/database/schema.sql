-- Database setup for the secure comments example (MySQL / MariaDB)

CREATE TABLE comments (
    id         INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    author     VARCHAR(50)   NOT NULL,
    body       VARCHAR(1000) NOT NULL,
    created_at TIMESTAMP     NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Least privilege: the web app gets its own account that can only
-- read and add comments. It cannot drop tables, delete rows, or touch
-- any other table. Never connect the website as root.
CREATE USER 'comments_app'@'localhost'
    IDENTIFIED BY 'replace-with-a-long-randomly-generated-password';

GRANT SELECT, INSERT ON appdb.comments TO 'comments_app'@'localhost';
