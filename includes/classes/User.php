<?php
class User {
    private $user;
    private $con;

    public function __construct($con, $user) {
        $this->con = $con;
        $stmt = $this->con->prepare("SELECT * FROM users WHERE username = ?");
        $stmt->bind_param("s", $user);
        $stmt->execute();
        $result = $stmt->get_result();
        $this->user = $result->fetch_assoc();
    }

    public function getUsername() {
        return $this->user['username'];
    }

    public function getNumberOfFriendRequests() {
        $username = $this->user['username'];
        $stmt = $this->con->prepare("SELECT * FROM friend_requests WHERE user_to = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->num_rows;
    }

    public function getNumPosts() {
        $username = $this->user['username'];
        $stmt = $this->con->prepare("SELECT num_posts FROM users WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        return $row['num_posts'];
    }

    public function getProfilePic() {
        $username = $this->user['username'];
        $stmt = $this->con->prepare("SELECT profile_pic FROM users WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        return $row['profile_pic'];
    }

    public function getFriendArray() {
        $username = $this->user['username'];
        $stmt = $this->con->prepare("SELECT friend_array FROM users WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        return $row['friend_array'];
    }

    public function isClosed() {
        $username = $this->user['username'];
        $stmt = $this->con->prepare("SELECT user_closed FROM users WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        return $row['user_closed'] === 'yes';
    }

    public function isFriend($username_to_check) {
        $usernameComma = "," . $username_to_check . ",";
        if (strstr($this->user['friend_array'], $usernameComma) || $username_to_check == $this->user['username']) {
            return true;
        }
        return false;
    }

    public function didReceiveRequest($user_from) {
        $user_to = $this->user['username'];
        $stmt = $this->con->prepare("SELECT * FROM friend_requests WHERE user_to = ? AND user_from = ?");
        $stmt->bind_param("ss", $user_to, $user_from);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->num_rows > 0;
    }

    public function didSendRequest($user_to) {
        $user_from = $this->user['username'];
        $stmt = $this->con->prepare("SELECT * FROM friend_requests WHERE user_to = ? AND user_from = ?");
        $stmt->bind_param("ss", $user_to, $user_from);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->num_rows > 0;
    }

    public function cancelRequest($user_to) {
        $user_from = $this->user['username'];
        $stmt = $this->con->prepare("DELETE FROM friend_requests WHERE user_to = ? AND user_from = ?");
        $stmt->bind_param("ss", $user_to, $user_from);
        $stmt->execute();
    }

    public function removeFriend($user_to_remove) {
        $logged_in_user = $this->user['username'];

        // Update friend array for the logged-in user
        $new_friend_array = str_replace($user_to_remove . ",", "", $this->user['friend_array']);
        $stmt = $this->con->prepare("UPDATE users SET friend_array = ? WHERE username = ?");
        $stmt->bind_param("ss", $new_friend_array, $logged_in_user);
        $stmt->execute();

        // Update friend array for the other user
        $stmt = $this->con->prepare("SELECT friend_array FROM users WHERE username = ?");
        $stmt->bind_param("s", $user_to_remove);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $friend_array_username = $row['friend_array'];

        $new_friend_array = str_replace($this->user['username'] . ",", "", $friend_array_username);
        $stmt = $this->con->prepare("UPDATE users SET friend_array = ? WHERE username = ?");
        $stmt->bind_param("ss", $new_friend_array, $user_to_remove);
        $stmt->execute();
    }

    public function sendRequest($user_to) {
        $user_from = $this->user['username'];
        $stmt = $this->con->prepare("INSERT INTO friend_requests (user_to, user_from) VALUES (?, ?)");
        $stmt->bind_param("ss", $user_to, $user_from);
        $stmt->execute();
    }

    public function getMutualFriends($user_to_check) {
        $mutualFriends = 0;
        $user_array = explode(",", $this->user['friend_array']);

        $stmt = $this->con->prepare("SELECT friend_array FROM users WHERE username = ?");
        $stmt->bind_param("s", $user_to_check);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $user_to_check_array = explode(",", $row['friend_array']);

        foreach ($user_array as $i) {
            if (in_array($i, $user_to_check_array) && $i != "") {
                $mutualFriends++;
            }
        }
        return $mutualFriends;
    }

    public function getFriendsList() {
        $friend_array_string = trim($this->user['friend_array'], ",");
        return explode(",", $friend_array_string);
    }
}
?>
