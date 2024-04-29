<?php  
    require 'config/config.php';
    include("includes/classes/User.php");
    include("includes/classes/Post.php");
    include("includes/classes/Notification.php");

    if (isset($_SESSION['username'])) {
        $userLoggedIn = $_SESSION['username'];
        $stmt = $con->prepare("SELECT * FROM users WHERE username=?");
        $stmt->bind_param("s", $userLoggedIn);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();
        $stmt->close();
    } else {
        header("Location: register.php");
    }
?>

<html>
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title></title>
    <link rel="stylesheet" type="text/css" href="assets/css/header.css">
</head>
<body>

    <style type="text/css">
    * {
        font-size: 12px;
        font-family: Arial, Helvetica, Sans-serif;
    }
    </style>

    <script>
        function toggle() {
            var element = document.getElementById("comment_section");

            if(element.style.display == "block") 
                element.style.display = "none";
            else 
                element.style.display = "block";
        }
    </script>

    <?php  
    if(isset($_GET['post_id'])) {
        $post_id = $_GET['post_id'];
        $stmt = $con->prepare("SELECT added_by, user_to FROM posts WHERE id = ?");
        $stmt->bind_param("s", $post_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $posted_to = $row['added_by'];
        $user_to = $row['user_to'];
        $stmt->close();
    }

    if(isset($_POST['postComment' . $post_id])) {
        $post_body = $_POST['post_body'];
        $post_body = mysqli_real_escape_string($con, $post_body);
        $date_time_now = date("Y-m-d H:i:s");
        $stmt = $con->prepare("INSERT INTO comments (post_body, posted_by, posted_to, date_added, removed, post_id) VALUES (?, ?, ?, ?, 'no', ?)");
        $stmt->bind_param("sssss", $post_body, $userLoggedIn, $posted_to, $date_time_now, $post_id);
        $stmt->execute();
        $stmt->close();

        if($posted_to != $userLoggedIn){
            $notification = new Notification($con, $userLoggedIn);
            $notification->insertNotification($post_id, $posted_to, "comment");
        }
        
        if($user_to != 'none' && $user_to != $userLoggedIn){
            $notification = new Notification($con, $userLoggedIn);
            $notification->insertNotification($post_id, $user_to, "profile_comment");
        }

        $notified_users = array();
        $stmt = $con->prepare("SELECT posted_by FROM comments WHERE post_id = ?");
        $stmt->bind_param("s", $post_id);
        $stmt->execute();
        $result = $stmt->get_result();
        while($row = $result->fetch_assoc()){
            if($row['posted_by'] != $posted_to && $row['posted_by'] != $user_to
                && $row['posted_by'] != $userLoggedIn && !in_array($row['posted_by'], $notified_users)){
                    $notification = new Notification($con, $userLoggedIn);
                    $notification->insertNotification($post_id, $row['posted_by'], "comment_non_owner");

                    array_push($notified_users, $row['posted_by']);
            }
        }
        $stmt->close();

        echo "<p>Comment Posted! </p>";
    }
    ?>

    <form action="comment_frame.php?post_id=<?php echo $post_id; ?>" id="comment_form" name="postComment<?php echo $post_id; ?>" method="POST">
        <textarea name="post_body"></textarea>
        <input type="submit" name="postComment<?php echo $post_id; ?>" value="Post">
    </form>

    <!-- Load comments -->
    <?php  
    $stmt = $con->prepare("SELECT * FROM comments WHERE post_id = ? ORDER BY id ASC");
    $stmt->bind_param("s", $post_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $count = $result->num_rows;

    if($count != 0) {
        while($comment = $result->fetch_assoc()) {
            $comment_body = $comment['post_body'];
            $posted_to = $comment['posted_to'];
            $posted_by = $comment['posted_by'];
            $date_added = $comment['date_added'];
            $removed = $comment['removed'];
            include("includes/timeframe_function.php"); // assuming you have a timeframe function to handle the time message
            $time_message = timeframe($date_added);

            $user_obj = new User($con, $posted_by);
            ?>
            <div class="comment_section">
                <a href="<?php echo $posted_by?>" target="_parent"> <b> <?php echo $user_obj->getUsername(); ?> </b></a>
                &nbsp;&nbsp;&nbsp;&nbsp; <?php echo $time_message . "<br>" . $comment_body; ?> 
                <hr>
            </div>
            <?php
        }
    } else {
        echo "<center><br><br>No Comments to Show!</center>";
    }
    $stmt->close();
    ?>

</body>
</html>
