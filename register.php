<?php
require 'config/config.php';
require 'includes/form_handlers/register_handler.php';

?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
   <script
      src="https://kit.fontawesome.com/64d58efce2.js"
      crossorigin="anonymous"
    ></script>
    <link rel="stylesheet" href="assets/css/register.css" />
    <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js" type="text/javascript"></script>
    <title>Sign in & Sign up Form</title>
  </head>
<body>
<div class="container">
      <div class="forms-container">
        <div class="signin-signup">
          <form action="register.php" method="POST" class="sign-in-form">
            <h2 class="title">Sign in</h2>
            <div class="input-field">
              <i class="fas fa-user"></i>
              <input type="text" name="log_email" placeholder="Email" />
            </div>
            <div class="input-field">
              <i class="fas fa-lock"></i>
              <input name="log_password" id="password" type="password" placeholder="Password" />
              <i style="cursor: pointer; margin-left:60px;" id="togglePassword" class="fas fa-eye"></i>
            </div>
            <input name="log_btn" type="submit" value="Login" class="btn solid" />
            <a href="verification/forgot_password.php" style="text-decoration: none; color:black;">Forgot Password?</a>
            </form>


          <form action="register.php" method="POST" class="sign-up-form" id="signup">
            <h2 class="title">Sign up</h2>
            <div class="input-field">
              <i class="fas fa-user"></i>
              <input type="text" name="sign_username" placeholder="Username" />
            </div>
            <div class="input-field">
              <i class="fas fa-signature"></i>
              <input type="text" name="sign_firstname" placeholder="First Name" />
            </div>
            <div class="input-field">
              <i class="fas fa-signature"></i>
              <input type="text" name="sign_lastname" placeholder="Last Name" />
            </div>
            <div class="input-field">
              <i class="fas fa-envelope"></i>
              <input type="email" name="sign_email" placeholder="Email" />
            </div>
            <div class="input-field">
              <i class="fas fa-lock"></i>
              <input name="sign_password" id="password1" type="password" placeholder="Password" >
              <i style="cursor: pointer; margin-left:60px;" id="togglePassword1" class="fas fa-eye"></i>
            </div>
            <input type="submit" name="sign_btn" class="btn" value="Sign up" />
          </form>
        </div>
      </div>

      <div class="panels-container">
        <div class="panel left-panel">
          <div class="content">
            <h3>New here ?</h3>
            <p>
              We are excited to Welcome you
            </p>
            <button class="btn transparent" id="sign-up-btn">
              Sign up
            </button>
          </div>
          <img src="assets/images/svg/register.svg" class="image" alt="" />
        </div>
        <div class="panel right-panel">
          <div class="content">
            <h3>One of us ?</h3>
            <p>
              Hey buddy! Welcome back 
            </p>
            <button class="btn transparent" id="sign-in-btn">
              Sign in
            </button>
          </div>
          <img src="assets/images/svg/register2.svg" class="image" alt="" />
        </div>
      </div>
    </div>
  <script type="text/javascript" src="assets/js/register.js"></script>
  <!-- <script src="https://apis.google.com/js/platform.js" async defer></script>   -->
  <!-- <style type="text/css">
    body {
      overflow-x: hidden;
      overflow-y: hidden;
    }
  </style> -->
</body>
</html>