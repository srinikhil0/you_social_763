<?php  
require 'config/config.php';
include("includes/classes/User.php");
include("includes/classes/Post.php");
include("includes/classes/Message.php");
include("includes/classes/Notification.php");


if (isset($_SESSION['username'])) {
	$userLoggedIn = $_SESSION['username'];
	$user_details_query = mysqli_query($con, "SELECT * FROM users WHERE username='$userLoggedIn'");
	$user = mysqli_fetch_array($user_details_query);
}
else {
	header("Location: register.php");
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>You.social</title>

    <!-- Javascript -->
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
	<script src="assets/js/bootstrap.js"></script>
	<script src="assets/js/bootbox.min.js"></script>
	<script src="assets/js/demo.js"></script>
	<script src="assets/js/jquery.jcrop.js"></script>
	<script src="assets/js/jcrop_bits.js"></script>


    <link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css">
	<link href="assets/css/header.css" rel="stylesheet" type="text/css">
	<!-- <link href="assets/css/bootstrap.css" rel="stylesheet" type="text/css">
	<link href="assets/css/bootstrap.min.css" rel="stylesheet" type="text/css"> -->

    <link href="assets/fontawesome/css/all.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/jquery.Jcrop.css" type="text/css" />
</head>

<body>
<div class="continer"> 

	<header>
            <div class="logo">
                <a href="index.php" style="text-decoration: none;"><h1>You.Social</h1></a>
			</div>
			
			<div class="search">
                <form action="search.php" method="GET" name="search_form">
                    <div class="row">
                        <input type="text" onkeyup="getLiveSearchUsers(this.value, '<?php echo $userLoggedIn; ?>')" id="search_text_input" name="q" placeholder="Search Profile..." autocomplete="off">
                        <button type="submit"><span><i class="fa fa-search"></i></span></button>
                    </div>
				</form>
				
				<div class="search_results">
				</div>

				<div class="search_results_footer_empty">
				</div>
			</div>
			
            <div class="nav">
                <?php

                //Unread Notifications
                $notifications = new Notification($con, $userLoggedIn);
                $num_notifications = $notifications->getUnreadNumber(); 

                //Unread Requests 
				$user_obj = new User($con, $userLoggedIn);
				$num_requests = $user_obj->getNumberOfFriendRequests();
				
				//Unread Messages 
				$messages = new Message($con, $userLoggedIn);
                $num_messages = $messages->getUnreadNumber();
                
                ?>
                <ul>
					
                    <li>
                        <a href="index.php" style="text-decoration: none;"><h3><i class="fas fa-home"></i></h3></a>
                    </li>
                     <li>
						<a href="messages.php"
						style="text-decoration: none;"><h3><i class="fas fa-inbox"></i></h3>
						<?php
							if($num_messages>0){
								echo '<span class="notification_badge" id="unread_messages">' . $num_messages . '</span>';
							}
						?>
					</a>
                    </li>
                    <li>
                        <a href="javascript:void(0);" onclick="getDropdownData('<?php echo $userLoggedIn; ?>', 'notification')" 
                        style="text-decoration: none;"><h3><i class="fab fa-wpexplorer" style="transform: rotateY(180deg); font-weight: bold;"></i></h3>
                            <?php
                                if($num_notifications>0){
                                    echo '<span class="notification_badge" id="unread_notification">' . $num_notifications . '</span>';
								}
                            ?>
                        </a>
                    </li>
                    <li>
						<a href="requests.php" style="text-decoration: none;"><h3><i class="fas fa-dot-circle"></i></h3>
							<?php
							if($num_requests){
								echo '<span class="notification_badge" id="unread_requests">' . $num_requests . '</span>';
							}
							?>
						</a>
					</li>
					<li>
						<a href="<?php echo $userLoggedIn; ?>" style="text-decoration: none;" ><img src="<?php echo $user['profile_pic']; ?>" width="20px" style="height: 20px; border-radius: 50%; width: 20px;"></a>
					</li>
					<li>
                        <a href="settings.php" style="text-decoration: none;"><h3><i class="fas fa-toolbox"></i></h3></a>
                    </li>
					<li>
                        <a href="includes/handlers/logout.php" style="text-decoration: none;"><h3>Logout</h3></a>
					</li>
                </ul>
            </div>
        
        <div class="dropdown_data_window" style="height:0px; border:none;"></div>
		<input type="hidden" id="dropdown_data_type" value="">
		</header>
</div>


<script>
	$(function(){

		var userLoggedIn = '<?php echo $userLoggedIn; ?>';
		var dropdownInProgress = false;

	    $(".dropdown_data_window").scroll(function() {
	    	var bottomElement = $(".dropdown_data_window a").last();
			var noMoreData = $('.dropdown_data_window').find('.noMoreDropdownData').val();

	        // isElementInViewport uses getBoundingClientRect(), which requires the HTML DOM object, not the jQuery object. The jQuery equivalent is using [0] as shown below.
	        if (isElementInView(bottomElement[0]) && noMoreData == 'false') {
	            loadPosts();
	        }
	    });

	    function loadPosts() {
	        if(dropdownInProgress) { //If it is already in the process of loading some posts, just return
				return;
			}
			
			dropdownInProgress = true;

			var page = $('.dropdown_data_window').find('.nextPageDropdownData').val() || 1; //If .nextPage couldn't be found, it must not be on the page yet (it must be the first time loading posts), so use the value '1'

			var pageName; //Holds name of page to send ajax request to
			var type = $('#dropdown_data_type').val();

			if(type == 'notification')
				pageName = "ajax_load_notifications.php";
			else if(type == 'message')
				pageName = "ajax_load_messages.php";

			$.ajax({
				url: "includes/handlers/" + pageName,
				type: "POST",
				data: "page=" + page + "&userLoggedIn=" + userLoggedIn,
				cache:false,

				success: function(response) {

					$('.dropdown_data_window').find('.nextPageDropdownData').remove(); //Removes current .nextpage 
					$('.dropdown_data_window').find('.noMoreDropdownData').remove();

					$('.dropdown_data_window').append(response);

					dropdownInProgress = false;
				}
			});
	    }

	    //Check if the element is in view
	    function isElementInView (el) {
	        var rect = el.getBoundingClientRect();

	        return (
	            rect.top >= 0 &&
	            rect.left >= 0 &&
	            rect.bottom <= (window.innerHeight || document.documentElement.clientHeight) && //* or $(window).height()
	            rect.right <= (window.innerWidth || document.documentElement.clientWidth) //* or $(window).width()
	        );
	    }
	});

	</script>

	<script>
		if (window.history.replaceState) {
        	window.history.replaceState(null, null, window.location.href);
		}
	</script>

<div class="wrapper">