<?php
Include("db.php");
$db = new db();
//connect to the database 

ob_start();
require_once 'EpiCurl.php';
require_once 'EpiFoursquare.php';
$clientId = '0WCSKT1EIWUIB34U04YLVJAJCNHZDOVPOUZLTTMKBILNZRRJ';
$clientSecret = 'DHCEXUUVBFDPYU0YONXASXC4YRLT3O2DA3L0B1KGZVZRJQYE';
$code = 'BFVH1JK5404ZUCI4GUTHGPWO3BUIUTEG3V3TKQ0IHVRVGVHS';
$accessToken = 'DT32251AY1ED34V5ADCTNURTGSNHWXCNTOMTQM5ANJLBLO2O';
$redirectUri = 'http://hexagrammbooks.com/moebii/likeness/foursquare/index.php';
$fsObj = new EpiFoursquare($clientId, $clientSecret, $accessToken);
$fsObjUnAuth = new EpiFoursquare($clientId, $clientSecret);

function objectToArray($d) 
	{
		if (is_object($d)) {
			// Gets the properties of the given object
			// with get_object_vars function
			$d = get_object_vars($d);
		}
 
		if (is_array($d)) {
			/*
			* Return array converted to object
			* Using __FUNCTION__ (Magic constant)
			* for recursive call
			*/
			return array_map(__FUNCTION__, $d);
		}
		else {
			// Return array
			return $d;
		}
	}
?>
<?php
echo "<h4>Trying to add foursquare IDs to distinct facebook places</h4>";

unset($data);
$sqlRefresh="select distinct t1.facebookPlaceID, t1.foursquarePlaceID, t2.placeName, t2.facebookPlaceID, t2.placeLat, t2.placeLng from 
distinctPlaces as t1,
placeDetailsFB as t2 where 
t1.facebookPlaceID=t2.facebookPlaceID and t1.foursquarePlaceID ='' limit 0,100";


$rsRefresh = $db->Query($sqlRefresh,$data);
$arrayRefresh = $rsRefresh->returnArray();

		
	//echo "<p>refreshing for some reason</p>";
	
	foreach ($arrayRefresh as $tmp)
	{
	

    $venue = $fsObjUnAuth->get('/venues/search', 

	array(
	        'v' => '20140129',
	        'intent' => 'match',
	        'll' => ''.$tmp[4].','.$tmp[5].'',
	        'query' => ''.$tmp[2].''
	        )
	    ); 

    
	
	//print_r($venue->response); 
	
	$array = objectToArray($venue);
 
	// Print objects and array

	$placeID = $venue->response->venues[0]->id;
	$placeName = $venue->response->venues[0]->name;
	$facebookPlaceID = $tmp[0];
	echo $placeID."<br>";
	
	unset($data);
	if (empty($placeID)) {
  		echo "No ID Found";
		} else {
				 
			$sql="update distinctPlaces set foursquarePlaceID = '$placeID' where facebookPlaceID = $facebookPlaceID";
			
			echo $sql."<hr>";
			
			$rs = $db->Query($sql,$data);
    
		}
	}
	
	
	
    

?>
