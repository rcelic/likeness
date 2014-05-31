<?php
session_start();
$ses_id = session_id(); 
set_time_limit(0);
include_once("db.php");
include_once('config.php');
$db = new db();

include_once "fbmain.php";
?>
<!DOCTYPE html>
<html>
  <head>
    <meta charset='utf-8'>
    <meta http-equiv="X-UA-Compatible" content="chrome=1">
    <link href='https://fonts.googleapis.com/css?family=Chivo:900' rel='stylesheet' type='text/css'>
    <link rel="stylesheet" type="text/css" href="http://shalinguyen.github.io/socialicious/example/bootstrap.css" media="screen" />
    <link rel="stylesheet" type="text/css" href="http://shalinguyen.github.io/socialicious/example/styles.css" media="screen" />
    <link rel="stylesheet" type="text/css" href="http://shalinguyen.github.io/socialicious/css/socialicious.css" media="screen" />
    <!--[if lt IE 9]>
    <script src="//html5shiv.googlecode.com/svn/trunk/html5.js"></script>
    <![endif]-->
    <title>likeness - social media aggregation</title>
  </head>
<body>
<div id="container">
<div class="inner">
<h4><a href="<?php echo $BaseUrl?>">Refresh this page</a></h4>
<header><h1>Facebook posts & check ins</h1></header>
<div id="fb-root"></div>
    <script type="text/javascript" src="http://connect.facebook.net/en_US/all.js"></script>
     <script type="text/javascript">
       FB.init({
         appId  : '<?php echo $Appid; ?>',
         status : true, // check login status
         cookie : true, // enable cookies to allow the server to access the session
         xfbml  : true  // parse XFBML
       });
       
     </script>
     

    <?php if (!$user) { ?>
        <h4><a href="<?php echo $loginUrl?>">Facebook Login</a></h4>
    <?php } else { ?>
        <h4><a href="<?php echo $logoutUrl?>">Facebook Logout</a></h4>
    <?php } ?>

    <!-- all time check if user session is valid or not -->
