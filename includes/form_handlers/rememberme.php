<?php 

if(isset($_POST['rememberme'])){
    setcookie('emailcookie', $email, time()+86400);
    setcookie('passwordcookie', $password, time()+86400);
    $_SESSION['email'] = "";
}
else{
    $_SESSION['email'] = "";
}

?>