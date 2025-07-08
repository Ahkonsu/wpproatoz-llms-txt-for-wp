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

		return apply_filters( 'llms_txt_markdown_content', $markdown, $post );
	}
}

?>