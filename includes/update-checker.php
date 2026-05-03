<?php

add_filter('pre_set_site_transient_update_plugins', function($transient) {

    if (empty($transient->checked)) return $transient;

    $plugin_slug = 'firepips-wp';
    $plugin_file = 'firepips-wp/firepips-wp.php';
    $current_version = '1.1.0';

    // GitHub API URL
    $response = wp_remote_get('https://api.github.com/repos/favouradjenuvurhe/firepips-wp/releases/latest');

    if (is_wp_error($response)) return $transient;

    $data = json_decode(wp_remote_retrieve_body($response));

    if (!$data || empty($data->tag_name)) return $transient;

    $latest_version = ltrim($data->tag_name, 'v');

    // Compare versions
    if (version_compare($current_version, $latest_version, '<')) {

        $plugin = new stdClass();
        $plugin->slug = $plugin_slug;
        $plugin->plugin = $plugin_file;
        $plugin->new_version = $latest_version;
        $plugin->url = $data->html_url;
        $plugin->package = $data->zipball_url;

        $transient->response[$plugin_file] = $plugin;
    }

    return $transient;

});
