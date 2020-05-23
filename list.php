<?php 
include("config.php"); 

  $user = $_COOKIE["user_name"];
  $check_user_query = "SELECT * FROM grepool WHERE confirmed = 0 and username = '$user'";
  $query_result = mysqli_query($db,$check_user_query);
  $loggedin = mysqli_fetch_assoc($query_result);
  $valid  =  mysqli_num_rows($query_result);
  if ($valid)
  {
  header("location:emailconfirm.php");
  exit();   
  }

if($_SERVER["REQUEST_METHOD"] == "GET")
{
  $locations_array=array();
  $mysql = "SELECT * from `locations`";
  $output = mysqli_query($db,$mysql);
  while($row = mysqli_fetch_assoc($output))
  {
    array_push($locations_array, $row);
  }

  $starting_point = ($_GET['starting_point'] != '') ? mysqli_real_escape_string($db,$_GET['starting_point']) : '';
  $destination = ($_GET['destination'] != '') ? mysqli_real_escape_string($db,$_GET['destination']) : '';
  $date = '';
  if($_GET["date"] != '')
  {
    $date = mysqli_real_escape_string($db,$_GET['date']);
    $date = date("Y-m-d",strtotime($date));
  }
  $_SESSION['list_params']=$_SERVER['QUERY_STRING'];
  $from_time = ($_GET['from_time'] != '') ? mysqli_real_escape_string($db,$_GET['from_time']) : '';
  $type = ($_GET['type'] != '') ? mysqli_real_escape_string($db,$_GET['type']) : '';
  $userwithoutimage = isset($_GET['userwithoutimage']) ? mysqli_real_escape_string($db,$_GET['userwithoutimage']) : '';
  $page = isset($_GET["page"]) ? $_GET["page"] : 1;
  $start_limit = (($page - 1)*LIST_COUNT);
  $field=" ,69*haversine(destination_table.latitude,destination_table.longitude,starting_table.latitude, starting_table.longitude)  as journey_distance";
  $query = 'SELECT j.*,
  (SELECT COUNT(id) FROM images 
  where images.journey_id = j.id) as image_count,
  (SELECT image FROM images where images.journey_id = j.id 
  ORDER BY id desc LIMIT 1) as user_image '.$field.' 
  FROM journeys as j JOIN locations as starting_table ON starting_table.id=j.starting_point 
  JOIN locations as destination_table ON destination_table.id=j.destination';

  $where="";
  if($type!=''){
    $where .=" AND type = '".$type."'";
  }
  if($starting_point != '')
  {
    $where .= ' AND starting_point = "'.$starting_point.'"';
  }
    if($user_name != '')
  {
    $where .= ' AND user_id = "'.$user_name.'"';
  }
  
  if($destination != '')
  {
    $where .= ' AND destination = "'.$destination.'"';
  }
  if($date != '')
  {
    $where .= ' AND from_date = "'.$date.'"';
  }
  
  if($where!=''){
    $where =' where '.substr($where, 4);
  }

  if($userwithoutimage != '' || $from_time != '')
  {
    $where .= ' HAVING ';

    if($userwithoutimage != '')
    {
      $where .= ' image_count > 0 ';  
      $where .= ($from_time != '') ? ' AND ' : '';
    }  

    if($from_time != '')
    {
      $from_timestamp = $from_time.':00';
      $where .= ' from_time >= "'.$from_timestamp.'"';  
    }
  }
  
  $query=$query.$where;
  $total_records_count_query = mysqli_query($db,$query);
  $total_records_count = mysqli_num_rows($total_records_count_query);
  if($longitude!='' && $latitude!=''){
    $query.= " ORDER BY journey_distance";
  }
  $query .= ' LIMIT '.$start_limit.','.LIST_COUNT;
  $total_pages = ceil($total_records_count/LIST_COUNT);
  $journeys_result = mysqli_query($db,$query);

  if($starting_point!=''){
    $query = "select name from locations where id='".$starting_point."'";
    $result = mysqli_query($db,$query);
    $starting_point_details = mysqli_fetch_assoc($result);  
  }

  if($destination!=''){
    $query = "select name from locations where id='".$destination."'";
    $result = mysqli_query($db,$query);
    $destination_details = mysqli_fetch_assoc($result); 
  }
}
?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <title>Greenwich Carpool - Search</title>
  <?php include("head.php"); ?>
  <link rel="stylesheet" type="text/css" href="css/pikaday.css">
  <style type="text/css">
  body
  {
    background-color: grey;
  }
