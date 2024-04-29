<?php 
require '../../config/config.php';

$userLoggedIn = $_SESSION["username"];

if(isset($_GET['post_id'])) {
    $post_id = $_GET['post_id'];
}

if(isset($_POST['result'])) {
    if($_POST['result'] == 'true') {
        // Prepare statement to mark the post as deleted
        $stmt = $con->prepare("UPDATE posts SET deleted='yes' WHERE id = ?");
        $stmt->bind_param("i", $post_id); // 'i' specifies the variable type => 'integer'
        $stmt->execute();
        $stmt->close();

        // Prepare statement to decrement the post count
        $stmt = $con->prepare("UPDATE users SET num_posts=num_posts-1 WHERE username = ?");
        $stmt->bind_param("s", $userLoggedIn); // 's' specifies the variable type => 'string'
        $stmt->execute();
        $stmt->close();
    }
}
?>
