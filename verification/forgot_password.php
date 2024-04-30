<?php

require '../config/config.php';
require_once 'vendor/autoload.php'; // Load PHPMailer
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();


$msg = "";

if(isset($_POST['recover'])){
    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);

    // Using prepared statements
    if ($stmt = $con->prepare("SELECT last_name, vkey FROM users WHERE email = ? LIMIT 1")) {
      $stmt->bind_param("s", $email);
      $stmt->execute();
      $result = $stmt->get_result();

      if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $last_name = $row['last_name'];
        $vkey = $row['vkey'];
        
        $mail->isSMTP();
        $mail->Host = $_ENV['SMTP_HOST'];
        $mail->SMTPAuth = true;
        $mail->Username = $_ENV['SMTP_USER'];
        $mail->Password = $_ENV['SMTP_PASSWORD'];
        $mail->SMTPSecure = $_ENV['SMTP_SECURE'];
        $mail->Port = $_ENV['SMTP_PORT'];

        $mail->setFrom($_ENV['SMTP_FROM_EMAIL'], $_ENV['SMTP_FROM_NAME']);
        $mail->addAddress($email);

        $mail->isHTML(true);

        $mail->Subject = 'Forgot Password';
        $mail->Body    = "Dear {$last_name}, <br><br> To reset your password <a href='<a href='https://0cf7-38-42-234-45.ngrok-free.app/you_social_763/verification/reset_password.php?vkey={$vkey}'>Click Here</a>";
        $mail->AltBody = 'Please use the following link to verify your account: https://0cf7-38-42-234-45.ngrok-free.app/you_social_763/verification/reset_password.php?vkey=' . $vkey;
        $headers = "MIME-Version: 1.0" . "\r\n";
        $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";

        $mail->send();
        echo 'Please check your email for further Instructions';
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
                  <div class="panel-body">
    
                    <form id="register-form" role="form" autocomplete="off" class="form" method="post">
    
                      <div class="form-group">
                        <div class="input-group">
                          <span class="input-group-addon"><i class="glyphicon glyphicon-envelope color-blue"></i></span>
                          <input id="email" name="email" placeholder="Email" class="form-control"  type="email">
                        </div>
                      </div>
                      <div class="form-group">
                        <input name="recover" class="btn btn-lg btn-primary btn-block" value="Reset Password" type="submit">
                      </div>
                      
                      <input type="hidden" class="hide" name="token" id="token" value=""> 
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