<?php set_time_limit(0);

$facebookMessage	=	'<h3 style="color:red;">Maximum allowed number of calls to Facebook Api reached. Please try again after few moments.</h3>';
// Getting UID/checkins
if($usedToday == false || $toupdate != false){ // not used today by this user, so update friends data
echo "<p style='color:green;'>All Friends locations are being updated</p>";
$friends = array(); // Result array for friend records accumulation
$friends = $facebook->api("/me/friends", 'GET');
$total_friends	=	count($friends['data']);
$lucky_num = 70;
//echo "Number of total friends are ".$total_friends."<br/>";
//if($total_friends > 200 )
	$number_iteration = ceil($total_friends/$lucky_num);
	
	//echo "Number of iterations are ".$number_iteration."<br/>";
//$srno = 1;
//printArray($friends['data']);
//    printArray($arrayFriends,true);
for($i=1; $i<=$number_iteration; $i++){
	if($i == $number_iteration){
		$starting = ( $i - 1 ) * $lucky_num;
		$ending   = $total_friends;

	}
	else{
		$starting = ( $i - 1 ) * $lucky_num;
		$ending   = $i * $lucky_num;
	}
	
	//echo "starting is ".$starting." and ending is ".$ending."<br/><br/>";
for($j = $starting; $j < $ending; $j++){
	$currentFrnds[]	=	array('id' => $friends['data'][$j]['id'],  'name' => $friends['data'][$j]['name']);
}






//printArray($currentFrnds );
foreach($currentFrnds as $onefriend){
	
	//echo" one frnd is" .$onefriend."<br>";
   $since_time = '';
   $user_id = $user;
    $friend_id = $onefriend['id'];
    $friend_name = $onefriend['name'];
	//echo "Friend id is ".$friend_id." and friend name is ".$friend_name."<br/>";
 // echo "Starting limit is = ". $starting. "And Ending limit is =".$ending. "Friend name is". $friend_name = $onefriend['name']."<br>";
    
    $sqlFriends="SELECT * FROM `friends` WHERE `user_id` = ? AND `friend_id` = ?";
    
    if($db->Query_count($sqlFriends,array($user,$onefriend['id'])) == 0){
        $since_time = '&since='.$since_time;
        $since_time = '';
        $sqlInsertFriend = "INSERT INTO `friends` (user_id,friend_id,friend_name,last_updated) VALUES (?,?,?,?)";
        $db->Query($sqlInsertFriend,array($user_id,$friend_id,$friend_name,$since_time));
    }
    else{
        $rsFriends = $db->Query($sqlFriends,array($user,$onefriend['id']));
        $arrayFriend = $rsFriends->returnArray();
        $since_time = '&since='.$arrayFriend['0']['last_updated'];
    }

try{
    $forcheckins = '/'.$onefriend['id']."/checkins?date_format=U&fields=id,message,place".$since_time;
    $checkins = $facebook -> api($forcheckins);

if(!empty($checkins['data'])){
//    echo "<br/>.....<br/>";
    foreach($checkins['data'] as $onecheckin){
        //$created_time = date('d M Y',strtotime($onecheckin['created_time']));
      if(empty($onecheckin['place'])) { continue; }
//                echo "<p>".$onefriend['name']." was at <a target='_blank' href='http://fb.com/".$onecheckin['id']."'>".$onecheckin['place']['name']." <i>".$onecheckin['place']['street']." ".	$onecheckin['place']['city']." " .$onecheckin['place']['country']."</i></a> by ".$created_time."<p>";
      $chk_already = "SELECT `placeID` FROM `friendsplaceDetailsFB` WHERE `facebookCheckInID` = ?";
//      echo $chk_already."<br/>";
    if($db->Query_count($chk_already,array($onecheckin['id'])) == 0){ //  if this specific post didn't already exist
        $placeName         =   addslashes($onecheckin['place']['name']);
        $placeAddress      =   addslashes($onecheckin['place']['location']['street']);
        $placeLat          =   addslashes($onecheckin['place']['location']['latitude']);
        $placeLng          =   addslashes($onecheckin['place']['location']['longitude']);
        $placeCity         =   addslashes($onecheckin['place']['location']['city']);
        $placeState        =   addslashes($onecheckin['place']['location']['state']);
        $placeCountry      =   addslashes($onecheckin['place']['location']['country']);
        $facebookPlaceID   =   addslashes($onecheckin['place']['id']);
        $facebookCheckInID =   addslashes($onecheckin['id']);
        if(array_key_exists('created_time',$onecheckin)) $createdAt = addslashes($onecheckin['created_time']);
        else $createdAt  =    addslashes($onecheckin['updated_time']);

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
   // printArray($fbe,true);
  echo '<h3 style="color:red;">Maximum allowed number of calls to Facebook Api reached. Please try again after few moments.</h3>';
	die();
    continue;
    
    }

// Checkin ends here

try{
$foronefriend = '/'.$onefriend['id'].'/posts?with=location&date_format=U&fields=place,updated_time'.$since_time;
$friends_statuses = $facebook->api($foronefriend);
if(!empty($friends_statuses['data'])){
foreach ($friends_statuses['data'] as $onestatus){
   if(empty($onestatus['place'])) { continue; }
//            echo "<br/>.....<br/>";
            $spec_checkin_id = explode('_',$onestatus['id']);
            $spec_checkin_id = $spec_checkin_id['1'];
//                $placename = $onestatus['place']['name']." <i>".$onestatus['place']['street']." ".	$onestatus['place']['city']." " .$onestatus['place']['country'];
//            echo "<p>".$onefriend['name']." was at <a target='_blank' href='http://fb.com/".$spec_checkin_id."'>".$placename."</i></a> by ".$created_time."<p>";
    $chk_already = "SELECT `placeID` FROM `friendsplaceDetailsFB` WHERE `facebookCheckInID` = ?";
//                              echo $chk_already."<br/>";
if($db->Query_count($chk_already,array($spec_checkin_id)) == 0){ //  if this specific post didn't already exist
    $placeName         =   addslashes($onestatus['place']['name']);
    $placeAddress      =   addslashes($onestatus['place']['location']['street']);
    $placeLat          =   addslashes($onestatus['place']['location']['latitude']);
    $placeLng          =   addslashes($onestatus['place']['location']['longitude']);
    $placeCity         =   addslashes($onestatus['place']['location']['city']);
    $placeState        =   addslashes($onestatus['place']['location']['state']);
    $placeCountry      =   addslashes($onestatus['place']['location']['country']);
    $facebookPlaceID   =   addslashes($onestatus['place']['id']);
    $facebookCheckInID =   addslashes($spec_checkin_id);
    if(array_key_exists('created_time',$onestatus)) $createdAt = addslashes($onestatus['created_time']);
    else $createdAt    =   addslashes($onestatus['updated_time']);
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
    //printArray($fbe, true);
	echo '<h3 style="color:red;">Maximum allowed number of calls to Facebook Api reached. Please try again after few moments.</h3>';
	die();
    continue;
}
    $sqlInsertFriend = "UPDATE `friends` SET `last_updated` = ?,`friend_name` = ? WHERE `friend_id` = ?";
    $db->Query($sqlInsertFriend,array($nowtime,$onefriend['name'],$onefriend['id']));

	}
unset($currentFrnds);
}

} // not used today by this user
echo "<h2>My Friends's Places</h2>";
    $sqlFriendsPlaces="SELECT * FROM `friendsplaceDetailsFB` WHERE `facebookUserID` = ? ORDER BY `createdAt` DESC";
    $rsFriendsPlaces = $db->Query($sqlFriendsPlaces,array($user));
    $arrayFriendsPlaces = $rsFriendsPlaces->returnArray();

foreach ($arrayFriendsPlaces as $tmp){
    $created_time = date('d M Y',$tmp['createdAt']);
    $placename = stripslashes($tmp['placeName'])." <i>".stripslashes($tmp['placeAddress'])." ".stripslashes($tmp['placeCity'])." " .stripslashes($tmp['placeCountry']);
    echo "<p>".$tmp['friendUsername']." was at <a target='_blank' href='http://fb.com/".$tmp['facebookCheckinID']."'>".$placename."</i></a> on ".$created_time."<p>";
}

?>