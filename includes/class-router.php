<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class UDOC_Router {

	public static function resolve_slug( $shortcode_slug = '' ) {
		if ( is_string( $shortcode_slug ) && '' !== trim( $shortcode_slug ) ) {
			$raw = trim( wp_unslash( $shortcode_slug ), '/' );
			return preg_replace( '#[^a-z0-9/_-]#', '', strtolower( $raw ) );
		}

		if ( isset( $_GET['doc'] ) ) {
			$raw = sanitize_text_field( wp_unslash( $_GET['doc'] ) );
			$raw = trim( $raw, '/' );
			if ( '' !== $raw ) {
				return preg_replace( '#[^a-z0-9/_-]#', '', strtolower( $raw ) );
			}
		}

		return 'index';
	}

	public static function get_doc_url( $slug ) {
		return add_query_arg( 'doc', rawurlencode( $slug ), get_permalink() ?: home_url( '/' ) );
	}
}
