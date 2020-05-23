<?php
include("config.php"); 
if(!isset($_SESSION["is_loggedin"]))
{ 
  header('Location: login.php'); 
  exit();   
}
$type = (isset($_GET["type"]) && $_GET["type"] != '') ? $_GET["type"] : '';
$id = (isset($_GET["id"]) && $_GET["id"] != '') ? $_GET["id"] : '';
if($_SERVER["REQUEST_METHOD"] == "POST")
{
  $journey_id = mysqli_real_escape_string($db,$_POST['journey_id']);
  $starting_point = mysqli_real_escape_string($db,$_POST['starting_point']);
  $destination = mysqli_real_escape_string($db,$_POST['destination']);
  $from_date = mysqli_real_escape_string($db,$_POST['from_date']);
  $from_date = date("Y-m-d",strtotime($from_date));
  $to_date = mysqli_real_escape_string($db,$_POST['to_date']);
  $to_date = date("Y-m-d",strtotime($to_date));
  $type = mysqli_real_escape_string($db,$_POST['type']);
  $car_type = mysqli_real_escape_string($db,$_POST['car_type']);
  $from_time = mysqli_real_escape_string($db,$_POST['from_time']);
  $to_time = mysqli_real_escape_string($db,$_POST['to_time']);
  $seats_available = mysqli_real_escape_string($db,$_POST['seats_available']);
  $driving_licence_state = mysqli_real_escape_string($db,$_POST['driving_licence_state']);
  $insurance_state = mysqli_real_escape_string($db,$_POST['insurance_state']);
  $cost_sharing = mysqli_real_escape_string($db,$_POST['cost_sharing']);
  $note = mysqli_real_escape_string($db,$_POST['note']);

  if (mysqli_connect_errno())
  {
    echo "Flop" . mysqli_connect_error();
  }

  if($journey_id > 0)
  {
    $mysql = "SELECT * from `journeys` WHERE id ='$journey_id' and user_id ='".$_SESSION["user_id"]."'";
    $output = mysqli_query($db,$mysql);
    $journey = mysqli_fetch_assoc($output); 

    if(mysqli_num_rows($output) > 0)
    {
      $update_query = "UPDATE journeys SET starting_point = '".$starting_point."',destination = '".$destination."',from_date = '".$from_date."',to_date = '".$to_date."',from_time = '".$from_time."',to_time = '".$to_time."',seats_available = '".$seats_available."',type = '".$type."',car_type = '".$car_type."',driving_licence_state = '".$driving_licence_state."',insurance_state = '".$insurance_state."',cost_sharing = '".$cost_sharing."',note = '".$note."'";
      $update_result = mysqli_query($db,$update_query);
      mysqli_close($db);

      $_SESSION["status"] = "success";
      $_SESSION["msg"] = "Route edited successfully.";
      header('Location: route.php'); 
      exit();   
    }  
    else
    {
      $_SESSION["status"] = "error";
      $_SESSION["msg"] = "You are not authorized to edit this journey.";
      header('Location: route.php'); 
      exit();   
    }
  }
  else
  { 
    $insert_query = "INSERT INTO journeys (user_id,starting_point,destination,from_date,to_date,from_time,to_time,seats_available,type,car_type,driving_licence_state,insurance_state,cost_sharing,note) VALUES ('".$_SESSION["user_id"]."','$starting_point','$destination','$from_date','$to_date','$from_time','$to_time','$seats_available','$type','$car_type','$driving_licence_state','$insurance_state','$cost_sharing','$note')";
    
    $insert_result = mysqli_query($db,$insert_query);
    mysqli_close($db);

    $_SESSION["status"] = "success";
    $_SESSION["msg"] = "Route added successfully.";
  }

  header('Location: route.php'); 
  exit();   
} 
?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <title>Route</title>
  <link rel="stylesheet" type="text/css" href="css/greenwichcarpool.css">
  <link rel="stylesheet" type="text/css" href="css/pikaday.css">
  <style>
  body
  {
    background-color: black;
  }
