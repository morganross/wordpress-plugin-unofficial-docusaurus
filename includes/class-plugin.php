<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class UDOC_Plugin {

	public static function init() {
		add_action( 'init', array( __CLASS__, 'register' ) );
		add_action( 'admin_menu', array( __CLASS__, 'register_admin_page' ) );
		add_filter( 'body_class', array( __CLASS__, 'add_body_class' ) );
	}

	public static function register() {
		UDOC_Shortcode::register();
	}

	public static function add_body_class( $classes ) {
		global $post;

		if ( $post instanceof WP_Post && has_shortcode( $post->post_content, 'acm_docs' ) ) {
			$classes[] = 'udoc-page';
		}

		return $classes;
	}

	public static function register_admin_page() {
		add_options_page(
			__( 'Unofficial Docusaurus', 'unofficial-docusaurus' ),
			__( 'Unofficial Docusaurus', 'unofficial-docusaurus' ),
			'manage_options',
			'unofficial-docusaurus',
			array( __CLASS__, 'render_admin_page' )
		);
	}

	public static function render_admin_page() {
		$docs = UDOC_Doc_Registry::get_docs();
		?>
		<div class="wrap">
			<h1><?php esc_html_e( 'Unofficial Docusaurus for WordPress', 'unofficial-docusaurus' ); ?></h1>
			<p><?php esc_html_e( 'WordPress keeps the real site shell. This plugin renders markdown docs inside it.', 'unofficial-docusaurus' ); ?></p>
			<p><strong><?php esc_html_e( 'Shortcode:', 'unofficial-docusaurus' ); ?></strong> <code>[acm_docs]</code></p>
			<p><strong><?php esc_html_e( 'Loaded docs:', 'unofficial-docusaurus' ); ?></strong> <?php echo esc_html( count( $docs ) ); ?></p>
			<table class="widefat striped">
				<thead>
					<tr>
						<th><?php esc_html_e( 'Slug', 'unofficial-docusaurus' ); ?></th>
						<th><?php esc_html_e( 'Title', 'unofficial-docusaurus' ); ?></th>
						<th><?php esc_html_e( 'Path', 'unofficial-docusaurus' ); ?></th>
					</tr>
				</thead>
				<tbody>
					<?php foreach ( $docs as $doc ) : ?>
						<tr>
							<td><code><?php echo esc_html( $doc['slug'] ); ?></code></td>
							<td><?php echo esc_html( $doc['title'] ); ?></td>
							<td><code><?php echo esc_html( $doc['path'] ); ?></code></td>
						</tr>
					<?php endforeach; ?>
				</tbody>
			</table>
		</div>
		<?php
	}
}
