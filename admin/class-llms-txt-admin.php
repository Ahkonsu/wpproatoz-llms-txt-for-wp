<?php
/**
 * The admin-specific functionality of the plugin.
 *
 * @package LLMsTxtForWP
 */

class LLMS_Txt_Admin {

	/**
	 * Plugin settings.
	 *
	 * @var array
	 */
	private $settings;

	/**
	 * Initialize the class and set its properties.
	 */
	public function __construct() {
		$this->settings = LLMS_Txt_Core::get_settings();
	}

	/**
	 * Add options page to the admin menu.
	 */
	public function add_plugin_admin_menu() {
		add_options_page(
			__( 'LLMs.txt Settings', 'wpproatoz-llms-txt-for-wp' ),
			__( 'LLMs.txt', 'wpproatoz-llms-txt-for-wp' ),
			'manage_options',
			'llms-txt-settings',
			array( $this, 'display_plugin_settings_page' )
		);
	}

	/**
	 * Register plugin settings.
	 */
	public function register_settings() {
		register_setting(
			'llms_txt_settings',
			'llms_txt_settings',
			array( $this, 'validate_settings' )
		);

		add_settings_section(
			'llms_txt_general_section',
			__( 'General Settings', 'wpproatoz-llms-txt-for-wp' ),
			array( $this, 'render_section_info' ),
			'llms-txt-settings'
		);

		add_settings_field(
			'selected_post',
			__( 'Selected Page for llms.txt', 'wpproatoz-llms-txt-for-wp' ),
			array( $this, 'render_selected_post_field' ),
			'llms-txt-settings',
			'llms_txt_general_section'
		);

		add_settings_field(
			'post_types',
			__( 'Post Types to Include', 'wpproatoz-llms-txt-for-wp' ),
			array( $this, 'render_post_types_field' ),
			'llms-txt-settings',
			'llms_txt_general_section'
		);

		add_settings_field(
			'categories',
			__( 'Categories to Include', 'wpproatoz-llms-txt-for-wp' ),
			array( $this, 'render_categories_field' ),
			'llms-txt-settings',
			'llms_txt_general_section'
		);

		add_settings_field(
			'posts_limit',
			__( 'Posts Limit', 'wpproatoz-llms-txt-for-wp' ),
			array( $this, 'render_posts_limit_field' ),
			'llms-txt-settings',
			'llms_txt_general_section'
		);

		add_settings_field(
			'enable_md_support',
			__( 'Enable *.md Support', 'wpproatoz-llms-txt-for-wp' ),
			array( $this, 'render_md_support_field' ),
			'llms-txt-settings',
			'llms_txt_general_section'
		);
	}

