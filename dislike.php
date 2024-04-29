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

        session_start(); // Ensure session start at the beginning

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
            exit();
        }

        if(isset($_GET['post_id'])) {
            $post_id = $_GET['post_id'];
        }

        $stmt = $con->prepare("SELECT dislikes, added_by FROM posts WHERE id=?");
        $stmt->bind_param("s", $post_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $total_dislikes = $row['dislikes'];
        $user_disliked = $row['added_by'];
        $stmt->close();

        $stmt = $con->prepare("SELECT * FROM users WHERE username=?");
        $stmt->bind_param("s", $user_disliked);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $total_user_dislikes = $row['num_dislikes'];
        $stmt->close();

        if(isset($_POST['dislike_button'])) {
            $total_dislikes++;
            $stmt = $con->prepare("UPDATE posts SET dislikes=? WHERE id=?");
            $stmt->bind_param("is", $total_dislikes, $post_id);
            $stmt->execute();
            $stmt->close();

            $total_user_dislikes++;
            $stmt = $con->prepare("UPDATE users SET num_dislikes=? WHERE username=?");
            $stmt->bind_param("is", $total_user_dislikes, $user_disliked);
            $stmt->execute();
            $stmt->close();

            $stmt = $con->prepare("INSERT INTO dislikes (user, post_id) VALUES (?, ?)");
            $stmt->bind_param("ss", $userLoggedIn, $post_id);
            $stmt->execute();
            $stmt->close();
        }

        if(isset($_POST['unlike_button'])) {
            $total_dislikes--;
            $stmt = $con->prepare("UPDATE posts SET dislikes=? WHERE id=?");
            $stmt->bind_param("is", $total_dislikes, $post_id);
            $stmt->execute();
            $stmt->close();

            $total_user_dislikes--;
            $stmt = $con->prepare("UPDATE users SET num_dislikes=? WHERE username=?");
            $stmt->bind_param("is", $total_user_dislikes, $user_disliked);
            $stmt->execute();
            $stmt->close();

            $stmt = $con->prepare("DELETE FROM dislikes WHERE username=? AND post_id=?");
            $stmt->bind_param("ss", $userLoggedIn, $post_id);
            $stmt->execute();
            $stmt->close();
        }

        $stmt = $con->prepare("SELECT * FROM dislikes WHERE username=? AND post_id=?");
        $stmt->bind_param("ss", $userLoggedIn, $post_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $num_rows = $result->num_rows;
        $stmt->close();

        if($num_rows > 0) {
            echo '<form action="dislike.php?post_id=' . $post_id . '" method="POST">
                    <input type="submit" class="comment_dislike" name="unlike_button" value="💔">
                    <div class="dislike_value">
                        '. $total_dislikes .' Likes
                    </div>
                </form>
            ';
        } else {
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
