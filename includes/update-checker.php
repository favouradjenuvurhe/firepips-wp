<?php

add_filter('pre_set_site_transient_update_plugins', function ($transient) {

    if (empty($transient->checked)) return $transient;

    $plugin_file = 'firepips-wp/firepips-wp.php';

    // Get installed version safely
    if (!function_exists('get_plugin_data')) {
        require_once ABSPATH . 'wp-admin/includes/plugin.php';
    }

    $plugin_data = get_plugin_data(WP_PLUGIN_DIR . '/' . $plugin_file);
    $current_version = $plugin_data['Version'];

    // GitHub API
    $response = wp_remote_get('https://api.github.com/repos/favouradjenuvurhe/firepips-wp/releases/latest', [
        'headers' => [
            'User-Agent' => 'Firepips-WP-Updater'
        ],
        'timeout' => 15
    ]);

    if (is_wp_error($response)) return $transient;

    $data = json_decode(wp_remote_retrieve_body($response));

    if (empty($data->tag_name)) return $transient;

    $latest_version = ltrim($data->tag_name, 'v');

    // Compare versions
    if (version_compare($current_version, $latest_version, '<')) {

        $update = new stdClass();
        $update->slug = 'firepips-wp';
        $update->plugin = $plugin_file;
        $update->new_version = $latest_version;

        // IMPORTANT: use your own ZIP (NOT zipball_url)
        $update->package = 'https://github.com/favouradjenuvurhe/firepips-wp/releases/download/' 
            . $data->tag_name . '/firepips-wp.zip';

        $update->url = $data->html_url;

        $transient->response[$plugin_file] = $update;
    }

    return $transient;
});
