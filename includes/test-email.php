<?php

add_action('admin_post_firepips_send_test', function() {

    if (!current_user_can('manage_options')) return;

    $to = sanitize_email($_POST['test_email']);

    wp_mail($to, 'Firepips SMTP Test', 'Your SMTP is working!');

    wp_redirect(admin_url('admin.php?page=firepips-smtp&sent=1'));
    exit;
});
