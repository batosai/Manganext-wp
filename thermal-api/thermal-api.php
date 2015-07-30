<?php

/*
  Plugin Name: Thermal API Custom
  Version:     1
 */

define( "THERMAL_API_MIN_PHP_VER", '5.3.0' );

register_activation_hook( __FILE__, 'thermal_activation' );

function thermal_activation() {
	if ( version_compare( phpversion(), THERMAL_API_MIN_PHP_VER, '<' ) ) {
		die( sprintf( "The minimum PHP version required for Thermal API is %s", THERMAL_API_MIN_PHP_VER ) );
	}
}

if ( version_compare( phpversion(), THERMAL_API_MIN_PHP_VER, '>=' ) ) {
  @include(__DIR__ . '/vendor/autoload.php');
	require(__DIR__ . '/dispatcher.php');
	new Voce\Thermal\API_Dispatcher();
}
