<?php

require_once 'vendor/autoload.php';

// init configuration
$clientID = '419355197556-i50nniroktkdve0c5tu2c8sbasv33qir.apps.googleusercontent.com';
$clientSecret = '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';
$redirectUri = 'http://localhost:8080/ipt_laboratory5/googleindex.php';


// create Client Request to access Google API
$client = new Google_Client();
$client->setClientId($clientID);
$client->setClientSecret($clientSecret);
$client->setRedirectUri($redirectUri);
$client->addScope("email");
$client->addScope("profile");

