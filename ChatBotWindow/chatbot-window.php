<?php
/*
Plugin Name: ChatBotWindow
Description: ChatBot plugin using React frontend.
Version: 0.1
*/

if (!defined('ABSPATH')) exit;

function cbw_register_settings() {
    register_setting('cbw_options', 'cbw_settings');
    register_setting('cbw_options', 'cbw_tasks', 'cbw_sanitize_tasks');
}
add_action('admin_init', 'cbw_register_settings');

function cbw_add_admin_menu() {
    add_options_page('ChatBotWindow', 'ChatBotWindow', 'manage_options', 'cbw', 'cbw_options_page');
}
add_action('admin_menu', 'cbw_add_admin_menu');

function cbw_options_page() {
    $settings = get_option('cbw_settings');
    $tasks    = get_option('cbw_tasks', []);
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

            <h2>Tasks</h2>
            <table class="form-table" id="cbw-tasks-table">
                <tr>
                    <th>Task Name</th>
                    <th>HTML</th>
                </tr>
                <?php foreach ($tasks as $slug => $task) : ?>
                <tr>
                    <td><input type="text" name="cbw_tasks[<?php echo esc_attr($slug); ?>][name]" value="<?php echo esc_attr($task['name']); ?>" class="regular-text" /></td>
                    <td><textarea name="cbw_tasks[<?php echo esc_attr($slug); ?>][html]" rows="3" class="large-text code"><?php echo esc_textarea($task['html']); ?></textarea></td>
                </tr>
                <?php endforeach; ?>
                <tr class="cbw-empty-row">
                    <td><input type="text" name="cbw_tasks[new][name]" class="regular-text" /></td>
                    <td><textarea name="cbw_tasks[new][html]" rows="3" class="large-text code"><div class="cbw-row">
  <div class="cbw-col">Column 1</div>
  <div class="cbw-col">Column 2</div>
</div></textarea></td>
                </tr>
            </table>
            <p><button type="button" class="button" id="cbw-add-task">Add Task</button></p>
            <?php submit_button(); ?>
        </form>
        <?php if (!empty($tasks)) : ?>
        <h2>Shortcodes</h2>
        <p>
            <?php foreach ($tasks as $slug => $task) : ?>
                <code>[cbw_task id="<?php echo esc_html($slug); ?>"]</code><br />
            <?php endforeach; ?>
        </p>
        <?php endif; ?>
        <script>
        document.getElementById('cbw-add-task').addEventListener('click', function(){
            const table = document.getElementById('cbw-tasks-table');
            const clone = table.querySelector('.cbw-empty-row').cloneNode(true);
            table.appendChild(clone);
        });
        </script>
    </div>
    <?php
}

function cbw_register_assets() {
    $asset_url = plugin_dir_url(__FILE__) . 'assets/';
    wp_enqueue_style('cbw-style', $asset_url . 'app.css');
    wp_enqueue_script('cbw-main', $asset_url . 'app.js', [], null, true);
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

function cbw_sanitize_tasks($tasks) {
    $sanitized = [];
    if (!is_array($tasks)) return $sanitized;
    foreach ($tasks as $task) {
        if (empty($task['name'])) continue;
        $slug = sanitize_title($task['name']);
        $html = isset($task['html']) ? wp_kses_post($task['html']) : '';
        $sanitized[$slug] = [
            'name' => sanitize_text_field($task['name']),
            'html' => $html,
        ];
    }
    return $sanitized;
}

function cbw_task_shortcode($atts) {
    $atts = shortcode_atts(['id' => ''], $atts);
    $tasks = get_option('cbw_tasks', []);
    if ($atts['id'] && isset($tasks[$atts['id']])) {
        return $tasks[$atts['id']]['html'];
    }
    return '';
}
add_shortcode('cbw_task', 'cbw_task_shortcode');

