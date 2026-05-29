CREATE TABLE IF NOT EXISTS print_template_custom_blocks (
    id INT AUTO_INCREMENT PRIMARY KEY,
    block_name VARCHAR(255) NOT NULL,
    block_html TEXT,
    block_category VARCHAR(100) DEFAULT 'Custom',
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);