<?php
if($firsttimeuser){
    echo "<h4>Facebook checkins/statuses have been added.  <a href='../likeness/index.php'>link other accounts</a></h4>";
}
if ($user){  // if really a Facebook user exists
$user_statuses = $facebook->api('/me/statuses?with=location&fields=place,updated_time&limit=500&date_format=U','GET');
foreach ($user_statuses['data'] as $tmp){
    if(empty($tmp['place']['name'])) { continue; }
    $chk_already = "SELECT `placeID` FROM `placeDetailsFB` WHERE `facebookCheckInID` = ?";
//     echo $chk_already."<br/>";
    if($db->Query_count($chk_already,array($tmp['id'])) == 0){ //  if this specific post didn't already exist
        $placeName         = addslashes($tmp['place']['name']);
        $placeAddress      = addslashes($tmp['place']['location']['street']);
        $placeLat          = addslashes($tmp['place']['location']['latitude']);
        $placeLng          = addslashes($tmp['place']['location']['longitude']);
        $placeCity         = addslashes($tmp['place']['location']['city']);
        $placeState        = addslashes($tmp['place']['location']['state']);
        $placeCountry      = addslashes($tmp['place']['location']['country']);
        $facebookPlaceID   = addslashes($tmp['place']['id']);
        $facebookCheckInID = addslashes($tmp['id']);
        $createdAt         = addslashes($tmp['updated_time']);
        $facebookUserID    = $user;
        $likenessID        = $user;
        $type              = "status";

        $sql="INSERT INTO `placeDetailsFB` SET 
            `placeName` = ?,`placeAddress` = ?,`placeLat`= ?,`placeLng` = ?,`placeCity` = ?,`placeState` = ?,
            `placeCountry` = ?,`facebookPlaceID` = ?,`facebookCheckInID` = ?,`createdAt` = ?,`facebookUserID`= ?, `type`  = ?";
        
        $sqlArray = array($placeName,$placeAddress,$placeLat,$placeLng,$placeCity,$placeState,$placeCountry,$facebookPlaceID,
                          $facebookCheckInID,$createdAt,$facebookUserID,$type);
        
        $db->Query($sql,$sqlArray);
        unset($sqlArray);
    }
}	

$user_posts = $facebook->api('/me/posts?fields=id,object_id,place,updated_time','GET'); 
foreach ($user_posts['data'] as $tmp){
    if(empty($tmp['object_id'])) { continue; }
    $chk_already = "SELECT `placeID` FROM `placeDetailsFB` WHERE `facebookCheckInID` = ?";
//                              echo $chk_already."<br/>";
    if($db->Query_count($chk_already,array($tmp['object_id'])) == 0){ //  if this specific post didn't already exist
        $placeName         = addslashes($tmp['place']['name']);
        $placeAddress      = addslashes($tmp['place']['location']['street']);
        $placeLat          = addslashes($tmp['place']['location']['latitude']);
        $placeLng          = addslashes($tmp['place']['location']['longitude']);
        $placeCity         = addslashes($tmp['place']['location']['city']);
        $placeState        = addslashes($tmp['place']['location']['state']);
        $placeCountry      = addslashes($tmp['place']['location']['country']);
        $facebookPlaceID   = addslashes($tmp['place']['id']);
        $facebookCheckInID = addslashes($tmp['object_id']);
        $createdAt         = addslashes($tmp['updated_time']);
        $facebookUserID    = $user;
        $likenessID        = $user;
        $type              = "status";

        $sql="INSERT INTO `placeDetailsFB` SET 
            `placeName` = ?,`placeAddress` = ?,`placeLat`= ?,`placeLng` = ?,`placeCity` = ?,`placeState` = ?,
            `placeCountry` = ?,`facebookPlaceID` = ?,`facebookCheckInID` = ?,`createdAt` = ?,`facebookUserID`= ?, `type`  = ?";
        
        $sqlArray = array($placeName,$placeAddress,$placeLat,$placeLng,$placeCity,$placeState,$placeCountry,$facebookPlaceID,
                          $facebookCheckInID,$createdAt,$facebookUserID,$type);
        
        $db->Query($sql,$sqlArray);
        unset($sqlArray);
    }
}	


$user_photos = $facebook->api('/me/photos?fields=place,created_time&limit=500&date_format=U','GET'); 
echo "<p><h4>Total tagged photos of yours are ".count($user_photos['data'])."</h4></p>";
foreach ($user_photos['data'] as $tmp){
    if(empty($tmp['place']['name'])) { continue; }
    $chk_already = "SELECT `placeID` FROM `placeDetailsFB` WHERE `facebookCheckInID` = ?";
//                              echo $chk_already."<br/>";
    if($db->Query_count($chk_already,array($tmp['id'])) == 0){ //  if this specific post didn't already exist
        $placeName         = addslashes($tmp['place']['name']);
        $placeAddress      = addslashes($tmp['place']['location']['street']);
        $placeLat          = addslashes($tmp['place']['location']['latitude']);
        $placeLng          = addslashes($tmp['place']['location']['longitude']);
        $placeCity         = addslashes($tmp['place']['location']['city']);
        $placeState        = addslashes($tmp['place']['location']['state']);
        $placeCountry      = addslashes($tmp['place']['location']['country']);
        $facebookPlaceID   = addslashes($tmp['place']['id']);
        $facebookCheckInID = addslashes($tmp['id']);
        $createdAt         = addslashes($tmp['updated_time']);
        $facebookUserID    = $user;
        $likenessID        = $user;
        $type              = "status";

        $sql="INSERT INTO `placeDetailsFB` SET 
            `placeName` = ?,`placeAddress` = ?,`placeLat`= ?,`placeLng` = ?,`placeCity` = ?,`placeState` = ?,
            `placeCountry` = ?,`facebookPlaceID` = ?,`facebookCheckInID` = ?,`createdAt` = ?,`facebookUserID`= ?, `type`  = ?";
        
        $sqlArray = array($placeName,$placeAddress,$placeLat,$placeLng,$placeCity,$placeState,$placeCountry,$facebookPlaceID,
                          $facebookCheckInID,$createdAt,$facebookUserID,$type);
        
        $db->Query($sql,$sqlArray);
        unset($sqlArray);
   } //  if this specific post didn't already exist
}  // foreach on user photos
            

$sqlPlaces="SELECT * FROM `placeDetailsFB` WHERE `facebookUserID` = ? ORDER BY `createdAt` DESC";
    $rsPlaces = $db->Query($sqlPlaces,array($user));
    $arrayPlaces = $rsPlaces->returnArray();
echo "<h2>My Places</h2>";
foreach ($arrayPlaces as $tmp){
    echo "<p><a target='_blank' href='http://fb.com/".$tmp['facebookCheckinID']."'>".$tmp['placeName']." <i>".$tmp['placeCity']." ".$tmp['placeState']."</i></a><p>";
}


//include_once('friends_checkins.php');

}// if really a Facebook user exists


