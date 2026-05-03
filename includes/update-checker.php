<?php

add_filter('pre_set_site_transient_update_plugins', function ($transient) {

    if (empty($transient->checked)) return $transient;

    $plugin_file = 'firepips-wp/firepips-wp.php';

    if (!function_exists('get_plugin_data')) {
        require_once ABSPATH . 'wp-admin/includes/plugin.php';
    }

    $plugin_data = get_plugin_data(WP_PLUGIN_DIR . '/' . $plugin_file);
    $current_version = $plugin_data['Version'];

    // GitHub API
    $response = wp_remote_get(
        'https://api.github.com/repos/favouradjenuvurhe/firepips-wp/releases/latest',
        [
            'headers' => [
                'User-Agent' => 'Firepips-WP-Updater'
            ],
            'timeout' => 20
        ]
    );

    if (is_wp_error($response)) return $transient;

    $data = json_decode(wp_remote_retrieve_body($response));

    if (empty($data->tag_name)) return $transient;

    $latest_version = ltrim($data->tag_name, 'v');

    if (version_compare($current_version, $latest_version, '<')) {

        $transient->response[$plugin_file] = (object) [
            'slug'        => 'firepips-wp',
            'plugin'      => $plugin_file,
            'new_version' => $latest_version,
            'package'     => 'https://github.com/favouradjenuvurhe/firepips-wp/releases/download/'
                . $data->tag_name . '/firepips-wp.zip',
            'url'         => $data->html_url
        ];
    }

    return $transient;
}, 20);


// FORCE WORDPRESS UPDATE CACHE REFRESH
add_action('admin_init', function () {
    delete_site_transient('update_plugins');
});
