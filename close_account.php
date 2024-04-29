<?php
include("includes/header.php");

if(isset($_POST['cancel'])) {
	header("Location: settings.php");
}

if(isset($_POST['temp_close_account'])) {
	$close_query = mysqli_query($con, "UPDATE users SET user_closed='yes' WHERE username='$userLoggedIn'");
	session_destroy();
	header("Location: register.php");
}

if(isset($_POST['close_account'])){
	$close_account = mysqli_query($con, "DELETE FROM users WHERE username='$userLoggedIn'");
	session_destroy();
	header("Location: register.php");
}


?>

<div class="main_column column">

	<h4>Close Account</h4><br>

	Are you sure you want to close your account?<br><br>
	Instead temporarily close your account and come again when ever you want.<br><br>
	Closing your account will Permanently close your account.<br><br>
	Retreving is undone. <br><br>

	<form action="close_account.php" method="POST">
		<input type="submit" name="close_account" id="close_account" value="Close" class="danger settings_submit">
		<input type="submit" name="cancel" id="update_details" value="Cancel" class="info settings_submit">
	</form>

</div>

<div class="main_column column">

	<h4>Temporarily Close Account</h4><br>

	Closing your account will hide your profile and all your activity from other users.<br><br>
	You can re-open your account at any time by simply logging in.<br><br>

	<form action="close_account.php" method="POST">
		<input type="submit" name="temp_close_account" id="temp_close_account" value="Temporary Close" class="danger settings_submit">
		<input type="submit" name="cancel" id="update_details" value="Cancel" class="info settings_submit">
	</form>

</div>