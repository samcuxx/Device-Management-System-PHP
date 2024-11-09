<?php
/**
 * Returns the appropriate Bootstrap color class based on device status
 */
function getStatusColor($status) {
    switch ($status) {
        case 'Pending':
            return 'warning';
        case 'In Progress':
            return 'info';
        case 'Completed':
            return 'success';
        default:
            return 'secondary';
    }
}

/**
 * Format date to a readable string
 */
function formatDate($date) {
    return date('M d, Y', strtotime($date));
}

/**
 * Sanitize output
 */
function h($string) {
    return htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
}

/**
 * Generate random string
 */
function generateRandomString($length = 10) {
    return bin2hex(random_bytes($length));
}

/**
 * Format file size
 */
function formatFileSize($bytes) {
    $units = ['B', 'KB', 'MB', 'GB', 'TB'];
    $bytes = max($bytes, 0);
    $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
    $pow = min($pow, count($units) - 1);
    $bytes /= pow(1024, $pow);
    return round($bytes, 2) . ' ' . $units[$pow];
}

/**
 * Get settings by group
 */
function getSettings($db, $group) {
    $query = "SELECT setting_key, setting_value FROM settings WHERE setting_group = :group";
    $stmt = $db->prepare($query);
    $stmt->execute(['group' => $group]);
    
    $settings = [];
    while ($row = $stmt->fetch()) {
        $settings[$row['setting_key']] = $row['setting_value'];
    }
    
    return $settings;
}

/**
 * Update settings
 */
function updateSettings($db, $group, $settings) {
    try {
        $db->beginTransaction();
        
        foreach ($settings as $key => $value) {
            $query = "UPDATE settings SET setting_value = :value, updated_at = CURRENT_TIMESTAMP 
                     WHERE setting_key = :key AND setting_group = :group";
            $stmt = $db->prepare($query);
            $stmt->execute([
                'value' => $value,
                'key' => $key,
                'group' => $group
            ]);
        }
        
        $db->commit();
        return true;
    } catch (Exception $e) {
        $db->rollBack();
        error_log($e->getMessage());
        return false;
    }
}

/**
 * Get setting value
 */
function getSetting($db, $key, $default = '') {
    $query = "SELECT setting_value FROM settings WHERE setting_key = :key LIMIT 1";
    $stmt = $db->prepare($query);
    $stmt->execute(['key' => $key]);
    
    if ($row = $stmt->fetch()) {
        return $row['setting_value'];
    }
    
    return $default;
}

/**
 * Apply system settings
 */
function applySystemSettings($db) {
    // Set timezone
    $timezone = getSetting($db, 'timezone', 'UTC');
    date_default_timezone_set($timezone);
    
    // Set other system settings as needed
    return true;
} 