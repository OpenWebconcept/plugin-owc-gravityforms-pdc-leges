<?php

declare(strict_types=1);

/**
 * Gravity Forms addon — provides the plugin settings page.
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

use GFAddon;
use Gravity_Forms\Gravity_Forms\Settings\Fields\Text;

/**
 * Registers the plugin settings page under Forms > Settings > OWC PDC Leges.
 *
 * @since 1.0.0
 */
class PdcLegesAddon extends GFAddon
{
	/**
	 * @var string
	 */
	protected $_slug = OWC_GF_PDC_LEGES_PLUGIN_SLUG;

	/**
	 * @var string
	 */
	protected $_title = 'OWC PDC Leges';

	/**
	 * @var string
	 */
	protected $_short_title = 'PDC Leges';

	/**
	 * @var self|null
	 */
	private static ?self $_instance = null;

	/**
	 * @var string
	 */
	protected $_full_path = __FILE__;

	/**
	 * @var string|array A string or an array of capabilities or roles that have access to the form settings
	 */
	protected $_capabilities_form_settings = array( 'gravityforms_edit_forms' );

	public static function get_instance(): self
	{
		if ( null === self::$_instance ) {
			self::$_instance = new self();
		}

		return self::$_instance;
	}

	public function get_menu_icon()
	{
		return 'dashicons-yard-y';
	}

	/**
	 * Defines the plugin addon settings fields shown under Forms > Settings > OWC PDC Leges.
	 */
	public function plugin_settings_fields(): array
	{
		return array(
			array(
				'title'       => __( 'PDC leges settings', 'owc-gravityforms-pdc-leges' ),
				'description' => '<p>' . esc_html__( 'Configure the PDC leges endpoint. Prices are automatically fetched and cached for product fields linked to a PDC Leges ID.', 'owc-gravityforms-pdc-leges' ) . '</p>',
				'fields'      => array(
					array(
						'name'                => 'owc-gf-pdc-leges-endpoint',
						'label'               => __( 'PDC leges API endpoint', 'owc-gravityforms-pdc-leges' ),
						'type'                => 'text',
						'class'               => 'large',
						'required'            => false,
						'description'         => __( 'Full URL to the PDC leges endpoint. Example: https://gemeente.nl/wp-json/pdc-leges/v1/products', 'owc-gravityforms-pdc-leges' ),
						'validation_callback' => $this->validate_endpoint_url( ... ),
						'feedback_callback'   => $this->validate_endpoint_url_value( ... ),
					),
				),
			),
		);
	}

	public function validate_endpoint_url( Text $field, mixed $value ): bool
	{
		if ( ! is_string( $value ) || '' === trim( $value ) ) {
			return true; // Setting is not required, so empty is allowed.
		}

		$sanitized = trim( esc_url_raw( $value ) );

		if ( '' === $sanitized || ! wp_http_validate_url( $sanitized ) ) {
			$this->set_field_error( $field, __( 'Please enter a valid URL for the PDC leges endpoint.', 'owc-gravityforms-pdc-leges' ) );
			return false;
		}

		return true;
	}

	public function validate_endpoint_url_value( mixed $value ): bool
	{
		if ( ! is_string( $value ) || '' === trim( $value ) ) {
			return true; // Setting is not required, so empty is allowed.
		}

		$sanitized = trim( esc_url_raw( $value ) );

		if ( '' === $sanitized || ! wp_http_validate_url( $sanitized ) ) {
			return false;
		}

		return true;
	}

	/**
	 * Sanitizes the endpoint URL before it is saved.
	 */
	public function plugin_settings_sanitize( array $settings ): array
	{
		if ( isset( $settings['owc-gf-pdc-leges-endpoint'] ) ) {
			$settings['owc-gf-pdc-leges-endpoint'] = esc_url_raw( trim( $settings['owc-gf-pdc-leges-endpoint'] ) );
		}

		return $settings;
	}

	/**
	 * Enqueues the custom icon styles for the plugin settings page.
	 */
	public static function enqueue_icons_styles(): void
	{
		$url = sprintf( '%s/resources/css/dashicons/yard-icon.css', untrailingslashit( OWC_GF_PDC_LEGES_PLUGIN_URL ) );

		wp_enqueue_style(
			'owc-pdc-leges-icons',
			$url,
			array(),
			OWC_GF_PDC_LEGES_VERSION
		);
	}
}
