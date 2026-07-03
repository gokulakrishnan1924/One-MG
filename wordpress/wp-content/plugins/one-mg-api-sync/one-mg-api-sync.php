<?php
/**
 * Plugin Name: One MG API Sync
 * Description: Fetches posts from a public API (JSONPlaceholder), stores them in a custom
 *              WordPress table, shows them on an admin page with a manual refresh button,
 *              and exposes the latest 5 entries via a [omg_latest_posts] shortcode.
 * Version:     1.0.0
 * Author:      Full Stack Technical Task
 * License:     GPL-2.0+
 *
 * ---------------------------------------------------------------------------
 * FILE MAP (single-file plugin, sections separated for readability):
 *   1. Constants & bootstrap
 *   2. Activation / deactivation (custom table creation via dbDelta)
 *   3. Core sync logic (fetch API -> upsert into custom table)
 *   4. Admin page (list table + Refresh button)
 *   5. AJAX handler for the Refresh button
 *   6. Shortcode for front-end display (latest 5 entries)
 * ---------------------------------------------------------------------------
 */

// Block direct access to this file.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/* =========================================================================
 * 1. CONSTANTS & BOOTSTRAP
 * ========================================================================= */

define( 'OMG_API_SYNC_VERSION', '1.0.0' );
define( 'OMG_API_SYNC_TABLE', 'omg_posts' ); // Actual name is $wpdb->prefix . this.
define( 'OMG_API_SYNC_API_URL', 'https://jsonplaceholder.typicode.com/posts' );
define( 'OMG_API_SYNC_NONCE_ACTION', 'omg_api_sync_refresh' );

/* =========================================================================
 * 2. ACTIVATION / DEACTIVATION
 * ========================================================================= */

/**
 * Runs once on plugin activation.
 * Creates the custom table using dbDelta(), which is the WordPress-safe way
 * to create/upgrade tables (it diff's the schema and only applies changes).
 */
function omg_api_sync_activate() {
	global $wpdb;

	$table_name      = $wpdb->prefix . OMG_API_SYNC_TABLE;
	$charset_collate = $wpdb->get_charset_collate();

	// api_post_id has a UNIQUE key so re-syncing the same API record updates
	// it instead of creating duplicate rows (upsert behaviour).
	$sql = "CREATE TABLE {$table_name} (
		id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
		api_post_id BIGINT UNSIGNED NOT NULL,
		user_id BIGINT UNSIGNED NOT NULL DEFAULT 0,
		title TEXT NOT NULL,
		body TEXT NOT NULL,
		created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
		updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
		PRIMARY KEY  (id),
		UNIQUE KEY api_post_id (api_post_id)
	) {$charset_collate};";

	require_once ABSPATH . 'wp-admin/includes/upgrade.php';
	dbDelta( $sql );

	// Store the version so future upgrades can detect schema changes if needed.
	update_option( 'omg_api_sync_db_version', OMG_API_SYNC_VERSION );

	// Do an initial pull so the admin page / shortcode have data immediately.
	omg_api_sync_fetch_and_store();
}
register_activation_hook( __FILE__, 'omg_api_sync_activate' );

/**
 * Deactivation is intentionally light-touch: we do NOT drop the custom table
 * on deactivation, only on uninstall, so users don't lose data by accident.
 */
function omg_api_sync_deactivate() {
	// Nothing to clean up here on purpose (see uninstall.php pattern below).
}
register_deactivation_hook( __FILE__, 'omg_api_sync_deactivate' );

/* =========================================================================
 * 3. CORE SYNC LOGIC
 * ========================================================================= */

/**
 * Fetches data from the public API and saves it into the custom table.
 *
 * Security considerations:
 * - wp_remote_get() is used instead of curl/file_get_contents so WordPress'
 *   HTTP API (with its safety filters, timeouts, and SSL verification) is used.
 * - The response is validated (WP_Error check + HTTP status) before use.
 * - JSON is decoded with json_decode(), never eval()'d.
 * - Every value written to the DB goes through $wpdb->prepare() or an
 *   explicitly typed $wpdb->replace() call — no raw string concatenation
 *   into SQL anywhere in this plugin.
 * - Text fields are sanitized (sanitize_text_field / wp_kses_post) before
 *   being stored, and escaped again on output (see admin page & shortcode).
 *
 * @return array{success:bool,message:string,count:int}
 */
