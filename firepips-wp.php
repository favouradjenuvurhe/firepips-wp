<?php
/**
 * Plugin Name: firepips
 * Description: Advanced SMTP plugin with logging, test email, and GitHub auto updates.
 * Version: 1.1.3
 * Author: Favour Adjenuvurhe
 */

if (!defined('ABSPATH')) exit;

// PATHS
define('FIREPIPS_SMTP_PATH', plugin_dir_path(__FILE__));

// INCLUDE FILES
require_once FIREPIPS_SMTP_PATH . 'includes/smtp-config.php';
require_once FIREPIPS_SMTP_PATH . 'includes/logger.php';
require_once FIREPIPS_SMTP_PATH . 'includes/test-email.php';
require_once FIREPIPS_SMTP_PATH . 'admin/settings-page.php';
require_once FIREPIPS_SMTP_PATH . 'includes/update-checker.php';

// CREATE DB TABLE
register_activation_hook(__FILE__, function() {
    global $wpdb;

    $table = $wpdb->prefix . 'firepips_email_logs';

    $sql = "CREATE TABLE $table (
        id INT AUTO_INCREMENT PRIMARY KEY,
        email_to TEXT,
        subject TEXT,
        message LONGTEXT,
        status VARCHAR(20),
        error TEXT,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP
    )";

    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);
});