</style>
</head>
<body>
  <?php include("header.php"); ?>
  <div id="search_result_container">

    <form action='' method="get">
      <input type="hidden" name="latitude" id="latitude">
      <input type="hidden" name="longitude" id="longitude">
      <div class="sidebar"> 
        <table width="100%">
          <thead>
            <tr>
              <td colspan="2" class="text-center">
                Filter your results
              </td>
            </tr>
          </thead>
          <tbody>
            <tr>
              <td>
                <h4 class="widget-title">Departing From</h4>
                <select class="form-control" name="starting_point">
                  <option value="">Departing From...</option>    
                  <?php 
                  if(!empty($locations_array)){
                    foreach ($locations_array as $key => $value) {
                      ?>
                      <option value="<?php print $value['id'];?>" <?php print ($value['id']==$starting_point)?"selected":""; ?> ><?php print $value['name'];?></option>
                      <?php 
                    }
                  }
                  ?>
                </select>
              </td>
              <td>
                <h4 class="widget-title">Destination</h4>
                <select class="form-control" name="destination">
                  <option value="">Arriving At...</option>  
                  <?php 
                  if(!empty($locations_array)){
                    foreach ($locations_array as $key => $value) {
                      ?>
                      <option value="<?php print $value['id'];?>" <?php print ($value['id']==$destination)?"selected":""; ?>><?php print $value['name'];?></option>
                      <?php 
                    }
                  }
                  ?>  
                </select>
              </td>
            </tr>
            <tr>
              <td>
                <h4 class="widget-title">Service Type</h4>
                <select class=Service--S name="type">
                  <option value="">Service Type...</option>    
                  <option value="o" <?php echo ($type == "o") ? 'selected' : ''; ?>>Obtain</option> 
                  <option value="p" <?php echo ($type == "p") ? 'selected' : ''; ?>>Provide</option> 
                </select>
              </td>
              <td>
                <h4 class="widget-title">Date</h4>
                <input type="text" name="date" class="datepicker" id="date" placeholder="Enter From Date" value="<?php echo $date; ?>">
              </td>
            </tr>
            <tr>
              <td>
                <h4 class="widget-title">From Time</h4>
                <input type="time" name="from_time" id="filter_from_time" placeholder="Enter From Time" value="<?php echo $from_time; ?>">
              </td>
              <td>
                <h4 class="widget-title">Exclude members without image?</h4>
                <ul class="optionlist">
                  <li>
                    <input type="checkbox" name="userwithoutimage" id="auto" <?php echo ($userwithoutimage != '') ? 'checked' : ''; ?>>
                    <label for="auto"></label>
                    Yes 
                  </li>
                </ul>
              </td>
            </tr>
            <tr>
              <td colspan="2" class="text-center">
                <div class="searchnt">
                  <button class="btn">Update Results</button>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
      <div class="search_records"> 
        <table width="100%" class="search-table nocolor border-bottom">
          <?php 
          if(mysqli_num_rows($journeys_result) > 0)
          {
            while($row = mysqli_fetch_assoc($journeys_result))
            {
              ?>
              <tr>
                <td  width="50%" class="text-center">
                  <div class="adimg">
                    <?php if($row["user_image"]!=''){?>
                    <img src="data:image/png;base64,<?php echo base64_encode($row["user_image"]); ?>" height="150px" alt="image">
                    <?php } else{ ?>
                    <img src="<?php echo SITE_URL.'images/default.png'; ?>" alt="image" height="150px">
                    <?php } ?>
                  </div>
                </td>
                <td width="50%">
                  <h3>
                    Starting Point : <?php 
                    $journey_query = "select name from locations where id='".$row['starting_point']."'";
                    $journey_result = mysqli_query($db,$journey_query);
                    $journey = mysqli_fetch_assoc($journey_result);  
                    echo $journey["name"]; 
                    ?>
                  </h3>
                  <h3>
                    Destination : <?php 
                    $journey_query = "select name from locations where id='".$row['destination']."'";
                    $journey_result = mysqli_query($db,$journey_query);
                    $journey = mysqli_fetch_assoc($journey_result);  
                    echo $journey["name"]; 
                    ?> 
                  </h3>
                  <?php
                  echo "<h3>".$starting_point_details['name']." to ".$destination_details['name']." distance : ".round($row['journey_distance'],2)." miles</h3>";
                  ?>
                  <h3>Service Type : <?php echo ($row["type"]=='o')?"Obtain":"Provide"; ?> </h3>

                  <h3>
                    Posted By <?PHP echo " : " . $row["user_id"] ;?>
                    <?php 
                    echo $user_final["username"]; 
                    ?>
                  </h3>

                  <div class="listbtn"><a href="<?php echo SITE_URL.'detail.php?id='.$row["id"]; ?>">View Details </a></div>
                </td>
              </tr>
              <?php
            }
          } 
          else
          {
            echo '<h3 class="journey--heading">No records found.</h3>';
          }
          ?>
        </table>
        <!-- Pagination Start -->
        <?php if($total_pages > 1 && mysqli_num_rows($journeys_result) > 0){ 
          $end_point = $start_limit + LIST_COUNT;
          ?>
          <div class="pagiWrap">
            <div class="row">
              <div class="col-md-4 col-sm-4">
                <div class="showreslt">Showing <?php echo $start_limit + 1; ?> - <?php echo $end_point; ?> of total <?php echo $total_records_count; ?> records</div>
              </div>
              <div class="">
                <ul class="pagination">
                  <?php for($i = $total_pages;$i >=1;$i--) {?>
                  <li class="<?php print ($i==$page)?"active":"";?>"><a href="<?php echo SITE_URL.'list.php?starting_point='.$starting_point.'&destination='.$destination.'&type='.$type.'&date='.$date.'&from_time='.$from_time.'&page='.$i; ?>"><?php echo $i; ?></a></li>
                  <?php } ?>
                </ul>
              </div>
            </div>
          </div>
          <?php } ?>
        </div>
      </div>
    </form>
    <script src="js/jquery-3.2.1.min.js"></script>
    <script src="js/bootstrap.min.js"></script>
    <script src="js/pikaday.js"></script>
    <script type="text/javascript">
      var picker = new Pikaday({ field: document.getElementById('date') });
    </script>
  </body>
  </html>
