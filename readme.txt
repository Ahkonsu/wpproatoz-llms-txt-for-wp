=== LLMs.txt for WP ===
Contributors: ahkonsu
Tags: llms, ai, large language models, markdown, seo
Requires at least: 6.0
Tested up to: 6.6
Stable tag: 1.2.0
Requires PHP: 8.0
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Generate an AI-friendly llms.txt file for your WordPress site and enable Markdown versions of your posts and pages.

== Description ==

**LLMs.txt for WP** is a WordPress plugin that makes your site ready for Large Language Models (LLMs) like ChatGPT, Gemini, and Claude. It automatically generates an `llms.txt` file, a standardized Markdown file that aggregates key content in a machine-readable format, helping AI tools discover and understand your site. The plugin also supports generating Markdown versions of your posts and pages, accessible by appending `.md` to their URLs.

Inspired by the [llms.txt standard](https://llmstxt.org/), this plugin is perfect for bloggers, businesses, and developers who want to optimize their content for AI-driven search and discovery. It’s lightweight, customizable, and easy to set up.

= Features =
* **Generate llms.txt**: Create an `llms.txt` file with a selected page or posts from chosen post types and categories in a format optimized for LLMs.
* **Markdown Support**: Enable Markdown versions of your content by appending `.md` to post/page URLs (e.g., `https://example.com/your-post.md`).
* **Customizable Settings**: Choose a specific page, post types, or categories to include in `llms.txt` via an intuitive admin settings page.
* **Post Limit Control**: Set the maximum number of posts to include in `llms.txt` to keep the output manageable.
* **SEO-Friendly**: Improve your site’s visibility in AI-driven search by providing structured content.
* **Caching**: Uses transients to cache `llms.txt` content, reducing database queries for better performance.
* **Open Source**: Licensed under GPLv2, with contributions welcome on [GitHub](https://github.com/Ahkonsu/wpproatoz-llms-txt-for-wp).

= Dependencies =
This plugin requires the `league/html-to-markdown` library (version ^2.2.7) for Markdown conversion. It is loaded via Composer. Ensure Composer dependencies are installed by running `composer install` in the plugin directory.

= Why Use llms.txt? =
The `llms.txt` standard is like `robots.txt` for AI. It ensures that Large Language Models can easily access and interpret your content, improving discoverability in AI-powered tools and search engines. Whether you’re a content creator or a developer, this plugin helps you stay ahead in the era of AI.

== Installation ==

1. **Install Dependencies**:
   - Ensure Composer is installed on your server.
   - Navigate to the plugin directory and run `composer install` to install `league/html-to-markdown` (^2.2.7).
2. **Download the Plugin**:
   - Download the plugin ZIP file from the [GitHub releases page](https://github.com/Ahkonsu/wpproatoz-llms-txt-for-wp/releases) or the WordPress plugin repository.
3. **Upload and Install**:
   - In your WordPress admin dashboard, go to **Plugins > Add New > Upload Plugin**.
   - Upload the ZIP file and click **Install Now**.
4. **Activate the Plugin**:
   - After installation, click **Activate Plugin** from the Plugins menu.
5. **Configure Settings**:
   - Navigate to **Settings > LLMs.txt Settings** in the WordPress admin.
   - Select a specific page, post types, or categories to include in `llms.txt`.
   - Set the maximum number of posts to include.
   - Enable Markdown support if desired.
6. **Verify Output**:
   - Visit `https://yourdomain.com/llms.txt` to view the generated file.
   - If Markdown support is enabled, append `.md` to a post’s URL (e.g., `https://example.com/your-post.md`) to view its Markdown version.

== Frequently Asked Questions ==

= What is an llms.txt file? =
An `llms.txt` file is a Markdown file that provides structured content for Large Language Models (LLMs). It’s similar to `robots.txt` but designed for AI tools, helping them discover and understand your site’s content.

= How do I access the Markdown version of a post? =
Enable Markdown support in the plugin settings. Then, append `.md` to any post or page URL (e.g., `https://example.com/your-post.md`) to view its Markdown version.

= Can I choose which content appears in llms.txt? =
Yes! In the **Settings > LLMs.txt Settings** page, you can select a specific page, post types, categories, and set a maximum post limit.

= Can I filter posts by category in llms.txt? =
Yes! Select specific categories or choose "All Categories" in the settings. Specific categories will appear as separate sections with their descriptions alongside post types, while the Posts section will always include the latest posts regardless of category. Selecting a category will deselect "All Categories" to ensure your selections are saved. Applies to post types that support categories.

= Does this plugin affect my site’s SEO? =
While the plugin doesn’t directly modify traditional SEO, it optimizes your content for AI-driven search and discovery, which is increasingly important as AI tools like ChatGPT and Perplexity gain popularity.

= Is the plugin compatible with multisite installations? =
The current version is designed for single-site installations. Multisite support may be added in future releases.

= How can I contribute to the plugin? =
Contributions are welcome! Fork the repository on [GitHub](https://github.com/Ahkonsu/wpproatoz-llms-txt-for-wp), submit issues, or create pull requests.

= What if the League\HTMLToMarkdown library is missing? =
If the library is not installed, an admin notice will appear, and the plugin will disable Markdown functionality. Run `composer install` in the plugin directory to install it.

= What happens when I uninstall the plugin? =
Uninstalling the plugin removes the `llms_txt_settings` option, `llms_txt_cache` transient, and any other transients created by the plugin. No physical `llms.txt` file is created, but the plugin checks for and deletes it if present.

= What if llms.txt or .md URLs return 404 errors? =
This may indicate a rewrite rule conflict with another plugin or theme. Go to **Settings > Permalinks and click Save Changes to flush rewrite rules. If the issue persists, check for conflicting plugins/themes or contact support via the GitHub repository.

= How does caching work for llms.txt? =
The plugin caches the `llms.txt` content using a transient (`llms_txt_cache`) for 1 hour to reduce database queries. The cache is invalidated when settings are updated or relevant posts are modified.

== Screenshots ==

1. **Settings Page**: Configure which content to include in `llms.txt` and enable Markdown support.
2. **llms.txt Output**: View the generated `llms.txt` file at `https://yourdomain.com/llms.txt`.
3. **Markdown Output**: Access a post’s Markdown version by appending `.md` to its URL.

== Changelog ==
= 1.2.1 =
* by dewolfe001 added functionality to pick file and make more complete and link in file
* Adding a <link...> meta tag to link from the web page to the .md version of the page.
* Change to add wp_remote_get() if the markdown content is too thin.

= 1.2.0 =
* Added category filtering for posts in `llms.txt`, with separate sections for selected categories including their descriptions as h4 headings (Markdown mode) or plain text (non-Markdown mode).
* Modified `llms.txt` to include unfiltered Posts section with latest posts when specific categories are selected, alongside category sections.
* Fixed bug where selecting specific categories did not deselect "All Categories", ensuring category selections are saved.

= 1.1.2 =
* Fixed 404 errors for `.md` URLs of custom post types by using post slugs without post type prefixes.

= 1.1.1 =
* Fixed cache notice appearing prematurely after settings updates.

= 1.1.0 =
* Finalized plugin with all security improvements from assessment.
* Removed debug logging for production-ready release.

= 1.0.8 =
* Added transient caching for llms.txt content to improve performance.
* Implemented cache invalidation on settings and post changes.
* Added admin notice for cache issues.

= 1.0.7 =
* Added checks for rewrite rule conflicts with other plugins/themes.
* Improved activation/deactivation hooks to detect and notify about rule issues.

= 1.0.6 =
* Enhanced uninstall process to clean up settings and potential transients.
* Improved deactivation hook with logging for cleanup actions.
* Fixed invalid callback in parse_request hook.

= 1.0.5 =
* Improved error handling with detailed logging for content generation.
* Added admin notices for invalid settings and rewrite rule issues.
* Enhanced debug mode safety to prevent sensitive information exposure.

= 1.0.4 =
* Added version check and fallback for League\HTMLToMarkdown library.
* Improved security for third-party library usage.

= 1.0.3 =
* Added capability checks for llms.txt and Markdown content generation.
* Improved error handling for public requests.

= 1.0.2 =
* Improved error handling and input validation for public requests.
* Standardized text domain to `wpproatoz-llms-txt-for-wp`.
* Added deactivation and uninstall hooks for cleanup.

= 1.0.1 =
* Fixed invalid callback in template_redirect hooks.
* Improved settings sanitization for security.

= 1.0.0 =
* Initial release.
* Added functionality to generate `llms.txt` file.
* Added Markdown support for posts and pages.
* Implemented admin settings page for content customization.

== Upgrade Notice ==

= 1.2.0 =
Update adds category filtering with descriptions, unfiltered Posts section, and fixes category selection bug for llms.txt content. Ensure `league/html-to-markdown` (^2.2.7) is installed via Composer.

?>
