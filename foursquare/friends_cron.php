<?php set_time_limit(0);
echo "<h4>Now Trying to update locations of Friends for each user</h4>";
$checkFbToken   	=  	"SELECT * FROM `userGeneral` WHERE `facebookToken` != ?";
$countFbToken		=	$db->Query_count($checkFbToken,array(''));
$checkUserStatus  	= 	"SELECT * FROM `userGeneral` WHERE `update_status` = ? AND `facebookToken` != ?";
$countUserStatus	=	$db->Query_count($checkUserStatus,array('1',''));

if($countFbToken == $countUserStatus){
	$sqlUpdateUserStatus = "UPDATE `userGeneral` SET `update_status` = ? WHERE `facebookToken` != ?";
     $db->Query($sqlUpdateUserStatus,array('0' ,'')); 
 
}
	
$allFBUsers  = "SELECT userID,facebookUserID,facebookToken FROM userGeneral WHERE `facebookToken` != ? AND `update_status` = ?";
$allFBUsers .= 'LIMIT 2';
 if ($db->Query_count($allFBUsers,array('','0')) != 0) { // If the Facebook users really exists and their Token exists in database
    $allFBUsers = $db->Query($allFBUsers,array('','0')); // Fetach all FB users saved in Database
    $allFBUsers = $allFBUsers -> returnArray(); // Fetach all FB users saved in Database
	
    //printArray($allFBUsers,true);
      foreach($allFBUsers as $oneFBUser){ // loop through all existing Facebook users 
          $facebook -> setAccessToken($oneFBUser['facebookToken']);
          $user = $oneFBUser['facebookUserID'];
          echo "Getting checkins/posts for all friends of ".$user."<br/>";
          $sqloneUserFriends = "SELECT * FROM `friends` WHERE `user_id` = ?";
          if($db->Query_count($sqloneUserFriends,array($user)) != 0){ // if really some friends exists in database for this user
              $Friends = $db->Query($sqloneUserFriends,array($user));
              $Friends = $Friends -> returnArray();
			  
			 //printArray($Friends , true);
			   $total_friends	=	count($Friends);
	 	//	echo $total_friends;
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
				$currentFrnds[]	=	array('id' => $Friends[$j]['friend_id'],  'name' => $Friends[$j]['friend_name'] , 'last updated' => $Friends[$j]['last_updated']);
			}
			

			 
				//printArray($currentFrnds);
              foreach($currentFrnds as $friend){ // loop through all friends of every user
                  $since_time        = "&since=".$friend['last_updated'];
                  $onefriend_fbuid   = $friend['id'];
                  $onefriend_fbname  = stripslashes($friend['name']);
				  
				  //echo "Friend id is ".$onefriend_fbuid." and friend name is ".$onefriend_fbname."<br/>";
                  
                  // getting checkins of every friend of a user code starts here
                  $forcheckins = '/'.$onefriend_fbuid."/checkins?date_format=U&fields=id,message,place".$since_time;
                  try{
                    $checkins = $facebook -> api($forcheckins);
//                        echo "Getting checkins for ".$onefriend_fbuid.", he/she is friend of ".$user."<br/>";
                    if(!empty($checkins['data'])){
                        foreach($checkins['data'] as $onecheckin){
                        if(empty($onecheckin['place'])) { continue; }
                        $chk_already = "SELECT `placeID` FROM `friendsplaceDetailsFB` WHERE `facebookCheckInID` = ?";
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
                            $friendUserID      =   $onefriend_fbuid;
                            $friendUsername    =   addslashes($onefriend_fbname);
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
                   echo '<h3 style="color:red;">Maximum allowed number of calls to Facebook Api reached. Please try again after few moments.</h3>';
					die();
                }
                // getting checkins of every friend of a user code ends here
                
                // getting posts of every friend of a user code starts here 
                
                $foronefriend = '/'.$onefriend_fbuid.'/posts?with=location&date_format=U&fields=place,updated_time'.$since_time;
                try{
                    $friends_statuses = $facebook->api($foronefriend);
//                    echo "Getting posts for ".$onefriend_fbuid.", he/she is friend of ".$user."<br/>";
                    if(!empty($friends_statuses['data'])){
                    foreach ($friends_statuses['data'] as $onestatus){
                    if(empty($onestatus['place'])) { continue; }
                        $spec_checkin_id = explode('_',$onestatus['id']);
                        $spec_checkin_id = $spec_checkin_id['1'];
                        $chk_already = "SELECT `placeID` FROM `friendsplaceDetailsFB` WHERE `facebookCheckInID` = ?";
                    if($db->Query_count($chk_already,array($spec_checkin_id)) == 0){  //  if this specific post didn't already exist
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
                        
                        
                        $friendUserID      =   $onefriend_fbuid;
                        $friendUsername    =   addslashes($onefriend_fbname);
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
                       echo '<h3 style="color:red;">Maximum allowed number of calls to Facebook Api reached. Please try again after few moments.</h3>';
						die();
                    }
                    
                    $sqlInsertFriend = "UPDATE `friends` SET `last_updated` = ?,`friend_name` = ? WHERE `friend_id` = ?";
                    $db->Query($sqlInsertFriend,array($nowtime,$onefriend_fbname,$onefriend_fbuid));
					
                    // getting posts of every friend of a user code ends here
                
                
              			}// loop through all friends of a single user
						
				unset($currentFrnds);			
		  }
		  
          } // if really some friends exists in database for this user
		  $sqlUpdateUser = "UPDATE `userGeneral` SET `update_status` = ?,`last_updated_time` = ? WHERE `facebookUserID` = ?";
           $db->Query($sqlUpdateUser,array('1',$nowtime,$user));
      } // loop through all existing Facebook users 

 } // If the Facebook users really exists and their Token exists in database
?>