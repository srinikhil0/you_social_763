<?php

include("includes/header.php");

$query = "";
$type = "name"; // Default search type

if (isset($_GET['q'])) {
    $query = $_GET['q'];
}

if (isset($_GET['type'])) {
    $type = $_GET['type'];
}
?>

<div class="main_column column" id="main_column">
    <?php 
    if ($query == "") {
        echo "You must enter something in the search box.";
    } else {
        // Prepare and execute search query
        if ($type == "username") {
            $stmt = $con->prepare("SELECT * FROM users WHERE username LIKE CONCAT(?, '%') AND user_closed='no' LIMIT 8");
            $stmt->bind_param("s", $query);
        } else {
            $names = explode(" ", $query);
            if (count($names) == 3) {
                $stmt = $con->prepare("SELECT * FROM users WHERE (first_name LIKE CONCAT(?, '%') AND last_name LIKE CONCAT(?, '%')) AND user_closed='no'");
                $stmt->bind_param("ss", $names[0], $names[2]);
            } else if (count($names) == 2) {
                $stmt = $con->prepare("SELECT * FROM users WHERE (first_name LIKE CONCAT(?, '%') OR last_name LIKE CONCAT(?, '%')) AND user_closed='no'");
                $stmt->bind_param("ss", $names[0], $names[1]);
            } else {
                $stmt = $con->prepare("SELECT * FROM users WHERE (first_name LIKE CONCAT(?, '%') OR last_name LIKE CONCAT(?, '%')) AND user_closed='no'");
                $stmt->bind_param("s", $names[0]);
            }
        }
        $stmt->execute();
        $result = $stmt->get_result();

        // Check if results were found
        if ($result->num_rows == 0) {
            echo "We can't find anyone with a " . $type . " like: " . $query;
        } else {
            echo $result->num_rows . " results found: <br> <br>";

            while ($row = $result->fetch_assoc()) {
                $user_obj = new User($con, $row['username']);
                $button = "";
                $mutual_friends = "";

                if ($userLoggedIn != $row['username']) {
                    // Generate button depending on friendship status
                    if ($user_obj->isFriend($row['username'])) {
                        $button = "<input type='submit' name='" . $row['username'] . "' class='danger' value='Remove Friend'>";
                    } else if ($user_obj->didReceiveRequest($row['username'])) {
                        $button = "<input type='submit' name='" . $row['username'] . "' class='warning' value='Respond to request'>";
                    } else if ($user_obj->didSendRequest($row['username'])) {
                        $button = "<input type='submit' class='default' value='Request Sent'>";
                    } else {
                        $button = "<input type='submit' name='" . $row['username'] . "' class='success' value='Add Friend'>";
                    }

                    $mutual_friends = $user_obj->getMutualFriends($row['username']) . " friends in common";

                    // Button forms
                    if (isset($_POST[$row['username']])) {
                        if ($user_obj->isFriend($row['username'])) {
                            $user_obj->removeFriend($row['username']);
                            header("Location: http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]");
                        } else if ($user_obj->didReceiveRequest($row['username'])) {
                            header("Location: requests.php");
                        } else if ($user_obj->didSendRequest($row['username'])) {
                            // Do nothing
                        } else {
                            $user_obj->sendRequest($row['username']);
                            header("Location: http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]");
                        }
                    }
                }

                echo "<div class='search_result'>
                        <div class='searchPageFriendButtons'>
                            <form action='' method='POST'>
                                " . $button . "
                                <br>
                            </form>
                        </div>

                        <div class='result_profile_pic'>
                            <a href='" . $row['username'] . "'><img src='" . $row['profile_pic'] . "'></a>
                        </div>

                        <a href='" . $row['username'] . "'>
                            <p id='grey' style='padding:15px'> " . $row['username'] . "</p>
                        </a>
                        <br>
                        " . $mutual_friends . "<br>

                    </div>
                    <hr id='search_hr'>";
            } // End while
        }
    }
    ?>

</div>
