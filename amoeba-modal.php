<?php

/**
 * Plugin Name: Amoeba - Custom Modal Creator
 * Description: Easily create modals for your WordPress site plugin.
 * Version: 5.2.7 (CVs)
 * Author: José Melgares 🐌
 * Text Domain: amoeba-modal-creator
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

define('AMOEBA_MODAL_VERSION', '5.2.7' );
define('AMOEBA_MODAL_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define('AMOEBA_MODAL_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
define('AMOEBA_DB_VERSION', '2.0.0' );

// Include required files
require_once AMOEBA_MODAL_PLUGIN_DIR . 'scripts/amoeba-scripts.php';
require_once AMOEBA_MODAL_PLUGIN_DIR . 'includes/amoeba-dashboard.php';
require_once AMOEBA_MODAL_PLUGIN_DIR . 'includes/amoeba-settings.php';
require_once AMOEBA_MODAL_PLUGIN_DIR . 'includes/amoeba-shortcode.php';

// Activation hook
register_activation_hook( __FILE__, 'amoeba_install' );

/**
 * Database installation and setup.
 */
function amoeba_install() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'amoeba_modals';
    $charset_collate = $wpdb->get_charset_collate();

    $sql = "CREATE TABLE $table_name (
        id mediumint(9) NOT NULL AUTO_INCREMENT,
        title varchar(255) NOT NULL,
        content text NOT NULL,
        picture_url varchar(255) DEFAULT '' NOT NULL,
        custom_css text DEFAULT '' NOT NULL,
        linkedin varchar(255) DEFAULT '' NOT NULL,
        twitter varchar(255) DEFAULT '' NOT NULL,
        PRIMARY KEY  (id)
    ) $charset_collate;";

    require_once ABSPATH . 'wp-admin/includes/upgrade.php';
    dbDelta( $sql );

    add_option( 'amoeba_db_version', AMOEBA_DB_VERSION );
}


add_action( 'plugins_loaded', 'amoeba_update_db_check' );
function amoeba_update_db_check() {
    if ( get_option( 'amoeba_db_version' ) != AMOEBA_DB_VERSION ) {
        amoeba_install();
    }
}

add_action( 'plugins_loaded', 'amoeba_init_plugin');
function amoeba_init_plugin() {
    new Amoeba_Scripts();
    if( is_admin() ) {
        new Amoeba_Dashboard();
        new Amoeba_Settings();
    }
    new Amoeba_Shortcode();
}