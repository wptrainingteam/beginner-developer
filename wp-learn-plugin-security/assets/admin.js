jQuery( document ).ready(
	function ($) {
		console.log( 'WP Learn Plugin Security admin.js loaded' );
		$( '.delete-submission' ).on(
			'click',
			function (event) {
				console.log( 'Delete button clicked' );
				var this_button = $( this );
				event.preventDefault();
				var id = this_button.data( 'id' );
				console.log( 'Delete submission id ' + id );
				jQuery.post(
					wp_learn_ajax.ajax_url,
					{
						'action': 'delete_form_submission',
						'id': id,
					},
					function (response) {
						console.log( response );
						alert( 'Form submission deleted' );
						document.location.reload();
					}
				);
			}
		);
	}
);
