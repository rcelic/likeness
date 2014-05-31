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
echo "<h4>Trying to update locations of Foursquare users in our system</h4>";
unset($data);
$sqlRefresh="select userID,foursquareUserID,foursquareToken,foursquareLastRefresh from userGeneral where foursquareToken != ?";
$data[] = '';
$rsRefresh = $db->Query($sqlRefresh,$data);
$arrayRefresh = $rsRefresh->returnArray();
	
if($arrayRefresh)
    {
		
    	//echo "<p>refreshing for some reason</p>";
		
		foreach ($arrayRefresh as $tmp)
		{
    	
    	$foursquareUserID = $tmp['1'];
    	$fsObjUnAuth->setAccessToken($_COOKIE['access_token']);
        $checkins = $fsObjUnAuth->get('/users/'.$tmp[1].'/checkins', 
        								array('afterTimestamp' => $tmp[3],
        										'oauth_token' => $tmp[2]
        								));
        $array = objectToArray($checkins->response);
        foreach ($array['checkins']['items'] as $tmp)
		   {
			   	$createdAt = $tmp['createdAt'];
			   	$foursquareCheckInID = $tmp['id'];
				$foursquareID = $tmp['venue']['id'];
				$placeName = $tmp['venue']['name'];
				$placeAddress = $tmp['venue']['location']['address'];
				$placeLat = $tmp['venue']['location']['lat'];
				$placeLng = $tmp['venue']['location']['lng'];
				$placeCity = $tmp['venue']['location']['city'];
				$placeState = $tmp['venue']['location']['state'];
				$placeCountry = $tmp['venue']['location']['country'];
				$placeURL = $tmp['venue']['url'];
				$placeType = $tmp['venue']['categories'][0]['name'];
				$foursquareLike = $tmp['venue']['like'];
				
				echo $placeName."<hr>";
				unset($data);
				 
				$sql="INSERT INTO placeDetailsFSQ(placeName, placeAddress, placeLat, placeLng, placeCity, placeState, placeCountry, foursquareID, placeURL, placeType, foursquareLike, foursquareCheckInID, createdAt, foursquareUserID) 
				VALUES(?,?,?,?,?,?,?,?,?,?,?,?,?,?)";
				
				$data[] = $placeName;
				$data[] = $placeAddress;
				$data[] = $placeLat;
				$data[] = $placeLng;
				$data[] = $placeCity;
				$data[] = $placeState;
				$data[] = $placeCountry;
				$data[] = $foursquareID;
				$data[] = $placeURL;
				$data[] = $placeType;
				$data[] = $foursquareLike;
				$data[] = $foursquareCheckInID;
				$data[] = $createdAt;
				$data[] = $foursquareUserID;
				
				$rs = $db->Query($sql,$data);
				
			}
			unset($data);
		$sqlRefreshUpdate = "update userGeneral set foursquareLastRefresh = ?  where foursquareUserID = ?";
    	
    	$data[] = time();
    	$data[] = $foursquareUserID;
    	
    	$rsRefreshUpdate = $db->Query($sqlRefreshUpdate,$data);	
    	echo "<h4>check in's refreshed.  <a href='../index.php'>link other accounts</a></h4>";
		}
	
    }

?>
