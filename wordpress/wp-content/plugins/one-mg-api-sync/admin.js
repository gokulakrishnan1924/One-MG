/**
 * Handles the "Refresh from API" button on the API Sync admin page.
 * Relies on omgApiSync (ajaxUrl, nonce) localized via wp_localize_script().
 */
jQuery( function ( $ ) {
	$( '#omg-refresh-btn' ).on( 'click', function () {
		var $btn    = $( this );
		var $status = $( '#omg-refresh-status' );

		$btn.prop( 'disabled', true );
		$status.text( 'Refreshing…' );

		$.post( omgApiSync.ajaxUrl, {
			action: 'omg_api_sync_refresh',
			nonce: omgApiSync.nonce
		} )
			.done( function ( response ) {
				if ( response.success ) {
					$status.text( response.data.message );
					// Simplest reliable way to reflect new data: reload the page.
					setTimeout( function () {
						window.location.reload();
					}, 800 );
				} else {
					$status.text( 'Error: ' + ( response.data && response.data.message ? response.data.message : 'Unknown error' ) );
				}
			} )
			.fail( function () {
				$status.text( 'Request failed. Please try again.' );
			} )
			.always( function () {
				$btn.prop( 'disabled', false );
			} );
	} );
} );
