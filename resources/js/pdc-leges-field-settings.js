/**
 * OWC PDC Leges — form editor field setting script.
 *
 * Responsibilities:
 *  1. Show the "PDC Leges ID" input only when the selected field type is "product".
 *  2. Populate the input from the saved field property when the user selects a field.
 *  3. Persist the value back to the GF field model when the input changes.
 *
 * GF's JavaScript API used here:
 *  - `gform_load_field_settings` event — fired when a field is selected in the editor.
 *  - `SetFieldProperty(name, value)`  — saves a property to the current GF field object.
 *
 * @package OWC_GravityForms_PDC_Leges
 * @since   1.0.0
 */

( function ( $ ) {
	'use strict';

	const SETTING_SELECTOR  = '.owc-pdc-leges-id-setting';
	const INPUT_SELECTOR    = '#field_leges_id';
	const PRODUCT_FIELD_TYPE = 'product';

	/**
	 * Show or hide the PDC Leges ID setting based on field type.
	 *
	 * @param {Object} field - The currently selected GF field object.
	 */
	function toggleLegesIdSetting( field ) {
		if ( field.type === PRODUCT_FIELD_TYPE ) {
			$( SETTING_SELECTOR ).show();
		} else {
			$( SETTING_SELECTOR ).hide();
		}
	}

	/**
	 * Populate the input with the stored leges_id value.
	 *
	 * @param {Object} field - The currently selected GF field object.
	 */
	function loadLegesIdValue( field ) {
		$( INPUT_SELECTOR ).val( field.leges_id || '' );
	}

	/**
	 * Fired by GF every time the user selects or changes a field in the editor.
	 *
	 * @param {jQuery.Event} event - The jQuery event.
	 * @param {Object}       field - The GF field object.
	 * @param {Object}       form  - The GF form object.
	 */
	$( document ).on( 'gform_load_field_settings', function ( event, field ) {
		toggleLegesIdSetting( field );
		loadLegesIdValue( field );
	} );

	/**
	 * Persist the typed value to the GF field model on change.
	 * The `onchange` attribute on the input already calls SetFieldProperty(),
	 * but this handler provides a delegated fallback for programmatic changes.
	 */
	$( document ).on( 'change', INPUT_SELECTOR, function () {
		if ( typeof SetFieldProperty === 'function' ) {
			SetFieldProperty( 'leges_id', $( this ).val() );
		}
	} );

} )( window.jQuery );
