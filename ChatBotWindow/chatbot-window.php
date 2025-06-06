<?php
/*
Plugin Name: ChatBotWindow
Description: ChatBot plugin using React frontend.
Version: 0.1
*/

if (!defined('ABSPATH')) exit;

function cbw_register_settings() {
    register_setting('cbw_options', 'cbw_settings');
}
add_action('admin_init', 'cbw_register_settings');

function cbw_add_admin_menu() {
    add_options_page('ChatBotWindow', 'ChatBotWindow', 'manage_options', 'cbw', 'cbw_options_page');
}
add_action('admin_menu', 'cbw_add_admin_menu');

function cbw_options_page() {
    $settings = get_option('cbw_settings');
    ?>
    <div class="wrap">
        <h1>ChatBotWindow Settings</h1>
        <form method="post" action="options.php">
            <?php settings_fields('cbw_options'); ?>
            <table class="form-table" role="presentation">
                <tr><th scope="row"><label for="cbw_api_url">API URL</label></th>
                    <td><input type="text" id="cbw_api_url" name="cbw_settings[api_url]" value="<?php echo esc_attr($settings['api_url'] ?? ''); ?>" class="regular-text" /></td></tr>
                <tr><th scope="row"><label for="cbw_api_key">API Key</label></th>
                    <td><input type="text" id="cbw_api_key" name="cbw_settings[api_key]" value="<?php echo esc_attr($settings['api_key'] ?? ''); ?>" class="regular-text" /></td></tr>
            </table>
            <?php submit_button(); ?>
        </form>
    </div>
    <?php
}

function cbw_register_assets() {
    $asset_url = plugin_dir_url(__FILE__) . 'assets/';
    wp_enqueue_style('cbw-style', $asset_url . 'static/css/main.css');
    wp_enqueue_script('cbw-runtime', $asset_url . 'static/js/runtime-main.js', [], null, true);
    wp_enqueue_script('cbw-main', $asset_url . 'static/js/main.js', ['cbw-runtime'], null, true);
}
add_action('wp_enqueue_scripts', 'cbw_register_assets');

add_action('rest_api_init', function () {
    register_rest_route('chatbot/v1', '/query', [
        'methods' => 'POST',
        'callback' => 'cbw_query_handler',
    ]);
});

function cbw_query_handler($request) {
    $settings = get_option('cbw_settings');
    $api_url = $settings['api_url'];
    $api_key = $settings['api_key'];

    $response = wp_remote_post($api_url, [
        'headers' => [
            'Content-Type' => 'application/json',
            'X-API-KEY' => $api_key,
        ],
        'body' => json_encode(['query' => $request->get_json_params()['query'] ?? ''])
    ]);

    if (is_wp_error($response)) {
        return new WP_REST_Response(['error' => 'APIリクエストに失敗しました'], 500);
    }

    return json_decode(wp_remote_retrieve_body($response), true);
}

