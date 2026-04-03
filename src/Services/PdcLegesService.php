<?php

declare(strict_types=1);

/**
 * PDC Leges service — fetches and caches prices from the PDC-leges API endpoint.
 *
 * @package OWC_GravityForms_PDC_Leges
 * @author  Yard | Digital Agency
 * @since   1.0.0
 */

namespace OWC\GravityForms\PdcLeges\Services;

/**
 * Exit when accessed directly.
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Retrieves product prices from the configured PDC-leges API endpoint.
 *
 * Responses are cached in a WordPress transient (default: 6 hours) to
 * avoid hitting the external endpoint on every form render.
 *
 * Each unique leges ID is fetched from {endpoint}/{leges_id} and cached
 * under its own transient key.
 *
 * @since 1.0.0
 */
class PdcLegesService
{
	private const TRANSIENT_PREFIX   = 'owc_gf_pdc_leges_';
	private const TRANSIENT_LIFETIME = 21600;
	private const OPTION_KEY         = 'gravityformsaddon_owc-gravityforms-pdc-leges_settings';
	private string $endpoint;

	public function __construct( string $endpoint )
	{
		$this->endpoint = $endpoint;
	}

	public static function make(): self
	{
		$settings = (array) get_option( self::OPTION_KEY, array() );
		$endpoint = isset( $settings['owc-gf-pdc-leges-endpoint'] )
			? (string) $settings['owc-gf-pdc-leges-endpoint']
			: '';

		return new self( $endpoint );
	}

	/**
	 * Returns the price for the given leges ID, or null when it cannot be resolved.
	 * An exception is never thrown so callers can safely fall back to the GF configured price.
	 */
	public function get_price( string $leges_id ): ?string
	{
		if ( '' === $this->endpoint ) {
			return null;
		}

		$lege = $this->get_cached_data( $leges_id );

		if ( null === $lege ) {
			return null;
		}

		if ( ! isset( $lege['id'] ) ) {
			return null;
		}

		if ( (string) $lege['id'] !== $leges_id ) {
			return null;
		}

		$price = trim( (string) $lege['price'] );

		if ( '' === $price ) {
			return null;
		}

		if ( ! is_numeric( $price ) ) {
			return null;
		}

		return (string) $price;
	}

	/**
	 * Retrieves the endpoint data from cache when available, or performs a fresh fetch when not.
	 */
	private function get_cached_data( string $leges_id ): ?array
	{
		$transient_key = self::TRANSIENT_PREFIX . md5( $this->endpoint . '_' . $leges_id );

		$cached = get_transient( $transient_key );

		if ( is_array( $cached ) ) {
			return $cached;
		}

		$data = $this->fetch_data( $leges_id );

		if ( null === $data ) {
			return null;
		}

		set_transient( $transient_key, $data, self::TRANSIENT_LIFETIME );

		return $data;
	}

	/**
	 * Performs the actual HTTP request to the PDC-leges endpoint.
	 */
	private function fetch_data( string $leges_id ): ?array
	{
		$response = wp_remote_get(
			sprintf( '%s/%s', untrailingslashit( $this->endpoint ), $leges_id ),
			array(
				'timeout'    => 10,
				'user-agent' => 'OWC GravityForms PDC Leges/' . OWC_GF_PDC_LEGES_VERSION,
			)
		);

		if ( is_wp_error( $response ) ) {
			// Log the error so administrators can diagnose endpoint issues.
			// phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_error_log
			error_log( sprintf( '[OWC PDC Leges] Endpoint request failed: %s', $response->get_error_message() ) );

			return null;
		}

		$status_code = (int) wp_remote_retrieve_response_code( $response );

		if ( 200 !== $status_code ) {
			// phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_error_log
			error_log( sprintf( '[OWC PDC Leges] Endpoint returned HTTP %d for URL: %s/%s', $status_code, untrailingslashit( $this->endpoint ), $leges_id ) );

			return null;
		}

		$body = wp_remote_retrieve_body( $response );
		$json = json_decode( $body, true );

		if ( ! is_array( $json ) ) {
			// phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_error_log
			error_log( '[OWC PDC Leges] Endpoint response is not valid JSON.' );

			return null;
		}

		if ( ! isset( $json['price'] ) ) {
			// phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_error_log
			error_log( '[OWC PDC Leges] Endpoint response does not contain a "price" field.' );

			return null;
		}

		return $json;
	}
}
