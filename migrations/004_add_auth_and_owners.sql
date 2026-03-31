CREATE TABLE IF NOT EXISTS system_users (
                                            id SERIAL PRIMARY KEY,
                                            email VARCHAR(255) UNIQUE NOT NULL,
                                            password VARCHAR(255) NOT NULL
);

INSERT INTO system_users (email, password)
VALUES ('admin@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi');

ALTER TABLE organization ADD COLUMN IF NOT EXISTS owner_id INT;

UPDATE organization SET owner_id = 1 WHERE owner_id IS NULL;

ALTER TABLE organization ADD CONSTRAINT fk_organization_owner FOREIGN KEY (owner_id) REFERENCES system_users(id) ON DELETE CASCADE;