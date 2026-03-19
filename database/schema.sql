-- Tourist Attraction Finder Database Schema

-- Create database if it doesn't exist
CREATE DATABASE IF NOT EXISTS tourist_attraction_finder CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- Use the database
USE tourist_attraction_finder;

-- Users table
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_email (email)
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;

-- Attractions table
CREATE TABLE IF NOT EXISTS attractions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(200) NOT NULL,
    location VARCHAR(200) NOT NULL,
    description TEXT,
    image_url VARCHAR(500),
    rating DECIMAL(3, 2) DEFAULT 0.00,
    status ENUM(
        'active',
        'inactive',
        'pending'
    ) DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_location (location),
    INDEX idx_rating (rating),
    INDEX idx_status (status)
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;

-- User sessions table (for JWT token management if needed)
CREATE TABLE IF NOT EXISTS user_sessions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    token VARCHAR(500) NOT NULL,
    expires_at TIMESTAMP NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE CASCADE,
    INDEX idx_token (token (255)),
    INDEX idx_expires_at (expires_at)
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;

-- Insert sample data for attractions
INSERT INTO
    attractions (
        name,
        location,
        description,
        image_url,
        rating,
        status
    )
VALUES (
        'Manukan Island',
        'Zamboanga City',
        'A beautiful island with white sand beaches and clear waters perfect for snorkeling and diving.',
        'public/assets/img/manukan.png',
        4.8,
        'active'
    ),
    (
        'Katipunan Falls',
        'Katipunan, Zamboanga del Norte',
        'A stunning waterfall surrounded by lush forest, perfect for nature lovers and photographers.',
        'public/assets/img/katipunan.png',
        4.5,
        'active'
    ),
    (
        'Sindangan Bay',
        'Sindangan, Zamboanga del Norte',
        'A serene bay with calm waters, ideal for swimming and boat rides.',
        'public/assets/img/sindangan.png',
        4.2,
        'active'
    ),
    (
        'Tampilisan Caves',
        'Tampilisan, Zamboanga del Norte',
        'Ancient limestone caves with impressive rock formations and underground streams.',
        'public/assets/img/tampilisan.png',
        4.0,
        'active'
    ),
    (
        'Sungkilaw Falls',
        'Sungkilaw, Zamboanga del Norte',
        'A multi-tiered waterfall with natural pools perfect for swimming and picnics.',
        'public/assets/img/sungkilaw.png',
        4.3,
        'active'
    ),
    (
        'Polanco Beach',
        'Polanco, Zamboanga del Norte',
        'A peaceful beach with golden sand and gentle waves, great for relaxation.',
        'public/assets/img/polanco.png',
        4.1,
        'active'
    );

-- Insert sample admin user (password: admin123)
INSERT INTO
    users (name, email, password_hash)
VALUES (
        'Admin User',
        'admin@example.com',
        '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi'
    );
-- password: admin123