<?php  
if(isset($_POST['update_details'])) {

	$first_name = $_POST['first_name'];
	$last_name = $_POST['last_name'];
	$email = $_POST['email'];

	$stmt = $con->prepare("SELECT username FROM users WHERE email=?");
	$stmt->bind_param("s", $email);
	$stmt->execute();
	$stmt->store_result();
	$stmt->bind_result($matched_user);
	$stmt->fetch();

	if(empty($matched_user) || $matched_user == $userLoggedIn) {
		$message = "Details updated!<br><br>";

		$stmt = $con->prepare("UPDATE users SET first_name=?, last_name=?, email=? WHERE username=?");
		$stmt->bind_param("ssss", $first_name, $last_name, $email, $userLoggedIn);
		$stmt->execute();
	}
	else 
		$message = "That email is already in use!<br><br>";

}
else 
	$message = "";


//**************************************************

if(isset($_POST['update_password'])) {

	$old_password = strip_tags($_POST['old_password']);
	$new_password_1 = strip_tags($_POST['new_password_1']);
	$new_password_2 = strip_tags($_POST['new_password_2']);

	$stmt = $con->prepare("SELECT pass FROM users WHERE username=?");
	$stmt->bind_param("s", $userLoggedIn);
	$stmt->execute();
	$stmt->store_result();
	$stmt->bind_result($db_password);
	$stmt->fetch();

	if(password_verify($old_password, $db_password)) {

		if($new_password_1 == $new_password_2) {


			if(strlen($new_password_1) <= 4) {
				$password_message = "Sorry, your password must be greater than 4 characters<br><br>";
			}	
			else {
				$new_password_md5 = password_hash($new_password_1, PASSWORD_DEFAULT);
				$stmt = $con->prepare("UPDATE users SET pass=? WHERE username=?");
				$stmt->bind_param("ss", $new_password_md5, $userLoggedIn);
				$stmt->execute();
				$password_message = "Password has been changed!<br><br>";
			}


		}
		else {
			$password_message = "Your two new passwords need to match!<br><br>";
		}

	}
	else {
			$password_message = "The old password is incorrect! <br><br>";
	}

}
else {
	$password_message = "";
}


if(isset($_POST['close_account'])) {
	header("Location: close_account.php");
}

if(isset($_POST['temp_close_account'])){
	header("Location: close_account.php");
}


?>
