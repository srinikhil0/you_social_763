<?php  
include("../../config/config.php");
include("../classes/User.php");

$query = $_POST['query'];
$userLoggedIn = $_POST['userLoggedIn'];

$names = explode(" ", $query);

if(strpos($query, "_") !== false) {
	$stmt = $con->prepare("SELECT * FROM users WHERE username LIKE ? AND user_closed='no' LIMIT 8");
	$search_term = $query . '%';
	$stmt->bind_param("s", $search_term);
	$stmt->execute();
	$usersReturned = $stmt->get_result();
}
else if(count($names) == 2) {
	$stmt = $con->prepare("SELECT * FROM users WHERE (username LIKE ? AND username LIKE ?) AND user_closed='no' LIMIT 8");
	$search_term_1 = '%' . $names[0] . '%';
	$search_term_2 = '%' . $names[1] . '%';
	$stmt->bind_param("ss", $search_term_1, $search_term_2);
	$stmt->execute();
	$usersReturned = $stmt->get_result();
}
else {
	$stmt = $con->prepare("SELECT * FROM users WHERE (username LIKE ? OR username LIKE ?) AND user_closed='no' LIMIT 8");
	$search_term = '%' . $names[0] . '%';
	$stmt->bind_param("ss", $search_term, $search_term);
	$stmt->execute();
	$usersReturned = $stmt->get_result();
}

if($query != "") {
	while($row = mysqli_fetch_array($usersReturned)) {

		$user = new User($con, $userLoggedIn);

		if($row['username'] != $userLoggedIn) {
			$mutual_friends = $user->getMutualFriends($row['username']) . " friends in common";
		}
		else {
			$mutual_friends = "";
		}

		if($user->isFriend($row['username'])) {
			echo "<div class='resultDisplay'>
					<a href='messages.php?u=" . $row['username'] . "' style='color: #000'>
						<div class='liveSearchProfilePic'>
							<img src='". $row['profile_pic'] . "'>
						</div>

						<div class='liveSearchText'>
							
							<p style='margin: 0; padding-top: 10px;'>". $row['username'] . "</p>
							<p id='grey'>".$mutual_friends . "</p>
						</div>
					</a>
				</div>";
		}
	}
}
?>
