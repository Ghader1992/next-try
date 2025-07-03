// CAPF Pro Admin Scripts
// This file will contain JavaScript for admin interactions,
// such as managing field groups, conditional logic UI, etc.

(function( $ ) {
	'use strict';

	$(document).ready(function() {
		var $addNewGroupButton = $('#capf-add-new-group-button');
		var $addNewGroupFormWrapper = $('#capf-add-new-group-form-wrapper');
		var $cancelNewGroupButton = $('#capf-cancel-new-group');
		var $saveNewGroupButton = $('#capf-save-new-group'); // We'll use this in the next step
		var $newGroupNameInput = $('#capf-new-group-name');

		// Show the "Add New Group" form
		$addNewGroupButton.on('click', function(e) {
			e.preventDefault();
			$addNewGroupFormWrapper.slideDown();
			$(this).hide(); // Hide the "Add New" button itself
		});

		// Hide the "Add New Group" form on cancel
		$cancelNewGroupButton.on('click', function(e) {
			e.preventDefault();
			$addNewGroupFormWrapper.slideUp(function() {
				$newGroupNameInput.val(''); // Clear input
				$addNewGroupButton.show(); // Show the "Add New" button again
			});
		});

		// Save the new group
		$saveNewGroupButton.on('click', function(e) {
			e.preventDefault();
			var groupName = $newGroupNameInput.val().trim();

			if ( !groupName ) {
				alert(capf_admin_params.i18n.emptyGroupName); // Using wp_localize_script for translations
				$newGroupNameInput.focus();
				return;
			}

			// Add a loading indicator (optional)
			$(this).prop('disabled', true).text(capf_admin_params.i18n.saving);

			$.ajax({
				url: ajaxurl, // WordPress AJAX URL
				type: 'POST',
				data: {
					action: 'capf_create_field_group', // Our WP AJAX action
					security: capf_admin_params.create_group_nonce, // Nonce from wp_localize_script
					group_name: groupName
				},
				success: function(response) {
					if (response.success) {
						// Add the new group to the list
						var newGroupHtml = '<li data-group-id="' + response.data.group_id + '">' +
						                   '<strong>' + $('<div />').text(response.data.group_name).html() + '</strong>' + // Basic XSS protection
						                   ' (ID: ' + response.data.group_id + ')' +
						                   ' <small><a href="#" class="capf-edit-group">' + capf_admin_params.i18n.edit + '</a> | ' +
						                   '<a href="#" class="capf-delete-group" style="color:#a00;">' + capf_admin_params.i18n.delete + '</a></small>' +
						                   '</li>';

						// If it's the first group, remove the placeholder message
						if ($('#capf-existing-field-groups').find('p').length > 0 && $('#capf-existing-field-groups').find('ul').length === 0) {
							$('#capf-existing-field-groups').html('<ul></ul>');
						} else if ($('#capf-existing-field-groups').find('ul').length === 0) {
                            // Ensure ul exists if p was already removed by a previous add
                            $('#capf-existing-field-groups').html('<ul></ul>');
                        }


						$('#capf-existing-field-groups ul').append(newGroupHtml);

						$newGroupNameInput.val(''); // Clear input
						$addNewGroupFormWrapper.slideUp(function() {
							$addNewGroupButton.show(); // Show the "Add New" button again
						});

					} else {
						alert(capf_admin_params.i18n.errorPrefix + response.data.message);
					}
				},
				error: function(xhr, status, error) {
					alert(capf_admin_params.i18n.ajaxError + error );
				},
				complete: function() {
					// Remove loading indicator
					$saveNewGroupButton.prop('disabled', false).text(capf_admin_params.i18n.saveGroup);
				}
			});
		});

	});

})( jQuery );
