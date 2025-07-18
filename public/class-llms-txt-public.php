<?php
/**
 * The public-facing functionality of the plugin.
 *
 * @package LLMsTxtForWP
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

class LLMS_Txt_Public {

	/**
	 * Add custom query vars.
	 *
	 * @param array $vars The array of query vars.
	 * @return array
	 */
	public function add_query_vars( $vars ) {
		$vars[] = 'markdown';
		$vars[] = 'llms_txt';
		return $vars;
	}

	/**
	 * Add rewrite rules for markdown endpoints.
	 */
	public function add_rewrite_rules() {
		$settings = LLMS_Txt_Core::get_settings();

		// Check for existing conflicting rules.
		$existing_rules = get_option( 'rewrite_rules', array() );
		$conflict_detected = false;
		foreach ( $existing_rules as $pattern => $rewrite ) {
			if ( preg_match( '/^llms\.txt$|^(.+?)\.md$/', $pattern ) && $rewrite !== 'index.php?llms_txt=1' && $rewrite !== 'index.php?markdown=1&llms_md_path=$matches[1]' ) {
				$conflict_detected = true;
			}
		}
		if ( $conflict_detected && current_user_can( 'manage_options' ) ) {
			add_action( 'admin_notices', array( $this, 'rewrite_conflict_notice' ) );
		}

		// Add llms.txt rule.
		add_rewrite_rule(
			'^llms\.txt$',
			'index.php?llms_txt=1',
			'top'
		);

		// Add .md rule if enabled, without post type prefix.
		if ( 'yes' === $settings['enable_md_support'] ) {
			add_rewrite_rule(
				'(.+?)\.md$',
				'index.php?markdown=1&llms_md_path=$matches[1]',
				'top'
			);
		}
	}

	/**
	 * Display admin notice for rewrite rule conflicts.
	 */
	public function rewrite_conflict_notice() {
		?>
		<div class="notice notice-warning is-dismissible">
			<p><?php esc_html_e( 'LLMs.txt for WP: Potential rewrite rule conflict detected for llms.txt or .md URLs. Please go to Settings > Permalinks and click Save Changes, or check for conflicting plugins/themes.', 'wpproatoz-llms-txt-for-wp' ); ?></p>
		</div>
		<?php
	}

	/**
	 * Instead of using rewrite rules, we'll parse the request ourselves
	 */
	public function parse_request( $wp ) {
		$settings = LLMS_Txt_Core::get_settings();

		if ( 'yes' !== $settings['enable_md_support'] ) {
			return;
		}

		$server_request_uri = isset( $_SERVER['REQUEST_URI'] ) ? sanitize_text_field( wp_unslash( $_SERVER['REQUEST_URI'] ) ) : '';
		$_SERVER['REQUEST_URI'] = preg_replace( '/\.md$/', '', $_SERVER['REQUEST_URI'] );

		// Check if the current URL ends with .md
		if ( preg_match( '/\.md$/', $server_request_uri ) && ! preg_match( '|^/wp-admin/|', $server_request_uri ) ) {
			// Let WordPress parse the clean URL normally
			$wp->parse_request();

			// Now add our markdown flag
			$wp->query_vars['markdown'] = 1;
			$wp->query_vars['llms_md_path'] = sanitize_text_field( rtrim( $server_request_uri, '.md' ) );
		}
	}

	/**
	 * Handle markdown requests.
	 */
	public function handle_markdown_requests() {
		$settings = LLMS_Txt_Core::get_settings();

		if ( 'yes' !== $settings['enable_md_support'] || ! get_query_var( 'markdown' ) ) {
			return;
		}

		$path = get_query_var( 'llms_md_path', '' );
		if ( empty( $path ) ) {
			wp_die( esc_html__( 'Invalid Markdown request.', 'wpproatoz-llms-txt-for-wp' ), 400 );
		}

		// Query for the post across all selected post types using the slug.
		$args = array(
			'name'           => $path,
			'post_type'      => $settings['post_types'],
			'post_status'    => 'publish',
			'posts_per_page' => 1,
		);
		$posts = get_posts( $args );
		$post = ! empty( $posts ) ? $posts[0] : null;

		if ( ! $post ) {
			wp_die( esc_html__( 'Post not found.', 'wpproatoz-llms-txt-for-wp' ), 404 );
		}

		// Check if the post is accessible.
		if ( 'publish' !== $post->post_status || ! current_user_can( 'read_post', $post->ID ) ) {
			wp_redirect( get_permalink( $post ) );
			exit;
		}

		// Check if this post should be included.
		$should_include = in_array( $post->post_type, $settings['post_types'], true );
		$should_include = apply_filters( 'llms_txt_include_post', $should_include, $post, current_user_can( 'read_post', $post->ID ) );
		if ( ! $should_include ) {
			// Redirect to the .md-less version of the post.
			wp_redirect( get_permalink( $post ) );
			exit;
		}

		// Prepare the Markdown content.
		try {
			$markdown_content = LLMS_Txt_Markdown::convert_post_to_markdown( $post, true );
			if ( empty( $markdown_content ) ) {
				throw new Exception( 'Failed to generate Markdown content for post ID ' . $post->ID );
			}

			// Output the Markdown content with proper headers.
			header( 'Content-Type: text/markdown; charset=utf-8' );
			echo $markdown_content; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Content is escaped in the conversion method.
			exit;
		} catch ( Exception $e ) {
			wp_die( esc_html__( 'Error generating Markdown content.', 'wpproatoz-llms-txt-for-wp' ), 500 );
		}
	}

	/**
	 * Handle llms.txt requests.
	 */
	public function handle_llms_txt_requests() {
		if ( ! get_query_var( 'llms_txt' ) ) {
			return;
		}

		// Check for cached content.
		$cache_key = 'llms_txt_cache';
		$output = get_transient( $cache_key );
		if ( false !== $output ) {
			header( 'Content-Type: text/plain; charset=utf-8' );
			echo apply_filters( 'llms_txt_index_content', $output, current_user_can( 'read' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Content is already escaped.
			exit;
		}

		$settings = LLMS_Txt_Core::get_settings();
		$output   = '';

		try {
			if ( ! empty( $settings['selected_post'] ) ) {
				// Selected post/page.
				$post = get_post( $settings['selected_post'] );
				if ( $post && 'publish' === $post->post_status && current_user_can( 'read_post', $post->ID ) ) {
					// Output post title and content.
					$output .= LLMS_Txt_Markdown::convert_post_to_markdown( $post );
				} else {
					throw new Exception( 'Selected post is not accessible or does not exist: ID ' . $settings['selected_post'] );
				}
			} else {
				// Validate post types.
				$valid_post_types = get_post_types( array( 'public' => true ), 'names' );
				$settings['post_types'] = array_intersect( $settings['post_types'], $valid_post_types );

				// All posts, grouped by post type and categories. Also include site name and description.
				$output .= '# ' . esc_html( get_bloginfo( 'name' ) ) . "\n\n";
				$bloginfo = get_bloginfo( 'description' );
				if ( ! empty( $bloginfo ) ) {
					$output .= esc_html( $bloginfo ) . "\n\n";
				}
				$output .= "---\n\n";

				if ( 'yes' === $settings['enable_md_support'] ) {
					$output .= "## Available Content\n\n";

					// List posts by post type.
					foreach ( $settings['post_types'] as $post_type ) {
						$args = array(
							'post_type'      => $post_type,
							'posts_per_page' => $settings['posts_limit'],
							'post_status'    => 'publish',
							'fields'         => 'ids', // Optimize query.
						);
						// Apply category filter only for non-post post types that support categories.
						if ( $post_type !== 'post' && ! empty( $settings['categories'] ) && in_array( 'category', get_object_taxonomies( $post_type ), true ) ) {
							$args['category__in'] = $settings['categories'];
						}
						$posts = get_posts( $args );

						if ( ! empty( $posts ) ) {
							$post_type_obj = get_post_type_object( $post_type );
							if ( ! current_user_can( 'read', $post_type ) ) {
								continue; // Skip post types the user cannot read.
							}
							$output .= '### ' . esc_html( $post_type_obj->labels->name ) . "\n\n";

							foreach ( $posts as $post_id ) {
								if ( current_user_can( 'read_post', $post_id ) ) {
									$post = get_post( $post_id );
									// Use post slug directly without post type prefix.
									$slug = $post->post_name;
									$output .= '* [' . esc_html( $post->post_title ) . '](' . esc_url( home_url( '/' . $slug . '.md' ) ) . ")\n";
								}
							}
							$output .= "\n";
						}
					}

					// List posts by category if specific categories are selected.
					if ( ! empty( $settings['categories'] ) ) {
						$categories = get_categories( array(
							'include'    => $settings['categories'],
							'hide_empty' => false,
						) );
						foreach ( $categories as $category ) {
							$args = array(
								'post_type'      => $settings['post_types'],
								'posts_per_page' => $settings['posts_limit'],
								'post_status'    => 'publish',
								'fields'         => 'ids',
								'category__in'   => array( $category->term_id ),
							);
							$posts = get_posts( $args );

							if ( ! empty( $posts ) ) {
								$output .= '### ' . esc_html( $category->name ) . "\n\n";
								if ( ! empty( $category->description ) ) {
									$output .= '#### ' . esc_html( $category->description ) . "\n\n";
								}
								foreach ( $posts as $post_id ) {
									if ( current_user_can( 'read_post', $post_id ) ) {
										$post = get_post( $post_id );
										$slug = $post->post_name;
										$output .= '* [' . esc_html( $post->post_title ) . '](' . esc_url( home_url( '/' . $slug . '.md' ) ) . ")\n";
									}
								}
								$output .= "\n";
							}
						}
					}
				} else {
					// If .md support is not enabled, show the post title and content.
					foreach ( $settings['post_types'] as $post_type ) {
						$args = array(
							'post_type'      => $post_type,
							'posts_per_page' => $settings['posts_limit'],
							'post_status'    => 'publish',
						);
						// Apply category filter only for non-post post types that support categories.
						if ( $post_type !== 'post' && ! empty( $settings['categories'] ) && in_array( 'category', get_object_taxonomies( $post_type ), true ) ) {
							$args['category__in'] = $settings['categories'];
						}
						$args = apply_filters( 'llms_txt_posts_args', $args, $post_type, current_user_can( 'read', $post_type ) );
						$posts = get_posts( $args );

						if ( ! empty( $posts ) ) {
							if ( ! current_user_can( 'read', $post_type ) ) {
								continue; // Skip post types the user cannot read.
							}
							$output .= '### ' . esc_html( get_post_type_object( $post_type )->labels->name ) . "\n\n";

							foreach ( $posts as $post ) {
								if ( current_user_can( 'read_post', $post->ID ) ) {
									$output .= LLMS_Txt_Markdown::convert_post_to_markdown( $post, true ) . "\n\n";
									$output .= "---\n\n";
								}
							}
						}
					}

					// List posts by category if specific categories are selected and .md support is disabled.
					if ( ! empty( $settings['categories'] ) ) {
						$categories = get_categories( array(
							'include'    => $settings['categories'],
							'hide_empty' => false,
						) );
						foreach ( $categories as $category ) {
							$args = array(
								'post_type'      => $settings['post_types'],
								'posts_per_page' => $settings['posts_limit'],
								'post_status'    => 'publish',
								'category__in'   => array( $category->term_id ),
							);
							$posts = get_posts( $args );

							if ( ! empty( $posts ) ) {
								$output .= '### ' . esc_html( $category->name ) . "\n\n";
								if ( ! empty( $category->description ) ) {
									$output .= esc_html( $category->description ) . "\n\n";
								}
								foreach ( $posts as $post ) {
									if ( current_user_can( 'read_post', $post->ID ) ) {
										$output .= LLMS_Txt_Markdown::convert_post_to_markdown( $post, true ) . "\n\n";
										$output .= "---\n\n";
									}
								}
							}
						}
					}
				}
			}

			// Cache the output.
			if ( ! set_transient( $cache_key, $output, HOUR_IN_SECONDS ) && current_user_can( 'manage_options' ) ) {
				add_action( 'admin_notices', function() {
					?>
					<div class="notice notice-warning is-dismissible">
						<p><?php esc_html_e( 'LLMs.txt for WP: Failed to set cache for llms.txt content. Check server caching configuration.', 'wpproatoz-llms-txt-for-wp' ); ?></p>
					</div>
					<?php
				} );
			}

			// Record cache attempt.
			set_transient( 'llms_txt_cache_attempted', true, HOUR_IN_SECONDS );

			// Output the llms.txt content with proper headers.
			header( 'Content-Type: text/plain; charset=utf-8' );
			echo apply_filters( 'llms_txt_index_content', $output, current_user_can( 'read' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Content is already escaped.
			exit;
		} catch ( Exception $e ) {
			wp_die( esc_html__( 'Error generating llms.txt content.', 'wpproatoz-llms-txt-for-wp' ), 500 );
		}
	}
}

?>