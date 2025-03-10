<?php

require_once('../vendor/autoload.php');

/**
 * The directory for the templates.
 */
const TEMPLATE_DIR = __DIR__ . '/../templates';

// Load the environment variables if they are not set.
if (!isset($_ENV['DB_HOST'])) {
    $dotenv = new Symfony\Component\Dotenv\Dotenv();
    $dotenv->load(__DIR__ . '/../../../.env');
}

$session = new Hive\Session();
$database = new Hive\Database();

$app = new Hive\App($session, $database);
$app->handle();