function omg_api_sync_fetch_and_store() {
	global $wpdb;

	$response = wp_remote_get(
		OMG_API_SYNC_API_URL,
		array(
			'timeout' => 15,
		)
	);

	if ( is_wp_error( $response ) ) {
		return array(
			'success' => false,
			'message' => $response->get_error_message(),
			'count'   => 0,
		);
	}

	$status_code = wp_remote_retrieve_response_code( $response );
	if ( 200 !== (int) $status_code ) {
		return array(
			'success' => false,
			'message' => sprintf( 'API returned HTTP %d', $status_code ),
			'count'   => 0,
		);
	}

	$body  = wp_remote_retrieve_body( $response );
	$items = json_decode( $body, true );

	if ( ! is_array( $items ) ) {
		return array(
			'success' => false,
			'message' => 'Unexpected API response format.',
			'count'   => 0,
		);
	}

	$table_name = $wpdb->prefix . OMG_API_SYNC_TABLE;

	// Only store the first 20 items to keep the demo table small; adjust as needed.
	$items = array_slice( $items, 0, 20 );

	$saved = 0;
	foreach ( $items as $item ) {
		if ( empty( $item['id'] ) ) {
			continue;
		}

		// $wpdb->replace() performs an INSERT ... ON DUPLICATE KEY UPDATE
		// under the hood (relies on the UNIQUE key on api_post_id) and,
		// like $wpdb->insert()/update(), takes a $format array so every
		// value is safely cast/escaped — no manual SQL string building.
		$result = $wpdb->replace(
			$table_name,
			array(
				'api_post_id' => absint( $item['id'] ),
				'user_id'     => isset( $item['userId'] ) ? absint( $item['userId'] ) : 0,
				'title'       => sanitize_text_field( $item['title'] ?? '' ),
				'body'        => sanitize_textarea_field( $item['body'] ?? '' ),
				'updated_at'  => current_time( 'mysql' ),
			),
			array( '%d', '%d', '%s', '%s', '%s' )
		);

		if ( false !== $result ) {
			$saved++;
		}
	}

	update_option( 'omg_api_sync_last_synced', current_time( 'mysql' ) );

	return array(
		'success' => true,
		'message' => sprintf( '%d item(s) synced.', $saved ),
		'count'   => $saved,
	);
}

/* =========================================================================
 * 4. ADMIN PAGE
 * ========================================================================= */

function omg_api_sync_admin_menu() {
	add_menu_page(
		'API Sync',
		'API Sync',
		'manage_options',        // Capability required — admins only.
		'omg-api-sync',
		'omg_api_sync_render_admin_page',
		'dashicons-update',
		26
	);
}
add_action( 'admin_menu', 'omg_api_sync_admin_menu' );

/**
 * Enqueue a small inline-friendly JS file only on our admin page.
 */
function omg_api_sync_admin_assets( $hook ) {
	if ( 'toplevel_page_omg-api-sync' !== $hook ) {
		return;
	}

	wp_enqueue_script(
		'omg-api-sync-admin',
		plugins_url( 'admin.js', __FILE__ ),
		array( 'jquery' ),
		OMG_API_SYNC_VERSION,
		true
	);

	// Safely pass the AJAX URL + a nonce to JS. Nonces protect the AJAX
	// endpoint from CSRF; capability checks (below) protect it from
	// unauthorized users even if the nonce were somehow leaked.
	wp_localize_script(
		'omg-api-sync-admin',
		'omgApiSync',
		array(
			'ajaxUrl' => admin_url( 'admin-ajax.php' ),
			'nonce'   => wp_create_nonce( OMG_API_SYNC_NONCE_ACTION ),
		)
	);
}
add_action( 'admin_enqueue_scripts', 'omg_api_sync_admin_assets' );

