<?php 
include_once("../../db.php");
$db = new db();

//find distinct places from friends database
$distinctPlaces = 	"select distinct(friendsplaceDetailsFB.facebookPlaceID)
				   	from friendsplaceDetailsFB
			   		left outer join distinctPlaces on distinctPlaces.facebookPlaceID=friendsplaceDetailsFB.facebookPlaceID
				    where distinctPlaces.facebookPlaceID is null";
	
	    			$rs = $db->Query($distinctPlaces,$data);
				    $array = $rs->returnArray();
				    foreach ($array as $tmp){ 
				    	
				    	unset($data);
			            $query_insert = "insert into distinctPlaces (facebookPlaceID) values (?)";
			            $data[] = $tmp['facebookPlaceID'];
			            
			            $rs2 = $db->Query($query_insert,$data);
			            $array = $rs2->returnArray();
			            foreach ($array as $tmp2){ }
			            
						echo $tmp['facebookPlaceID']."<hr>";
				    	}

//find distinct places from user database
$distinctPlaces = 	"select distinct(placeDetailsFB.facebookPlaceID)
				   	from placeDetailsFB
			   		left outer join distinctPlaces on distinctPlaces.facebookPlaceID=placeDetailsFB.facebookPlaceID
				    where distinctPlaces.facebookPlaceID is null";
	
	    			$rs = $db->Query($distinctPlaces,$data);
				    $array = $rs->returnArray();
				    foreach ($array as $tmp){ 
				    	
				    	unset($data);
			            $query_insert = "insert into distinctPlaces (facebookPlaceID) values (?)";
			            $data[] = $tmp['facebookPlaceID'];
			            
			            $rs2 = $db->Query($query_insert,$data);
			            $array = $rs2->returnArray();
			            foreach ($array as $tmp2){ }
			            
						echo $tmp['facebookPlaceID']."<hr>";
				    	}

?>
