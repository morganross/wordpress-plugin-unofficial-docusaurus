<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class UDOC_Shortcode {

	public static function register() {
		add_shortcode( 'acm_docs', array( __CLASS__, 'render' ) );
	}

	public static function render( $atts ) {
		$atts = shortcode_atts(
			array(
				'slug'  => '',
				'title' => '',
			),
			$atts,
			'acm_docs'
		);

		$slug = UDOC_Router::resolve_slug( $atts['slug'] );
		$doc  = UDOC_Doc_Registry::get_doc( $slug );

		if ( ! $doc ) {
			return '<div class="udoc-error"><p>' . esc_html__( 'Docs page not found.', 'unofficial-docusaurus' ) . '</p></div>';
		}

		UDOC_Assets::enqueue();

		$data = array(
			'currentSlug' => $doc['slug'],
			'docs'        => array_values( UDOC_Doc_Registry::get_docs() ),
			'homeUrl'     => home_url( '/' ),
		);

		wp_add_inline_script(
			'udoc-app',
			'window.udocData = ' . wp_json_encode( $data ) . ';',
			'before'
		);

		$title = $atts['title'] ? $atts['title'] : $doc['title'];

		ob_start();
		?>
		<div class="udoc-shell" data-udoc-shell="true">
			<div id="udoc-root" class="udoc-root">
				<div class="udoc-layout">
					<aside class="udoc-sidebar" aria-label="<?php esc_attr_e( 'Docs navigation', 'unofficial-docusaurus' ); ?>">
						<?php echo wp_kses_post( UDOC_Doc_Renderer::render_sidebar( $doc['slug'] ) ); ?>
					</aside>
					<main class="udoc-main">
						<header class="udoc-header">
							<p class="udoc-kicker"><?php esc_html_e( 'Documentation', 'unofficial-docusaurus' ); ?></p>
							<h1><?php echo esc_html( $title ); ?></h1>
						</header>
						<article class="udoc-article">
							<?php echo wp_kses_post( UDOC_Doc_Renderer::render_doc( $doc ) ); ?>
						</article>
						<nav class="udoc-pagination" aria-label="<?php esc_attr_e( 'Docs pagination', 'unofficial-docusaurus' ); ?>">
							<?php echo wp_kses_post( UDOC_Doc_Renderer::render_pagination( $doc['slug'] ) ); ?>
						</nav>
					</main>
				</div>
			</div>
		</div>
		<?php
		return ob_get_clean();
	}
}
