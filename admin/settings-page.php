<?php

add_action('admin_menu', function() {
    add_menu_page(
        'Firepips SMTP',
        'Firepips SMTP',
        'manage_options',
        'firepips-smtp',
        'firepips_page'
    );
});

function firepips_page() {
?>
<div class="wrap">
    <h1>Firepips SMTP Settings</h1>

    <form method="post" action="options.php">
        <?php
        settings_fields('firepips_settings');
        do_settings_sections('firepips');
        submit_button();
        ?>
    </form>

    <h2>Send Test Email</h2>
    <form method="post" action="<?php echo admin_url('admin-post.php'); ?>">
        <input type="hidden" name="action" value="firepips_send_test">
        <input type="email" name="test_email" required placeholder="Enter email">
        <button class="button button-primary">Send</button>
    </form>
</div>
<?php
}

// REGISTER SETTINGS
add_action('admin_init', function() {

    register_setting('firepips_settings', 'firepips_smtp_host');
    register_setting('firepips_settings', 'firepips_smtp_port');
    register_setting('firepips_settings', 'firepips_smtp_user');
    register_setting('firepips_settings', 'firepips_smtp_pass');
    register_setting('firepips_settings', 'firepips_smtp_secure');
    register_setting('firepips_settings', 'firepips_smtp_from_email');
    register_setting('firepips_settings', 'firepips_smtp_from_name');

    add_settings_section('firepips_section', 'SMTP Configuration', null, 'firepips');

    function firepips_input($name, $type='text') {
        echo "<input type='$type' name='$name' value='".esc_attr(get_option($name))."' class='regular-text'>";
    }

    add_settings_field('host', 'SMTP Host', fn()=>firepips_input('firepips_smtp_host'), 'firepips', 'firepips_section');
    add_settings_field('port', 'SMTP Port', fn()=>firepips_input('firepips_smtp_port'), 'firepips', 'firepips_section');
    add_settings_field('user', 'Username', fn()=>firepips_input('firepips_smtp_user'), 'firepips', 'firepips_section');
    add_settings_field('pass', 'Password', fn()=>firepips_input('firepips_smtp_pass','password'), 'firepips', 'firepips_section');
});
