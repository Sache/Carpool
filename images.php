<?php
include("config.php"); 
if(!isset($_SESSION["is_loggedin"]))
{ 
  header('Location: login.php'); 
  exit();   
}

$type = (isset($_GET["type"]) && $_GET["type"] != '') ? $_GET["type"] : '';
$route_id = (isset($_GET["route_id"]) && $_GET["route_id"] != '') ? $_GET["route_id"] : '';
$id = (isset($_GET["id"]) && $_GET["id"] != '') ? $_GET["id"] : '';
if($_SERVER["REQUEST_METHOD"] == "POST")
{
  if (mysqli_connect_errno())
  {
    echo "Flop" . mysqli_connect_error();
  }
  
  if(count($_FILES['images']['name']) > 0)
  {
    $insert_query = 'INSERT into `images`(journey_id,user_id,image) VALUES ';
    for($i=0; $i<count($_FILES['images']['name']); $i++) 
    {
      $tmpFilePath = $_FILES['images']['tmp_name'][$i];
      if($tmpFilePath != "")
      {
        $imgContent = addslashes(file_get_contents($tmpFilePath));
        if($imgContent != '')
        {
          $insert_query .= '("'.$route_id.'","'.$_SESSION["user_id"].'","'.$imgContent.'"),';
        }
      }
    }

    if($insert_query != '')
    {
      $insert_query = rtrim($insert_query,",");
      $insert_result = mysqli_query($db,$insert_query);
      mysqli_close($db);
      $_SESSION["status"] = "success";
      $_SESSION["msg"] = "Images uploaded successfully.";
      header('Location: images.php?route_id='.$route_id); 
      exit();
    }
  }
} 

/*get user images*/

$images_query = "SELECT * FROM `images` WHERE journey_id= '".$route_id."' AND `user_id` = '".$_SESSION["user_id"]."'";
$images_result = mysqli_query($db,$images_query);

/*get user images ends*/

?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <title>Greenwich Carpool - Images</title>
  <link rel="stylesheet" type="text/css" href="css/greenwichcarpool.css">
  <style>
  body
  {
    background-color: black;
  }
</style>
</head>
<body>
  <?php include("header.php"); ?>
  <h3 class="journey--heading">Image</h3>
  <p class="plan-p"><a href="<?php echo SITE_URL.'route.php'; ?>">Go Back to list</a></p>
  <?php 
  if(isset($_SESSION["msg"]) && $_SESSION["msg"] != '')
  {
    $msg_type = ($_SESSION["status"] == 'success') ? 'successinfo' : 'errorinfo';
    $msg  = $_SESSION["msg"];
    echo '<p class="'.$msg_type.'">'.$msg.'</p>';
    unset($_SESSION['status']);
    unset($_SESSION['msg']);
  }
  
  if($type == '')
  {
    ?>
    <form action="" method="post" enctype="multipart/form-data">
      <div class="form_container">
        <label>Image</label>  
        <input type="file" name="images[]" multiple>
        <input type="submit">
      </div>
    </form>

    <table id="image_list_table">
      <?php 
      if(mysqli_num_rows($images_result) > 0)
      { 
        ?>
        <tr>
          <th>Image</th>
          <th>Operations</th>
        </tr>
        <?php 
        while($row = mysqli_fetch_assoc($images_result))
        {
          ?>
          <tr>
            <td>
                <img src="data:image/png;base64,<?php echo base64_encode($row["image"]); ?>" height="150px" alt="image">
            </td>
            <td>
              <a class="delete_image" href="<?php echo SITE_URL.'images.php?type=delete&route_id='.$route_id.'&id='.$row["id"]; ?>" onclick='return delete_image_fn()'>Delete</a>
            </td>
          </tr>

          <?php   
        }
      }
      else
      {
        echo '<h3 class="journey--heading">No records found.</h3>';
      }
      echo '</table>';
    }
    else if($type == 'delete' && $id > 0)
    {
      $mysql = "SELECT * from `images` WHERE id ='$id' and user_id ='".$_SESSION["user_id"]."'";
      $output = mysqli_query($db,$mysql);
      $image_data = mysqli_fetch_assoc($output);  
      if(mysqli_num_rows($output) == 0)
      {
        $_SESSION["status"] = "error";
        $_SESSION["msg"] = "You are not authorized to delete this image.";
        header('Location: images.php?route_id='.$route_id); 
        exit();   
      }
      else
      {
        $delete_query = "DELETE from `images` WHERE id ='$id' and user_id ='".$_SESSION["user_id"]."'";
        $delete_output = mysqli_query($db,$delete_query);

        $_SESSION["status"] = "success";
        $_SESSION["msg"] = "Image deleted successfully.";
        header('Location: images.php?route_id='.$route_id); 
        exit();
      }
    } 
    ?>  

    <script type="text/javascript">
      function delete_image_fn()
      {
        if (confirm("Are you sure? Want to delete?") != true) 
        {
          return false;  
        }
      }
    </script>
  </body>
  </html>