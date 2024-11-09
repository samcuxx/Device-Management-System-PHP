<?php
require_once "../config/database.php";

$database = new Database();
$db = $database->getConnection();

try {
    // Create settings table
    $db->exec("
        CREATE TABLE IF NOT EXISTS settings (
            id INT PRIMARY KEY AUTO_INCREMENT,
            setting_key VARCHAR(50) UNIQUE NOT NULL,
            setting_value TEXT,
            setting_group VARCHAR(50) NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        )
    ");

    // Insert default settings if they don't exist
    $default_settings = [
        ['company_name', 'Comprint Services', 'company_settings'],
        ['company_address', '', 'company_settings'],
        ['company_phone', '', 'company_settings'],
        ['company_email', '', 'company_settings'],
        ['default_pagination', '10', 'system_settings'],
        ['date_format', 'Y-m-d', 'system_settings'],
        ['timezone', 'Africa/Johannesburg', 'system_settings']
    ];

    $stmt = $db->prepare("
        INSERT IGNORE INTO settings (setting_key, setting_value, setting_group) 
        VALUES (:key, :value, :group)
    ");

    foreach ($default_settings as $setting) {
        $stmt->execute([
            'key' => $setting[0],
            'value' => $setting[1],
            'group' => $setting[2]
        ]);
    }

    echo "Settings table created and initialized successfully!";

} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
} 