<?php

// SUCCESS LOG
add_action('wp_mail_succeeded', function($mail_data) {
    global $wpdb;

    $wpdb->insert($wpdb->prefix.'firepips_email_logs', [
        'email_to' => maybe_serialize($mail_data['to']),
        'subject' => $mail_data['subject'],
        'message' => $mail_data['message'],
        'status' => 'sent',
        'error' => ''
    ]);
});

// FAILED LOG
add_action('wp_mail_failed', function($wp_error) {
    global $wpdb;

    $data = $wp_error->get_error_data();

    $wpdb->insert($wpdb->prefix.'firepips_email_logs', [
        'email_to' => maybe_serialize($data['to'] ?? ''),
        'subject' => $data['subject'] ?? '',
        'message' => $data['message'] ?? '',
        'status' => 'failed',
        'error' => $wp_error->get_error_message()
    ]);
});
