<?php
include('config.php');
$id = isset($_GET["id"]) ? $_GET["id"] : '';
if(!isset($_COOKIE["user_name"]))
{ 
  $_SESSION["history_link"] = SITE_URL.'detail.php?id='.$id;
  header('Location: login.php'); 
  exit();   
}
$journey_query = "SELECT * FROM `journeys` as j WHERE j.`id` = '".$id."'";
$journey_result = mysqli_query($db,$journey_query);
$journey = mysqli_fetch_assoc($journey_result);


$images_query = "SELECT * FROM `images` WHERE journey_id = '".$id."'";
$images_result = mysqli_query($db,$images_query);

?>
<html>
<head>
  <meta charset="UTF-8">
  <title>Greenwich Carpool - Detail</title>
  <?php include("head.php"); ?>
  <style>
  body{
    background-color: black;
  }
</style>
</head>
<body>
  <?php include("header.php"); ?>
  <h3 class="journey--heading">Detail</h3>
  <p class="plan-p"><a href="<?php echo SITE_URL.'list.php?'.$_SESSION['list_params']; ?>">Go Back to list</a></p>
  <?php if(!empty($journey)){ ?>
  <form>
    <table width="100%">
      <tr>
        <th>Starting Point</th>
        <td><?php 
        $journey_query = "select name from locations where id='".$journey['starting_point']."'";
        $journey_result = mysqli_query($db,$journey_query);
        $journey_final = mysqli_fetch_assoc($journey_result);  
        echo $journey_final["name"]; 
        ?></td>
      </tr>
      <tr>
        <th>Destination</th>
        <td><?php 
        $journey_query = "select name from locations where id='".$journey['destination']."'";
        $journey_result = mysqli_query($db,$journey_query);
        $journey_final = mysqli_fetch_assoc($journey_result);  
        echo $journey_final["name"]; 
        ?> </td>
      </tr>
      <tr>
        <th>From Date</th>
        <td><?php echo $journey["from_date"]; ?></td>
      </tr>
      <tr>
        <th>To Date</th>
        <td><?php echo $journey["to_date"]; ?></td>
      </tr>    
      <tr>
        <th>From Time</th>
        <td><?php echo date("H:i",strtotime($journey["from_time"])); ?></td>
      </tr>    
      <tr>
        <th>To Time</th>
        <td><?php echo date("H:i",strtotime($journey["to_time"])); ?></td>
      </tr>    
      <tr>
        <th>Seats Available</th>
        <td><?php echo $journey["seats_available"]; ?></td>
      </tr>    
      <tr>
        <th>Type</th>
        <td><?php echo ($journey["type"] == 'o') ? "Obtain" : "Provide"; ?></td>
      </tr>    
      <tr>
        <th>Car Type</th>
        <td><?php echo $journey["car_type"]; ?></td>
      </tr>   
      <tr>
        <th>Driving Licence</th>
        <td><?php echo $journey["driving_licence_state"];?></td>
      </tr>   
      <tr>
        <th>Insurance State</th>
        <td><?php echo $journey["insurance_state"]; ?></td>
      </tr>       
      <tr>
        <th>Cost Sharing</th>
        <td><?php echo $journey["cost_sharing"]; ?></td>
      </tr>       
      <tr>
        <th>Note</th>
        <td><?php echo $journey["note"]; ?></td>
      </tr>

      <tr>
        <th>Posted By</th>
        <td><?php 
        echo $journey['user_id'];
        ?> </td>
      </tr>


    </table>

    <?php 
    if(mysqli_num_rows($images_result) > 0)
    {

     ?>

     <table id="images_table" width="100%" border="0">

      <?php
      $i = 0;
      while($row1 = mysqli_fetch_assoc($images_result))
      {

        echo ($i == 0) ? '<tr>' : '';

        ?>

        <td class="text-center">
            <img src="data:image/png;base64,<?php echo base64_encode($row1["image"]); ?>" height="100px" alt="image">
        </td>
        <?php
        $i++;
        echo ($i == 5) ? '</tr>' : ''; 
        $i = ($i == 5) ? 0 : $i;
      }
      ?>
    </table>

    <?php
  }
  ?>


</form>
<?php } ?>
</body>
</html>