function omg_api_sync_render_admin_page() {
	if ( ! current_user_can( 'manage_options' ) ) {
		wp_die( esc_html__( 'You do not have permission to access this page.', 'omg-api-sync' ) );
	}

	global $wpdb;
	$table_name = $wpdb->prefix . OMG_API_SYNC_TABLE;

	// Read-only query built with a static string (no user input involved),
	// still using $wpdb for a properly cached/prepared connection.
	$rows = $wpdb->get_results(
		"SELECT api_post_id, user_id, title, body, updated_at FROM {$table_name} ORDER BY updated_at DESC LIMIT 50"
	);

	$last_synced = get_option( 'omg_api_sync_last_synced', '' );
	?>
	<div class="wrap">
		<h1>API Sync — Synced Posts</h1>

		<p>
			<strong>Last synced:</strong>
			<?php echo $last_synced ? esc_html( $last_synced ) : 'Never'; ?>
		</p>

		<p>
			<button type="button" id="omg-refresh-btn" class="button button-primary">
				Refresh from API
			</button>
			<span id="omg-refresh-status" style="margin-left:10px;"></span>
		</p>

		<table class="widefat striped">
			<thead>
				<tr>
					<th>API ID</th>
					<th>User ID</th>
					<th>Title</th>
					<th>Body</th>
					<th>Updated</th>
				</tr>
			</thead>
			<tbody id="omg-table-body">
			<?php if ( empty( $rows ) ) : ?>
				<tr><td colspan="5">No data yet. Click "Refresh from API" to pull data.</td></tr>
			<?php else : ?>
				<?php foreach ( $rows as $row ) : ?>
					<tr>
						<td><?php echo esc_html( $row->api_post_id ); ?></td>
						<td><?php echo esc_html( $row->user_id ); ?></td>
						<td><?php echo esc_html( $row->title ); ?></td>
						<td><?php echo esc_html( wp_trim_words( $row->body, 12 ) ); ?></td>
						<td><?php echo esc_html( $row->updated_at ); ?></td>
					</tr>
				<?php endforeach; ?>
			<?php endif; ?>
			</tbody>
		</table>
	</div>
	<?php
}

/* =========================================================================
 * 5. AJAX HANDLER FOR REFRESH BUTTON
 * ========================================================================= */

function omg_api_sync_ajax_refresh() {
	// 1. Verify the nonce to prevent CSRF.
	check_ajax_referer( OMG_API_SYNC_NONCE_ACTION, 'nonce' );

	// 2. Verify the current user is actually allowed to trigger this.
	if ( ! current_user_can( 'manage_options' ) ) {
		wp_send_json_error( array( 'message' => 'Permission denied.' ), 403 );
	}

	// 3. Do the actual sync (reuses the same function as activation).
	$result = omg_api_sync_fetch_and_store();

	if ( $result['success'] ) {
		wp_send_json_success( $result );
	} else {
		wp_send_json_error( $result );
	}
}
add_action( 'wp_ajax_omg_api_sync_refresh', 'omg_api_sync_ajax_refresh' );
// Intentionally NOT hooked to wp_ajax_nopriv_* — only logged-in admins may refresh.

/* =========================================================================
 * 6. SHORTCODE — LATEST 5 ENTRIES ON THE FRONT END
 * ========================================================================= */

/**
 * [omg_latest_posts] shortcode.
 * Renders the 5 most recently updated rows from the custom table.
 */
function omg_api_sync_shortcode( $atts ) {
	global $wpdb;
	$table_name = $wpdb->prefix . OMG_API_SYNC_TABLE;

	$atts = shortcode_atts(
		array(
			'count' => 5,
		),
		$atts,
		'omg_latest_posts'
	);

	// User-supplied shortcode attribute, so it MUST go through $wpdb->prepare().
	$count = absint( $atts['count'] );
	$rows  = $wpdb->get_results(
		$wpdb->prepare(
			"SELECT title, body, updated_at FROM {$table_name} ORDER BY updated_at DESC LIMIT %d",
			$count
		)
	);

	if ( empty( $rows ) ) {
		return '<p>No posts available yet.</p>';
	}

	ob_start();
	?>
	<div class="omg-latest-posts">
		<ul>
			<?php foreach ( $rows as $row ) : ?>
				<li>
					<strong><?php echo esc_html( $row->title ); ?></strong>
					<p><?php echo esc_html( wp_trim_words( $row->body, 20 ) ); ?></p>
				</li>
			<?php endforeach; ?>
		</ul>
	</div>
	<?php
	return ob_get_clean();
}
add_shortcode( 'omg_latest_posts', 'omg_api_sync_shortcode' );
