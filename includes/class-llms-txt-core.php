<?php
/**
 * The core plugin class.
 *
 * @package LLMsTxtForWP
 */

class LLMS_Txt_Core {

	/**
	 * Admin instance.
	 *
	 * @var LLMS_Txt_Admin
	 */
	private $admin;

	/**
	 * Public instance.
	 *
	 * @var LLMS_Txt_Public
	 */
	private $public;

	/**
	 * Initialize the class and set its properties.
	 */
	public function __construct() {
		$this->load_dependencies();
		$this->init_hooks();
	}

	/**
	 * Load the required dependencies for this plugin.
	 */
	private function load_dependencies() {
		// Ideally use autoloading; here we require files directly.
		require_once LLMS_TXT_PLUGIN_DIR . 'includes/class-llms-txt-markdown.php';
		require_once LLMS_TXT_PLUGIN_DIR . 'admin/class-llms-txt-admin.php';
		require_once LLMS_TXT_PLUGIN_DIR . 'public/class-llms-txt-public.php';
	}

	/**
	 * Register all hooks for the plugin.
	 */
	private function init_hooks() {
		// Admin hooks.
		$this->admin = new LLMS_Txt_Admin();
		add_action( 'admin_menu', array( $this->admin, 'add_plugin_admin_menu' ) );
		add_action( 'admin_init', array( $this->admin, 'register_settings' ) );
		add_filter( 'plugin_action_links_' . plugin_basename( LLMS_TXT_PLUGIN_FILE ), array( $this->admin, 'add_action_links' ) );
		add_action( 'admin_notices', array( $this, 'settings_error_notice' ) );

		// Public hooks.
		$this->public = new LLMS_Txt_Public();
		add_action( 'init', array( $this->public, 'add_rewrite_rules' ) );
		add_action( 'parse_request', array( $this->public, 'parse_request' ) );
		add_filter( 'query_vars', array( $this->public, 'add_query_vars' ) );
		add_action( 'template_redirect', array( $this->public, 'handle_markdown_requests' ), 1 );
		add_action( 'template_redirect', array( $this->public, 'handle_llms_txt_requests' ), 1 );

		// Activation hook to flush rewrite rules.
		register_activation_hook( LLMS_TXT_PLUGIN_FILE, array( $this, 'activate' ) );
		// Deactivation hook to clean up.
		register_deactivation_hook( LLMS_TXT_PLUGIN_FILE, array( $this, 'deactivate' ) );

		// Cache invalidation hooks.
		add_action( 'update_option_llms_txt_settings', array( $this, 'invalidate_cache' ) );
		add_action( 'save_post', array( $this, 'invalidate_cache_on_post_change' ), 10, 3 );
		add_action( 'delete_post', array( $this, 'invalidate_cache_on_post_change' ), 10, 1 );
	}

	/**
	 * Activation hook callback.
	 */
	public function activate() {
		// Ensure public instance is initialized.
		if ( ! isset( $this->public ) ) {
			$this->public = new LLMS_Txt_Public();
		}
		// Add rewrite rules.
		$this->public->add_rewrite_rules();

		// Flush rewrite rules to make the new rules effective.
		$rules_flushed = flush_rewrite_rules();
		if ( ! $rules_flushed ) {
			add_action( 'admin_notices', array( $this, 'activation_error_notice' ) );
		}

		// Check for rewrite rule conflicts.
		$rules = get_option( 'rewrite_rules' );
		if ( ! isset( $rules['^llms\.txt$'] ) && current_user_can( 'manage_options' ) ) {
			add_action( 'admin_notices', array( $this, 'rewrite_conflict_notice' ) );
		}
	}

	/**
	 * Deactivation hook callback.
	 */
	public function deactivate() {
		// Ensure public instance is initialized.
		if ( ! isset( $this->public ) ) {
			$this->public = new LLMS_Txt_Public();
		}

		// Delete llms.txt file if it exists (for future-proofing).
		if ( ! function_exists( 'WP_Filesystem' ) ) {
			require_once ABSPATH . 'wp-admin/includes/file.php';
		}
		if ( WP_Filesystem() ) {
			global $wp_filesystem;
			$file = ABSPATH . 'llms.txt';
			if ( $wp_filesystem->exists( $file ) ) {
				if ( ! $wp_filesystem->delete( $file ) ) {
					add_action( 'admin_notices', array( $this, 'deactivation_error_notice' ) );
				}
			}
		} else {
			add_action( 'admin_notices', array( $this, 'deactivation_error_notice' ) );
		}

		// Flush rewrite rules.
		flush_rewrite_rules();

		// Clear cache on deactivation.
		$this->invalidate_cache();
	}

