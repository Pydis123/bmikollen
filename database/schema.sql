-- BMIKollen Database Schema with prefix bmi_

-- Users Table
CREATE TABLE IF NOT EXISTS bmi_users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(255) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    email_verified_at TIMESTAMP NULL,
    height_cm DECIMAL(5,2) NULL,
    theme_pref ENUM('system', 'light', 'dark') DEFAULT 'system',
    timezone VARCHAR(100) DEFAULT 'Europe/Stockholm',
    status VARCHAR(20) DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Roles Table
CREATE TABLE IF NOT EXISTS bmi_roles (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50) NOT NULL UNIQUE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT IGNORE INTO bmi_roles (name) VALUES ('admin'), ('user');

-- User Roles Mapping
CREATE TABLE IF NOT EXISTS bmi_user_roles (
    user_id INT NOT NULL,
    role_id INT NOT NULL,
    PRIMARY KEY (user_id, role_id),
    FOREIGN KEY (user_id) REFERENCES bmi_users(id) ON DELETE CASCADE,
    FOREIGN KEY (role_id) REFERENCES bmi_roles(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- DB Sessions
CREATE TABLE IF NOT EXISTS bmi_sessions (
    id VARCHAR(128) PRIMARY KEY,
    data TEXT NOT NULL,
    timestamp INT NOT NULL,
    user_id INT NULL,
    INDEX (timestamp),
    INDEX (user_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Invites
CREATE TABLE IF NOT EXISTS bmi_invites (
    id INT AUTO_INCREMENT PRIMARY KEY,
    token VARCHAR(64) NOT NULL UNIQUE,
    email VARCHAR(255) NULL,
    created_by INT NULL,
    consumed_at TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (created_by) REFERENCES bmi_users(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Password Reset & Email Verification Tokens
CREATE TABLE IF NOT EXISTS bmi_tokens (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    token_hash VARCHAR(128) NOT NULL,
    type ENUM('email_verify', 'password_reset') NOT NULL,
    expires_at TIMESTAMP NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES bmi_users(id) ON DELETE CASCADE,
    INDEX (token_hash)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Login Throttling
CREATE TABLE IF NOT EXISTS bmi_login_throttles (
    id INT AUTO_INCREMENT PRIMARY KEY,
    ip_address VARCHAR(45) NOT NULL,
    identifier VARCHAR(255) NOT NULL, -- e.g. email
    attempts INT DEFAULT 1,
    last_attempt_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY (ip_address, identifier)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Plans
CREATE TABLE IF NOT EXISTS bmi_plans (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    is_active BOOLEAN DEFAULT TRUE,
    start_date DATE NOT NULL,
    target_end_date DATE NULL,
    closed_at TIMESTAMP NULL,
    height_cm DECIMAL(5,2) NOT NULL,
    kcal_target INT NOT NULL,
    protein_target INT NOT NULL,
    steps_target INT NOT NULL,
    intensity_preset VARCHAR(50) NULL, -- gentle, normal, aggressive
    activity_level VARCHAR(50) NULL,
    training_goal VARCHAR(50) NULL,
    
    -- Encrypted Fields
    weight_goal_ciphertext BLOB NULL,
    weight_goal_iv VARBINARY(16) NULL,
    weight_goal_tag VARBINARY(16) NULL,
    
    min_weight_ciphertext BLOB NULL,
    min_weight_iv VARBINARY(16) NULL,
    min_weight_tag VARBINARY(16) NULL,
    
    override_flags JSON NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES bmi_users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Plan Audit
CREATE TABLE IF NOT EXISTS bmi_plan_audit (
    id INT AUTO_INCREMENT PRIMARY KEY,
    plan_id INT NOT NULL,
    user_id INT NOT NULL,
    reason VARCHAR(255) NULL,
    old_targets_json JSON NULL,
    new_targets_json JSON NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (plan_id) REFERENCES bmi_plans(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES bmi_users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Weights (Daily)
CREATE TABLE IF NOT EXISTS bmi_weights (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    date DATE NOT NULL,
    weight_ciphertext BLOB NOT NULL,
    weight_iv VARBINARY(16) NOT NULL,
    weight_tag VARBINARY(16) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY (user_id, date),
    FOREIGN KEY (user_id) REFERENCES bmi_users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Day Logs (kcal, protein, steps deltas)
CREATE TABLE IF NOT EXISTS bmi_day_logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    date DATE NOT NULL, -- YYYY-MM-DD in user's timezone
    time TIME NOT NULL, -- HH:MM:SS
    logged_at_utc TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    kcal_delta INT DEFAULT 0,
    protein_delta INT DEFAULT 0,
    steps_delta INT DEFAULT 0,
    label VARCHAR(255) NULL,
    note TEXT NULL,
    source ENUM('quickform', 'manual', 'meal') DEFAULT 'manual',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES bmi_users(id) ON DELETE CASCADE,
    INDEX (user_id, date),
    INDEX (user_id, logged_at_utc)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
