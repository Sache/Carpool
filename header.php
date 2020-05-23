<nav class="nav">
  <div class="nav--heading">
    <p class="nav--headingText"><a class="h3--heading" href="<?php echo SITE_URL; ?>">Greenwich/Carpool</a></p>
  </div>
  <div class="nav--listWrapper">
      <?php if(isset($_SESSION["is_loggedin"]) && $_SESSION["is_loggedin"] == "yes" && $_COOKIE["user_name"] != ''){ ?>
    
    <?php } ?> 
     

    <ul class="nav--list">
      <li><a href="<?php echo SITE_URL; ?>">Home</a></li>
      <li><a href="<?php echo SITE_URL; ?>about.php" class="active">About</a></li>
      
      <?php if(!isset($_COOKIE["user_name"])){ ?>
      <li><a href="<?php echo SITE_URL; ?>login.php">Login</a></li>
      <li><a href="<?php echo SITE_URL; ?>register.php">Register</a></li>      
      <?php } ?>   

      <?php if(isset($_COOKIE["user_name"])){ ?>
      <li><a href="<?php echo SITE_URL; ?>route.php">Route</a></li>
      <?php } ?>   
      
      <li><a href="<?php echo SITE_URL; ?>contact.php">Contact</a></li>

      <?php if(isset($_COOKIE["user_name"])){ ?>

      <li><a href="<?php echo SITE_URL; ?>logout.php">Welcome <?php echo $_COOKIE["user_name"]; ?> - Logout</a></li>
      
      <?php } ?>   


    </ul>
  </div>
</nav>