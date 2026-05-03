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

    if (version_compare($current_version, $latest_version, '<')) {

        $update = new stdClass();
        $update->slug = 'firepips-wp';
        $update->plugin = $plugin_file;
        $update->new_version = $latest_version;

        // IMPORTANT: stable release ZIP
        $update->package = 'https://github.com/favouradjenuvurhe/firepips-wp/releases/download/' 
            . $data->tag_name . '/firepips-wp.zip';

        $update->url = $data->html_url;

        $transient->response[$plugin_file] = $update;
    }

    return $transient;
}, 10, 1);


// FORCE WordPress to SHOW update details properly
add_filter('plugins_api', function ($res, $action, $args) {

    if ($action !== 'plugin_information') return $res;

    if (!isset($args->slug) || $args->slug !== 'firepips-wp') return $res;

    $response = wp_remote_get('https://api.github.com/repos/favouradjenuvurhe/firepips-wp/releases/latest', [
        'headers' => [
            'User-Agent' => 'Firepips-WP-Updater'
        ],
        'timeout' => 15
    ]);

    if (is_wp_error($response)) return $res;

    $data = json_decode(wp_remote_retrieve_body($response));

    if (empty($data)) return $res;

    $res = new stdClass();
    $res->name = 'Firepips WP SMTP';
    $res->slug = 'firepips-wp';
    $res->version = ltrim($data->tag_name, 'v');
    $res->author = 'Firepips';
    $res->homepage = $data->html_url;
    $res->download_link = 'https://github.com/favouradjenuvurhe/firepips-wp/releases/download/' 
        . $data->tag_name . '/firepips-wp.zip';

    $res->sections = [
        'description' => 'SMTP plugin with logging and GitHub auto updates.',
        'changelog' => $data->body ?? 'No changelog available.'
    ];

    return $res;

}, 10, 3);
