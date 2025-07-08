<?php
/**
 * Plugin Name: WPProAtoz LLMs.txt for WP
 * Plugin URI: https://github.com/Ahkonsu/wpproatoz-llms-txt-for-wp
 * Forked from: https://github.com/WP-Autoplugin/llms-txt-for-wp
 * Description: This plugin will generate LLM-friendly content as an llms.txt file and provides markdown versions of posts. This make it easier for AI to search your site and use less resources when doing do. Generates an llms.txt file for AI-friendly content and supports Markdown versions of posts and pages 
 * Version: 1.1.0
 * Requires at least: 6.0
 * Requires PHP: 8.0
 * Author: WPProAtoZ
 * Author URI: https://WPProAtoZ.com
 * OriginalAuthor: Balázs Piller
 * OriginalAuthor URI: https://wp-autoplugin.com
 * License: GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: wpproatoz-llms-txt-for-wp
 * Domain Path: /languages
 * Update URI: https://github.com/Ahkonsu/wpproatoz-llms-txt-for-wp/releases
 * GitHub Plugin URI: https://github.com/Ahkonsu/wpproatoz-llms-txt-for-wp/releases
 * GitHub Branch: main
 */

// Abort if this file is called directly.
if ( ! defined( 'WPINC' ) ) {
	die;
}

 ////***check for updates code

require 'plugin-update-checker/plugin-update-checker.php';
use YahnisElsts\PluginUpdateChecker\v5\PucFactory;

$myUpdateChecker = PucFactory::buildUpdateChecker(
	'https://github.com/Ahkonsu/wpproatoz-llms-txt-for-wp/',
	__FILE__,
	'wpproatoz-llms-txt-for-wp'
);

//Set the branch that contains the stable release.
$myUpdateChecker->setBranch('main');

//$myUpdateChecker->getVcsApi()->enableReleaseAssets();
 
 
//Optional: If you're using a private repository, specify the access token like this:
//$myUpdateChecker->setAuthentication('your-token-here');

/////////////////////

// Define constants.
define( 'LLMS_TXT_VERSION', '1.1.0' );
define( 'LLMS_TXT_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'LLMS_TXT_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
define( 'LLMS_TXT_PLUGIN_FILE', __FILE__ );

// Check for League\HTMLToMarkdown dependency.
$composer_autoload = LLMS_TXT_PLUGIN_DIR . 'vendor/autoload.php';
if ( ! class_exists( 'League\HTMLToMarkdown\HtmlConverter' ) && file_exists( $composer_autoload ) ) {
	require $composer_autoload;
}

// Load the core class only if dependencies are met.
if ( class_exists( 'League\HTMLToMarkdown\HtmlConverter' ) ) {
	require LLMS_TXT_PLUGIN_DIR . 'includes/class-llms-txt-core.php';
	// Initialize the plugin.
	new LLMS_Txt_Core();
} else {
	add_action( 'admin_notices', function() {
		?>
		<div class="notice notice-error is-dismissible">
			<p><?php esc_html_e( 'LLMs.txt for WP: The required League\HTMLToMarkdown library is missing. Please install dependencies via Composer.', 'wpproatoz-llms-txt-for-wp' ); ?></p>
		</div>
		<?php
	} );
}

?>