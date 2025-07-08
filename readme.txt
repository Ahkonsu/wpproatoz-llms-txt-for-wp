=== LLMs.txt for WP ===
Contributors: ahkonsu
Tags: llms, ai, large language models, markdown, seo
Requires at least: 5.0
Tested up to: 6.6
Stable tag: 1.0.0
Requires PHP: 8.0
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Generate an AI-friendly llms.txt file for your WordPress site and enable Markdown versions of your posts and pages.

== Description ==

**LLMs.txt for WP** is a WordPress plugin that makes your site ready for Large Language Models (LLMs) like ChatGPT, Gemini, and Claude. It automatically generates an `llms.txt` file, a standardized Markdown file that aggregates key content in a machine-readable format, helping AI tools discover and understand your site. The plugin also supports generating Markdown versions of your posts and pages, accessible by appending `.md` to their URLs.

Inspired by the [llms.txt standard](https://llmstxt.org/), this plugin is perfect for bloggers, businesses, and developers who want to optimize their content for AI-driven search and discovery. It’s lightweight, customizable, and easy to set up.

= Features =
* **Generate llms.txt**: Create an `llms.txt` file with selected posts, pages, or custom post types in a format optimized for LLMs.
* **Markdown Support**: Enable Markdown versions of your content by appending `.md` to post/page URLs (e.g., `https://example.com/your-post.md`).
* **Customizable Settings**: Choose which content to include in `llms.txt` via an intuitive admin settings page.
* **Post Limit Control**: Set the maximum number of posts to include in `llms.txt` to keep the file manageable.
* **SEO-Friendly**: Improve your site’s visibility in AI-driven search by providing structured content.
* **Open Source**: Licensed under GPLv2, with contributions welcome on [GitHub](https://github.com/Ahkonsu/wpproatoz-llms-txt-for-wp).

= Why Use llms.txt? =
The `llms.txt` standard is like `robots.txt` for AI. It ensures that Large Language Models can easily access and interpret your content, improving discoverability in AI-powered tools and search engines. Whether you’re a content creator or a developer, this plugin helps you stay ahead in the era of AI.

== Installation ==

1. **Download the Plugin**:
   - Download the plugin ZIP file from the [GitHub releases page](https://github.com/Ahkonsu/wpproatoz-llms-txt-for-wp/releases) or the WordPress plugin repository.
2. **Upload and Install**:
   - In your WordPress admin dashboard, go to **Plugins > Add New > Upload Plugin**.
   - Upload the ZIP file and click **Install Now**.
3. **Activate the Plugin**:
   - After installation, click **Activate Plugin** from the Plugins menu.
4. **Configure Settings**:
   - Navigate to **Settings > LLMs.txt Settings** in the WordPress admin.
   - Select the post types (e.g., posts, pages) or specific posts/pages to include in `llms.txt`.
   - Set the maximum number of posts to include.
   - Enable Markdown support if desired.
5. **Verify Output**:
   - Visit `https://yourdomain.com/llms.txt` to view the generated file.
   - If Markdown support is enabled, append `.md` to a post’s URL (e.g., `https://example.com/your-post.md`) to view its Markdown version.

== Frequently Asked Questions ==

= What is an llms.txt file? =
An `llms.txt` file is a Markdown file that provides structured content for Large Language Models (LLMs). It’s similar to `robots.txt` but designed for AI tools, helping them discover and understand your site’s content.

= How do I access the Markdown version of a post? =
Enable Markdown support in the plugin settings. Then, append `.md` to any post or page URL (e.g., `https://example.com/your-post.md`) to view its Markdown version.

= Can I choose which content appears in llms.txt? =
Yes! In the **Settings > LLMs.txt Settings** page, you can select specific post types, individual posts/pages, and set a maximum post limit.

= Does this plugin affect my site’s SEO? =
While the plugin doesn’t directly modify traditional SEO, it optimizes your content for AI-driven search and discovery, which is increasingly important as AI tools like ChatGPT and Perplexity gain popularity.

= Is the plugin compatible with multisite installations? =
The current version is designed for single-site installations. Multisite support may be added in future releases.

= How can I contribute to the plugin? =
Contributions are welcome! Fork the repository on [GitHub](https://github.com/Ahkonsu/wpproatoz-llms-txt-for-wp), submit issues, or create pull requests.

== Screenshots ==

1. **Settings Page**: Configure which content to include in `llms.txt` and enable Markdown support.
2. **llms.txt Output**: View the generated `llms.txt` file at `https://yourdomain.com/llms.txt`.
3. **Markdown Output**: Access a post’s Markdown version by appending `.md` to its URL.

== Changelog ==

= 1.0.0 =
* Initial release.
* Added functionality to generate `llms.txt` file.
* Added Markdown support for posts and pages.
* Implemented admin settings page for content customization.

== Upgrade Notice ==

= 1.0.0 =
Initial release of LLMs.txt for WP. Install and configure to make your site AI-friendly!