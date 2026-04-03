<?php

declare(strict_types=1);

/**
 * Bootstrap providers.
 *
 * @package OWC_GravityForms_PDC_Leges
 * @author  Yard | Digital Agency
 * @since   1.0.0
 */

namespace OWC\GravityForms\PdcLeges;

/**
 * Exit when accessed directly.
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use OWC\GravityForms\PdcLeges\Providers\GravityFormsServiceProvider;

/**
 * Bootstraps all service providers.
 *
 * @since 1.0.0
 */
final class Bootstrap
{
	/**
	 * @var array<\OWC\GravityForms\PdcLeges\Providers\ServiceProvider>
	 */
	private array $providers;

	public function __construct()
	{
		add_action(
			'after_setup_theme',
			function () {
				$this->register_plugin_text_domain();
				$this->providers = $this->get_providers();
				$this->register_providers();
				$this->boot_providers();
			}
		);
	}

	private function get_providers(): array
	{
		return array(
			new GravityFormsServiceProvider(),
		);
	}

	private function register_providers(): void
	{
		foreach ( $this->providers as $provider ) {
			$provider->register();
		}
	}

	private function boot_providers(): void
	{
		foreach ( $this->providers as $provider ) {
			$provider->boot();
		}
	}

	/**
	 * Ensures translations are loaded in non-standard setups where
	 * load_plugin_textdomain() cannot resolve the correct path.
	 * Loads the .mo file manually via load_textdomain().
	 */
	protected function register_plugin_text_domain(): void
	{
		$locale = determine_locale();
		$mofile = sprintf( '%s/languages/%s-%s.mo', untrailingslashit( OWC_GF_PDC_LEGES_DIR_PATH ), untrailingslashit( OWC_GF_PDC_LEGES_PLUGIN_SLUG ), $locale );

		if ( ! file_exists( $mofile ) ) {
			return;
		}

		load_textdomain( OWC_GF_PDC_LEGES_PLUGIN_SLUG, $mofile );
	}
}
