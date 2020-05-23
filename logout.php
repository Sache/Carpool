<?php
  $_SESSION["status"] = "success";
  $_SESSION["msg"] = "Logged out successfully.";
  unset($_SESSION["user_name"]);
  
  unset($_SESSION["user_id"]);
  unset($_SESSION["is_loggedin"]);
    
  setcookie("user_name", "", time() + 3600, "/");
  unset ($_COOKIE["user_name"]); 

header('Location: index.php'); 
exit(); 

?>