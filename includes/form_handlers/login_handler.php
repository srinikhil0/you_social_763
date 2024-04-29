<?php  

require 'includes/form_handlers/register_handler.php';

if(isset($_POST['log_btn'])) {
 
    $email = filter_var($_POST['log_email'], FILTER_SANITIZE_EMAIL); //sanitize email
    $_SESSION['log_email'] = $email; //Store email into session variable 
    $pass = $_POST['log_password']; //Get password

    // Using prepared statements to ensure safe SQL execution
    $stmt = $con->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    $check_login_query = $result->num_rows;
 
    if($check_login_query == 1) {
        while($row = $result->fetch_assoc()) {
            if(password_verify($pass, $row['pass'])) {
                $name = $row['username'];
 
                // Checking if user's account is closed and reopening it
                $stmt_user_closed = $con->prepare("SELECT * FROM users WHERE email = ? AND user_closed = 'yes'");
                $stmt_user_closed->bind_param("s", $email);
                $stmt_user_closed->execute();
                $result_user_closed = $stmt_user_closed->get_result();

                if($result_user_closed->num_rows == 1) {
                    $stmt_reopen_account = $con->prepare("UPDATE users SET user_closed = 'no' WHERE email = ?");
                    $stmt_reopen_account->bind_param("s", $email);
                    $stmt_reopen_account->execute();
                    $stmt_reopen_account->close();
                }

                $_SESSION['username'] = $name;
                echo "Name: " .$name;
                header("Location: profile/profiledetails1.php");
                exit();
            }
        }
        $stmt_user_closed->close();
        
    } else {
        echo "Email or password was incorrect<br>";
    }

    $stmt->close();
 
}
 
?>
