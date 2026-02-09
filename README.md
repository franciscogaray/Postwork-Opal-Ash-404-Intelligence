# Opal & Ash 404 Intelligence

## Overview
Opal & Ash 404 Intelligence is a lightweight, conversion-focused WordPress plugin designed for boutique DTC brands. It transforms standard 404 error pages into recovery tools by logging broken links and displaying contextual "Smart CTAs" based on the requested URL.

## Installation
1.  **Download**: Create a folder named `oa-404-intelligence` and include the plugin files.
2.  **Upload**: Upload the entire folder to your WordPress site's `/wp-content/plugins/` directory via FTP or the WordPress Admin dashboard.
3.  **Activate**: Navigate to the **Plugins** menu in the WordPress dashboard and click **Activate** on "Opal & Ash 404 Intelligence".
4.  **Database Setup**: Upon activation, the plugin automatically creates a custom database table (`wp_oa_404_logs`) to track error hits.
5.  **Configure**: Go to **Settings > Opal & Ash 404** to define your custom 404 headline and body text.

## Shortcodes
The plugin provides a primary shortcode to render the recovery content within your theme's 404 template:

* `[oa_404_content]`: Renders the admin-defined headline, body text, and the URL-based "Smart CTA." 
    * *Usage*: Place this inside a Gutenberg Shortcode block or call it in your PHP template using `<?php echo do_shortcode('[oa_404_content]'); ?>`.

## Developer Documentation: Hooks & Filters
To ensure the plugin is developer-friendly and extensible, the following hooks are available:

### Filters
* `oa_404_filter_cta`: Modify the contextual CTA programmatically.
    * **Arguments**: 
        * `$cta` (string): The HTML string for the CTA.
        * `$url` (string): The requested URL that triggered the 404.
    * **Example**:
        ```php
        add_filter('oa_404_filter_cta', function($cta, $url) {
            if (strpos($url, 'special-edition') !== false) {
                return '<p>Looking for a vault item? Sign up for our newsletter!</p>';
            }
            return $cta;
        }, 10, 2);
        ```

### Action Hooks
* `oa_404_log_entry`: Fires immediately after a 404 hit is logged to the database.
    * **Arguments**: 
        * `$log_data` (array): Contains `requested_url`, `referrer`, and `timestamp`.

## 404 Error Logging
You can view recorded errors by navigating to **Settings > 404 Logs**. This dashboard displays:
* **Requested URL**: The exact link the customer tried to visit.
* **Referrer**: Where the user came from (e.g., an expired social media post).
* **Timestamp**: The exact time of the error (synchronized with your WordPress timezone).
* **Clear Logs**: Use the "Clear All Logs" button to truncate the table after addressing broken links.
