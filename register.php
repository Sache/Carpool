<?php
//include('captcha.php');
include('config.php');
if(isset($_COOKIE["user_name"]))
{
header('Location: route.php'); 
exit();    
}
if($_SERVER["REQUEST_METHOD"] == "POST"){
  $email = mysqli_real_escape_string($db,$_POST['email']);
  $username = mysqli_real_escape_string($db,$_POST['username']);
  $password = mysqli_real_escape_string($db,$_POST['password']);
  if (mysqli_connect_errno())
  {
    echo "Flop" . mysqli_connect_error();
  }
if(isset($_POST["captchain"])&&$_POST["captchain"]!=""&&$_SESSION["code"]==$_POST["captchain"])
{

  $e_password = md5($password);
  $confirmcode = mt_rand(10000,99999);
  $user_exist_query = "SELECT `username` FROM `grepool` WHERE `username` = '$username'";
  $user_result = mysqli_query($db,$user_exist_query);
  if(mysqli_num_rows($user_result) == 0)
  {  
    $insert_query = "INSERT INTO grepool (username, password, email, confirmcode) VALUES ('$username','$e_password','$email','$confirmcode')";
    $insert_result = mysqli_query($db,$insert_query);
    mysqli_close($db);
    $message =
    "Confirm Your Account: $username \n
    Enter the Verification code: $confirmcode or \n
    Click the Link Below to Verify Your Account\n
    http://stuweb.cms.gre.ac.uk/~sk9699a/login.php";
    mail ($email,"Greenwich Carpool Confirm Email",$message, "From: donotreply@greenwich.ac.uk ");
    
    //$_SESSION["user_name"] = $username;  
    setcookie("user_name", $username, time() + 3600, "/"); 
    $_SESSION["status"] = "success";
    $_SESSION["msg"] = "You are registered successfully.Check email to verify your account.";
    header('Location: emailconfirm.php'); 
    exit();   
  }

  else
  {
    
    $_SESSION["status"] = "error";
    $_SESSION["msg"] = "Username already exist.Please try other username.";
    header('Location: register.php'); 
    exit();
  }
    
}
    else{
        $_SESSION["msg"] = "Wrong CAPTCHA Entered!";
        header('Location: register.php');
        exit();
    }
    
}
?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <title>Greenwich Carpool - Register</title>
  <?php include("head.php"); ?>
  <style>
  body{
    background-color: black;
  }
</style>
</head>
<body>
  <?php include("header.php"); ?>
  <div style="padding-left:16px;height:700px" class="form" >
    <div class="login-screen">
      
      <div>
        <form name="myForm" action="" onsubmit="return validateForm()" method="post" >
          <h3 class="regis">Register</h3>
          <?php 
          if(isset($_SESSION["msg"]) && $_SESSION["msg"] != '')
          {
            $msg  = $_SESSION["msg"];
            echo '<p class="errorinfo">'.$msg.'</p>';
            unset($_SESSION['status']);
            unset($_SESSION['msg']);
          }
          ?>
          <p class="info">Please Register Below to Become a Member</p>
          <div>
            <input type="text" placeholder="Enter Username"  name="username" tabindex="1" id="username" required>
            </div>
            <div>
            <input type="password" placeholder="Enter Password" name="password" pattern=".{5,10}" title="5 to 10 characters" tabindex="2"required>
          </div>
          <div>
            <input type="email" placeholder="Enter Email" name="email" tabindex="3" required>
          </div>
          <div>
              <img src="captcha.php" /><br>
<!--            <input type="text" id="captcha" name="captcha" readonly>   -->
            <input type="text" placeholder="Enter Above Captcha Code" name="captchain" id="captchain" tabindex="4" required>
          </div>
          <div class="check">
            <input id="agreed" type="checkbox" name="checkbox" tabindex="5"  required>
            <label for="agreed">By creating your account, you agree to our Privacy Policy. Your personal details will be managed by Greenwich Carpool, with its company address at Greenwich House, Cutty Sark Road, London, SE10 9LS</label>
          </div>
          <input type="submit" tabindex="6">
        </form>

<script type="text/javascript">
                 
 function validateForm() {
    var user = document.forms["myForm"]["username"].value;
    var pass = document.forms["myForm"]["password"].value;
    var email = document.forms["myForm"]["email"].value;
    var check = document.forms["myForm"]["checkbox"].value;
    
    if (user == "") {
        alert("Please Enter a Username");
        return false;
    }
     
     if (pass == "") {
        alert("Please Enter a Password");
        return false;
     }
     
      if (email == "") {
        alert("Please Enter Correct Format for Email");
        return false;
     }
      if (user == email) {
        alert("You Cannot have the same USERNAME and EMAIL");
        return false;
     }
     
}
            
</script>


<!--
<script>
            function check(username) 
            {
            var a = /^[0-9A-Za-z]+$/;
            if(username.value.match(a))
            {
          
            }
            else
            {   
            alert("Fail, avoid using special character"); 
            }
             window.location.reload();
            }
            
</script>
-->



<!--
<script type="text/javascript">
          /*Captcha Script*/
          document.getElementById("captcha").focus();
          var a = Math.ceil(Math.random() * 9)+ '';
          var b = Math.ceil(Math.random() * 9)+ '';
          var c = Math.ceil(Math.random() * 9)+ '';
          var d = Math.ceil(Math.random() * 9)+ '';
          var e = Math.ceil(Math.random() * 9)+ '';
          var code = a + b + c + d + e;
          document.getElementById("captcha").value = code;
          document.getElementById("Captcha").innerHTML = code;
          function checkform(theform)
          {
            var why = "";
            if(theform.captchain.value == ""){
              why += "Please Enter CAPTCHA Code.\n";
            }
            if(theform.captchain.value != ""){
              if(ValidCaptcha(theform.captchain.value) == false){
                why += "The CAPTCHA Code Does Not Match.\n";
              }
            }
            if(why != ""){
              alert(why);
              return false;
            }
        
          }
          /*Validate input against the generated number*/
          function ValidCaptcha(){
            var str1 = removeSpaces(document.getElementById('captcha').value);
            var str2 = removeSpaces(document.getElementById('captchain').value);
            if (str1 == str2){
              return true;
            }else{
              return false;
            }
          }
          /*Remove the spaces from the entered and generated code*/
          function removeSpaces(string){
          return string.split(' ').join('');
          } 
        </script>
-->
      </div>   
    </div>
  </div>
</body>
</html>