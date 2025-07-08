<?php
/**
 * Uninstall script for WPProAtoz LLMs.txt for WP.
 *
 * Cleans up plugin data when the plugin is deleted.
 *
 * @package LLMsTxtForWP
 */

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
    exit;
}

// Initialize WP_Filesystem for potential file cleanup.
if ( ! function_exists( 'WP_Filesystem' ) ) {
    require_once ABSPATH . 'wp-admin/includes/file.php';
}
if ( WP_Filesystem() ) {
    global $wp_filesystem;
    $file = ABSPATH . 'llms.txt';
    if ( $wp_filesystem->exists( $file ) ) {
        $wp_filesystem->delete( $file );
    }
}

// Delete plugin settings.
delete_option( 'llms_txt_settings' );

// Delete llms.txt cache transient.
delete_transient( 'llms_txt_cache' );

// Clean up any other transients.
global $wpdb;
$transients = $wpdb->get_col( "SELECT option_name FROM $wpdb->options WHERE option_name LIKE '_transient_llms_txt_%' OR option_name LIKE '_site_transient_llms_txt_%'" );
foreach ( $transients as $transient ) {
    $name = str_replace( '_transient_', '', $transient );
    $name = str_replace( '_site_transient_', '', $name );
    delete_transient( $name );
}

?>