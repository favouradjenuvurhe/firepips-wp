<?php

add_filter('pre_set_site_transient_update_plugins', function ($transient) {

    if (empty($transient->checked)) return $transient;

    $plugin_file = 'firepips-wp/firepips-wp.php';

    if (!function_exists('get_plugin_data')) {
        require_once ABSPATH . 'wp-admin/includes/plugin.php';
    }

    $plugin_data = get_plugin_data(WP_PLUGIN_DIR . '/' . $plugin_file);
    $current_version = $plugin_data['Version'];

    // GitHub API (LATEST RELEASE)
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

            // stable release ZIP
            'package'     => 'https://github.com/favouradjenuvurhe/firepips-wp/releases/download/'
                . $data->tag_name . '/firepips-wp.zip',

            'url'    => $data->html_url,
            'tested' => '6.9.4'
        ];
    }

    return $transient;
}, 20);


// 🔥 VIEW DETAILS (FULL GITHUB POWERED)
add_filter('plugins_api', function ($res, $action, $args) {

    if ($action !== 'plugin_information') return $res;

    if (!isset($args->slug) || $args->slug !== 'firepips-wp') return $res;

    $response = wp_remote_get(
        'https://api.github.com/repos/favouradjenuvurhe/firepips-wp/releases/latest',
        [
            'headers' => [
                'User-Agent' => 'Firepips-WP-Updater'
            ],
            'timeout' => 20
        ]
    );

    if (is_wp_error($response)) return $res;

    $data = json_decode(wp_remote_retrieve_body($response));

    if (empty($data)) return $res;

    $release_body = $data->body ?? 'No release notes provided.';

    // OPTIONAL: convert markdown-like formatting
    $release_body_html = nl2br($release_body);

    $res = new stdClass();

    $res->name    = 'firepips';
    $res->slug    = 'firepips-wp';
    $res->version = ltrim($data->tag_name, 'v');
    $res->author  = '<a href="https://github.com/favouradjenuvurhe">Firepips</a>';
    $res->homepage = 'https://github.com/favouradjenuvurhe/firepips-wp';

    $res->download_link = 'https://github.com/favouradjenuvurhe/firepips-wp/releases/download/'
        . $data->tag_name . '/firepips-wp.zip';

    // 🔥 IMPORTANT: EVERYTHING FROM GITHUB
    $res->sections = [
        'description' => $release_body_html,
        'changelog'   => $release_body_html
    ];

    // ICONS
    $res->icons = [
        '1x' => 'https://raw.githubusercontent.com/favouradjenuvurhe/firepips-wp/assets/icon-128.png',
        '2x' => 'https://raw.githubusercontent.com/favouradjenuvurhe/firepips-wp/assets/icon-256.png'
    ];

    // BANNER
    $res->banners = [
        'low'  => 'https://raw.githubusercontent.com/favouradjenuvurhe/firepips-wp/assets/banner-772x250.png',
        'high' => 'https://raw.githubusercontent.com/favouradjenuvurhe/firepips-wp/assets/banner-1544x500.png'
    ];

    // WP compatibility
    $res->tested = '6.9.4';

    return $res;

}, 10, 3);
