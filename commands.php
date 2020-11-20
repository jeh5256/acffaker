<?php
require_once 'vendor/autoload.php';

use AcfFaker\AcfFakerCommands;

if (!class_exists('WP_CLI')) {
    return;
}
WP_CLI::success(__DIR__ );
//$instance = new AcfFakerCommands();
//WP_CLI::add_command('acffake', $instance);