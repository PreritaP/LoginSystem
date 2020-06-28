<?php
  session_start();
	include("config.php");
	include("dataBase.php");

	if(isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true){
	    header("location: welcome.php");
	    exit;
	}   

   	if($_SERVER["REQUEST_METHOD"] == "POST") {
   		$username = $_POST['username'];
   		$password = md5($_POST['password']);

   		$db = Database::getInstance();
   		$result = $db->selectQuery('user','user_name',$username,"","",'user_id',"","",""," AND user_password = '$password' ");
   		$result = json_decode($result,true);

		if(isset($result[0]['user_id']) && $result[0]['user_id'] > 0 ) {
			session_start();
      $_SESSION['login_user'] = $username;
			$_SESSION['loggedin'] 	= true;
			header("location: welcome.php");
		} else {
			$error = "Your Login Name or Password is invalid";
			session_destroy();
		}
   	}
?>
<html>   
	<head>
		<title> Login </title>
		<link href="CSS/bootstrap.css" rel="stylesheet" type="text/css">
		<link href="CSS/style.css" rel="stylesheet" type="text/css">
	</head>
   
	<body bgcolor = "#FFFFFF">
    <div class="wrapper">
        <h2>Login</h2>
        <form action = "" method = "post">
            <div class="form-group">
                <label>Username</label>
                <input type="text" name="username" class="form-control">
            </div>    
            <div class="form-group">
                <label>Password</label>
                <input type="password" name="password" class="form-control">
            </div>
            <div class="form-group">
                <input type="submit" class="btn btn-primary" value="Login">
            </div>
            <p>Don't have an account? <a href="signup.php">Sign up now</a>.</p>
        </form>
        <div style = "font-size:11px; color:#cc0000; margin-top:10px"><?php echo $error; ?></div>
    </div>

	</body>
</html>