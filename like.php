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
    include("includes/classes/Notification.php");

    if (isset($_SESSION['username'])) {
        $userLoggedIn = $_SESSION['username'];
        $stmt = $con->prepare("SELECT * FROM users WHERE username = ?");
        $stmt->bind_param("s", $userLoggedIn);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();
    } else {
        header("Location: register.php");
    }

    // Get id of post
    if(isset($_GET['post_id'])) {
        $post_id = $_GET['post_id'];
    }

    $stmt = $con->prepare("SELECT likes, added_by FROM posts WHERE id = ?");
    $stmt->bind_param("i", $post_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    $total_likes = $row['likes']; 
    $user_liked = $row['added_by'];

    $stmt = $con->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->bind_param("s", $user_liked);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    $total_user_likes = $row['num_likes'];

    // Like button
    if(isset($_POST['like_button'])) {
        $total_likes++;
        $stmt = $con->prepare("UPDATE posts SET likes = ? WHERE id = ?");
        $stmt->bind_param("ii", $total_likes, $post_id);
        $stmt->execute();

        $total_user_likes++;
        $stmt = $con->prepare("UPDATE users SET num_likes = ? WHERE username = ?");
        $stmt->bind_param("is", $total_user_likes, $user_liked);
        $stmt->execute();

        $stmt = $con->prepare("INSERT INTO likes VALUES('', ?, ?)");
        $stmt->bind_param("si", $userLoggedIn, $post_id);
        $stmt->execute();

        // Insert Notification
        if($user_liked != $userLoggedIn){
            $notification = new Notification($con, $userLoggedIn);
            $notification->insertNotification($post_id, $user_liked, "like");
        }
    }

    // Unlike button
    if(isset($_POST['unlike_button'])) {
        $total_likes--;
        $stmt = $con->prepare("UPDATE posts SET likes = ? WHERE id = ?");
        $stmt->bind_param("ii", $total_likes, $post_id);
        $stmt->execute();

        $total_user_likes--;
        $stmt = $con->prepare("UPDATE users SET num_likes = ? WHERE username = ?");
        $stmt->bind_param("is", $total_user_likes, $user_liked);
        $stmt->execute();

        $stmt = $con->prepare("DELETE FROM likes WHERE username = ? AND post_id = ?");
        $stmt->bind_param("si", $userLoggedIn, $post_id);
        $stmt->execute();
    }

    // Check for previous likes
    $stmt = $con->prepare("SELECT * FROM likes WHERE username = ? AND post_id = ?");
    $stmt->bind_param("si", $userLoggedIn, $post_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $num_rows = $result->num_rows;

    if($num_rows > 0) {
        echo '<form action="like.php?post_id=' . $post_id . '" method="POST">
                <input type="submit" class="comment_like" name="unlike_button" value="💖">
                <div class="like_value">
                    '. $total_likes .' Likes
                </div>
            </form>
        ';
    }
    else {
        echo '<form action="like.php?post_id=' . $post_id . '" method="POST">
                <input type="submit" class="comment_like" name="like_button" value="💖"></input>
                <div class="like_value">
                    '. $total_likes .' Likes
                </div>
            </form>
        ';
    }
    ?>
</body>
</html>
