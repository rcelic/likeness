<?php
include_once('config.php');
include_once('db.php');
$db = new db();

include_once "fbapi/facebook.php";
$facebook = new Facebook(array(
    'appId'  => $Appid,
    'secret' => $AppSecret
));
unset($data);
$placeProfile = 	"select facebookPlaceID from distinctPlaces where indexed='' limit 0,10";
					
	    			$rs = $db->Query($placeProfile,$data);
				    $array = $rs->returnArray();
				    
				    foreach ($array as $tmp){ 
						$placeDetail = $facebook->api('/'.$tmp['facebookPlaceID'].'','GET');
					//	print_r($placeDetail);
						$name  = $placeDetail['name'];

						echo $name."<hr>";
				    	

				    }

?>
