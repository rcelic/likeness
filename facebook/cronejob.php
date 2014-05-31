<?php
include_once('config.php');
include_once('db.php');
$db = new db();

include_once "fbapi/facebook.php";
$facebook = new Facebook(array(
    'appId'  => $Appid,
    'secret' => $AppSecret
));

include_once('users_cron.php');
include_once('friends_cron.php');
?>
