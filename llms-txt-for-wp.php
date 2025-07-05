<?php
/**
 * Plugin Name: LLMs.txt for WP
 * Plugin URI: https://github.com/WP-Autoplugin/llms-txt-for-wp
 * Description: Generates LLM-friendly content as llms.txt and provides markdown versions of posts.
 * Version: 1.0.0
 * Author: Balázs Piller
 * Author URI: https://wp-autoplugin.com
 * License: GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: llms-txt-for-wp
 * Domain Path: /languages
 *
 * @package LLMsTxtForWP
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
define( 'LLMS_TXT_VERSION', '1.0.0' );
define( 'LLMS_TXT_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'LLMS_TXT_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
define( 'LLMS_TXT_PLUGIN_FILE', __FILE__ );

// Include Composer autoloader if available.
if ( file_exists( LLMS_TXT_PLUGIN_DIR . 'vendor/autoload.php' ) ) {
	require LLMS_TXT_PLUGIN_DIR . 'vendor/autoload.php';
}

// Load the core class.
require LLMS_TXT_PLUGIN_DIR . 'includes/class-llms-txt-core.php';

// Initialize the plugin.
new LLMS_Txt_Core();
