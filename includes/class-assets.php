<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class UDOC_Assets {

	private static $did_enqueue = false;

	public static function enqueue() {
		if ( self::$did_enqueue ) {
			return;
		}

		self::$did_enqueue = true;

		$assets_dir = UDOC_PLUGIN_DIR . 'assets/docs-app/';
		$assets_url = UDOC_PLUGIN_URL . 'assets/docs-app/';
		$js_file    = null;
		$css_file   = null;

		$js_files = glob( $assets_dir . 'index-*.js' );
		if ( ! empty( $js_files ) ) {
			$js_file = basename( $js_files[0] );
		} elseif ( file_exists( $assets_dir . 'index-placeholder.js' ) ) {
			$js_file = 'index-placeholder.js';
		}

		$css_files = glob( $assets_dir . 'index-*.css' );
		if ( ! empty( $css_files ) ) {
			$css_file = basename( $css_files[0] );
		} elseif ( file_exists( $assets_dir . 'index-placeholder.css' ) ) {
			$css_file = 'index-placeholder.css';
		}

		if ( $css_file ) {
			wp_enqueue_style(
				'udoc-app',
				$assets_url . $css_file,
				array(),
				(string) filemtime( $assets_dir . $css_file )
			);
		}

		if ( $js_file ) {
			wp_enqueue_script(
				'udoc-app',
				$assets_url . $js_file,
				array(),
				(string) filemtime( $assets_dir . $js_file ),
				true
			);

			add_filter(
				'script_loader_tag',
				function ( $tag, $handle ) {
					if ( 'udoc-app' === $handle ) {
						return str_replace( ' src', ' type="module" src', $tag );
					}
					return $tag;
				},
				10,
				2
			);
		}
	}
}
