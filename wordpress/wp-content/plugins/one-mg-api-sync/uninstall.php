<?php
/**
 * Fires only when the plugin is deleted from the Plugins screen (not on
 * simple deactivation), so data isn't lost by accident.
 */

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

global $wpdb;

$table_name = $wpdb->prefix . 'omg_posts';

// Table name is a fixed constant, not user input, so it's safe to
// interpolate directly here; there is no variable user data in this query.
$wpdb->query( "DROP TABLE IF EXISTS {$table_name}" );

delete_option( 'omg_api_sync_db_version' );
delete_option( 'omg_api_sync_last_synced' );
