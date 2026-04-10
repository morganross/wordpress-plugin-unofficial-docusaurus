<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class UDOC_Doc_Renderer {

	public static function render_doc( $doc ) {
		$content = $doc['content'];
		$lines   = preg_split( '/\r\n|\r|\n/', $content );
		$html    = '';
		$in_list = false;
		$in_code = false;
		$buffer  = array();

		foreach ( $lines as $line ) {
			if ( preg_match( '/^```/', $line ) ) {
				if ( $in_code ) {
					$html   .= '<pre><code>' . esc_html( implode( "\n", $buffer ) ) . '</code></pre>';
					$buffer  = array();
					$in_code = false;
				} else {
					self::flush_paragraph( $buffer, $html, $in_list );
					$in_code = true;
				}
				continue;
			}

			if ( $in_code ) {
				$buffer[] = $line;
				continue;
			}

			if ( preg_match( '/^(#{1,6})\s+(.+)$/', $line, $matches ) ) {
				self::flush_paragraph( $buffer, $html, $in_list );
				$level = strlen( $matches[1] );
				$text  = trim( $matches[2] );
				$id    = sanitize_title( $text );
				$html .= sprintf(
					'<h%d id="%s">%s</h%d>',
					$level,
					esc_attr( $id ),
					esc_html( $text ),
					$level
				);
				continue;
			}

			if ( preg_match( '/^- (.+)$/', $line, $matches ) ) {
				if ( ! $in_list ) {
					self::flush_paragraph( $buffer, $html, $in_list );
					$html   .= '<ul>';
					$in_list = true;
				}
				$html .= '<li>' . self::render_inline( $matches[1] ) . '</li>';
				continue;
			}

			if ( '' === trim( $line ) ) {
				self::flush_paragraph( $buffer, $html, $in_list );
				continue;
			}

			$buffer[] = $line;
		}

		self::flush_paragraph( $buffer, $html, $in_list );

		return $html;
	}

	public static function render_sidebar( $current_slug ) {
		$docs     = UDOC_Doc_Registry::get_docs();
		$grouped  = array();
		$sections = array(
			'index'  => __( 'Start Here', 'unofficial-docusaurus' ),
			'help'   => __( 'Help', 'unofficial-docusaurus' ),
			'users'  => __( 'Users', 'unofficial-docusaurus' ),
			'admins' => __( 'Admins', 'unofficial-docusaurus' ),
			'devs'   => __( 'Developers', 'unofficial-docusaurus' ),
		);

		foreach ( $docs as $doc ) {
			$grouped[ $doc['section'] ][] = $doc;
		}

		$html = '';

		foreach ( $sections as $section_slug => $label ) {
			if ( empty( $grouped[ $section_slug ] ) ) {
				continue;
			}

			$html .= '<section class="udoc-sidebar-group">';
			$html .= '<h2>' . esc_html( $label ) . '</h2>';
			$html .= '<ul>';

			foreach ( $grouped[ $section_slug ] as $doc ) {
				$is_active = $doc['slug'] === $current_slug;
				$html     .= sprintf(
					'<li><a href="%s" class="%s">%s</a></li>',
					esc_url( UDOC_Router::get_doc_url( $doc['slug'] ) ),
					$is_active ? 'is-active' : '',
					esc_html( $doc['title'] )
				);
			}

			$html .= '</ul>';
			$html .= '</section>';
		}

		return $html;
	}

	public static function render_pagination( $current_slug ) {
		$docs      = array_values( UDOC_Doc_Registry::get_docs() );
		$current   = null;
		$previous  = null;
		$next      = null;
		$docs_count = count( $docs );

		for ( $index = 0; $index < $docs_count; $index++ ) {
			if ( $docs[ $index ]['slug'] !== $current_slug ) {
				continue;
			}

			$current  = $index;
			$previous = $index > 0 ? $docs[ $index - 1 ] : null;
			$next     = $index < ( $docs_count - 1 ) ? $docs[ $index + 1 ] : null;
			break;
		}

		$html = '';

		if ( $previous ) {
			$html .= sprintf(
				'<a class="udoc-page-link prev" href="%s"><span>%s</span><strong>%s</strong></a>',
				esc_url( UDOC_Router::get_doc_url( $previous['slug'] ) ),
				esc_html__( 'Previous', 'unofficial-docusaurus' ),
				esc_html( $previous['title'] )
			);
		}

		if ( $next ) {
			$html .= sprintf(
				'<a class="udoc-page-link next" href="%s"><span>%s</span><strong>%s</strong></a>',
				esc_url( UDOC_Router::get_doc_url( $next['slug'] ) ),
				esc_html__( 'Next', 'unofficial-docusaurus' ),
				esc_html( $next['title'] )
			);
		}

		return $html;
	}

	private static function flush_paragraph( &$buffer, &$html, &$in_list ) {
		if ( $in_list ) {
			$html   .= '</ul>';
			$in_list = false;
		}

		if ( empty( $buffer ) ) {
			return;
		}

		$text   = trim( implode( ' ', $buffer ) );
		$buffer = array();

		if ( '' !== $text ) {
			$html .= '<p>' . self::render_inline( $text ) . '</p>';
		}
	}

	private static function render_inline( $text ) {
		$text = esc_html( $text );
		$text = preg_replace( '/`([^`]+)`/', '<code>$1</code>', $text );
		$text = preg_replace( '/\*\*([^*]+)\*\*/', '<strong>$1</strong>', $text );
		$text = preg_replace( '/\*([^*]+)\*/', '<em>$1</em>', $text );
		$text = preg_replace( '/\[([^\]]+)\]\(([^)]+)\)/', '<a href="$2">$1</a>', $text );
		return $text;
	}
}
