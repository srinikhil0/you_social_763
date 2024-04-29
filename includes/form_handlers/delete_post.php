<?php 
require '../../config/config.php';
 
$userLoggedIn = $_SESSION["username"];
 
if(isset($_GET['post_id']))
	$post_id = $_GET['post_id'];
 
if(isset($_POST['result'])) {
   if($_POST['result'] == 'true') {
     $query = mysqli_query($con, "UPDATE posts SET deleted='yes' WHERE id='$post_id'");
     $decrement_post_count = mysqli_query($con, "UPDATE users SET num_posts=num_posts-1 WHERE username='$userLoggedIn'");
   }
}
 
?>