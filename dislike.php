<html>
<head>
	<title></title>
	<link rel="stylesheet" type="text/css" href="assets/css/header.css">
</head>
<body>

	<style type="text/css">
	* {
		font-family: Arial, Helvetica, Sans-serif;
	}
	body {
		background-color: #fff;
	}

	form {
		position: absolute;
		top: 0;
	}

	</style>

	<?php  
	require 'config/config.php';
	include("includes/classes/User.php");
	include("includes/classes/Post.php");

	if (isset($_SESSION['username'])) {
		$userLoggedIn = $_SESSION['username'];
		$user_details_query = mysqli_query($con, "SELECT * FROM users WHERE username='$userLoggedIn'");
		$user = mysqli_fetch_array($user_details_query);
	}
	else {
		header("Location: register.php");
	}

	//Get id of post
	if(isset($_GET['post_id'])) {
		$post_id = $_GET['post_id'];
	}

	$get_dislikes = mysqli_query($con, "SELECT dislikes, added_by FROM posts WHERE id='$post_id'");
	$row = mysqli_fetch_array($get_dislikes);
	$total_dislikes = $row['dislikes']; 
	$user_disliked = $row['added_by'];

	$user_details_query = mysqli_query($con, "SELECT * FROM users WHERE username='$user_disliked'");
	$row = mysqli_fetch_array($user_details_query);
	$total_user_dislikes = $row['num_dislikes'];

	//Dislike button
	if(isset($_POST['dislike_button'])) {
		$total_dislikes++;
		$query = mysqli_query($con, "UPDATE posts SET dislikes='$total_dislikes' WHERE id='$post_id'");
		$total_user_dislikes++;
		$user_dislikes = mysqli_query($con, "UPDATE users SET num_dislikes='$total_user_dislikes' WHERE username='$user_disliked'");
		$insert_user = mysqli_query($con, "INSERT INTO dislikes VALUES('', '$userLoggedIn', '$post_id')");

		//Insert Notification
	}
	//Unlike button
	if(isset($_POST['unlike_button'])) {
		$total_dislikes--;
		$query = mysqli_query($con, "UPDATE posts SET dislikes='$total_dislikes' WHERE id='$post_id'");
		$total_user_dislikes--;
		$user_dislikes = mysqli_query($con, "UPDATE users SET num_dislikes='$total_user_dislikes' WHERE username='$user_disliked'");
		$insert_user = mysqli_query($con, "DELETE FROM dislikes WHERE username='$userLoggedIn' AND post_id='$post_id'");
	}

	//Check for previous dislikes
	$check_query = mysqli_query($con, "SELECT * FROM dislikes WHERE username='$userLoggedIn' AND post_id='$post_id'");
	$num_rows = mysqli_num_rows($check_query);

	if($num_rows > 0) {
		echo '<form action="dislike.php?post_id=' . $post_id . '" method="POST">
				<input type="submit" class="comment_dislike" name="unlike_button" value="💔">
				<div class="dislike_value">
					'. $total_dislikes .' Likes
				</div>
			</form>
		';
	}
	else {
		echo '<form action="dislike.php?post_id=' . $post_id . '" method="POST">
				<input type="submit" class="comment_dislike" name="dislike_button" value="💔"></input>
				<div class="dislike_value">
					'. $total_dislikes .' Likes
				</div>
			</form>
		';
	}


	?>




</body>
</html>