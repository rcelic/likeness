<?php

$db_Host  =    'localhost';
$db_User  =    'hexagr5_admin';
$db_Pass  =    'likemike12';
$db_Name  =    'hexagr5_likeness';

$Appid     = '470066449785975';

$AppSecret = '3bebedff45507709b4a2febee715e972';

$BaseUrl   = 'http://hexagrammbooks.com/moebii/likeness/facebook/index.php';

$mytime = date('d-M-Y h:i:s a');

$mytimzone =  - 0 ; // for testing on my server ( janbark.com )
$enhanced_date = strtotime($mytime . " ".$mytimzone." hours");
$nowtime = date('d-M-Y h:i:s a',$enhanced_date);
$nowtime = strtotime($nowtime);

$today_date = date('d-M-Y'); 

include_once('globaldatabase.php');


?>