	/**
	 * Render the settings page.
	 */
	public function display_plugin_settings_page() {
		?>
		<div class="wrap">
			<h2><?php echo esc_html__( 'LLMs.txt Settings', 'wpproatoz-llms-txt-for-wp' ); ?></h2>
			<form method="post" action="options.php">
				<?php
				settings_fields( 'llms_txt_settings' );
				do_settings_sections( 'llms-txt-settings' );
				?>
				<p class="description" style="margin-bottom: -10px;">
					<?php
					printf(
						esc_html__( 'With these settings, your %1$s file will show %2$s.', 'wpproatoz-llms-txt-for-wp' ),
						'<a href="' . esc_url( home_url( 'llms.txt' ) ) . '" target="_blank">llms.txt</a>',
						'<strong id="llms-txt-settings-hint"></strong>'
					);
					?>
					<span id="llms-txt-settings-hint-has-md-support" style="display: none;">
						<?php
						printf(
							// translators: %1$s is a list of post types.
							esc_html__( 'Markdown versions will also be available when you add the .md extension to the URL of %1$s.', 'wpproatoz-llms-txt-for-wp' ),
							'<strong id="llms-txt-settings-hint-md-support-post-types"></strong>'
						);
						?>
					</span>
					<span id="llms-txt-settings-hint-no-md-support" style="display: none;">
						<?php esc_html_e( 'Markdown versions of posts will not be available when you add the .md extension to the URL.', 'wpproatoz-llms-txt-for-wp' ); ?>
					</span>
				</p>
				<div style="margin-top: 30px; display: flex; align-items: center; gap: 16px;">
					<?php submit_button( null, 'primary', null, false ); ?>
					<p class="description" style="margin: 0;">
						<?php
						printf(
							esc_html__( 'Tip: you can use the available %1$s to customize the content of your llms.txt file.', 'wpproatoz-llms-txt-for-wp' ),
							'<a href="https://github.com/search?q=repo%3AWP-Autoplugin%2Fllms-txt-for-wp%20apply_filters&type=code" target="_blank">' . esc_html__( 'filter hooks', 'wpproatoz-llms-txt-for-wp' ) . '</a>'
						);
						?>
					</p>
				</div>
			</form>
		</div>
		<script>
			(function() {
				var selectedPost = document.getElementById('llms_txt_settings_selected_post');
				var postTypes = document.querySelectorAll('input[name="llms_txt_settings[post_types][]"]');
				var categories = document.querySelectorAll('input[name="llms_txt_settings[categories][]"]');
				var allCategories = document.querySelector('input[name="llms_txt_settings[all_categories]"]');
				var mdSupport = document.getElementById('llms_txt_settings_enable_md_support');
				var hint = document.getElementById('llms-txt-settings-hint');
				var hintHasMdSupport = document.getElementById('llms-txt-settings-hint-has-md-support');
				var hintNoMdSupport = document.getElementById('llms-txt-settings-hint-no-md-support');
				var mdSupportPostTypes = document.getElementById('llms-txt-settings-hint-md-support-post-types');
				var postsLimit = document.getElementById('llms_txt_settings_posts_limit');

				function updateHint() {
					var hasMdSupport = mdSupport.checked;
					var selectedPostValue = selectedPost.value;
					var selectedPostText = selectedPost.options[selectedPost.selectedIndex].textContent.trim();
					var types = Array.from(postTypes).filter(function(type) {
						return type.checked;
					}).map(function(type) {
						return type.nextElementSibling ? type.nextElementSibling.textContent : '';
					});
					var selectedCategories = Array.from(categories).filter(function(cat) {
						return cat.checked;
					}).map(function(cat) {
						return cat.nextElementSibling ? cat.nextElementSibling.textContent : '';
					});
					var allCategoriesSelected = allCategories.checked && selectedCategories.length === 0;

					if (selectedPostValue) {
						hint.textContent = 'the content of the "' + selectedPostText + '" page';
					} else {
						if (types.length) {
							var content = '';
							if (hasMdSupport) {
								content = 'links to the .md versions of the ';
							} else {
								content = 'the contents of the ';
							}
							var categoryText = allCategoriesSelected || !selectedCategories.length ? '' : ' and categories ' + selectedCategories.join(', ') + ' with their descriptions';
							if (!allCategoriesSelected && selectedCategories.length && types.includes('Posts')) {
								content += 'latest ' + postsLimit.value + ' Posts, ';
							}
							hint.textContent = content + types.join(', ') + categoryText;
						} else {
							hint.textContent = 'just the site name and description';
						}
					}

					if (hasMdSupport && types.length) {
						hintHasMdSupport.style.display = 'inline';
						hintNoMdSupport.style.display = 'none';
						mdSupportPostTypes.textContent = types.join(', ');
					} else {
						hintHasMdSupport.style.display = 'none';
						hintNoMdSupport.style.display = 'inline';
					}
				}

				// Auto-deselect "All Categories" when a specific category is checked.
				function handleCategorySelection() {
					var anyCategoryChecked = Array.from(categories).some(function(cat) {
						return cat.checked;
					});
					if (anyCategoryChecked) {
						allCategories.checked = false;
					}
					updateHint();
				}

				selectedPost.addEventListener('change', updateHint);
				postsLimit.addEventListener('change', updateHint);
				postTypes.forEach(function(type) {
					type.addEventListener('change', updateHint);
				});
				categories.forEach(function(cat) {
					cat.addEventListener('change', handleCategorySelection);
				});
				allCategories.addEventListener('change', function() {
					if (allCategories.checked) {
						categories.forEach(function(cat) {
							cat.checked = false;
						});
					}
					updateHint();
				});
				mdSupport.addEventListener('change', updateHint);

				updateHint();
			})();
		</script>
		<?php
	}

	/**
	 * Render section information.
	 */
	public function render_section_info() {
		echo '<p>';
		printf(
			esc_html__( 'Configure your %1$s settings below.', 'wpproatoz-llms-txt-for-wp' ) . '</p>',
			'<a href="' . esc_url( home_url( 'llms.txt' ) ) . '" target="_blank">llms.txt</a>'
		);
	}

	/**
	 * Render selected post field.
	 */
	public function render_selected_post_field() {
		wp_dropdown_pages(
			array(
				'name'              => 'llms_txt_settings[selected_post]',
				'id'				=> 'llms_txt_settings_selected_post',
				'show_option_none'  => __( 'Select a page', 'wpproatoz-llms-txt-for-wp' ),
				'option_none_value' => '',
				'selected'          => $this->settings['selected_post'],
			)
		);
		echo '<p class="description">' . esc_html__( 'If a page is selected, only that page will be included in the llms.txt file. If no page is selected, posts from selected post types and categories will be included.', 'wpproatoz-llms-txt-for-wp' ) . '</p>';
	}

