<?php
session_start();
require_once('Facebook/autoload.php');

$FBObject = new \Facebook\Facebook([
    'app_id' => '381792807958342',
    'app_secret' => '84c69d9ac3200a851436c995e9679d54',
    'default_graph_version' => 'v19.0'
]);

$handler = $FBObject -> getRedirectLoginHelper();
?>
