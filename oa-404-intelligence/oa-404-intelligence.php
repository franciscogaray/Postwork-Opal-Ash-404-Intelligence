<?php
/**
 * Plugin Name: Opal & Ash 404 Intelligence
 * Description: Transforms 404 pages into conversion tools with smart CTAs and error logging.
 * Version: 1.0.4
 * Author: Francisco Garay
 * Author URI: https://franciscogaray.me
 * Text Domain: oa-404-intel
 */

if ( ! defined( 'ABSPATH' ) ) exit;

define( 'OA_404_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );

require_once OA_404_PLUGIN_DIR . 'inc/class-oa-404-logger.php';
require_once OA_404_PLUGIN_DIR . 'inc/class-oa-404-settings.php';

class OA_404_Intelligence {
    public function __construct() {
        // Core Setup
        register_activation_hook( __FILE__, array( 'OA_404_Logger', 'create_db_table' ) );
        
        // Logging & Display
        add_action( 'template_redirect', array( $this, 'detect_and_log_404' ), 20 );
        add_shortcode( 'oa_404_content', array( $this, 'render_404_content' ) );
        add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_styles' ) );

        // Initialize Classes to register menus and settings
        new OA_404_Settings();
        new OA_404_Logger(); 
    }

    public function detect_and_log_404() {
        if ( is_404() ) {
            $logger = new OA_404_Logger();
            $logger->log_hit();
        }
    }

    public function enqueue_styles() {
        // Injecting the artisanal aesthetic directly for ease of use
        $custom_css = "
            .oa-404-container {
                padding: 40px;
                background: #fdfaf7;
                border: 1px solid #e0d7cf;
                border-radius: 8px;
                text-align: center;
                font-family: 'Georgia', serif;
                color: #4a443f;
                max-width: 600px;
                margin: 20px auto;
            }
            .oa-404-container h2 { color: #2c2926; font-size: 2rem; margin-bottom: 15px; }
            .oa-cta-box {
                margin-top: 25px;
                padding: 15px;
                background: #fff;
                border-left: 4px solid #c4a484;
                font-style: italic;
            }
        ";
        wp_add_inline_style( 'main-style', $custom_css ); // Assumes theme has a 'main-style' handle
    }

    public function render_404_content() {
        $headline = get_option( 'oa_404_headline', 'Our Workshop is Quiet...' );
        $body = get_option( 'oa_404_body', 'The piece you are looking for has been archived or moved.' );
        $url = $_SERVER['REQUEST_URI'];
        $cta = '';

        if ( stripos( $url, 'pen' ) !== false ) {
            $cta = '<div class="oa-cta-box">Missing a specific pen? Our small-batch drops sell out fast. <a href="/shop">Browse the Vault.</a></div>';
        }

        $cta = apply_filters( 'oa_404_filter_cta', $cta, $url );

        return '<div class="oa-404-container"><h2>' . esc_html( $headline ) . '</h2>' . wp_kses_post( $body ) . $cta . '</div>';
    }
}
new OA_404_Intelligence();