<?php
require_once("facebookconfig.php");

try{
    $accessToken = $handler->getAccessToken();
}catch(\Facebook\Exceptions\FacebookResponseException $e){
    echo "Response Exception: " . $e->getMessage();
    exit();
}catch(\Facebook\Exceptions\FacebookSDKException $e){
    echo "SDK Exception: " . $e->getMessage();
    exit();
}

if(!$accessToken){
    header('Location: login-v2.php');
    exit();
}$oAuth2Client = $FBObject->getOAuth2Client();
if(!$accessToken->isLongLived()) 
    $accessToken = $oAuth2Client->getLongLivedAccessToken($accessToken);

    $response = $FBObject->get("/me?fields=id, first_name, last_name, email, picture.type(large)", $accessToken);
    $uesrData = $response->getGraphNode()->asArray();
    $_SESSION['userData'] = $uesrData;
    $_SESSION['access_token'] = (string) $accessToken;
    header('Location: facebookindex.php');
    exit();
?>