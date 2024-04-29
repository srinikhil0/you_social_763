<?php

$msg = "";

$con = mysqli_connect("localhost", "root", "", "students_asylum"); //connection variable

if(isset($_POST['set'])){

    if(isset($_GET['vkey'])){
        $vkey = $_GET['vkey'];
        $password1 = $_POST['password1']; //Get password1
        $password1 = password_hash($password1, PASSWORD_BCRYPT);
        // $password2 = $_POST['password2']; //Get password2
        // $password2 = password_hash($password2, PASSWORD_BCRYPT);

        // if(password_verify($password1, $password2)){
            $update_query = mysqli_query($con, "UPDATE users set pass = '$password1' WHERE vkey = '$vkey'");

            if($update_query){
                header('location: ../resetpassword.html');
            }
            else{
                header('location: reset_password.php');
            }
        // }
        // else{
        //     $_SESSION['passmsg'] = "Passwords are not matching. Please try again";
        // }

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
                  <p> <?php if(isset($_SESSION['passmsg'])) {echo $_SESSION['passmsg'];}else{echo $_SESSION['passmsg'] = "";} ?> </p>
                  <div class="panel-body">
    
                    <form id="register-form" role="form" autocomplete="off" class="form" method="post">
    
                      <div class="form-group">
                        <div class="input-group">  
                          <span class="input-group-addon"><i class="fa fa-key color-blue" aria-hidden="true"></i></span>
                          <input id="password" name="password1" placeholder="New Password" class="form-control"  type="password">
                          <!-- <input id="password" name="password2" placeholder="Repeat Password" class="form-control"  type="password"> -->
                        </div>
                      </div>
                      <div class="form-group">
                        <input name="set" class="btn btn-lg btn-primary btn-block" value="set" type="submit">
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