	/**
	 * Render post types field.
	 */
	public function render_post_types_field() {
		$args = array(
			'public'   => true,
		);
		$args = apply_filters( 'llms_txt_admin_post_types_args', $args );
		$post_types = get_post_types( $args, 'objects' );

		foreach ( $post_types as $post_type ) {
			// Skip attachments.
			if ( 'attachment' === $post_type->name ) {
				continue;
			}

			printf(
				'<label><input type="checkbox" name="llms_txt_settings[post_types][]" value="%s" %s> <span>%s</span></label><br>',
				esc_attr( $post_type->name ),
				checked( in_array( $post_type->name, $this->settings['post_types'], true ), true, false ),
				esc_html( $post_type->label )
			);
		}
		echo '<p class="description">' . esc_html__( 'Select the post types to include in the llms.txt file and the *.md support.', 'wpproatoz-llms-txt-for-wp' ) . '</p>';
	}

	/**
	 * Render categories field.
	 */
	public function render_categories_field() {
		$categories = get_categories( array( 'hide_empty' => false ) );
		$all_selected = empty( $this->settings['categories'] );

		// Add "All Categories" option.
		printf(
			'<label><input type="checkbox" name="llms_txt_settings[all_categories]" value="1" %s> <span>%s</span></label><br>',
			checked( $all_selected, true, false ),
			esc_html__( 'All Categories', 'wpproatoz-llms-txt-for-wp' )
		);

		// List individual categories.
		foreach ( $categories as $category ) {
			printf(
				'<label><input type="checkbox" name="llms_txt_settings[categories][]" value="%d" %s> <span>%s</span></label><br>',
				esc_attr( $category->term_id ),
				checked( in_array( $category->term_id, $this->settings['categories'], true ), true, false ),
				esc_html( $category->name )
			);
		}
		echo '<p class="description">' . esc_html__( 'Select specific categories to include posts in llms.txt as separate sections with their descriptions. Selecting a category will deselect "All Categories" to ensure your selections are saved. If "All Categories" is checked, posts will be listed only under post types. Posts will always include the latest posts regardless of category. Applies only to post types that support categories.', 'wpproatoz-llms-txt-for-wp' ) . '</p>';
	}

	/**
	 * Render posts limit field.
	 */
	public function render_posts_limit_field() {
		printf(
			'<input type="number" id="llms_txt_settings_posts_limit" name="llms_txt_settings[posts_limit]" value="%d" min="1">',
			esc_attr( $this->settings['posts_limit'] )
		);
	}

	/**
	 * Render MD support field.
	 */
	public function render_md_support_field() {
		echo '<p class="description"><label>';
		printf(
			'<input id="llms_txt_settings_enable_md_support" type="checkbox" name="llms_txt_settings[enable_md_support]" value="yes" %s>',
			checked( $this->settings['enable_md_support'], 'yes', false )
		);
		esc_html_e( 'Enable this option to provide a Markdown version of each post.', 'wpproatoz-llms-txt-for-wp' );
		echo '</label></p>';
	}

	/**
	 * Validate settings.
	 *
	 * @param array $input The input array.
	 * @return array
	 */
	public function validate_settings( $input ) {
		$output = array();

		// Sanitize selected_post
		$output['selected_post'] = isset( $input['selected_post'] ) ? absint( $input['selected_post'] ) : '';
		if ( $output['selected_post'] === 0 ) {
			$output['selected_post'] = '';
		}

		// Sanitize and validate post_types
		$output['post_types'] = isset( $input['post_types'] ) && is_array( $input['post_types'] ) ? array_map( 'sanitize_text_field', $input['post_types'] ) : array();
		$valid_post_types = get_post_types( array( 'public' => true ), 'names' );
		$output['post_types'] = array_filter( $output['post_types'], function( $post_type ) use ( $valid_post_types ) {
			return in_array( $post_type, $valid_post_types, true );
		});

		// Sanitize and validate categories
		$output['categories'] = array();
		if ( ! isset( $input['all_categories'] ) || $input['all_categories'] !== '1' || ! empty( $input['categories'] ) ) {
			$output['categories'] = isset( $input['categories'] ) && is_array( $input['categories'] ) ? array_map( 'absint', $input['categories'] ) : array();
			$valid_categories = get_categories( array( 'fields' => 'ids' ) );
			$output['categories'] = array_filter( $output['categories'], function( $cat_id ) use ( $valid_categories ) {
				return in_array( $cat_id, $valid_categories, true );
			});
		}

		// Sanitize posts_limit
		$output['posts_limit'] = isset( $input['posts_limit'] ) ? max( 1, absint( $input['posts_limit'] ) ) : 100;

		// Sanitize enable_md_support
		$output['enable_md_support'] = isset( $input['enable_md_support'] ) && $input['enable_md_support'] === 'yes' ? 'yes' : '';

		return $output;
	}

	/**
	 * Add plugin action link to the Settings page.
	 *
	 * @param array $links The existing links.
	 * @return array
	 */
	public function add_action_links( $links ) {
		$links[] = '<a href="' . esc_url( admin_url( 'options-general.php?page=llms-txt-settings' ) ) . '">' . esc_html__( 'Settings', 'wpproatoz-llms-txt-for-wp' ) . '</a>';
		return $links;
	}
}

?>