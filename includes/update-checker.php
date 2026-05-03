<?php

require_once FIREPIPS_SMTP_PATH . 'plugin-update-checker/plugin-update-checker.php';

use YahnisElsts\PluginUpdateChecker\v5\PucFactory;

$updateChecker = PucFactory::buildUpdateChecker(
    'https://github.com/favouradjenuvurhe/firepips-wp/',
    __FILE__,
    'firepips-wp'
);

$updateChecker->setBranch('main');
