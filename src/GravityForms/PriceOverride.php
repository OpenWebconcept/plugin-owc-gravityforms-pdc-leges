<?php

declare(strict_types=1);

/**
 * Price override — replaces the product field price with the PDC leges price.
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

use OWC\GravityForms\PdcLeges\Services\PdcLegesService;

/**
 * Overrides the basePrice of GF product fields that have a leges_id set.
 *
 * @since 1.0.0
 */
class PriceOverride
{
	/**
	 * Loops through all product fields in the form and replaces basePrice
	 * with the price from the PDC-leges endpoint when a leges_id is set.
	 */
	public function override_product_prices( array $form ): array
	{
		if ( ! is_array( $form['fields'] ?? null ) ) {
			return $form;
		}

		$service = PdcLegesService::make();

		foreach ( $form['fields'] as &$field ) {
			if ( 'product' !== $field->type ) {
				continue;
			}

			$leges_id = isset( $field->leges_id ) ? (string) $field->leges_id : '';

			if ( '' === $leges_id ) {
				continue; // No leges ID configured, keep the existing GF price as-is.
			}

			$price = $service->get_price( $leges_id );

			if ( null === $price ) {
				continue; // Price could not be retrieved, fall back to configured GF price.
			}

			// GF parses basePrice with GFCommon::to_number(), so a plain decimal
			// string like "180.00" is safe to assign here.
			$field->basePrice = $price;
		}

		return $form;
	}
}
