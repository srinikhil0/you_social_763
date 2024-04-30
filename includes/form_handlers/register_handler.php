<?php

require_once 'vendor/autoload.php'; // Load PHPMailer
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

function sendVerificationEmail($email, $vkey) {
    $mail = new PHPMailer(true);
    try {
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
        $mail->Subject = 'Account Verification';
        $mail->Body    = "Please click on the following link to verify your account: <a href='https://0cf7-38-42-234-45.ngrok-free.app/you_social_763/verification/verify.php?vkey=$vkey'>Verify Account</a>";
        $mail->AltBody = 'Please use the following link to verify your account: https://0cf7-38-42-234-45.ngrok-free.app/you_social_763/verification/verify.php?vkey=' . $vkey;

        $mail->send();
        echo 'Verification email has been sent.';
    } catch (Exception $e) {
        echo 'Verification email could not be sent. Mailer Error: ' . $mail->ErrorInfo;
    }
}


//Declarig variables to prevent errors
$first_name = ""; //first name
$last_name = ""; //last name
$username = ""; //username
$email = ""; //email
$pass = ""; //pass
$profile_pic = ""; //profile pic
$error_array = array();//holds error messages

if(isset($_POST['sign_btn'])) {

  //Registration form values

  //Username
  $username = mysqli_real_escape_string($con, htmlspecialchars(trim($_POST['sign_username']))); //Remove html, SQL tags
  $_SESSION['sign_username']=$username;//Stores user name into session variables

  //First Name
  $first_name = ucfirst(strtolower(trim(strip_tags($_POST['sign_firstname'])))); //Remove html tags
	$_SESSION['sign_firstname'] = $first_name; //Stores first name into session variable

	//Last name
	$last_name = ucfirst(strtolower(trim(strip_tags($_POST['sign_lastname'])))); //Remove html tags
	$_SESSION['sign_lastname'] = $last_name; //Stores last name in to session variable

  //Email
  $email = filter_var(strtolower(trim(strip_tags($_POST['sign_email'], FILTER_VALIDATE_EMAIL)))); //remove html tags
  $_SESSION['sign_email']=$email;//Stores email into session variables

  //password
  $pass = mysqli_real_escape_string($con, trim($_POST['sign_password'])); //remove html tags

  //Date
  $date = date("Y-m-d H:i:s");

  // Check for HTML special characters in inputs
  $inputs = [$username, $first_name, $last_name, $email, $pass];
  foreach ($inputs as $input) {
    if(preg_match('/[<>&"\'\/]/', $input)) {
      array_push($error_array, "Illegal characters are not allowed.");
      echo "Illegal characters are not allowed. <br>";
      break; // Stop checking further to avoid multiple similar errors
    }
    else
    {
      //Generate Verfication KEY (VKEY)
      $vkey=md5(time().$username); 

      //Profile Pic
      $profile_pic = "/you_social_763/assets/images/svg/default_profile_pic.svg";

      //Check if email already exists
      if (empty($error_array)) {
        $stmt = $con->prepare("SELECT email FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();
        
        if (! $stmt->execute()) {
            array_push($error_array, "Email already in use");
            echo "An account is tied up with this email, try logging in";
        }
        $stmt->close();
    }
      
      //Password Rules  
      if(strlen($username)>25 || strlen($username)<3){
        array_push($error_array,"Your username must be between 3 and 25 characters <br>");
        echo "Your username must be between 3 and 25 characters <br>";
      }
      if(strlen($first_name) > 25 || strlen($first_name) < 3) {
        array_push($error_array, "Your first name must be between 3 and 25 characters<br>");
      }
      if(strlen($last_name) > 25 || strlen($last_name) < 3) {
        array_push($error_array,  "Your last name must be between 3 and 25 characters<br>");
      }
      if(!preg_match("/\d/", $pass)){
        array_push($error_array, "Your password cannot contain any special chatacters <br>");
        echo "Your password must contain at least one digit <br>";
      }
      if(!preg_match("/[A-Z]/", $pass)){
        array_push($error_array, "Your password cannot contain any special chatacters <br>");
        echo "Your password must contain at least one Capital Letter <br>";
      }
      if(!preg_match("/[a-z]/", $pass)){
        array_push($error_array, "Your password cannot contain any special chatacters <br>");
        echo "Your password must contain at least one small Letter <br>";
      }
      if(!preg_match("/\W/", $pass)){
        array_push($error_array, "Your password cannot contain any special chatacters <br>");
        echo "Your password must contain at least one special character<br>";
      }
      if(preg_match("/\s/", $pass)){
        array_push($error_array, "Your password cannot contain any special chatacters <br>");
        echo "Your password must not contain any space<br>";
      }
      if(strlen($pass)>30 || strlen($pass)<8){
        array_push($error_array, "Your password must be between 8 and 30 characters <br>");
        echo "Your password must be between 8 and 30 characters <br>";
      }

      //Check if username already exists
      $check_username_query=mysqli_query($con,"SELECT username FROM users WHERE username='$username'");

      //Check username already exists or not
      while(mysqli_num_rows($check_username_query) != 0){
        array_push($error_array, "Username already exist. Please try another username");
        echo "Username already exist. Please try another username<br>";
      }

      if ($username == trim($username) && strpos($username, ' ') !== false){
        array_push($error_array, "No Spaces are allowed");
        echo "No Spaces are allowed in username<br>";
      }

      if(empty($error_array)){
        $pass = password_hash($pass, PASSWORD_DEFAULT);  //Encrypt password before sending to database
        // Prepare the INSERT statement
        $stmt = $con->prepare("INSERT INTO users (username, first_name, last_name, email, pass, profile_pic, date, vkey, verified, num_posts, num_likes, num_dislikes, user_closed, friend_array) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $verified = 0;
        $num_posts = 0;
        $num_likes = 0;
        $num_dislikes = 0;
        $user_closed = 'no';
        $friend_array = ''; // Assuming friend_array is a string or whatever default value
    
        // Bind parameters
        $stmt->bind_param('ssssssssiiisss', $username, $first_name, $last_name, $email, $pass, $profile_pic, $date, $vkey, $verified, $num_posts, $num_likes, $num_dislikes, $user_closed, $friend_array);
    
        // Execute the statement
        if ($stmt->execute()) {
            // Only send email if the user was successfully added to the database
            //send email verification
            sendVerificationEmail($email, $vkey);
            // Properly clear session variables or redirect as necessary
            $_SESSION['sign_username'] = "";
            $_SESSION['sign_email'] = "";
            $_SESSION['sign_pass'] = "";
            header("Location: emailverification.html"); // Redirect to prevent resubmission
            exit(); // Ensure no further code is executed after redirection
        } else {
            echo "Failed to register. Please try again.<br>";
        }
        $stmt->close();
      }   
      
    }
  }
	
}

if(isset($_POST['log_btn'])) {
  $email = filter_var($_POST['log_email'], FILTER_SANITIZE_EMAIL);
  $_SESSION['log_email'] = $email;
  $pass = $_POST['log_password'];

  $stmt = $con->prepare("SELECT id, username, pass FROM users WHERE email = ?");
  $stmt->bind_param("s", $email);
  $stmt->execute();
  $result = $stmt->get_result();

  if($result->num_rows == 1) {
      $row = $result->fetch_assoc();
      if(password_verify($pass, $row['pass'])) {
          $username = $row['username'];
          setcookie('emailbun', $email, time() + 31536000);
          setcookie('passbun', $pass, time() + 31536000);

          $stmt = $con->prepare("SELECT * FROM users WHERE email = ? AND user_closed = 'yes'");
          $stmt->bind_param("s", $email);
          $stmt->execute();
          $result = $stmt->get_result();

          if($result->num_rows == 1) {
              $stmt = $con->prepare("UPDATE users SET user_closed = 'no' WHERE email = ?");
              $stmt->bind_param("s", $email);
              $stmt->execute();
          }

          $_SESSION['username'] = $username;
          echo "Username: " . $username;
          header("Location: index.php");
          exit();
      } else {
          echo "Email or password was incorrect<br>";
      }
  } else {
      echo "Email or password was incorrect<br>";
  }
}
 
?>