// Update last login date
$sqlLastLoginDate = "UPDATE `userGeneral` SET `LastLoginDate` = ? WHERE `facebookUserID` = ? ";
$db->Query($sqlLastLoginDate,array($mytime,$user));
?>
                
    </div>

    </body>
</html>
<?php

$friends = array(); // Result array for friend records accumulation
$friends = $facebook->api("/me/friends", 'GET',array('limit'  => 500 ));
echo "<h2>My Friends's Places</h2>";
//$srno = 1;

//    printArray($arrayFriends,true);

foreach($friends['data'] as $onefriend){
   $since_time = '';
   $user_id = $user;
   $friend_id = $onefriend['id'];
    
    $sqlFriends="SELECT * FROM `friends` WHERE `user_id` = ? AND `friend_id` = ?";
    $rsFriends = $db->Query($sqlFriends,array($user,$onefriend['id']));
    $arrayFriend = $rsFriends->returnArray();
    if(empty($arrayFriend)){
        $since_time = '&since='.$since_time;
        $since_time = '';
        $sqlInsertFriend = "INSERT INTO `friends` (user_id,friend_id,last_updated) VALUES (?,?,?)";
        $db->Query($sqlInsertFriend,array($user_id,$friend_id,$since_time));
    }
    else{
        $since_time = '';
        $since_time = '&since='.$arrayFriend['0']['last_updated'];
        $sqlUpdateFriend = "UPDATE `friends` SET `last_updated` = ? WHERE `friend_id` = ?";
        $db->Query($sqlUpdateFriend,array($since_time,$friend_id));
    }

try{
    $forcheckins = '/'.$onefriend['id']."/checkins?fields=id,message,place".$since_time;
    $checkins = $facebook -> api($forcheckins);

if(!empty($checkins['data'])){
//    echo "<br/>.....<br/>";
    foreach($checkins['data'] as $onecheckin){
        //$created_time = date('d M Y',strtotime($onecheckin['created_time']));
      if(empty($onecheckin['place'])) { continue; }
//                echo "<p>".$onefriend['name']." was at <a target='_blank' href='http://fb.com/".$onecheckin['id']."'>".$onecheckin['place']['name']." <i>".$onecheckin['place']['street']." ".	$onecheckin['place']['city']." " .$onecheckin['place']['country']."</i></a> by ".$created_time."<p>";
      $chk_already = "SELECT `placeID` FROM `friendsplaceDetailsFB` WHERE `facebookCheckInID` = ?";
//      echo $chk_already."<br/>";
    if($db->Query_count($chk_already,array($onefriend['id'])) == 0){ //  if this specific post didn't already exist
        $placeName         =   addslashes($onecheckin['place']['name']);
        $placeAddress      =   addslashes($onecheckin['place']['location']['street']);
        $placeLat          =   addslashes($onecheckin['place']['location']['latitude']);
        $placeLng          =   addslashes($onecheckin['place']['location']['longitude']);
        $placeCity         =   addslashes($onecheckin['place']['location']['city']);
        $placeState        =   addslashes($onecheckin['place']['location']['state']);
        $placeCountry      =   addslashes($onecheckin['place']['location']['country']);
        $facebookPlaceID   =   addslashes($onecheckin['place']['id']);
        $facebookCheckInID =   addslashes($onecheckin['id']);
        if(array_key_exists('created_time',$onecheckin)) $createdAt = addslashes(strtotime($onecheckin['created_time']));
        else $createdAt  =    addslashes(strtotime($onecheckin['updated_time']));

        $facebookUserID    =   $user;
        $friendUserID      =   $onefriend['id'];
        $friendUsername    =   addslashes($onefriend['name']);
        $type              =   "status";

        $sql="INSERT INTO `friendsplaceDetailsFB` SET 
            `placeName`    = ?,`placeAddress`    = ?, `placeLat`  = ?,`placeLng` = ?,`placeCity` = ?,`placeState` = ?,
            `placeCountry` = ?,`facebookPlaceID` = ?, `facebookCheckInID` = ?,`createdAt` = ?,`facebookUserID`= ?,
            `friendUserID` = ?,`friendUsername`  = ?, `type`  = ?";
    
    $sqlArray = array($placeName,$placeAddress,$placeLat,$placeLng,$placeCity,$placeState,$placeCountry,$facebookPlaceID,
                          $facebookCheckInID,$createdAt,$facebookUserID,$friendUserID,$friendUsername,$type);
    $db->Query($sql,$sqlArray);
    unset($sqlArray);
    }
  }
}
	
}
catch(FacebookApiException $fbe){printArray($fbe);}

// Checkin ends here

try{
//$foronefriend = '/'.$onefriend['id'].'/posts?with=location&fields=place,updated_time&access_token='.$access_token;
$foronefriend = '/'.$onefriend['id'].'/posts?with=location&fields=place,updated_time'.$since_time;
$friends_statuses = $facebook->api($foronefriend);
if(!empty($friends_statuses['data'])){
foreach ($friends_statuses['data'] as $onestatus){
   if(empty($onestatus['place'])) { continue; }
//            echo "<br/>.....<br/>";
            $spec_checkin_id = explode('_',$onestatus['id']);
            $spec_checkin_id = $spec_checkin_id['1'];
//                $placename = $onestatus['place']['name']." <i>".$onestatus['place']['street']." ".	$onestatus['place']['city']." " .$onestatus['place']['country'];
//            echo "<p>".$onefriend['name']." was at <a target='_blank' href='http://fb.com/".$spec_checkin_id."'>".$placename."</i></a> by ".$created_time."<p>";
    $chk_already = "SELECT `placeID` FROM `friendsplaceDetailsFB` WHERE `facebookCheckInID` = ".$spec_checkin_id;
//                              echo $chk_already."<br/>";
if(runquery_count($chk_already) == 0){ //  if this specific post didn't already exist
    $placeName         =   addslashes($onestatus['place']['name']);
    $placeAddress      =   addslashes($onestatus['place']['location']['street']);
    $placeLat          =   addslashes($onestatus['place']['location']['latitude']);
    $placeLng          =   addslashes($onestatus['place']['location']['longitude']);
    $placeCity         =   addslashes($onestatus['place']['location']['city']);
    $placeState        =   addslashes($onestatus['place']['location']['state']);
    $placeCountry      =   addslashes($onestatus['place']['location']['country']);
    $facebookPlaceID   =   addslashes($onestatus['place']['id']);
    $facebookCheckInID =   addslashes($spec_checkin_id);
    if(array_key_exists('created_time',$onestatus)) $createdAt = addslashes(strtotime($onestatus['created_time']));
    else $createdAt    =   addslashes(strtotime($onestatus['updated_time']));
    $createdAt         =   addslashes(strtotime($onestatus['created_time']));
    $facebookUserID    =   $user;
    $friendUserID      =   $onefriend['id'];
    $friendUsername    =   addslashes($onefriend['name']);
    $type              =   "status";

    $sql="INSERT INTO `friendsplaceDetailsFB` SET 
            `placeName`    = ?,`placeAddress`    = ?, `placeLat`  = ?,`placeLng` = ?,`placeCity` = ?,`placeState` = ?,
            `placeCountry` = ?,`facebookPlaceID` = ?, `facebookCheckInID` = ?,`createdAt` = ?,`facebookUserID`= ?,
            `friendUserID` = ?,`friendUsername`  = ?, `type`  = ?";
    
    $sqlArray = array($placeName,$placeAddress,$placeLat,$placeLng,$placeCity,$placeState,$placeCountry,$facebookPlaceID,
                          $facebookCheckInID,$createdAt,$facebookUserID,$friendUserID,$friendUsername,$type);
    $db->Query($sql,$sqlArray);
    unset($sqlArray);
}
}
}
}
catch(FacebookApiException $fbe){
    printArray($fbe);
}
    $sqlInsertFriend = "UPDATE `friends` SET `last_updated` = ? WHERE `friend_id` = ?";
    $db->Query($sqlInsertFriend,array($nowtime,$onefriend['id']));

}


    $sqlFriendsPlaces="SELECT * FROM `friendsplaceDetailsFB` WHERE `facebookUserID` = ? ORDER BY `createdAt` DESC";
    $rsFriendsPlaces = $db->Query($sqlFriendsPlaces,array($user));
    $arrayFriendsPlaces = $rsFriendsPlaces->returnArray();

foreach ($arrayFriendsPlaces as $tmp){
    $created_time = date('d M Y',$tmp['createdAt']);
    $placename = $tmp['placeName']." <i>".$tmp['placeAddress']." ".$tmp['placeCity']." " .$tmp['placeCountry'];
    echo "<p>".$tmp['friendUsername']." was at <a target='_blank' href='http://fb.com/".$tmp['facebookCheckinID']."'>".$placename."</i></a> by ".$created_time."<p>";
    echo "<br/>";
}

?>