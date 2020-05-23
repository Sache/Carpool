<?php
include('config.php');
if($_SERVER["REQUEST_METHOD"] == "POST"){
  $username = mysqli_real_escape_string($db,$_POST['username']);
  $mysql = "SELECT id,username,confirmed from `grepool` WHERE username ='$username'";
  $output = mysqli_query($db,$mysql);
  $user = mysqli_fetch_assoc($output);
  if(!empty($user))
  { 
    if($user["confirmed"] == '1')
    {
      $_SESSION["status"] = "error";
      $_SESSION["msg"] = "Your account is already activated.";
      header('Location: login.php'); 
      exit();
    }
    else
    {
      $confirmcode = mt_rand(10000,99999);
      $update_query = "UPDATE `grepool` SET confirmcode = '".$confirmcode."' where id = '".$user["id"]."' ";
      $update_result = mysqli_query($db,$update_query);
      mysqli_close($db);
          $message =
    "Confirm Your Account \n
    Enter the Verification code: $confirmcode or \n
    Click the Link Below to Verify Your Account\n
    http://stuweb.cms.gre.ac.uk/~sk9699a/emailconfirm.php?username=$username&code=$confirmcode
    ";
    mail ($email,"Greenwich Carpool Confirm Email",$message, "From: donotreply@greenwich.ac.uk ");
      $_SESSION["status"] = "success";
      $_SESSION["msg"] = "You have regenerated verification code successfully.Check email to verify your account.";
      header('Location: emailconfirm.php'); 
      exit();   
    }
  }
  else
  {
    $_SESSION["status"] = "error";
    $_SESSION["msg"] = "Invalid username.Please try again.";
    header('Location: resebdverification.php'); 
    exit();
  }
}
?>
<!DOCTYPE html>
<html lang="en-GB">
<head>
  <title>Resend Verification Code</title>
  <meta charset="UTF-8">
  <meta name="description" content="">
  <meta name="keywords" content="">
  <?php include("head.php"); ?>
  <style>
  body{
    background-color: black;
  }
</style>
</head>
<body>
  <?php include("header.php"); ?>
  <form action = "" method = "POST">
    <h3 class="regis">Verification regenerate</h3>
    <p class="info">Please enter username to generate code again</p>
    <?php 
    if(isset($_SESSION["msg"]) && $_SESSION["msg"] != '')
    {
      $msg  = $_SESSION["msg"];
      echo '<p class="errorinfo">'.$msg.'</p>';
      unset($_SESSION['status']);
      unset($_SESSION['msg']);
    }
    ?>
    <input type="text" name = "username" placeholder = "Enter Username" required>
    <br/>
    <input type="submit" value="Submit">
  </form>
</body>
</html>