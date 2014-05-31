<?php session_start();
$ses_id = session_id(); 
include_once("db.php");
include_once('config.php');
$db = new db();

include_once "fbmain.php";
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
        <h1>Facebook</h1>
      </div>
    </header>


  <div class="container">

    <!-- Docs nav
    ================================================== -->
    <div class="row">
      <div class="span10">
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
     <section id="Introduction">
    <div class="page-header">
	<?php if (!$user) { ?>
	    <div class="page-header">
	        <h5>My posts & check ins</h5>
	    </div>
	    <h4><a href="<?php echo $loginUrl?>" class="btn btn-facebook"><i class="fa fa-facebook"></i> | Connect with Facebook</a></h4>
		<?php } else { ?>
	    <div class="page-header">
	        <h5>My posts & check ins</h5>
	    </div>
		<p style="float:right;"><a href="index.php?tu=ys">Update all places Now</a></p>
		<br><br>
<?php }
?>
        <!-- Introduction
        ================================================== -->
            
            
        	<div>

    </div>



    <!-- all time check if user session is valid or not -->
<?php
if($firsttimeuser){
    echo "<h4>Facebook checkins/statuses have been added.  <a href='../likeness/index.php'>link other accounts</a></h4>";
}
if ($user){  // if really a Facebook user exists
if($usedToday == false || $toupdate != false){ // not used by this user today
//echo "<p>Finding all place info for user</p>";
try{
$user_statuses = $facebook->api('/me/statuses?with=location&fields=place,updated_time&limit=500&date_format=U','GET');
//printArray($user_statuses,true);
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
        if(array_key_exists('created_time',$tmp)) $createdAt = addslashes($tmp['created_time']);
        else $createdAt  =    addslashes($tmp['updated_time']);
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
}
catch(FacebookApiException $fbe){
	echo '<h3 style="color:red;">Maximum allowed number of calls to Facebook Api reached. Please try again after few moments.</h3>';
	die();
    continue;
}

try{
$user_posts = $facebook->api('/me/posts?fields=id,object_id,place,updated_time&limit=100&date_format=U','GET'); 
//printArray($user_posts,true);
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
        if(array_key_exists('created_time',$tmp)) $createdAt = addslashes($tmp['created_time']);
        else $createdAt  =    addslashes($tmp['updated_time']);
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
}
catch(FacebookApiException $fbe){
	echo '<h3 style="color:red;">Maximum allowed number of calls to Facebook Api reached. Please try again after few moments.</h3>';
	die();
    //continue;
}

try{
$user_photos = $facebook->api('/me/photos?fields=place,created_time,updated_time&limit=500&date_format=U','GET'); 
//printArray($user_photos,true);
foreach ($user_photos['data'] as $tmp){
    if(empty($tmp['place'])) { continue; }
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
        if(array_key_exists('created_time',$tmp)) $createdAt = addslashes($tmp['created_time']);
        else $createdAt  =    addslashes($tmp['updated_time']);
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
}
catch(FacebookApiException $fbe){
	echo '<h3 style="color:red;">Maximum allowed number of calls to Facebook Api reached. Please try again after few moments.</h3>';
	die();
	//continue;
}	
} // not used today by this user

$sqlPlaces="SELECT * FROM `placeDetailsFB` WHERE `facebookUserID` = ? ORDER BY `createdAt` DESC";
    $rsPlaces = $db->Query($sqlPlaces,array($user));
    $arrayPlaces = $rsPlaces->returnArray();
echo "<h4>check in's refreshed.  <a href='../index.php'>link other accounts</a></h4>";
echo "<h2>My Places</h2>";
foreach ($arrayPlaces as $tmp){
    echo "<p><a target='_blank' href='http://fb.com/".$tmp['facebookCheckinID']."'>".$tmp['placeName']." <i>".$tmp['placeCity']." ".$tmp['placeState']."</i></a><p>";
}

$sqlLastLoginDate = "UPDATE `userGeneral` SET `LastLoginDate` = ?,`currentSessionID` = ?  WHERE `facebookUserID` = ? ";
$db->Query($sqlLastLoginDate,array($today_date,$ses_id,$user));

include_once('friends_checkins.php');

}// if really a Facebook user exists


// Update last login date

?>
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