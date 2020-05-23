<?php
include('config.php');

if(isset($_COOKIE["user_name"]))
{
header('Location: route.php'); 
exit();    
}
      
if($_SERVER["REQUEST_METHOD"] == "POST"){
  $username = mysqli_real_escape_string($db,$_POST['username']);
  $password = md5(mysqli_real_escape_string($db,$_POST['password']));
  $mysql = "SELECT * from grepool WHERE BINARY username = BINARY'$username' and password = BINARY'$password'";  
  $output = mysqli_query($db,$mysql);
  $loggedin = mysqli_fetch_assoc($output);
  mysqli_close($db);
  
  if(!empty($loggedin))
  {
      $_SESSION["status"] = "success";
      $_SESSION["msg"] = "Loggedin successfully.";
      $_SESSION["user_id"] = $loggedin["id"];
        
      setcookie("user_name", $loggedin["username"], time() + 3600, "/");
        
      //$_SESSION["user_name"] = $loggedin["username"];
      $_SESSION["is_loggedin"] = "yes";
      $history_link = $_SESSION["history_link"];
      if($history_link != '')
      {
        unset($_SESSION["history_link"]);
        header("location:".$history_link);
      }
      else
      {
        header("location:route.php");  
      }
      exit();
  }
  else
  {
    $_SESSION["status"] = "error";
    $_SESSION["msg"] = "Invalid username/password.Please try again.";
    header('Location: login.php'); 
    exit();
  }
}
?>
<!DOCTYPE html>
<html>
<head>

  <meta charset="UTF-8">
  <title>Greenwich Carpool - Login</title>
  <link rel="stylesheet" type="text/css" href="css/greenwichcarpool.css">

  <style>
  body{
    background-color: black;
  }
</style>

<?php include("header.php"); ?>

</head>
<body>
  <form name="myForm" action="login.php" onsubmit="return validateForm()" method="post">
    <h3 class="regis">Login</h3>
    <?php 
    if(isset($_SESSION["msg"]) && $_SESSION["msg"] != '')
    {
      $type = ($_SESSION["status"] == 'success') ? 'successinfo' : 'errorinfo';
      $msg  = $_SESSION["msg"];
      echo '<p class="'.$type.'">'.$msg.'</p>';
      unset($_SESSION['status']);
      unset($_SESSION['msg']);
    }
    ?>
    <p class="info">Please Login to your account below</p>
    <div>
      <input  name="username" placeholder="Enter Username" type="text">
    </div>
    <div>
      <input name="password" type="password" placeholder="Enter Password" pattern=".{5,10}"  title="5 to 10 characters">
    </div>
    <input  type="submit" value="Login" >
  </form>
    
<script type="text/javascript">
                 
 function validateForm() {
    var user = document.forms["myForm"]["username"].value;
    var pass = document.forms["myForm"]["password"].value;
    var a = /^[0-9A-Za-z]+$/;
    if (user == "") {
        alert("Please Enter a Username");
        return false;
    }
     
     if (pass == "") {
        alert("Please Enter a Password");
        return false;
     }
     if(username.value.match(a))
            {
          return true;
            }
            else
            {   
            alert("Fail, avoid using special character"); 
            return false;
            }
}
      
    
            
            
          
            
            
            

    
</script>
    
</body>
</html>
