<?php
class OA_404_Logger {
    public function __construct() {
        add_action( 'admin_menu', array( $this, 'add_log_page' ) );
        add_action( 'admin_init', array( $this, 'handle_clear_logs' ) );
    }

    public static function create_db_table() {
        global $wpdb;
        $table_name = $wpdb->prefix . 'oa_404_logs';
        $charset_collate = $wpdb->get_charset_collate();

        $sql = "CREATE TABLE $table_name (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            requested_url text NOT NULL,
            referrer text,
            hit_time datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
            PRIMARY KEY  (id)
        ) $charset_collate;";

        require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
        dbDelta( $sql );
    }

    public function log_hit() {
        global $wpdb;
        $wpdb->insert(
            $wpdb->prefix . 'oa_404_logs',
            array(
                'requested_url' => esc_url_raw( $_SERVER['REQUEST_URI'] ),
                'referrer'      => isset( $_SERVER['HTTP_REFERER'] ) ? esc_url_raw( $_SERVER['HTTP_REFERER'] ) : 'Direct Entry',
                'hit_time'      => current_time( 'mysql' )
            )
        );
    }

    /**
     * Deletes all entries from the custom log table.
     */
    public function handle_clear_logs() {
        if ( isset( $_POST['oa_clear_logs_submit'] ) && check_admin_referer( 'oa_clear_logs_action', 'oa_clear_logs_nonce' ) ) {
            if ( ! current_user_can( 'manage_options' ) ) {
                wp_die( 'Unauthorized access.' );
            }

            global $wpdb;
            $table_name = $wpdb->prefix . 'oa_404_logs';
            $wpdb->query( "TRUNCATE TABLE $table_name" );

            wp_redirect( admin_url( 'options-general.php?page=oa-404-logs&status=cleared' ) );
            exit;
        }
    }

    public function add_log_page() {
        add_options_page( '404 Logs', '404 Logs', 'manage_options', 'oa-404-logs', array( $this, 'render_log_table' ) );
    }

    public function render_log_table() {
        global $wpdb;
        $table_name = $wpdb->prefix . 'oa_404_logs';
        $logs = $wpdb->get_results( "SELECT * FROM $table_name ORDER BY hit_time DESC LIMIT 100" );
        
        echo '<div class="wrap"><h1>Opal & Ash 404 Logs</h1>';
        
        if ( isset( $_GET['status'] ) && $_GET['status'] === 'cleared' ) {
            echo '<div class="updated"><p>All logs have been cleared successfully.</p></div>';
        }

        if ( $logs ) {
            ?>
            <form method="post" style="margin-bottom: 20px;">
                <?php wp_nonce_field( 'oa_clear_logs_action', 'oa_clear_logs_nonce' ); ?>
                <input type="submit" name="oa_clear_logs_submit' class="button button-secondary" value="Clear All Logs" onclick="return confirm('Are you sure you want to delete all 404 records?');">
            </form>
            <?php
        }

        echo '<table class="wp-list-table widefat fixed striped"><thead><tr>';
        echo '<th>URL</th><th>Referrer Source</th><th>Date/Time</th></tr></thead><tbody>';
        
        if ( $logs ) {
            foreach ( $logs as $log ) {
                echo "<tr><td>" . esc_html( $log->requested_url ) . "</td><td>" . esc_html( $log->referrer ) . "</td><td>" . esc_html( $log->hit_time ) . "</td></tr>";
            }
        } else {
            echo '<tr><td colspan="3">No logs found.</td></tr>';
        }
        echo '</tbody></table></div>';
    }
}