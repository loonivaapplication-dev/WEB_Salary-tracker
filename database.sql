-- Member 2 tables: Wage_Settings and Work_Records
-- Assumes the Users table (Member 1) already exists with user_id as PRIMARY KEY.

CREATE TABLE IF NOT EXISTS Wage_Settings (
    wage_id     INT AUTO_INCREMENT PRIMARY KEY,
    user_id     INT NOT NULL,
    hourly_rate DECIMAL(8,2) NOT NULL CHECK (hourly_rate > 0),
    updated_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES Users(user_id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS Work_Records (
    work_id       INT AUTO_INCREMENT PRIMARY KEY,
    user_id       INT NOT NULL,
    work_date     DATE NOT NULL,
    start_time    TIME NOT NULL,
    end_time      TIME NOT NULL,
    break_minutes INT NOT NULL DEFAULT 0,
    total_hours   DECIMAL(5,2) NOT NULL,
    FOREIGN KEY (user_id) REFERENCES Users(user_id) ON DELETE CASCADE
);