-- Create attractions table for Tourist Attraction Finder API
CREATE TABLE IF NOT EXISTS attractions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    location VARCHAR(255) NOT NULL,
    description TEXT,
    image_url VARCHAR(500),
    category VARCHAR(100) NOT NULL,
    rating DECIMAL(3,2) DEFAULT 0.00,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

-- Indexes for performance
INDEX idx_location (location),
    INDEX idx_category (category),
    INDEX idx_rating (rating),
    INDEX idx_name (name)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insert sample data for testing
INSERT INTO
    attractions (
        name,
        location,
        description,
        image_url,
        category,
        rating
    )
VALUES (
        'Manukan Island',
        'Zamboanga City',
        'A beautiful island with white sand beaches and crystal clear waters perfect for swimming and snorkeling.',
        'assets/img/manukan.png',
        'Beach',
        4.8
    ),
    (
        'Katipunan Hill',
        'Zamboanga City',
        'A historical landmark offering panoramic views of the city and a peaceful retreat for visitors.',
        'assets/img/katipunan.png',
        'Historical',
        4.5
    ),
    (
        'Polanco Falls',
        'Polanco, Zamboanga del Norte',
        'A stunning waterfall surrounded by lush greenery, ideal for nature lovers and photographers.',
        'assets/img/polanco.png',
        'Nature',
        4.7
    ),
    (
        'Sindangan Bay',
        'Sindangan, Zamboanga del Norte',
        'A serene bay with calm waters, perfect for boating and enjoying the sunset.',
        'assets/img/sindangan.png',
        'Beach',
        4.3
    ),
    (
        'Sungkilaw Falls',
        'Siocon, Zamboanga del Norte',
        'A majestic waterfall with multiple tiers, offering a refreshing experience for adventure seekers.',
        'assets/img/sungkilaw.png',
        'Nature',
        4.6
    ),
    (
        'Tampilisan Hot Springs',
        'Tampilisan, Zamboanga del Norte',
        'Natural hot springs known for their therapeutic properties and relaxing atmosphere.',
        'assets/img/tampilisan.png',
        'Nature',
        4.4
    );