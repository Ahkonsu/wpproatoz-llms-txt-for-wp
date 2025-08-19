<?php
/**
 * Helper class for Markdown operations.
 *
 * @package LLMsTxtForWP
 */
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}
class LLMS_Txt_Markdown {
	/**
	 * Convert HTML to Markdown using a reliable library.
	 *
	 * @param string $html The HTML content.
	 * @return string
	 */
	public static function convert( $html ) {
		if ( ! class_exists( 'League\HTMLToMarkdown\HtmlConverter' ) ) {
			return '';
		}
		$markdown_arguments = array(
			'strip_tags' => true,
		);
		/**
		 * Filter the arguments used for converting HTML to Markdown.
		 */
		$markdown_arguments = apply_filters( 'llms_txt_markdown_arguments', $markdown_arguments );
		// Sanitize HTML input to prevent XSS.
		$html = wp_kses_post( $html );
		try {
			$converter = new League\HTMLToMarkdown\HtmlConverter( $markdown_arguments );
			$result = $converter->convert( $html );
			return $result;
		} catch ( Exception $e ) {
			return '';
		}
	}

	/**
	 * Convert a post object to Markdown.
	 *
	 * @param WP_Post $post         The post object.
	 * @param bool    $include_meta Whether to include meta information like title and date.
	 * @return string
	 */
	public static function convert_post_to_markdown( $post, $include_meta = true ) {
		if ( ! $post instanceof WP_Post ) {
			return '';
		}
		$markdown = '';
		if ( $include_meta ) {
			$markdown .= '# ' . esc_html( $post->post_title ) . "\n\n";
			// Add post meta.
			$markdown .= '*Published:* ' . esc_html( get_the_date( 'Y-m-d', $post ) ) . "\n";
			$markdown .= '*Author:* ' . esc_html( get_the_author_meta( 'display_name', $post->post_author ) ) . "\n\n";
		}
		// Convert content using the convert method.
		$content = apply_filters( 'the_content', $post->post_content );
		$converted_content = self::convert( $content );
		$markdown .= $converted_content;

		// Check if the content is too thin (30-120 characters)
		$content_length =  strlen( strip_tags( trim( $converted_content ) ) );

		if ( $content_length >= -1 && $content_length <= 120 ) {
			$enhanced_markdown = self::fetch_and_convert_public_content( $post, $include_meta );
			if ( ! empty( $enhanced_markdown ) ) {
				$markdown = $enhanced_markdown;
			}
		}

		return apply_filters( 'llms_txt_markdown_content', $markdown, $post );
	}

	/**
	 * Fetch the public-facing version of a post and convert its main content to markdown.
	 *
	 * @param WP_Post $post         The post object.
	 * @param bool    $include_meta Whether to include meta information like title and date.
	 * @return string
	 */
	private static function fetch_and_convert_public_content( $post, $include_meta = true ) {
		// Get the public URL for the post
		$post_url = get_permalink( $post->ID );
		if ( ! $post_url ) {
			return '';
		}

		// Fetch the public-facing page
		$response = wp_remote_get( $post_url, array(
			'timeout' => 30,
			'user-agent' => 'WordPress/' . get_bloginfo( 'version' ) . '; ' . get_bloginfo( 'url' ),
		) );

		if ( is_wp_error( $response ) ) {
			return '';
		}

		$response_code = wp_remote_retrieve_response_code( $response );
		if ( $response_code !== 200 ) {
			return '';
		}

		$html = wp_remote_retrieve_body( $response );
		if ( empty( $html ) ) {
			return '';
		}

		// Parse the HTML to extract content from #content
		$main_content = self::extract_main_content( $html );
		if ( empty( $main_content ) ) {
			return '';
		}

		// Build the markdown with meta if requested
		$markdown = '';
		if ( $include_meta ) {
			$markdown .= '# ' . esc_html( $post->post_title ) . "\n\n";
			$markdown .= '*Published:* ' . esc_html( get_the_date( 'Y-m-d', $post ) ) . "\n";
			$markdown .= '*Author:* ' . esc_html( get_the_author_meta( 'display_name', $post->post_author ) ) . "\n\n";
		}

		// Convert the extracted content to markdown
		$converted_content = self::convert( $main_content );
		$markdown .= $converted_content;

		return $markdown;
	}

	/**
	 * Extract main content from HTML, looking for #content or similar containers.
	 *
	 * @param string $html The full HTML content.
	 * @return string
	 */
	private static function extract_main_content( $html ) {
		// Use DOMDocument to parse HTML
		libxml_use_internal_errors( true );
		$dom = new DOMDocument();
		$dom->loadHTML( mb_convert_encoding( $html, 'HTML-ENTITIES', 'UTF-8' ), LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD );
		libxml_clear_errors();

		$xpath = new DOMXPath( $dom );

		// Try different selectors in order of preference
		$selectors = array(
			'//div[@id="content"]',
			'//main[@id="content"]',
			'//section[@id="content"]',
			'//div[contains(@class, "content")]',
			'//main[contains(@class, "content")]',
			'//article',
			'//div[contains(@class, "post-content")]',
			'//div[contains(@class, "entry-content")]',
			'//main',
		);

		foreach ( $selectors as $selector ) {
			$nodes = $xpath->query( $selector );
			if ( $nodes && $nodes->length > 0 ) {
				$content_node = $nodes->item( 0 );
				
				// Get the inner HTML of the content node
				$inner_html = '';
				foreach ( $content_node->childNodes as $child ) {
					$inner_html .= $dom->saveHTML( $child );
				}
				
				if ( ! empty( trim( $inner_html ) ) ) {
					return $inner_html;
				}
			}
		}

		// Fallback: return the entire body content
		$body_nodes = $xpath->query( '//body' );
		if ( $body_nodes && $body_nodes->length > 0 ) {
			$body_node = $body_nodes->item( 0 );
			$inner_html = '';
			foreach ( $body_node->childNodes as $child ) {
				$inner_html .= $dom->saveHTML( $child );
			}
			return $inner_html;
		}

		return '';
	}
}
?>
