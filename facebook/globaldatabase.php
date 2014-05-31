<?php

function db_connect()
{
  global $db_Host, $db_User, $db_Pass, $db_Name;
    $connection = mysql_connect($db_Host, $db_User, $db_Pass) or die("Can't use db : ".mysql_error());;
    mysql_select_db($db_Name, $connection);
    return $connection;
}

function runquery($dml_command)
{
	 $conn = db_connect();
	 $var = mysql_query($dml_command) or die("<div style='color:red;'>".mysql_error()." and the query is <br/><br/>".$dml_command."</div><br/><br/>");
	 mysql_close($conn);
	 return $var;
}

function runquery_count($sql)
{
	$conn = db_connect();
	$var = mysql_num_rows(mysql_query($sql));
	mysql_close($conn);
	return $var;
}


function runquery_return($dml_command)
{
	 $conn = db_connect();
	 $var  = mysql_query($dml_command) or die("<div style='color:red;'>".mysql_error()." and the query is <br/><br/>".$dml_command."</div><br/><br/>");
         $var  = mysql_insert_id();
	 mysql_close($conn);
	 return $var;
}

function getNextId($table, $column)
{
	//echo "SELECT MAX($column) as Value FROM $table";
	$query = db_execute_return("SELECT MAX($column) as Value FROM $table");
	if(mysql_num_rows($query) > 0)
	{
		$result = mysql_fetch_array($query);
		$nextval = $result[0];
		return $nextval;
	}
	else
		return 1;
}

function get_last_insert_id($dml_command){
	
	 $conn = db_connect();
	 mysql_query($dml_command);
	 $var = mysql_insert_id();
	 mysql_close($conn);
	 return $var;
}


function BS_alert($type='info',$heading = 'Well Done',$description='Good to go',$close=true){
  echo '<div class="alert alert-'.$type.'">';
  if($close)
  echo '<button type="button" class="close" data-dismiss="alert">&times;</button>';
  echo '<strong> '.$heading.' ! </strong> '.$description.' </div>';
}


function printArray($array,$die=false){
    echo "<pre>";
    print_r($array);
    echo "</pre>";
    if($die == true)
        die;
}

function UploadImage($files,$imagename){
    $toreturn = array();
    $allowedExts = array("gif", "jpeg", "jpg", "png");
    $temp = explode(".", $files[$imagename]["name"]);
    $extension = end($temp);
    $imageName = $temp[0]."_".time().".".$extension;
    if ((($_FILES[$imagename]["type"] == "image/gif")
            || ($_FILES[$imagename]["type"] == "image/jpeg")
            || ($_FILES[$imagename]["type"] == "image/jpg")
            || ($_FILES[$imagename]["type"] == "image/pjpeg")
            || ($_FILES[$imagename]["type"] == "image/x-png")
            || ($_FILES[$imagename]["type"] == "image/png"))
            && in_array($extension, $allowedExts)) {
        if ($files[$imagename]["error"] > 0) {
            $toreturn['error'] =  "Return error Code: " . $files[$imagename]["error"] . "<br>";
        } else {

            if (file_exists("upload/" . $files[$imagename]["name"])) {
                $toreturn['error'] = $files[$imagename]["name"] . " already exists. <br/>";
            } else {
                if(move_uploaded_file($files["$imagename"]["tmp_name"], "upload/" . $imageName)){
                      $toreturn['imageName'] = $imageName;
                }
                else
                      $toreturn['error'] = "Image not Uploaded, Some problem in permissions";
            }
        }
    } else {
        $toreturn['error'] = "Invalid file, Select a proper Image";
    }
    
    return $toreturn;
}

?>