</style>
</head>
<body>
  <?php include("header.php"); ?>

  <h3 class="journey--heading">Commute</h3>
  <p class="plan-p">Plan Your Route . . .</p>

  <?php 
  if(isset($_SESSION["msg"]) && $_SESSION["msg"] != '')
  {
    $msg_type = ($_SESSION["status"] == 'success') ? 'successinfo' : 'errorinfo';
    $msg  = $_SESSION["msg"];
    echo '<p class="'.$msg_type.'">'.$msg.'</p>';
    unset($_SESSION['status']);
    unset($_SESSION['msg']);
  }

  if($type == '') {
    echo '<p class="plan-p"><a class="add_route_link" href="'.SITE_URL.'route.php?type=add'.'">Add Route</a></p>';
    $journeys_query = "SELECT * FROM `journeys` WHERE `user_id` = '".$_SESSION["user_id"]."'";
    $journeys_result = mysqli_query($db,$journeys_query);

    if(mysqli_num_rows($journeys_result) > 0)
    {
      ?>
      <table id="list_table">
        <tr>
          <th>Starting Point</th>
          <th>Destination</th>
          <th>From Date</th>
          <th>To Date</th>
          <th>From Time</th>
          <th>To Time</th>
          <th>Seats Available</th>
          <th>Type</th>
          <th>Car Type</th>
          <th>Driving Licence State</th>
          <th>Insurance State</th>
          <th>Cost Sharing</th>
          <th>Note</th>
          <th>Operations</th>
        </tr>

        <?php

        while($row = mysqli_fetch_assoc($journeys_result))
        {
          ?>

          <tr>
            <td><?php echo $row["starting_point"]; ?></td>
            <td><?php echo $row["destination"]; ?></td>
            <td><?php echo $row["from_date"]; ?></td>
            <td><?php echo $row["to_date"]; ?></td>
            <td><?php echo $row["from_time"]; ?></td>
            <td><?php echo $row["to_time"]; ?></td>
            <td><?php echo $row["seats_available"]; ?></td>
            <td><?php echo ($row["type"] == 'o') ? "Obtain" : "Provide"; ?></td>
            <td><?php echo $row["car_type"]; ?></td>
            <td><?php echo ($row["driving_licence_state"] == 'y') ? "Yes" : "No";?></td>
            <td><?php echo ($row["insurance_state"] == 'y') ? "Yes" : "No"; ?></td>
            <td><?php echo $row["cost_sharing"]; ?></td>
            <td><?php echo $row["note"]; ?></td>
            <td>
              <a href="<?php echo SITE_URL.'route.php?type=edit&id='.$row["id"]; ?>">Edit</a>
              <a class="delete_journey" href="<?php echo SITE_URL.'route.php?type=delete&id='.$row["id"]; ?>" onclick='return delete_journey_fn()'>Delete</a>
            </td>
          </tr>

          <?php   
        }
        echo '</table>';
      }
    } 
    else if($type == "add" || $type == "edit")
    {
      if($type == "edit" && $id > 0)
      {
        $mysql = "SELECT * from `journeys` WHERE id ='$id' and user_id ='".$_SESSION["user_id"]."'";
        $output = mysqli_query($db,$mysql);
        $journey = mysqli_fetch_assoc($output);  
        if(mysqli_num_rows($output) == 0)
        {
          $_SESSION["status"] = "error";
          $_SESSION["msg"] = "You are not authorized to edit this route.";
          header('Location: route.php'); 
          exit();   
        }
      }

      ?>


      <form action="" method="post">

        <?php 
        if($type == "edit" && $id > 0)
        {
          echo '<input type="hidden" name="journey_id" value="'.$id.'">';  
        }
        ?>

        <div>
          <select class="Departing--D" name="starting_point">
            <option value="Departing">Departing From...</option>    
            <option value="Greenwich" <?php echo (isset($journey) && $journey["starting_point"] == "Greenwich") ? 'selected' : ''; ?>>Greenwich</option> 
            <option value="CW" <?php echo (isset($journey) && $journey["starting_point"] == "CW") ? 'selected' : ''; ?>>Canary Wharf</option> 
            <option value="Stratford" <?php echo (isset($journey) && $journey["starting_point"] == "Stratford") ? 'selected' : ''; ?>>Stratford</option> 
          </select>
        </div>

        <div>
          <select class="Arriving--A" name="destination">
            <option value="Arriving">Arriving At...</option>    
            <option value="Greenwich" <?php echo (isset($journey) && $journey["starting_point"] == "Greenwich") ? 'selected' : ''; ?>>Greenwich</option> 
            <option value="CW" <?php echo (isset($journey) && $journey["starting_point"] == "CW") ? 'selected' : ''; ?>>Canary Wharf</option> 
            <option value="Stratford" <?php echo (isset($journey) && $journey["starting_point"] == "Stratford") ? 'selected' : ''; ?>>Stratford</option> 
          </select>
        </div>

        <div>
          <input type="text" name="from_date" class="datepicker" id="from_date" placeholder="Enter From Date" value="<?php echo (isset($journey) && $journey["from_date"] != '') ? $journey["from_date"] : ''; ?>">
        </div>

        <div>
          <input type="text" name="to_date" class="datepicker" id="to_date" placeholder="Enter To Date" value="<?php echo (isset($journey) && $journey["to_date"] != '') ? $journey["to_date"] : ''; ?>">
        </div>

        <div>
          <select class=Service--S name="type">
            <option value="">Service Type...</option>    
            <option value="o" <?php echo (isset($journey) && $journey["type"] == "o") ? 'selected' : ''; ?>>Obtain</option> 
            <option value="p" <?php echo (isset($journey) && $journey["type"] == "p") ? 'selected' : ''; ?>>Provide</option> 
          </select>
        </div>

        <div>
          <input type="text" name="from_time" class="datepicker" id="from_time" placeholder="Enter From Time" value="<?php echo (isset($journey) && $journey["from_time"] != '') ? $journey["from_time"] : ''; ?>">
        </div>

        <div>
          <input type="text" name="to_time" class="datepicker" id="to_time" placeholder="Enter To Time" value="<?php echo (isset($journey) && $journey["to_time"] != '') ? $journey["to_time"] : ''; ?>">
        </div>

        <div>
          <select class=Service--S name="seats_available">
            <option value="">Seats Available</option> 
            <option value="1" <?php echo (isset($journey) && $journey["seats_available"] == "1") ? 'selected' : ''; ?>>1</option> 
            <option value="2" <?php echo (isset($journey) && $journey["seats_available"] == "2") ? 'selected' : ''; ?>>2</option> 
            <option value="3" <?php echo (isset($journey) && $journey["seats_available"] == "3") ? 'selected' : ''; ?>>3</option>
          </select>
        </div>

        <div>
          <label>Driving licence state</label>  
          <input type="radio" name="driving_licence_state" value="y" <?php echo (isset($journey) && $journey["driving_licence_state"] == "y") ? 'checked' : ''; ?>> Yes 
          <input type="radio" name="driving_licence_state" value="n" <?php echo (isset($journey) && $journey["driving_licence_state"] == "n") ? 'checked' : ''; ?>> No 
        </div>

        <div>
          <label>Insurance state</label>  
          <input type="radio" name="insurance_state" value="y" <?php echo (isset($journey) && $journey["insurance_state"] == "y") ? 'checked' : ''; ?>> Yes 
          <input type="radio" name="insurance_state" value="n" <?php echo (isset($journey) && $journey["insurance_state"] == "n") ? 'checked' : ''; ?>> No 
        </div>


        <div>
          <textarea name="car_type" placeholder="Car type"><?php echo (isset($journey) && $journey["car_type"] != '') ? $journey["car_type"] : ''; ?></textarea>
        </div>    

        <div>
          <textarea name="cost_sharing" placeholder="Cost sharing"><?php echo (isset($journey) && $journey["cost_sharing"] != '') ? $journey["cost_sharing"] : ''; ?></textarea>
        </div>

        <div>
          <textarea name="note" placeholder="Note"><?php echo (isset($journey) && $journey["note"] != '') ? $journey["note"] : ''; ?></textarea>
        </div>

        <input type="submit" >

      </form>

      <script src="js/jquery-3.2.1.min.js"></script>
      <script src="js/bootstrap.min.js"></script>
      <script src="js/pikaday.js"></script>
      <script type="text/javascript">
        var picker = new Pikaday({ field: document.getElementById('from_date') });
        var picker = new Pikaday({ field: document.getElementById('to_date') });
      </script>
      <?php
    }
    else if($type == "delete" && $id > 0)
    {
      $mysql = "SELECT * from `journeys` WHERE id ='$id' and user_id ='".$_SESSION["user_id"]."'";
      $output = mysqli_query($db,$mysql);
      $journey = mysqli_fetch_assoc($output);  
      if(mysqli_num_rows($output) == 0)
      {
        $_SESSION["status"] = "error";
        $_SESSION["msg"] = "You are not authorized to delete this journey.";
        header('Location: route.php'); 
        exit();   
      }
      else
      {
        $delete_query = "DELETE from `journeys` WHERE id ='$id' and user_id ='".$_SESSION["user_id"]."'";
        $delete_output = mysqli_query($db,$delete_query);

        $_SESSION["status"] = "success";
        $_SESSION["msg"] = "Route deleted successfully.";
        header('Location: route.php'); 
        exit();
      }
    }
    ?>

    <script type="text/javascript">
      function delete_journey_fn()
      {
        if (confirm("Are you sure? Want to delete?") != true) 
        {
          return false;  
        }
      }
    </script>
  </body>
  </html>