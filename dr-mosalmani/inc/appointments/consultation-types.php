<?php
/**
 * =========================================================
 * Create Database Table
 * =========================================================
 */
function lawyer_create_consultation_types_table() {
    global $wpdb;
    $table_name    = $wpdb->prefix . 'lawyer_consultation_types';
    $charset_collate = $wpdb->get_charset_collate();

    $sql = "CREATE TABLE {$table_name} (
        id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
        title VARCHAR(255) NOT NULL,
        description TEXT NULL,
        consultation_type VARCHAR(20) NOT NULL DEFAULT 'online',
        duration INT UNSIGNED NOT NULL DEFAULT 30,
        price DECIMAL(15,2) NOT NULL DEFAULT 0,
        status VARCHAR(20) NOT NULL DEFAULT 'active',
        created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
        updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        PRIMARY KEY (id),
        KEY status (status),
        KEY consultation_type (consultation_type)
    ) {$charset_collate};";

    require_once ABSPATH . 'wp-admin/includes/upgrade.php';
    dbDelta($sql);
}

/**
 * =========================================================
 * Database Version
 * =========================================================
 */
define('LAWYER_APPOINTMENTS_DB_VERSION', '1.0.1');

/**
 * =========================================================
 * Upgrade Table - Add consultation_type column for existing sites
 * =========================================================
 */
function lawyer_upgrade_consultation_types_table() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'lawyer_consultation_types';

    $column = $wpdb->get_results(
        "SHOW COLUMNS FROM {$table_name} LIKE 'consultation_type'"
    );

    if (empty($column)) {
        $wpdb->query(
            "ALTER TABLE {$table_name}
             ADD COLUMN consultation_type VARCHAR(20) NOT NULL DEFAULT 'online'
             AFTER description"
        );
    }
}

/**
 * =========================================================
 * Install / Upgrade Database
 * =========================================================
 */
function lawyer_install_appointments_database() {
    $installed_version = get_option('lawyer_appointments_db_version');

    if ($installed_version === LAWYER_APPOINTMENTS_DB_VERSION) {
        return;
    }

    lawyer_create_consultation_types_table();
    lawyer_upgrade_consultation_types_table();

    update_option('lawyer_appointments_db_version', LAWYER_APPOINTMENTS_DB_VERSION);
}

add_action('after_setup_theme', 'lawyer_install_appointments_database');