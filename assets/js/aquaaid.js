jQuery(document).ready(function($) {

	// Do nothing if there is no Gravity form
	if(!$('.gform_wrapper form').length) return;

	// Hide the input fields
	$('.aa-hidden-email, .aa-hidden-msg, .aa-hidden-copy').attr('style', 'display:none!important');

	// strore unique numeric value of form ID
	var	aa_gform_ID = $('.gform_wrapper form').attr('id').split('_')[1];

	// Submit button
	var $_submit_button = $('.gform_wrapper').find('input[type=submit]');

	// If the current form ID attribute matches any one of the specified form IDs, we continue...
	if (aa_gform_ID == aquaaid.gform_1 || aa_gform_ID == aquaaid.gform_2) {

		// The blur event is sent to an element when it loses focus
		$('#gform_'+aa_gform_ID+' .address_zip input').on('blur', function() {

			// Disable the submit button
			$_submit_button.attr('disabled', true);

			// Do nothing if there is no value
			if(!$(this).val()) return;

			// Make the post request
			$.post(aquaaid.ajax_url, {action: 'aa_ajax_fetch_from_db', aa_userInput: $(this).val()}, '', 'json')
				.then(function(data) {
					if (data) {
						if ($('.aa-hidden-email').length && $('.aa-hidden-msg').length) {
							$('.aa-hidden-email input').val(data[0].email);
							$('.aa-hidden-msg input').val(data[0].message);
							if(data[0].email_copy) {
								$('.aa-hidden-copy input').val(data[0].email_copy);
							}
						} else {
							console.error('Error: Cannot find field with containing element of class "aa-hidden"', 'Add class "aa-hidden" to email field: Appearance > Custom CSS Class');
						}
					} else {
						// Add default email address and message
						$('.aa-hidden-email input').val('marie@aquaid.co.uk');
						$('.aa-hidden-msg input').val('Thank you for contacting AquAid. We will respond to your enquiry with in 4 hours');
						$('.aa-hidden-copy input').val('');
						console.error("could not find an email address associated with the zip code specified");
					}

					// Re-enable submit button
					$_submit_button.attr('disabled', false);
				});
		});

	}

});