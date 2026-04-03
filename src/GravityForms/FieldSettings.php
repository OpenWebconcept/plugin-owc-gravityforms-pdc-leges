<?php

declare(strict_types=1);

/**
 * Field settings — adds "PDC Leges ID" input to product fields in the GF form editor.
 *
 * @package OWC_GravityForms_PDC_Leges
 * @author  Yard | Digital Agency
 * @since   1.0.0
 */

namespace OWC\GravityForms\PdcLeges\GravityForms;

/**
 * Exit when accessed directly.
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Handles the "PDC Leges ID" field setting in the Gravity Forms form editor.
 *
 * @since 1.0.0
 */
class FieldSettings
{
	public function render_leges_id_setting( int $position, mixed $form_id ): void
	{
		// Bottom of the Advanced settings tab.
		if ( -1 !== $position ) {
			return;
		}

		?>
		<li class="owc-pdc-leges-id-setting field_setting">
			<label for="field_leges_id">
				<?php esc_html_e( 'PDC Leges ID', 'owc-gravityforms-pdc-leges' ); ?>
				<?php gform_tooltip( 'owc_pdc_leges_id' ); ?>
			</label>
			<input
				type="text"
				id="field_leges_id"
				class="fieldwidth-3"
				placeholder="<?php esc_attr_e( 'E.g. 43004', 'owc-gravityforms-pdc-leges' ); ?>"
				onchange="SetFieldProperty('leges_id', this.value);"
			/>
			<p class="description">
				<?php esc_html_e( 'Enter the PDC Leges ID to automatically fetch the price from the configured endpoint. Leave empty to use the manually entered price.', 'owc-gravityforms-pdc-leges' ); ?>
			</p>
		</li>
		<?php
	}

	/**
	 * Outputs the inline JS needed to load the saved leges_id value when a field
	 * is selected in the GF form editor.
	 */
	public function render_editor_js(): void
	{
		?>
		<script type="text/javascript">
			jQuery(document).on('gform_load_field_settings', function (event, field) {
				jQuery('#field_leges_id').val(field['leges_id'] || '');
			});
		</script>
		<?php
	}

	/**
	 * Enqueues the external JS file that controls show/hide of the leges ID
	 * setting based on the selected field type.
	 */
	public function enqueue_editor_assets(): void
	{
		if ( ! $this->is_gf_form_editor() ) {
			return;
		}

		$url = sprintf( '%s/resources/js/pdc-leges-field-settings.js', untrailingslashit( OWC_GF_PDC_LEGES_PLUGIN_URL ) );

		wp_enqueue_script(
			'owc-pdc-leges-field-settings',
			$url,
			array( 'jquery' ),
			OWC_GF_PDC_LEGES_VERSION,
			true
		);

		// Translatable strings for JS.
		wp_localize_script(
			'owc-pdc-leges-field-settings',
			'owcPdcLeges',
			array(
				'tooltipText' => __( 'Enter the PDC leges ID to automatically fetch the price. Leave empty to use the manual price.', 'owc-gravityforms-pdc-leges' ),
			)
		);
	}

	private function is_gf_form_editor(): bool
	{
		if ( ! class_exists( 'GFForms' ) ) {
			return false;
		}

		// GF form editor page: toplevel_page_gf_edit_forms or similar.
		return isset( $_GET['page'] ) && 'gf_edit_forms' === sanitize_text_field( wp_unslash( $_GET['page'] ) ) // phpcs:ignore WordPress.Security.NonceVerification.Recommended
			&& isset( $_GET['id'] ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended
	}
}
