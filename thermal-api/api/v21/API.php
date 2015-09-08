<?php

namespace Voce\Thermal\v21;

if ( !defined( __NAMESPACE__ . '\\MAX_POSTS_PER_PAGE' ) ) {
	define( __NAMESPACE__ . '\\MAX_POSTS_PER_PAGE', 100 );
}

if ( !defined( __NAMESPACE__ . '\\MAX_TERMS_PER_PAGE' ) ) {
	define( __NAMESPACE__ . '\\MAX_TERMS_PER_PAGE', 100 );
}

if ( !defined( __NAMESPACE__ . '\\MAX_USERS_PER_PAGE' ) ) {
	define( __NAMESPACE__ . '\\MAX_USERS_PER_PAGE', 100 );
}

if ( !defined( __NAMESPACE__ . '\\MAX_COMMENTS_PER_PAGE' ) ) {
	define( __NAMESPACE__ . '\\MAX_COMMENTS_PER_PAGE', 100 );
}

/**
 *
 */
class API extends \Voce\Thermal\API_Base {

	protected $version = '2.1';

	/**
	 * Register the allowed routes.
	 * @param \Slim\Slim $app
	 */
	public function __construct( \Slim\Slim $app ) {
		parent::__construct( $app );

		$this->registerRoute( 'GET', 'posts/?', array( __NAMESPACE__ . '\\controllers\\Posts', 'find' ) );
		$this->registerRoute( 'GET', 'posts/:id/?', array( __NAMESPACE__ . '\\controllers\\Posts', 'findById' ) );
		$this->registerRoute( 'GET', 'posts/:id/comments/?', array( __NAMESPACE__ . '\\controllers\\Comments', 'findByPost' ) );
		$this->registerRoute( 'POST', 'posts/?', array( __NAMESPACE__ . '\\controllers\\Posts', 'posts' ) );
		$this->registerRoute( 'POST', 'comments/?', array( __NAMESPACE__ . '\\controllers\\Comments', 'create' ) );

		if ( function_exists( 'wp_using_ext_object_cache' ) && wp_using_ext_object_cache() ) {
			//add filters for last-modified based off of the 'last_changed' caching in core
			$filter_last_post_mod = function($last_modified) {
				if( $last_changed = wp_cache_get( 'last_changed', 'posts' ) ) {
					list( $micro, $time ) = explode( ' ', $last_changed );
					$last_modified = gmdate( 'Y-m-d H:i:s', $time );

				}
				return $last_modified;
			};
			add_filter('thermal_get_lastpostmodified', $filter_last_post_mod);

			$filter_last_comment_mod = function( $last_modified ) {
				if( $last_changed = wp_cache_get( 'last_changed', 'comments' ) ) {
					list( $micro, $time ) = explode( ' ', $last_changed );
					$last_modified = gmdate( 'Y-m-d H:i:s', $time );

				}
				return $last_modified;
			};

			add_filter('thermal_get_lastcommentmodified', $filter_last_comment_mod);
			add_filter('thermal_comment_last_modified', $filter_last_comment_mod);

			$filter_last_term_mod = function( $last_modified ) {
				if( $last_changed = wp_cache_get( 'last_changed', 'terms' ) ) {
					list( $micro, $time ) = explode( ' ', $last_changed );
					$last_modified = gmdate( 'Y-m-d H:i:s', $time );
				}
				return $last_modified;
			};

			add_filter('thermal_get_lasttermmodified', $filter_last_term_mod);
			add_filter('thermal_term_last_modified', $filter_last_term_mod);

		}
	}

}

function toBool( $value ) {
	return ( bool ) $value;
}

function toArray( $value ) {
	return ( array ) $value;
}

function applyInt( $value ) {
	return array_map( 'intval', $value );
}

function toCommaSeparated( $value ) {
	return implode( ',', $value );
}
