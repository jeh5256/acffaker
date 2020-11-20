<?php
require_once __DIR__ . 'vendor/autoload.php';

use AcfFaker\AcfFakerCommands;

if (!class_exists('WP_CLI')) {
    return;
}

$instance = new AcfFakerCommands();
WP_CLI::add_command('acffake', $instance);