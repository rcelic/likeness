<?php
session_start();
$ses_id = session_id(); 
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
<!DOCTYPE html>
<html lang="en">
  <head>
    <!-- META DATA -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimum-scale=1.0, maximum-scale=1.0">
    <meta name="description" content="Treble theme">
    <title>likeness - social media aggregation</title>

    <link rel="apple-touch-icon-precomposed" sizes="144x144" href="../../assets/images/logo1.png">
    <link rel="apple-touch-icon-precomposed" sizes="114x114" href="../../assets/images/logo1.png">
      <link rel="apple-touch-icon-precomposed" sizes="72x72" href="../../assets/images/logo1.png">
                    <link rel="apple-touch-icon-precomposed" href="../../assets/images/logo1.png">
                                   <link rel="shortcut icon" href="../../assets/images/logo1.png">


    <!-- TREBLE STYLESHEETS -->
    <link rel="stylesheet" href="../assets/style/bootstrap.css" type="text/css" media="all" />
    
    <!-- TREBLE DOCUMENTATION -->
    <link href="../assets/style/docs.css" rel="stylesheet">
    <link href="../../assets/style/socialButtons.css" rel="stylesheet">
    <link href="../assets/style/prettify.css" rel="stylesheet">
    <link href="//netdna.bootstrapcdn.com/font-awesome/4.1.0/css/font-awesome.min.css" rel="stylesheet">
    
    <!-- GOOGLE WEB FONTS -->
    <link href='http://fonts.googleapis.com/css?family=Open+Sans:300italic,400italic,600italic,700italic,800italic,400,700,600,300,800' rel='stylesheet' type='text/css'>
