# Brief: One MG API Sync Plugin

**Structure.** The plugin is organized into six clear sections inside a single main
file for easy review: constants/bootstrap, activation (table creation), core sync
logic, the admin page, the AJAX refresh handler, and the front-end shortcode. A
separate `admin.js` handles the button's AJAX call, and `uninstall.php` cleans up
the table only if the user explicitly deletes the plugin (not on deactivation),
so data isn't lost by accident.

**Logic.** On activation, `dbDelta()` creates a custom table (`wp_omg_posts`) with
a UNIQUE key on `api_post_id`. The core function `omg_api_sync_fetch_and_store()`
calls the JSONPlaceholder API via `wp_remote_get()`, validates the HTTP response,
decodes the JSON, and writes each record with `$wpdb->replace()` — an upsert, so
re-running the sync updates existing rows instead of duplicating them. This same
function powers both the initial activation sync and the manual "Refresh" button,
avoiding duplicated logic. The admin page lists the most recently updated rows;
the `[omg_latest_posts]` shortcode queries the 5 newest entries for front-end use.

**Security.** Every DB write uses `$wpdb->replace()`/`insert()` with typed format
arrays, and the one query with a variable (the shortcode's `count` attribute) uses
`$wpdb->prepare()` — no raw string concatenation anywhere. Output is escaped with
`esc_html()`. The AJAX endpoint is protected by a nonce (`check_ajax_referer`) to
block CSRF and a `current_user_can( 'manage_options' )` check to block unauthorized
users, and it is intentionally not registered for `nopriv` requests. Input from the
API is sanitized (`sanitize_text_field`/`sanitize_textarea_field`) before storage.
