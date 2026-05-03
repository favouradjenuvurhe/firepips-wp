<?php

add_action('phpmailer_init', function($phpmailer) {

    $phpmailer->isSMTP();
    $phpmailer->Host = get_option('firepips_smtp_host');
    $phpmailer->SMTPAuth = true;
    $phpmailer->Port = get_option('firepips_smtp_port');
    $phpmailer->Username = get_option('firepips_smtp_user');
    $phpmailer->Password = get_option('firepips_smtp_pass');
    $phpmailer->SMTPSecure = get_option('firepips_smtp_secure');

    $phpmailer->From = get_option('firepips_smtp_from_email');
    $phpmailer->FromName = get_option('firepips_smtp_from_name');
});
