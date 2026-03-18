BEGIN;

ALTER TABLE users ADD COLUMN organization_id INT;

WITH new_org AS (
    INSERT INTO organization (name) VALUES ('innowise') RETURNING id
)
UPDATE users SET organization_id = (SELECT id FROM new_org);

ALTER TABLE users ALTER COLUMN organization_id SET NOT NULL;
ALTER TABLE users ADD CONSTRAINT fk_organization FOREIGN KEY (organization_id) REFERENCES organization(id) ON DELETE CASCADE;

COMMIT;