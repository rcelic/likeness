<?php
echo "<h4>Trying to update locations of Facebook users in our System</h4>";
$allFBUsers  = "SELECT userID,facebookUserID,facebookToken FROM userGeneral WHERE `facebookToken` != ?";
$allFBUsers .= 'LIMIT 10';
 if ($db->Query_count($allFBUsers,array('')) != 0) { // If the Facebook users really exists and their Token exists in database
    $allFBUsers = $db->Query($allFBUsers,array(''));
    $allFBUsers = $allFBUsers -> returnArray();
      foreach($allFBUsers as $oneFBUser){
        $user = $oneFBUser['facebookUserID'];
        echo "Updating locations for user with Facebook id of ".$user."<br/>";
        $facebook -> setAccessToken($oneFBUser['facebookToken']);
        // For getting the status of the user 
        $forstatuses = '/me/statuses?with=location&fields=place,updated_time&limit=500&date_format=U';
        try{
        $user_statuses = $facebook->api($forstatuses);
          foreach ($user_statuses['data'] as $tmp){  // Single user statuses
            if(empty($tmp['place']['name'])) { continue; }
            $chk_already = "SELECT `placeID` FROM `placeDetailsFB` WHERE `facebookCheckInID` = ?";
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
               if(array_key_exists('created_time',$tmp)) $createdAt = addslashes($tmp['created_time']);
               else $createdAt    =   addslashes($tmp['updated_time']);
                $facebookUserID    = $user;
                $type              = "status";

                $sql="INSERT INTO `placeDetailsFB` SET 
                    `placeName` = ?,`placeAddress` = ?,`placeLat`= ?,`placeLng` = ?,`placeCity` = ?,`placeState` = ?,
                    `placeCountry` = ?,`facebookPlaceID` = ?,`facebookCheckInID` = ?,`createdAt` = ?,`facebookUserID`= ?, `type`  = ?";

                $sqlArray = array($placeName,$placeAddress,$placeLat,$placeLng,$placeCity,$placeState,$placeCountry,$facebookPlaceID,
                                $facebookCheckInID,$createdAt,$facebookUserID,$type);
                $db->Query($sql,$sqlArray);
                unset($sqlArray);
            }
          } // single user statuses
        }
        catch(FacebookApiException $fbe){
            $full_exception = $fbe->getResult();
            $error_code = $full_exception['error']['code'];
            if($error_code == 190){
                $sqlUserDelete = "DELETE FROM `userGeneral` WHERE `facebookUserID` = ?";
                $db->Query($sqlUserDelete,array($user));
                $sqlFriendsDelete = "DELETE FROM `friends` WHERE `user_id` = ?";
                $db->Query($sqlFriendsDelete,array($user));
                echo "user with FBUID ".$user." have uninstalled the application so deleted.<br/>";
                continue;
            }
        }
        // Statuses of the user code ends here  
        
        // getting posts of the user code starts here
        
        try{
        $user_posts = $facebook->api('/me/posts?fields=id,object_id,place,updated_time&date_format=U','GET'); 

        foreach ($user_posts['data'] as $tmp){
            if(empty($tmp['place']) || $tmp['object_id'] == '') { continue; }
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
                if(array_key_exists('created_time',$tmp)) $createdAt = addslashes(strtotime($tmp['created_time']));
                else $createdAt  =    addslashes($tmp['updated_time']);
                $facebookUserID    = $user;
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
        }
        catch(FacebookApiException $fbe){
            $full_exception = $fbe->getResult();
            $error_code = $full_exception['error']['code'];
            if($error_code == 190){
                $sqlUserDelete = "DELETE FROM `userGeneral` WHERE `facebookUserID` = ?";
                $db->Query($sqlUserDelete,array($user));
                $sqlFriendsDelete = "DELETE FROM `friends` WHERE `user_id` = ?";
                $db->Query($sqlFriendsDelete,array($user));
                echo "user with FBUID ".$user." have uninstalled the application, so this user deleted from database.<br/>";
                continue;
            }
        }
        
        // getting posts of the user ends here
        // getting photos of user code starts here
        try{
        $user_photos = $facebook->api('/me/photos?fields=place,created_time&limit=500&date_format=U','GET'); 
        foreach ($user_photos['data'] as $tmp){
            if(empty($tmp['place']['name'])) { continue; }
            $chk_already = "SELECT `placeID` FROM `placeDetailsFB` WHERE `facebookCheckInID` = ?";
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
                if(array_key_exists('created_time',$tmp)) $createdAt = addslashes($tmp['created_time']);
                else $createdAt    =   addslashes($tmp['updated_time']);
                echo "createdAt = ".$createdAt."<br/>";
                $facebookUserID    = $user;
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
        }
        catch(FacebookApiException $fbe){
            $full_exception = $fbe->getResult();
            $error_code = $full_exception['error']['code'];
            if($error_code == 190){
                $sqlUserDelete = "DELETE FROM `userGeneral` WHERE `facebookUserID` = ?";
                $db->Query($sqlUserDelete,array($user));
                echo "user with FBUID ".$user." have uninstalled the application so this user deleted from database.<br/>";
                continue;
            }
        }
          // getting photos of user code ends here
      }

 }

?>
