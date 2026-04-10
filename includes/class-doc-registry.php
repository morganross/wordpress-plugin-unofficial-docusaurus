<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class UDOC_Doc_Registry {

	private static $docs = null;

	public static function get_docs() {
		if ( null !== self::$docs ) {
			return self::$docs;
		}

		$docs      = array();
		$docs_root = trailingslashit( UDOC_PLUGIN_DIR . 'docs' );
		$files     = array();

		if ( is_dir( $docs_root ) ) {
			$iterator = new RecursiveIteratorIterator(
				new RecursiveDirectoryIterator(
					$docs_root,
					FilesystemIterator::SKIP_DOTS
				)
			);

			foreach ( $iterator as $file_info ) {
				if ( strtolower( $file_info->getExtension() ) !== 'md' ) {
					continue;
				}
				$files[] = $file_info->getPathname();
			}
		}

		foreach ( $files as $file ) {
			if ( ! is_file( $file ) ) {
				continue;
			}

			$relative = ltrim( str_replace( $docs_root, '', $file ), '/' );
			$slug     = preg_replace( '#\.md$#', '', $relative );
			$slug     = str_replace( DIRECTORY_SEPARATOR, '/', $slug );

			if ( 'README' === basename( $slug ) ) {
				$slug = trim( dirname( $slug ), '.' );
				$slug = '' === $slug ? 'index' : $slug;
			}

			$contents = file_get_contents( $file );
			if ( false === $contents ) {
				continue;
			}

			$meta = self::parse_front_matter( $contents );

			$docs[ $slug ] = array(
				'slug'        => $slug,
				'title'       => $meta['title'] ? $meta['title'] : self::humanize_slug( $slug ),
				'description' => $meta['description'],
				'path'        => $file,
				'content'     => $meta['content'],
				'section'     => strtok( $slug, '/' ),
			);
		}

		uksort(
			$docs,
			function ( $left, $right ) {
				if ( 'index' === $left ) {
					return -1;
				}
				if ( 'index' === $right ) {
					return 1;
				}
				return strcmp( $left, $right );
			}
		);

		self::$docs = $docs;
		return self::$docs;
	}

	public static function get_doc( $slug ) {
		$docs = self::get_docs();
		return isset( $docs[ $slug ] ) ? $docs[ $slug ] : null;
	}

	private static function parse_front_matter( $contents ) {
		$title       = '';
		$description = '';
		$body        = $contents;

		if ( preg_match( '/^---\s*(.*?)\s*---\s*(.*)$/s', $contents, $matches ) ) {
			$front_matter = trim( $matches[1] );
			$body         = $matches[2];
			$lines        = preg_split( '/\r\n|\r|\n/', $front_matter );

			foreach ( $lines as $line ) {
				if ( preg_match( '/^title:\s*(.+)$/i', $line, $title_match ) ) {
					$title = trim( $title_match[1], " \t\n\r\0\x0B\"'" );
				}
				if ( preg_match( '/^description:\s*(.+)$/i', $line, $desc_match ) ) {
					$description = trim( $desc_match[1], " \t\n\r\0\x0B\"'" );
				}
			}
		}

		return array(
			'title'       => $title,
			'description' => $description,
			'content'     => $body,
		);
	}

	private static function humanize_slug( $slug ) {
		$tail = basename( $slug );
		return ucwords( str_replace( array( '-', '_' ), ' ', $tail ) );
	}
}
