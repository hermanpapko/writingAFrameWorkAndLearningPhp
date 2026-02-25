CREATE TABLE users (
                       id SERIAL PRIMARY KEY,
                       country VARCHAR(100),
                       city VARCHAR(100),
                       is_active BOOLEAN,
                       gender VARCHAR(20),
                       birth_date DATE,
                       salary NUMERIC(10, 2),
                       has_children BOOLEAN,
                       family_status VARCHAR(50),
                       registration_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);