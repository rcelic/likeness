<?php

$user            =   null; //facebook user uid
include_once "fbapi/facebook.php";
// Create our Application instance.
$facebook = new Facebook(array(
    'appId'  => $Appid,
    'secret' => $AppSecret,
    'cookie' => true
));


//Facebook Authentication part
$user         = $facebook->getUser();
$access_token = $facebook->getAccessToken();

$loginUrl   = $facebook->getLoginUrl(
array(
    'scope'         => 'basic_info, friends_checkins, friends_photos, friends_status, 
        public_profile, user_checkins, user_friends, user_photos, user_status, read_stream',
    'redirect_uri'  => $BaseUrl
)
);
    $logoutUrl = $facebook ->getLogoutUrl();
    if ($user) {
      try {
        $facebook->api('/me');
      } catch (FacebookApiException $e) {
        $user = null;
      }
    }
   
    
    //if user is logged in and session is valid.
    if ($user){
      // These lines are for getting the extended access token of a Facebook user which works for 60 days 
       
        $extended_token = file_get_contents('https://graph.facebook.com/oauth/access_token?client_id='.$Appid.
                '&client_secret='.$AppSecret.'&grant_type=fb_exchange_token&fb_exchange_token='.$access_token);
        $access_token_explod = explode("=",$extended_token);
        $access_token_explod = explode("&",$access_token_explod[1]);
        $extended_access_token = $access_token_explod[0];

        $sqlUser="SELECT facebookUserID FROM userGeneral WHERE facebookUserID = ?";
        if ($db->Query_count($sqlUser,array($user)) == 0) {
            $sqlUserInsert = "INSERT INTO userGeneral (currentSessionID, facebookUserID, facebookLastRefresh, facebookToken)
                VALUES (?,?,?,?)";
            $db->Query($sqlUserInsert,array($ses_id,$user,0,$extended_access_token));
            $firsttimeuser = true;
        }
        else{
            $sqlFacebokUpdate = "UPDATE userGeneral set currentSessionID = ?, facebookLastRefresh = ?, facebookToken = ? 
                                    WHERE facebookUserID = ? ";
            $db->Query($sqlFacebokUpdate,array($ses_id,0,$extended_access_token,$user));
        }
        $sqlUser="SELECT * FROM `userGeneral` WHERE `facebookUserID` = ?";
        $CurrentUser = $db->Query($sqlUser,array($user));
        $CurrentUser = $CurrentUser -> returnArray();
        if($CurrentUser['0']['LastLoginDate'] == $today_date)
            $usedToday = true;
        else
            $usedToday = false;
        
        if(isset($_GET['tu'])  && $_GET['tu'] == 'ys') $toupdate = $_GET['tu'];
        else $toupdate = false;
            
      // These lines are for getting the extended access token of a Facebook user which works for 60 days 
        //get user basic description
        $userInfo           = $facebook->api("/$user");
        
        //update user's status using graph api
        //http://developers.facebook.com/docs/reference/dialogs/feed/
        if (isset($_GET['publish'])){
            try {
                $publishStream = $facebook->api("/$user/feed", 'post', array(
                    'message' => "I love thinkdiff.net for facebook app development tutorials. :)", 
                    'link'    => 'http://ithinkdiff.net',
                    'picture' => 'http://thinkdiff.net/ithinkdiff.png',
                    'name'    => 'iOS Apps & Games',
                    'description'=> 'Checkout iOS apps and games from iThinkdiff.net. I found some of them are just awesome!'
                    )
                );
                //as $_GET['publish'] is set so remove it by redirecting user to the base url 
            } catch (FacebookApiException $e) {
                d($e);
            }
            $redirectUrl     = $BaseUrl . '/index.php?success=1';
            header("Location: $redirectUrl");
        }

        //update user's status using graph api
        //http://developers.facebook.com/docs/reference/dialogs/feed/
        if (isset($_POST['tt'])){
            try {
                $statusUpdate = $facebook->api("/$user/feed", 'post', array('message'=> $_POST['tt']));
            } catch (FacebookApiException $e) {
                d($e);
            }
        }

        //fql query example using legacy method call and passing parameter
        try{
            $fql    =   "select name, hometown_location, sex, pic_square from user where uid=" . $user;
            $param  =   array(
                'method'    => 'fql.query',
                'query'     => $fql,
                'callback'  => ''
            );
            $fqlResult   =   $facebook->api($param);
        }
        catch(Exception $exception){
            printException($exception);
        }
    }
    
    function printException($d){
        echo '<pre>';
        print_r($d);
        echo '</pre>';
    }
?>