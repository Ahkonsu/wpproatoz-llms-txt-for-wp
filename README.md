# LLMs.txt for WP

![Plugin Version](https://img.shields.io/badge/version-1.2.0-blue.svg) ![WordPress](https://img.shields.io/badge/WordPress-6.0%2B-blue.svg) ![PHP](https://img.shields.io/badge/PHP-8.0%2B-blue.svg) ![License](https://img.shields.io/badge/license-GPLv2-green.svg)

A WordPress plugin that generates an AI-friendly `llms.txt` file and Markdown versions of posts, optimizing your site for Large Language Models (LLMs).

---

## Overview

The **LLMs.txt for WP** plugin makes your WordPress site ready for Large Language Models (LLMs) like ChatGPT, Gemini, and Claude. Forked from the original [llms-txt-for-wp](https://github.com/WP-Autoplugin/llms-txt-for-wp) by Balázs Piller, this updated version adds robust security, performance enhancements, and modern WordPress compatibility. It generates an `llms.txt` file with structured, machine-readable content and supports Markdown versions of posts/pages via `.md` URLs.

Developed by [WPProAtoZ](https://wpproatoz.com), this plugin is perfect for enhancing AI-driven discoverability while maintaining performance and security.

---

## Features

- **Generate llms.txt**: Aggregates a selected page or posts from chosen post types and categories into a standardized Markdown file for LLMs.
- **Markdown Support**: Access Markdown versions of posts/pages by appending `.md` to URLs (e.g., `https://example.com/your-post.md`).
- **Customizable Settings**: Configure `llms.txt` content via an admin settings page, selecting a single page, post types, or categories.
- **Post Limit Control**: Set a maximum number of posts to include, ensuring efficient output.
- **Performance Optimized**: Caches `llms.txt` content using transients to reduce database queries.
- **Secure Design**: Includes input sanitization, capability checks, and rewrite rule conflict detection.
- **Lightweight**: Minimal dependencies (`league/html-to-markdown`) and optimized queries for smooth performance.
- **Open Source**: Licensed under GPLv2, with contributions welcome on [GitHub](https://github.com/Ahkonsu/wpproatoz-llms-txt-for-wp).

---

## Configuration

### Generating llms.txt
Access the generated file at `https://yourdomain.com/llms.txt`. Configure content via **Settings > LLMs.txt Settings** in the WordPress admin.

### Markdown Support
Enable Markdown support in settings, then append `.md` to any post/page URL (e.g., `https://example.com/your-post.md`) to view its Markdown version.

### Admin Settings
- **Selected Page for llms.txt**: Choose a specific page to include in `llms.txt`. If none, posts from selected post types and categories are used.
- **Post Types to Include**: Select post types (e.g., posts, pages, custom post types) for `llms.txt` and `.md` support.
- **Categories to Include**: Select specific categories or "All Categories" to include posts in `llms.txt` as separate sections with their descriptions. The Posts section always includes the latest posts regardless of category.
- **Posts Limit**: Set the maximum number of posts (default: 100) to include in `llms.txt`.
- **Enable *.md Support**: Toggle to allow Markdown versions of posts/pages.

### Requirements
- **WordPress**: 6.0 or higher
- **PHP**: 8.0 or higher (8.3+ recommended)
- **Dependencies**: `league/html-to-markdown` (^2.2.7), installed via Composer

---

## Changelog

### Version 1.2.0
- Added category filtering for posts in `llms.txt`, with separate sections for selected categories including their descriptions as h4 headings (Markdown mode) or plain text (non-Markdown mode).
- Modified `llms.txt` to include unfiltered Posts section with latest posts when specific categories are selected, alongside category sections.
- Fixed bug where selecting specific categories did not deselect "All Categories", ensuring category selections are saved.
- Fixed 404 errors for `.md` URLs of custom post types by using post slugs without post type prefixes.

### Version 1.1.1
- Fixed cache notice appearing prematurely after settings updates.

### Version 1.1.0
- Final production-ready release with all security improvements.
- Removed debug logging for production use.
- Fixed cache notice appearing prematurely after settings updates.

### Version 1.0.8
- Added transient caching for `llms.txt` content to improve performance.
- Implemented cache invalidation on settings and post changes.
- Added admin notice for cache issues.

### Version 1.0.7
- Added checks for rewrite rule conflicts with other plugins/themes.
- Improved activation/deactivation hooks to detect and notify about rule issues.

### Version 1.0.6
- Enhanced uninstall process to clean up settings and transients.
- Fixed invalid callback in `parse_request` hook.

### Version 1.0.5
- Improved error handling with detailed logging for content generation.
- Added admin notices for invalid settings and rewrite rule issues.
- Enhanced debug mode safety to prevent sensitive information exposure.

### Version 1.0.4
- Added version check and fallback for `league/html-to-markdown` library.
- Improved security for third-party library usage.

### Version 1.0.3
- Added capability checks for `llms.txt` and Markdown content generation.
- Improved error handling for public requests.

### Version 1.0.2
- Improved error handling and input validation for public requests.
- Standardized text domain to `wpproatoz-llms-txt-for-wp`.
- Added deactivation and uninstall hooks for cleanup.

### Version 1.0.1
- Fixed invalid callback in `template_redirect` hooks.
- Improved settings sanitization for security.

### Version 1.0.0
- Initial release with `llms.txt` generation and Markdown support.
- Added admin settings page for content customization.

---

## Testing
- **llms.txt Output**: Visit `https://yourdomain.com/llms.txt` and verify content includes the selected page or posts grouped by post types (e.g., `Posts`, `Pages`, `TestingThings`) and selected categories (e.g., `Interviews`) with their descriptions as h4 headings. Ensure the `Posts` section includes unfiltered posts when specific categories are selected. Confirm `.md` URLs use post slugs only (e.g., `wish-it-where-simple.md`).
- **Markdown Output**: Enable Markdown support, then append `.md` to a post/page URL (e.g., `https://yourdomain.com/wish-it-where-simple.md` for custom post types) and confirm correct Markdown formatting.
- **Settings**: Test all admin settings (page selection, post types, categories, limit, Markdown support). Verify selecting a specific category deselects "All Categories" and `llms.txt` reflects selected categories with descriptions.
- **Caching**: Update settings or posts, visit `llms.txt`, and confirm cache updates correctly. Check for admin notices on cache issues.
- **Uninstallation**: Delete the plugin and confirm `llms_txt_settings` and `llms_txt_cache` are removed from the database.
- **Rewrite Rules**: Activate the plugin and verify no 404 errors for `llms.txt` or `.md` URLs. Check for conflict notices if applicable.

---

## Installation

1. **Install Dependencies**:
   ```bash
   cd /path/to/plugin/wpproatoz-llms-txt-for-wp
   composer install
   ```
   This installs `league/html-to-markdown` (^2.2.7).
2. **Download**:
   - Get the latest release from [Releases](https://github.com/Ahkonsu/wpproatoz-llms-txt-for-wp/releases).
3. **Upload**:
   - In WordPress admin, go to `Plugins > Add New > Upload Plugin`, and upload the `wpproatoz-llms-txt-for-wp-1.2.0.zip` file.
4. **Activate**:
   - Activate via the `Plugins` menu.
5. **Configure**:
   - Go to `Settings > LLMs.txt Settings` to set up content and Markdown options.

Alternatively, clone the repository:
```bash
git clone https://github.com/Ahkonsu/wpproatoz-llms-txt-for-wp.git wpproatoz-llms-txt-for-wp
cd wpproatoz-llms-txt-for-wp
composer install
