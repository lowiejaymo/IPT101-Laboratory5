<?php
session_start();
require_once('Facebook/autoload.php');
require_once 'vendor/autoload.php';

// Load environment variables from .env
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

$FBObject = new \Facebook\Facebook([
    'app_id' => $_ENV['FACEBOOK_APP_ID'],
    'app_secret' => $_ENV['FB_APP_SECRET'],
    'default_graph_version' => 'v19.0'
]);

$handler = $FBObject -> getRedirectLoginHelper();
?>
