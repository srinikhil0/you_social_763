<?php
require '../config/config.php'; // Ensure this config file sets up $con appropriately.

// session_start(); // Start a session if you are going to use session variables post verification.

if (isset($_GET['vkey'])) {
    $vkey = $_GET['vkey'];

    // Prepared statement to prevent SQL Injection
    $stmt = $con->prepare("SELECT verified, vkey FROM users WHERE verified = 0 AND vkey = ? LIMIT 1");
    $stmt->bind_param("s", $vkey);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 1) {
        // Validate email
        $updateStmt = $con->prepare("UPDATE users SET verified = 1 WHERE vkey = ? LIMIT 1");
        $updateStmt->bind_param("s", $vkey);
        $updateStmt->execute();

        if ($updateStmt->affected_rows == 1) {
            echo "<p>Your account has been verified, you may now login.</p>";
            header("refresh:5;url=../register.php");
            exit();
        } else {
            echo "Error updating record: " . mysqli_error($con);
        }
    } else {
        echo "<p>This account is invalid or already verified.</p>";
    }
} else {
    die("Something went wrong");
}
?>

<html>
<head>
    <title>Verification Status</title>
</head>
<body>
    
</body>
</html>
