<?php
class Notification {
    private $user_obj;
    private $con;

    public function __construct($con, $user) {
        $this->con = $con;
        $this->user_obj = new User($con, $user);
    }

    public function getUnreadNumber() {
        $userLoggedIn = $this->user_obj->getUsername();
        $stmt = $this->con->prepare("SELECT * FROM notifications WHERE viewed='no' AND user_to=?");
        $stmt->bind_param("s", $userLoggedIn);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->num_rows;
    }

    public function getNotifications($data, $limit) {
        $page = $data['page'];
        $userLoggedIn = $this->user_obj->getUsername();
        $return_string = "";

        if ($page == 1)
            $start = 0;
        else 
            $start = ($page - 1) * $limit;

        // Set notifications as viewed
        $stmt = $this->con->prepare("UPDATE notifications SET viewed='yes' WHERE user_to=?");
        $stmt->bind_param("s", $userLoggedIn);
        $stmt->execute();

        // Get notifications
        $stmt = $this->con->prepare("SELECT * FROM notifications WHERE user_to=? ORDER BY id DESC LIMIT ?, ?");
        $stmt->bind_param("sii", $userLoggedIn, $start, $limit);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows == 0) {
            echo "You have no notifications!";
            return;
        }

        while ($row = $result->fetch_assoc()) {
            $user_from = $row['user_from'];
            $user_data_query = $this->con->prepare("SELECT profile_pic FROM users WHERE username=?");
            $user_data_query->bind_param("s", $user_from);
            $user_data_query->execute();
            $user_data = $user_data_query->get_result()->fetch_assoc();

            $time_message = $this->formatTimeMessage(new DateTime($row['datetime']));

            $opened = $row['opened'];
            $style = ($opened == 'no') ? "background-color: #DDEDFF;" : "";

            $return_string .= "<a href='" . $row['link'] . "'> 
                                    <div class='resultDisplay resultDisplayNotification' style='" . $style . "'>
                                        <div class='notificationsProfilePic'>
                                            <img src='" . $user_data['profile_pic'] . "'>
                                        </div>
                                        <p class='timestamp_smaller' id='grey'>" . $time_message . "</p>" . $row['message'] . "
                                    </div>
                                </a>";
        }

        if ($result->num_rows >= $limit)
            $return_string .= "<input type='hidden' class='nextPageDropdownData' value='" . ($page + 1) . "'><input type='hidden' class='noMoreDropdownData' value='false'>";
        else 
            $return_string .= "<input type='hidden' class='noMoreDropdownData' value='true'> <p style='text-align: center;'>No more notifications to load!</p>";

        return $return_string;
    }

    private function formatTimeMessage($datetime) {
        $now = new DateTime();
        $interval = $datetime->diff($now);

        if ($interval->y >= 1) {
            return $interval->y . ($interval->y == 1 ? " year ago" : " years ago");
        } elseif ($interval->m >= 1) {
            return $interval->m . ($interval->m == 1 ? " month ago" : " months ago");
        } elseif ($interval->d >= 1) {
            return $interval->d . ($interval->d == 1 ? " day ago" : " days ago");
        } elseif ($interval->h >= 1) {
            return $interval->h . ($interval->h == 1 ? " hour ago" : " hours ago");
        } elseif ($interval->i >= 1) {
            return $interval->i . ($interval->i == 1 ? " minute ago" : " minutes ago");
        } else {
            return $interval->s < 30 ? "Just now" : $interval->s . " seconds ago";
        }
    }

    public function insertNotification($post_id, $user_to, $type) {
        $userLoggedIn = $this->user_obj->getUsername();
        $date_time = date("Y-m-d H:i:s");
        $message = $this->getMessageType($type);
        $link = "post.php?id=" . $post_id;

        $stmt = $this->con->prepare("INSERT INTO notifications (user_to, user_from, message, link, datetime, viewed, opened) VALUES (?, ?, ?, ?, ?, 'no', 'no')");
        $stmt->bind_param("sssss", $user_to, $userLoggedIn, $message, $link, $date_time);
        $stmt->execute();
    }

    private function getMessageType($type) {
        $userLoggedInName = $this->user_obj->getUsername();
        switch ($type) {
            case 'comment':
                return $userLoggedInName . " commented on your post";
            case 'like':
                return $userLoggedInName . " liked your post";
            case 'profile_post':
                return $userLoggedInName . " posted on your profile";
            case 'comment_non_owner':
                return $userLoggedInName . " commented on a post you commented on";
            case 'profile_comment':
                return $userLoggedInName . " commented on your profile post";
            default:
                return "Notification";
        }
    }
}
?>
