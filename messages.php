<?php 
ob_start();  // Start buffering
include("includes/header.php");

$message_obj = new Message($con, $userLoggedIn);

if(isset($_GET['u']))
	$user_to = $_GET['u'];
else {
	$user_to = $message_obj->getMostRecentUser();
	if($user_to == false)
		$user_to = 'new';
}

if($user_to != "new")
	$user_to_obj = new User($con, $user_to);

if(isset($_POST['post_message'])) {

	if(isset($_POST['message_body'])) {
		$body = mysqli_real_escape_string($con, $_POST['message_body']);
		$date = date("Y-m-d H:i:s");
		$message_obj->sendMessage($user_to, $body, $date);
		header("Location: messages.php?u=$user_to");
	}

}
ob_end_flush(); 

 ?>

 <!-- <div class="user_details column">
		<a href="<?php echo $userLoggedIn; ?>">  <img src="<?php echo $user['profile_pic']; ?>"> </a>

		<div class="user_details_left_right">
			<a href="<?php echo $userLoggedIn; ?>">
			<?php 
			echo $user['username'];

			 ?>
			</a>
			<br>
			<?php echo "Posts: " . $user['num_posts']. "<br>"; 
			echo "Likes: " . $user['num_likes'];

			?>
		</div>
	</div> -->

	<div class="main_column2 column" id="main_column">
		<?php  
		if($user_to != "new"){
			echo "<h4>You and <a href='$user_to'>" . $user_to_obj->getUsername() . "</a></h4><hr><br>";

			echo "<div class='loaded_messages' id='scroll_messages'>";
				echo $message_obj->getMessages($user_to);
			echo "</div>";
		}
		else {
			echo "<h4>New Message</h4>";
		}
		?>



		<div class="message_post">
			<form action="" method="POST">
				<?php
				if($user_to == "new") {
					echo "Select the friend you would like to message <br><br>";
					?> 
					To: <input type='text' onkeyup='getUsers(this.value, "<?php echo $userLoggedIn; ?>")' name='q' placeholder='Name' autocomplete='off' id='seach_text_input'>

					<?php
					echo "<div class='results'></div>";
				}
				else {
					// echo "<div class='me1 me2 me3 me4 me5 me6'>";
					// 	echo "<div class='em1'";
							// echo "<div>";
							// 	echo "<button class='butt1' type='button'";
							// 		echo "<div class='buttof";
							// 			echo "<svg aria-label='Emoji' class='_8-yf5 ' fill='#262626' height='24' viewBox='0 0 48 48' width='24'>";
							// 				echo "<path d='M24 48C10.8 48 0 37.2 0 24S10.8 0 24 0s24 10.8 24 24-10.8 24-24 24zm0-45C12.4 3 3 12.4 3 24s9.4 21 21 21 21-9.4 21-21S35.6 3 24 3z'>";
							// 				echo "</path>";
							// 				echo "<path d='M34.9 24c0-1.4-1.1-2.5-2.5-2.5s-2.5 1.1-2.5 2.5 1.1 2.5 2.5 2.5 2.5-1.1 2.5-2.5zm-21.8 0c0-1.4 1.1-2.5 2.5-2.5s2.5 1.1 2.5 2.5-1.1 2.5-2.5 2.5-2.5-1.1-2.5-2.5zM24 37.3c-5.2 0-8-3.5-8.2-3.7-.5-.6-.4-1.6.2-2.1.6-.5 1.6-.4 2.1.2.1.1 2.1 2.5 5.8 2.5 3.7 0 5.8-2.5 5.8-2.5.5-.6 1.5-.7 2.1-.2.6.5.7 1.5.2 2.1 0 .2-2.8 3.7-8 3.7z'>";
							// 				echo "</path>";
							// 			echo "</svg>";
							// 		echo "</div>";
							// 	echo "</button>";
							// echo "</div>";
							// echo "<div class='tx1 tx2 tx3 tx4 tx5'";
								echo "<textarea name='message_body' id='message_textarea' placeholder='Type a message'></textarea>";
								echo "<input type='submit' name='post_message' class='info' id='message_submit' value='Send'>";
					// 		echo "</div>";
					// 	echo "</div>";
					// echo "</div>";
				}

				?>
			</form>

		</div>

		<script>
			var div = document.getElementById("scroll_messages");
			div.scrollTop = div.scrollHeight;
		</script>

	</div>

	<div class="user_details2 column" id="conversations">
			<h4>Conversations</h4><br>

			<div class="loaded_conversations">
				<?php echo $message_obj->getConvos(); ?>
			</div><br>
			<a href="messages.php?u=new">New Message</a>

		</div>
