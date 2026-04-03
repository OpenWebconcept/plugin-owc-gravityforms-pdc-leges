<?php

declare(strict_types=1);

/**
 * Base service provider.
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

/**
 * Base service provider.
 *
 * @since 1.0.0
 */
abstract class ServiceProvider
{
	/**
	 * Register hooks and services.
	 */
	abstract public function register(): void;

	/**
	 * Boot the provider after all providers are registered.
	 */
	public function boot(): void {}
}
