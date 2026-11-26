CREATE DATABASE IF NOT EXISTS salary_calculator;

USE salary_calculator;

CREATE TABLE IF NOT EXISTS Salary_Records (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    work_date DATE NOT NULL,
    hours_worked DECIMAL(6,2) NOT NULL,
    hourly_wage DECIMAL(10,2) NOT NULL,
    total_earnings DECIMAL(10,2) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

