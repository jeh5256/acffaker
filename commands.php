<?php

namespace WP_CLI_Sage;

//require_once 'AcfFakerCommands.php';

use AcfCommand\AcfFakerCommands;

if (!class_exists('WP_CLI')) {
    return;
}

$instance = new AcfFakerCommands();
WP_CLI::add_command('acffake', $instance);