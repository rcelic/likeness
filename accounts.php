<?php session_start();
$ses_id = session_id(); 
include_once("db.php");
$db = new db();

?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <!-- META DATA -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimum-scale=1.0, maximum-scale=1.0">
    <meta name="description" content="Treble theme">
    <title>likeness - social media aggregation</title>

    <link rel="apple-touch-icon-precomposed" sizes="144x144" href="../assets/images/logo1.png">
    <link rel="apple-touch-icon-precomposed" sizes="114x114" href="../assets/images/logo1.png">
      <link rel="apple-touch-icon-precomposed" sizes="72x72" href="../assets/images/logo1.png">
                    <link rel="apple-touch-icon-precomposed" href="../assets/images/logo1.png">
                                   <link rel="shortcut icon" href="../assets/images/logo1.png">


    <!-- TREBLE STYLESHEETS -->
    <link rel="stylesheet" href="assets/style/bootstrap.css" type="text/css" media="all" />
    
    <!-- TREBLE DOCUMENTATION -->
    <link href="assets/style/docs.css" rel="stylesheet">
    <link href="../assets/style/socialButtons.css" rel="stylesheet">
    <link href="assets/style/prettify.css" rel="stylesheet">
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
                    <a style="text-decoration: none" class="brand pull-left" href="index.php">
                        <img src="../assets/images/logo1.png" width="35px" height="35px" alt="Treble"> <strong>LIKENESS</strong>
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
                        <li><a href="thesis.php">The Idea</a></li>
                        <li><a href="accounts.php">My Accounts</a></li>
                        <li><a href="places.php">My Places</a></li>
                        
                    </ul>
                    <!-- END MAIN MENU -->
                </div>
            </div>
        </div>
    </nav>
    <!-- END NAVIGATION -->
    
    <header class="jumbotron subhead" id="overview">
      <div class="container">
        <h1>My Accounts</h1>
      </div>
    </header>


  <div class="container">

    <!-- Docs nav
    ================================================== -->
    <div class="row">
      <div class="span10">

        <!-- Introduction
        ================================================== -->
        <section id="Introduction">
            <div class="page-header">
                <h3>Click to refresh</h3>
            </div>
            
                <?php
                $sqlCurrentSession = "select currentSessionID, foursquareUserID, facebookUserID from userGeneral where currentSessionID = ?";
		    	
		    	$data[] = $ses_id;
		    	$rsCurrentSession = $db->Query($sqlCurrentSession,$data);
		    	$arrayCurrentSession = $rsCurrentSession->returnArray();
		    	
		    	// if no session, no Foursquare ID or any other linked account, insert FSQ id, session id, and foursquare` into DB as a new user
		    	if(empty($arrayCurrentSession))
		    		{?>
		    		<h5>No accounts linked.  <a href='../index.php'>link other accounts</a> where you checkin</h5>	
		    			<?php } else 
		    			{ 
				        foreach ($arrayCurrentSession as $tmp)
						   {}
						   if($tmp['foursquareUserID'] != null){ ?>
						   <p><a href="foursquare/index.php" class="btn btn-foursquare"><i class="fa fa-foursquare"></i> | Foursquare</a></p>
						   <?php
						   	
						   }
						   if($tmp['facebookUserID'] != null){?>
						   <p><a href="facebook/index.php" class="btn btn-facebook"><i class="fa fa-facebook"></i> | Facebook</a></p>
						   <?php
						   }
		    			}
		    				
		    			?>
				  
        </section>

        
      </div>
    </div>

  </div>

<footer class="footer">
      <div class="container">
        <p>Likeness is the number crunching part of <a href="hexagrammbooks.com/moebii/index.php">moebii</a>.</p>
        <p>Check it out to search for cool travel articles</p>
        
      </div>
</footer>
    
    <!-- LOAD JS FILES -->
    
    <!-- Jquery -->
    <script src="assets/js/jquery-1.10.2.min.js" type="text/javascript"></script>
    
    <!-- Less and Twitter Bootstrap -->
    <script src="assets/js/bootstrap.min.js" type="text/javascript"></script>
            
    
    
    <!-- HTML5 shim, for IE6-8 support of HTML5 elements -->
    <!--[if lt IE 9]>
      <script src="assets/js/html5shiv.js"></script>
    <![endif]-->
    
    
    <script src="assets/js/prettify.js" type="text/javascript"></script>
    <script src="assets/js/application.js" type="text/javascript"></script>
    
</body>
</html>