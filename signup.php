<?php

include("config.php");
include("dataBase.php");

if($_SERVER["REQUEST_METHOD"] == "POST"){
	/*Creating a database object*/
	$db = Database::getInstance();
	$isValid = true;
	$error = "";
	$username ="";
	$password = "";
    /**Validation for username*/
	if(empty(trim($_POST["username"]))){
        $error .= "Please enter a username. ";
        $isValid = false;
    } else {
    	$username = trim($_POST["username"]);
    	$result = $db->selectQuery('user','user_name',$username,"","",'user_id',"","","","");
    	$result = json_decode($result,true);
    	if(isset($result[0]['user_id']) && $result[0]['user_id'] > 0 ) {
    		$error .="Username is already exist. ";
    		$isValid = false;
    	}	
    }

    /**validation for password*/
    if(empty(trim($_POST["password"]))){
        $error .= "Please enter a password. "; 
        $isValid = false;    
    } elseif(strlen(trim($_POST["password"])) < 6){
        $error .= "Password must have atleast 6 characters. ";
        $isValid = false;
    } else{
        $password = trim($_POST["password"]);
    }

    /**validation for confirm password*/
    if(empty(trim($_POST["confirm_password"]))){
        $error .= "Please confirm password. ";   
        $isValid = false;  
    } else{
        $confirm_password = trim($_POST["confirm_password"]);
        if(($isValid) && ($password != $confirm_password)){
            $error .= "Password did not match. ";
            $isValid = false;
        }
    }

    /**Successful registration*/
    if ($isValid) {
    	$insertArray = array();
    	$insertArray['user_name'] = $username;
    	$insertArray['user_password'] = md5($password);
    	$result = $db->insert('user',$insertArray);

    	if($result >0) {
    		echo "User successfully registerd. ";
    		header("location: index.php");
    	} else {
    		$error = " Some error ocuured. Please try again. ";
    	}
    }
}
	

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Sign Up</title>
    <link rel="stylesheet" href="CSS/bootstrap.css">
   	<link href="CSS/style.css" rel="stylesheet" type="text/css">
</head>
<body>
    <div class="wrapper">
        <h2>Sign Up</h2>
        <form action="" method="post">
            <div class="form-group">
                <label>Username</label>
                <input type="text" name="username" class="form-control" value="">
            </div>    
            <div class="form-group">
                <label>Password</label>
                <input type="password" name="password" class="form-control" value="">
            </div>
            <div class="form-group ">
                <label>Confirm Password</label>
                <input type="password" name="confirm_password" class="form-control" value="">
            </div>
            <div class="form-group">
                <input type="submit" class="btn btn-primary" value="Submit">
            </div>
            <p>Already have an account? <a href="index.php">Login here</a>.</p>
        </form>
        <div style = "font-size:11px; color:#cc0000; margin-top:10px"><?php echo $error; ?></div>
    </div>    
</body>