	/**
	 * Invalidate the llms.txt cache.
	 */
	public function invalidate_cache() {
		delete_transient( 'llms_txt_cache' );
		delete_transient( 'llms_txt_cache_attempted' );
	}

	/**
	 * Invalidate cache when a post is saved or deleted.
	 *
	 * @param int     $post_id Post ID.
	 * @param WP_Post $post    Post object.
	 * @param bool    $update  Whether this is an update.
	 */
	public function invalidate_cache_on_post_change( $post_id, $post = null, $update = null ) {
		$settings = self::get_settings();
		if ( $post && in_array( $post->post_type, $settings['post_types'], true ) ) {
			$this->invalidate_cache();
		} elseif ( ! $post && in_array( get_post_type( $post_id ), $settings['post_types'], true ) ) {
			$this->invalidate_cache();
		}
	}

	/**
	 * Display admin notice for deactivation errors.
	 */
	public function deactivation_error_notice() {
		?>
		<div class="notice notice-error is-dismissible">
			<p><?php esc_html_e( 'LLMs.txt for WP: Failed to delete llms.txt file or initialize file system during deactivation. Please check file permissions.', 'wpproatoz-llms-txt-for-wp' ); ?></p>
		</div>
		<?php
	}

	/**
	 * Display admin notice for activation errors.
	 */
	public function activation_error_notice() {
		?>
		<div class="notice notice-error is-dismissible">
			<p><?php esc_html_e( 'LLMs.txt for WP: Failed to flush rewrite rules during activation. Please go to Settings > Permalinks and click Save Changes.', 'wpproatoz-llms-txt-for-wp' ); ?></p>
		</div>
		<?php
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
	 * Display admin notice for invalid settings or cache issues.
	 */
	public function settings_error_notice() {
		$settings = self::get_settings();
		$errors = array();

		// Check for invalid selected_post.
		if ( ! empty( $settings['selected_post'] ) && ! get_post( $settings['selected_post'] ) ) {
			$errors[] = __( 'The selected page for llms.txt is invalid or does not exist.', 'wpproatoz-llms-txt-for-wp' );
		}

		// Check for empty post_types when selected_post is not set.
		if ( empty( $settings['selected_post'] ) && empty( $settings['post_types'] ) ) {
			$errors[] = __( 'No post types are selected for llms.txt, and no specific page is chosen. The file will only contain the site name and description.', 'wpproatoz-llms-txt-for-wp' );
		}

		// Check for excessive posts_limit.
		if ( $settings['posts_limit'] > 1000 ) {
			$errors[] = __( 'The posts limit is set very high, which may impact performance.', 'wpproatoz-llms-txt-for-wp' );
		}

		// Check for invalid categories.
		if ( ! empty( $settings['categories'] ) ) {
			$valid_categories = get_categories( array( 'fields' => 'ids' ) );
			$invalid_categories = array_diff( $settings['categories'], $valid_categories );
			if ( ! empty( $invalid_categories ) ) {
				$errors[] = __( 'One or more selected categories are invalid or do not exist.', 'wpproatoz-llms-txt-for-wp' );
			}
		}

		// Check for cache issues, but only if a recent attempt to generate llms.txt was made.
		$cache_attempted = get_transient( 'llms_txt_cache_attempted' );
		if ( false !== $cache_attempted && ! empty( $settings['post_types'] ) && $settings['posts_limit'] > 0 ) {
			$cache = get_transient( 'llms_txt_cache' );
			if ( false === $cache ) {
				$errors[] = __( 'Failed to retrieve or set llms.txt cache. Check server caching configuration.', 'wpproatoz-llms-txt-for-wp' );
			}
		}

		if ( ! empty( $errors ) ) {
			?>
			<div class="notice notice-warning is-dismissible">
				<p><?php esc_html_e( 'LLMs.txt for WP: Configuration issues detected:', 'wpproatoz-llms-txt-for-wp' ); ?></p>
				<ul>
					<?php foreach ( $errors as $error ) : ?>
						<li><?php echo esc_html( $error ); ?></li>
					<?php endforeach; ?>
				</ul>
			</div>
			<?php
		}
	}

	/**
	 * Retrieve the plugin settings.
	 *
	 * @return array
	 */
	public static function get_settings() {
		$defaults = array(
			'selected_post'     => '', // Single post ID or empty
			'post_types'        => array( 'post', 'page' ), // Default public post types
			'posts_limit'       => 100, // Positive integer
			'enable_md_support' => 'yes', // 'yes' or ''
			'categories'        => array(), // Selected category IDs
		);

		return wp_parse_args( get_option( 'llms_txt_settings', array() ), $defaults );
	}
}

?>