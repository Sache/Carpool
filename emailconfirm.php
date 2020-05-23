<?php
require 'config.php';

if (!isset($_COOKIE["user_name"]))
{
header("location:login.php");
exit();
}

  $user = $_COOKIE["user_name"];
  $check_user_query = "SELECT * FROM grepool WHERE confirmed = 0 and username = '$user'";
  $query_result = mysqli_query($db,$check_user_query);
  $loggedin = mysqli_fetch_assoc($query_result);
  $valid  =  mysqli_num_rows($query_result);
  if (!$valid)
  {
  header("location:route.php");
  exit();   
  }


if ($_SERVER["REQUEST_METHOD"] == "POST"){
  $veri = ($_POST['verify']);
  $username = ($_COOKIE["user_name"]);
  $check_user_query = "SELECT * FROM grepool  where confirmcode = '$veri' and username = '$username'";
  $query_result = mysqli_query($db,$check_user_query);
  $loggedin = mysqli_fetch_assoc($query_result);
  $valid  =  mysqli_num_rows($query_result);
  if ($valid == 1)
  {
      
    $update_query = "UPDATE grepool SET confirmed = 1 WHERE username = '$username'";
    mysqli_query($db,$update_query);
    $_SESSION["status"] = "success";
    $_SESSION["msg"] = "Your account verified successfully.";
    $_SESSION["user_id"] = $loggedin["id"];
    $_SESSION["is_loggedin"] = "yes";
    header("location:route.php");
    exit();
  } 
  else 
  {
    $_SESSION["status"] = "error";
    $_SESSION["msg"] = "Invalid Username / verification code.Please try again.";
    header('Location: emailconfirm.php'); 
    exit();
  }
}
?>
<!DOCTYPE html>
<html lang="en-GB">
<head>
  <title>Greenwich Carpool - Emain Confirmation</title>
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
    <h3 class="regis">Verification</h3>
    <?php 
    if(isset($_SESSION["msg"]) && $_SESSION["msg"] != '')
    {
      $msg  = $_SESSION["msg"];
      echo '<p class="errorinfo">'.$msg.'</p>';
      unset($_SESSION['status']);
      unset($_SESSION['msg']);
    }
    ?>
    <p class="info">Please Verify your account with the code provided</p>

    <div>
      <?php echo "<h3>" . " Your Username : " . $_COOKIE["user_name"] . "</h3>" ?>
    </div>

    <div>
      <input type="text" name = "verify" placeholder="Enter Verification Code" required> <br/><br/>
    </div>
    <a class="verifytag" href="<?php echo SITE_URL; ?>resendverification.php">Resend Verification Code?</a>
    <input type="submit" value="Submit">
  </form>
  <script type="text/javascript">
    document.getElementById("username").focus();
  </script>
</body>
</html>