</head>
<body>

    <!-- NAVIGATION -->
    <nav class="fixed-top fixed-visable" id="navigation">
        <div class="container">
            <div class="row-fluid">
                <div class="span12 center">
                    <!-- LOGO -->
                    <a style="text-decoration: none" class="brand pull-left" href="../index.php">
                        <img src="../../assets/images/logo1.png" width="35px" height="35px" alt="Treble"> <strong>LIKENESS</strong>
                    </a>
                    <!-- END LOGO -->

                    <!-- MOBILE MENU BUTTON -->
                    <div class="mobile-menu" data-toggle="collapse" data-target=".nav-collapse">
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                    </div>
                    <!-- END MOBILE MENU BUTTON -->
                    
                    <!-- MAIN MENU -->
                    <ul id="main-menu" class="nav-collapse collapse">
                        <li><a href="../thesis.php">The Idea</a></li>
                        <li><a href="../accounts.php">My Accounts</a></li>
                        <li><a href="../places.php">My Places</a></li>
                        
                    </ul>
                    <!-- END MAIN MENU -->
                </div>
            </div>
        </div>
    </nav>
    <!-- END NAVIGATION -->
    <header class="jumbotron subhead" id="overview">
      <div class="container">
        <h1>Foursquare</h1>
      </div>
    </header>


  <div class="container">

    <!-- Docs nav
    ================================================== -->
    <div class="row">
      <div class="span10">

 	<section id="Introduction">
    	<div class="page-header">
    		<h5>My check ins</h5>
	
	        <!-- Introduction
	        ================================================== -->
	        <?php if(!isset($_GET['code']) && !isset($_COOKIE['access_token'])) { ?>
			<h2>Link Your Foursquare Account</h2>
			<?php $authorizeUrl = $fsObjUnAuth->getAuthorizeUrl($redirectUri); ?>
			<a href="<?php echo $authorizeUrl; ?>" class="btn btn-foursquare"><i class="fa fa-foursquare"></i> | Connect with Foursquare</a>
			
			<?php } else { ?>
			        
			        <?php
			        if(!isset($_COOKIE['access_token'])) {	
			                $token = $fsObjUnAuth->getAccessToken($_GET['code'], $redirectUri);
			                setcookie('access_token', $token->access_token);
			                $_COOKIE['access_token'] = $token->access_token;
			        }
			        
			        $fsObjUnAuth->setAccessToken($_COOKIE['access_token']);
			        $userInfo = $fsObjUnAuth->get('/users/self');
			        
			        $arrayUser = objectToArray($userInfo->response);
			        
			        //grab Foursquare ID
			        $foursquareUserID = $arrayUser['user']['id'];
			        
					unset($data);
					$sqlUser="select foursquareUserID from userGeneral where foursquareUserID = ?";
					$data[] = $foursquareUserID;
					$rsUser = $db->Query($sqlUser,$data);
					$arrayUser = $rsUser->returnArray();
					if(empty($arrayUser))
					    {
					    	//no foursquare user id stored, check to see if a session has already been started and is linked to another account
					    	unset($data);
					    	$sqlCurrentSession = "select currentSessionID from userGeneral where currentSessionID = ?";
					    	$data[] = $ses_id;
					    	$rsCurrentSession = $db->Query($sqlCurrentSession,$data);
					    	$arrayCurrentSession = $rsCurrentSession->returnArray();
					    	
					    	// if no session, no Foursquare ID or any other linked account, insert FSQ id, session id, and foursquare` into DB as a new user
					    	if(empty($arrayCurrentSession))
						    		{
						    			//echo "<p>And no session identified</p>";
							    		unset($data);
								    	$sqlUserInsert = "INSERT INTO userGeneral (currentSessionID, foursquareUserID, foursquareLastRefresh, foursquareToken) VALUES (?,?,?,?)";
								    	$data[] = $ses_id;
								    	$data[] = $foursquareUserID;
								    	$data[] = 0;
								    	$data[] = $_COOKIE['access_token'];
								    	$rsUserInsert = $db->Query($sqlUserInsert,$data);
								    	
								    	
								//echo "<p>cooking with gas</p>";
								$fsObjUnAuth->setAccessToken($_COOKIE['access_token']);
						        $checkins = $fsObjUnAuth->get('/users/self/checkins');
						        
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
						    	$sqlRefreshUpdate = "update userGeneral set foursquareLastRefresh = ?, currentSessionID = ? where foursquareUserID = ?";
						    	$data[] = time();
						    	$data[] = $ses_id;
						    	$data[] = $foursquareUserID;
						    	
						    	$rsRefreshUpdate = $db->Query($sqlRefreshUpdate,$data);	
						    	echo "<h4>Foursquare checkins have been added.  <a href='../index.php'>link other accounts</a></h4>";
							    	
						    	//now find recent checkins from friends
					    		unset($data);
								$fsObjUnAuth->setAccessToken($_COOKIE['access_token']);
						        $checkins = $fsObjUnAuth->get('/checkins/recent');
						        //print_r($checkins);
						        $array = objectToArray($checkins->response);
						        foreach ($array['recent'] as $tmp)
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
									
										unset($data);
										 
										echo "<p>".$placeName."</p>";
										
									}
					    		
					    		}
					    		// otherwise update current session ID to exisiting 
					    		else{
					    			unset($data);
							    	$sqlFoursquareUpdate = "update userGeneral set foursquareUserID = ?, foursquareLastRefresh = ?, foursquareToken = ? where currentSessionID = ? ";
							    	
							    	$data[] = $foursquareUserID;
							    	$data[] = 0;
							    	$data[] = $ses_id;
							    	$data[] = $_COOKIE['access_token'];
							    	
							    	$rsFoursquareUpdate = $db->Query($sqlFoursquareUpdate,$data);	
							    	
							    	
								//echo "<p>cooking with gas</p>";
								$fsObjUnAuth->setAccessToken($_COOKIE['access_token']);
						        $checkins = $fsObjUnAuth->get('/users/self/checkins');
						        
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
						    	$sqlRefreshUpdate = "update userGeneral set foursquareLastRefresh = ?, foursquareToken = ? where foursquareUserID = ?";
						    	
						    	$data[] = time();
						    	$data[] = $_COOKIE['access_token'];
						    	$data[] = $foursquareUserID;
						    	
						    	$rsRefreshUpdate = $db->Query($sqlRefreshUpdate,$data);	
						    	echo "<h4>Foursquare checkins have been added.  <a href='../index.php'>link other accounts</a></h4>";
					    		}
					    }
					    else {
			        
			        	//echo "<p>Time to find last refresh</p>";
			        	unset($data);
						$sqlRefresh="select foursquareLastRefresh from userGeneral where foursquareUserID = ?";
						$data[] = $foursquareUserID;
						$rsRefresh = $db->Query($sqlRefresh,$data);
						$arrayRefresh = $rsRefresh->returnArray();
							
						if($arrayRefresh)
						    {
								
						    	//echo "<p>refreshing for some reason</p>";
								
								foreach ($arrayRefresh as $tmp)
								{
						    	
						    	$fsObjUnAuth->setAccessToken($_COOKIE['access_token']);
						        $checkins = $fsObjUnAuth->get('/users/self/checkins', array('afterTimestamp' => $tmp[0]));
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
								$sqlRefreshUpdate = "update userGeneral set foursquareLastRefresh = ?, foursquareToken = ?, currentSessionID = ? where foursquareUserID = ?";
						    	
						    	$data[] = time();
						    	$data[] = $_COOKIE['access_token'];
						    	$data[] = $ses_id;
						    	$data[] = $foursquareUserID;
						    	
						    	$rsRefreshUpdate = $db->Query($sqlRefreshUpdate,$data);	
						    	echo "<h4>check in's refreshed.  <a href='../index.php'>link other accounts</a></h4>";
								}
						    }
					    }
						    unset($data);
			    $sqlPlaces="select * from placeDetailsFSQ where foursquareUserID = ? order by createdAt desc ";
				$data[] = $foursquareUserID;
				$rsPlaces = $db->Query($sqlPlaces,$data);
				$arrayPlaces = $rsPlaces->returnArray();
		      	echo "<h2>My Places</h2>";
		      	foreach ($arrayPlaces as $tmp)
				   {
				   	echo "<p>".$tmp['placeName'].", <i>".$tmp['placeCity']." ".$tmp['placeState']."</i></p>";
				   }
			        
			       } ?>
			       <hr>
	            <div>
	    </div>



    <!-- all time check if user session is valid or not -->

        </section>

        
      </div>
    </div>

  </div>

<footer class="footer">
      <div class="container">
        <p>Likeness is the number crunching part of <a href="http://hexagrammbooks.com/moebii/index.php">moebii</a>.</p>
        <p>Check it out to search for cool travel articles</p>
        
      </div>
</footer>
    
    <!-- LOAD JS FILES -->
    
    <!-- Jquery -->
    <script src="../assets/js/jquery-1.10.2.min.js" type="text/javascript"></script>
    
    <!-- Less and Twitter Bootstrap -->
    <script src="../assets/js/bootstrap.min.js" type="text/javascript"></script>
            
    
    
    <!-- HTML5 shim, for IE6-8 support of HTML5 elements -->
    <!--[if lt IE 9]>
      <script src="assets/js/html5shiv.js"></script>
    <![endif]-->
    
    
    <script src="../assets/js/prettify.js" type="text/javascript"></script>
    <script src="../assets/js/application.js" type="text/javascript"></script>
    
    
</body>
</html>