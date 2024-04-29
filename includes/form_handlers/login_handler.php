<?php  

require 'includes/form_handlers/register_handler.php';

// $error_array = array();//holds error messages
 
if(isset($_POST['log_btn'])) {
 
	$email = filter_var($_POST['log_email'], FILTER_SANITIZE_EMAIL); //sanitize email
 
	$_SESSION['log_email'] = $email; //Store email into session variable 
	$pass = $_POST['log_password']; //Get password

	$check_database_query = mysqli_query($con, "SELECT * FROM users WHERE email='$email'");
	$check_login_query = mysqli_num_rows($check_database_query);
 
	if($check_login_query == 1) {
		while($row = mysqli_fetch_array($check_database_query)){
			if(password_verify($pass, $row['pass'])){
				$name = $row['username'];
 
				$user_closed_query = mysqli_query($con, "SELECT * FROM users WHERE email='$email' AND user_closed='yes'");
				if(mysqli_num_rows($user_closed_query) == 1) {
					$reopen_account = mysqli_query($con, "UPDATE users SET user_closed='no' WHERE email='$email'");
				}
		
				$_SESSION['username'] = $name;
				echo "Name: " .$name;
				header("Location: profile/profiledetails1.php");
				exit();
			}
		}
		
	}
	else {
		echo "Email or password was incorrect<br>";
	}
 
}
 
?>