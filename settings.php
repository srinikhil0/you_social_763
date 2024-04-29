<?php 
include("includes/header.php");
include("includes/form_handlers/settings_handler.php");
?>

<div class="profile_main_column column">

  <ul class="nav nav-tabs" role="tabs" id="settingsTabs">
    <li class="nav-item">
      <a class="nav-link active" href="#editprofile_div" aria-controls="editprofile_div" role="tab" data-toggle="tab"><i class="fas fa-user-edit"></i> Edit Profile</a>
    </li>
    <li class="nav-item">
      <a class="nav-link" href="#changepassword_div" aria-controls="changepassword_div" role="tab" data-toggle="tab"><i class="fas fa-key"></i> Change Password</a>
    </li>
    <li class="nav-item">
      <a class="nav-link" href="#closeaccount_div"aria-controls="closeaccount_div" role="tab" data-toggle="tab"><i class="fas fa-times-circle"></i> Close Account</a>
    </li>
  </ul><br>
    
    <div class="tab-content">

     	<div role="tabpanel" class="tab-pane fade in active" id="editprofile_div">
	  		<h4>Account Settings</h4>
			<?php
				echo "<img src='" . $user['profile_pic'] ."' class='small_profile_pic'>";
			?>
			<br>
			<a href="upload.php" style="color: #2271ff;">Upload new profile picture</a> <br><br>
		
		
			Modify the values and click 'Update Details'

			<?php
			$user_data_query = mysqli_query($con, "SELECT first_name, last_name, email FROM users WHERE username='$userLoggedIn'");
			$row = mysqli_fetch_array($user_data_query);

			$first_name = $row['first_name'];
			$last_name = $row['last_name'];
			$email = $row['email'];
			?>

			<form action="settings.php" method="POST">
				First Name: &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type="text" name="first_name" value="<?php echo $first_name; ?>" id="settings_input"><br>
				Last Name: &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type="text" name="last_name" value="<?php echo $last_name; ?>" id="settings_input"><br>
				Email: &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type="text" name="email" value="<?php echo $email; ?>" id="settings_input"><br>

				<?php echo $message; ?>

				<input type="submit" name="update_details" id="save_details" value="Update Details" class="info settings_submit"><br>
			</form>
		</div>
		   
      <div role="tabpanel" class="tab-pane fade" id="changepassword_div">
        <div>
		<h4>Change Password</h4>
			<form action="settings.php" method="POST">
				Old Password:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type="password" name="old_password" id="settings_input"><br>
				New Password:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type="password" name="new_password_1" id="settings_input"><br>
				confirm New Password:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type="password" name="new_password_2" id="settings_input"><br>

				<?php echo $password_message; ?>

				<input type="submit" name="update_password" id="save_details" value="Update Password" class="info settings_submit"><br>
			</form>
        </div>
      </div>

      <div role="tabpanel" class="tab-pane fade" id="closeaccount_div">
        <div>
		<h4>Close Account</h4>
			<form action="settings.php" method="POST">
				<input type="submit" name="close_account" id="close_account" value="Close Account" class="danger settings_submit">
			</form>
        </div>
      </div>
    </div>


	</div>