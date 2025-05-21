-- Reading Sessions Schema Migration

-- Book Reading Sessions (Track 3-day reads)
CREATE TABLE IF NOT EXISTS reading_session (
    rs_id         SERIAL PRIMARY KEY,
    ua_id         INT NOT NULL,
    b_id          INT NOT NULL,
    rs_started_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    rs_expires_at TIMESTAMP GENERATED ALWAYS AS (rs_started_at + INTERVAL '3 days') STORED,
    CONSTRAINT fk_reading_user FOREIGN KEY (ua_id) REFERENCES user_account(ua_id) ON DELETE CASCADE,
    CONSTRAINT fk_reading_book FOREIGN KEY (b_id) REFERENCES books(b_id) ON DELETE CASCADE
);

-- Reading Progress Tracking
CREATE TABLE IF NOT EXISTS reading_progress (
    rp_id          SERIAL PRIMARY KEY,
    rs_id          INT NOT NULL,
    current_page   INT DEFAULT 1,
    is_completed   BOOLEAN DEFAULT FALSE,
    last_updated   TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_progress_session FOREIGN KEY (rs_id) REFERENCES reading_session(rs_id) ON DELETE CASCADE
);

-- Add new activity type for reading sessions
INSERT INTO activity_type (at_code, at_name)
SELECT 'READ_SESSION', 'Reading Session'
WHERE NOT EXISTS (
    SELECT 1 FROM activity_type WHERE at_code = 'READ_SESSION'
);

-- Create index for performance
CREATE INDEX IF NOT EXISTS idx_reading_session_user ON reading_session(ua_id);
CREATE INDEX IF NOT EXISTS idx_reading_session_book ON reading_session(b_id);
CREATE INDEX IF NOT EXISTS idx_reading_progress_session ON reading_progress(rs_id); 