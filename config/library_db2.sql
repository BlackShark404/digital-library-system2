-- ROLES Table
CREATE TABLE user_role (
    ur_id SERIAL PRIMARY KEY,
    ur_role_name VARCHAR(20) UNIQUE NOT NULL
);

INSERT INTO user_role (ur_role_name) VALUES 
('user'),
('admin');

-- USERS Table
CREATE TABLE user_account (
    ua_id                          SERIAL PRIMARY KEY,
    ua_profile_url                 VARCHAR(255),
    ua_first_name                  VARCHAR(255) NOT NULL,
    ua_last_name                   VARCHAR(255) NOT NULL,
    ua_email                       VARCHAR(255) UNIQUE NOT NULL,
    ua_hashed_password             VARCHAR(255) NOT NULL,
    ua_phone_number                VARCHAR(20),
    ua_role_id                     INT DEFAULT 1,
    ua_is_active                   BOOLEAN DEFAULT TRUE,
    ua_remember_token              VARCHAR(255),
    ua_remember_token_expires_at   TIMESTAMP,
    ua_last_login                  TIMESTAMP,
    ua_created_at                  TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    ua_updated_at                  TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    ua_deleted_at                  TIMESTAMP,
    CONSTRAINT fk_user_role FOREIGN KEY (ua_role_id)
        REFERENCES user_role(ur_id) ON DELETE SET DEFAULT ON UPDATE CASCADE
);


CREATE TABLE genre (
    g_id SERIAL PRIMARY KEY,
    g_name VARCHAR(100) UNIQUE NOT NULL
);

-- BOOKS Table
CREATE TABLE books (
    b_id                 SERIAL PRIMARY KEY,
    b_cover_path 		 VARCHAR(255),
    b_title              VARCHAR(255) NOT NULL,
    b_author             VARCHAR(255) NOT NULL,
    b_publisher          VARCHAR(255),
    b_publication_date   DATE,
    b_isbn               VARCHAR(20) UNIQUE,
    b_genre_id           INT,
    b_pages              INTEGER,
    b_price              NUMERIC(10, 2),
    b_description        TEXT,
    b_created_at         TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    b_updated_at         TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    b_deleted_at         TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

	CONSTRAINT fk_book_genre FOREIGN KEY (b_genre_id) REFERENCES genre(g_id) ON DELETE SET NULL
);


-- Wishlist Table
CREATE TABLE wishlist (
    wl_id     SERIAL PRIMARY KEY,
    ua_id     INT NOT NULL,
    b_id      INT NOT NULL,
    wl_added_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_wishlist_user FOREIGN KEY (ua_id) REFERENCES user_account(ua_id) ON DELETE CASCADE,
    CONSTRAINT fk_wishlist_book FOREIGN KEY (b_id) REFERENCES books(b_id) ON DELETE CASCADE,
    CONSTRAINT unique_wishlist UNIQUE (ua_id, b_id)
);

-- Purchase Table (User owns the book forever)
CREATE TABLE user_purchase (
    up_id     SERIAL PRIMARY KEY,
    ua_id     INT NOT NULL,
    b_id      INT NOT NULL,
    up_purchased_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_purchase_user FOREIGN KEY (ua_id) REFERENCES user_account(ua_id) ON DELETE CASCADE,
    CONSTRAINT fk_purchase_book FOREIGN KEY (b_id) REFERENCES books(b_id) ON DELETE CASCADE,
    CONSTRAINT unique_user_purchase UNIQUE (ua_id, b_id)
);

-- Book Reading Sessions (Track 3-day reads)
CREATE TABLE reading_session (
    rs_id         SERIAL PRIMARY KEY,
    ua_id         INT NOT NULL,
    b_id          INT NOT NULL,
    rs_started_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    rs_expires_at TIMESTAMP GENERATED ALWAYS AS (rs_started_at + INTERVAL '3 days') STORED,
    CONSTRAINT fk_reading_user FOREIGN KEY (ua_id) REFERENCES user_account(ua_id) ON DELETE CASCADE,
    CONSTRAINT fk_reading_book FOREIGN KEY (b_id) REFERENCES books(b_id) ON DELETE CASCADE
);

-- Reading Progress Tracking
CREATE TABLE reading_progress (
    rp_id          SERIAL PRIMARY KEY,
    rs_id          INT NOT NULL,  -- Tied to a specific reading session
    current_page   INT DEFAULT 1,
    is_completed   BOOLEAN DEFAULT FALSE,
    last_updated   TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_progress_session FOREIGN KEY (rs_id) REFERENCES reading_session(rs_id) ON DELETE CASCADE
);


CREATE TABLE activity_type (
    at_id SERIAL PRIMARY KEY,
    at_code VARCHAR(20) UNIQUE NOT NULL, -- Short code used in logic or frontend
    at_name VARCHAR(50) UNIQUE NOT NULL  -- Full label used in UI
);


CREATE TABLE activity_log (
    al_id           SERIAL PRIMARY KEY,
    ua_id           INT,
    at_id           INT NOT NULL,
    al_description  TEXT,
    al_timestamp    TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    CONSTRAINT fk_log_user FOREIGN KEY (ua_id) REFERENCES user_account(ua_id) ON DELETE SET NULL,
    CONSTRAINT fk_log_type FOREIGN KEY (at_id) REFERENCES activity_type(at_id)
);

INSERT INTO activity_type (at_code, at_name) VALUES
('LOGIN', 'Login'),
('LOGOUT', 'Logout'),
('REGISTER', 'Register'),
('PROFILE_UPDATE', 'Profile Update'),
('PURCHASE', 'Book Purchase'),
('READ_SESSION', 'Reading Session'),
('BOOK_ADDED', 'Book Added'),
('BOOK_UPDATED', 'Book Updated'),
('BOOK_DELETED', 'Book Deleted');

INSERT INTO genre (g_name) VALUES
('Fiction'),
('Non-fiction'),
('Science Fiction'),
('Fantasy'),
('Biography'),
('Mystery'),
('Thriller'),
('Romance'),
('Historical'),
('Self-help');
