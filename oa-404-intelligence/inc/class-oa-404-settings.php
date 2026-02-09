<?php
class OA_404_Settings {
    public function __construct() {
        add_action( 'admin_menu', array( $this, 'add_menu' ) );
        add_action( 'admin_init', array( $this, 'register_settings' ) );
    }

    public function add_menu() {
        add_options_page( 'Opal & Ash Settings', 'Opal & Ash 404', 'manage_options', 'oa-404-settings', array( $this, 'render_page' ) );
    }

    public function register_settings() {
        register_setting( 'oa_404_settings_group', 'oa_404_headline', 'sanitize_text_field' );
        register_setting( 'oa_404_settings_group', 'oa_404_body', 'wp_kses_post' );
    }

    public function render_page() {
        ?>
        <div class="wrap">
            <h1>404 Intelligence Settings</h1>
            <form method="post" action="options.php">
                <?php settings_fields( 'oa_404_settings_group' ); ?>
                <table class="form-table">
                    <tr>
                        <th>Headline</th>
                        <td><input type="text" name="oa_404_headline" value="<?php echo esc_attr( get_option( 'oa_404_headline' ) ); ?>" class="regular-text" required /></td>
                    </tr>
                    <tr>
                        <th>Body Text</th>
                        <td><?php wp_editor( get_option( 'oa_404_body' ), 'oa_404_body', array( 'textarea_name' => 'oa_404_body' ) ); ?></td>
                    </tr>
                </table>
                <?php submit_button(); ?>
            </form>
        </div>
        <?php
    }
}