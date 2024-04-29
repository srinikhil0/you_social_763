<?php

require '../config/config.php';
session_start(); // Start the session to use $_SESSION

$msg = "";

if(isset($_POST['set'])){
    if(isset($_GET['vkey'])){
        $vkey = $_GET['vkey'];
        $password1 = $_POST['password1'];

        // Hash the password securely
        $passwordHashed = password_hash($password1, PASSWORD_BCRYPT);

        // Use prepared statements to prevent SQL Injection
        if ($stmt = $con->prepare("UPDATE users SET pass = ? WHERE vkey = ?")) {
            $stmt->bind_param("ss", $passwordHashed, $vkey);
            $stmt->execute();
            
            // Check if the password update was successful
            if($stmt->affected_rows > 0){
                header('Location: ../resetpassword.html');
            } else {
                header('Location: reset_password.php');
            }
            $stmt->close();
        } else {
            // Handle potential SQL error (if prepare fails)
            $msg = "Failed to update password. Please try again.";
        }
    }
}
?>

<!DOCTYPE html>
<html>
    <head>
        <title>Reset Password</title>
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
        <script src="//code.jquery.com/jquery-1.11.1.min.js"></script>
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.5.0/css/font-awesome.min.css">
        <link href="../assets/css/bootstrap.min.css" rel="stylesheet" id="bootstrap-css">
        <script src="//maxcdn.bootstrapcdn.com/bootstrap/3.3.0/js/bootstrap.min.js"></script>
    </head>
<body>
<div class="form-gap"></div>
<div class="container">
    <div class="row">
        <div class="col-md-4 col-md-offset-4">
            <div class="panel panel-default">
              <div class="panel-body">
                <div class="text-center">
                  <h3><i class="fa fa-lock fa-4x"></i></h3>
                  <h2 class="text-center">Forgot Password?</h2>
                  <p>You can reset your password here.</p>
                  <p> <?= $msg ?> </p>
                  <div class="panel-body">
                    <form id="register-form" role="form" autocomplete="off" class="form" method="post">
                      <div class="form-group">
                        <div class="input-group">  
                          <span class="input-group-addon"><i class="fa fa-key color-blue" aria-hidden="true"></i></span>
                          <input id="password" name="password1" placeholder="New Password" class="form-control"  type="password">
                        </div>
                      </div>
                      <div class="form-group">
                        <input name="set" class="btn btn-lg btn-primary btn-block" value="Set" type="submit">
                      </div>
                    </form>
                  </div>
                </div>
              </div>
            </div>
          </div>
    </div>
</div>
</body>
</html>
