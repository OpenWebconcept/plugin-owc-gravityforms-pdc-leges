<?php

declare(strict_types=1);

/**
 * Gravity Forms service provider.
 *
 * @package OWC_GravityForms_PDC_Leges
 * @author  Yard | Digital Agency
 * @since   1.0.0
 */

namespace OWC\GravityForms\PdcLeges\Providers;

/**
 * Exit when accessed directly.
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use GFAddOn;
use GFForms;
use OWC\GravityForms\PdcLeges\GravityForms\FieldSettings;
use OWC\GravityForms\PdcLeges\GravityForms\PdcLegesAddon;
use OWC\GravityForms\PdcLeges\GravityForms\PriceOverride;

/**
 * Registers all Gravity Forms related hooks and the addon.
 *
 * @since 1.0.0
 */
class GravityFormsServiceProvider extends ServiceProvider
{
	public function register(): void
	{
		$this->register_addon();
		$this->register_icon_styles_hooks();
		$this->register_field_setting_hooks();
		$this->register_price_override_hooks();
	}

	/**
	 * Registers the GF addon so it shows up under Forms > Settings > OWC PDC Leges.
	 */
	private function register_addon(): void
	{
		if ( ! method_exists( 'GFForms', 'include_addon_framework' ) ) {
			return;
		}

		GFForms::include_addon_framework();
		GFAddOn::register( PdcLegesAddon::class );
		PdcLegesAddon::get_instance();
	}

	private function register_icon_styles_hooks(): void
	{
		add_action( 'admin_enqueue_scripts', PdcLegesAddon::enqueue_icons_styles( ... ) );
	}

	private function register_field_setting_hooks(): void
	{
		$field_settings = new FieldSettings();

		add_action( 'gform_field_advanced_settings', $field_settings->render_leges_id_setting( ... ), 10, 2 );
		add_action( 'gform_editor_js', $field_settings->render_editor_js( ... ) );
		add_action( 'admin_enqueue_scripts', $field_settings->enqueue_editor_assets( ... ) );
	}

	private function register_price_override_hooks(): void
	{
		$price_override = new PriceOverride();

		add_filter( 'gform_pre_render', $price_override->override_product_prices( ... ), 10, 1 );
		add_filter( 'gform_pre_validation', $price_override->override_product_prices( ... ), 10, 1 );
	}
}
