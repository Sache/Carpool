<?php include("config.php"); 

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

$locations_array=array();
$mysql = "SELECT * from `locations`";
$output = mysqli_query($db,$mysql);
while($row = mysqli_fetch_assoc($output))
{
	array_push($locations_array, $row);
}
?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<title>Greenwich Carpool</title>
	<?php include("head.php"); ?>
	<link rel="stylesheet" type="text/css" href="css/pikaday.css">
</head>
<body>
	<?php include("header.php"); ?>
	<div class="background"></div>
	<form action="list.php" method="get">
		<input type="hidden" name="latitude" id="latitude">
		<input type="hidden" name="longitude" id="longitude">
		<table width="100%">
			<thead>
				<tr>
					<td colspan="3" class="text-center">
						<h3>Search your results</h3>
					</td>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td class="text-center">
						<select class="Departing--D" name="starting_point">
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
					<td class="text-center">
						<select class="Arriving--A" name="destination">
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
					<td class="text-center">
						<select class=Service--S name="type">
							<option value="">Service Type...</option>    
							<option value="o">Obtain</option> 
							<option value="p">Provide</option> 
						</select>
					</td>
				</tr>
				<tr>
					<td class="text-center">
						<input type="text" name="date" class="datepicker" id="date" placeholder="Enter From Date">
					</td>
					<td class="text-center">
						<label>From Time</label>
						<input type="time" name="from_time" class="datepicker" id="filter_from_time_index" placeholder="Enter From Time">
					</td>
					<td class="text-center">
						<input type="submit" class="float-none">
					</td>
				</tr>
			</tbody>
		</table>
	</form>
	<script src="js/jquery-3.2.1.min.js"></script>
	<script src="js/bootstrap.min.js"></script>
	<script src="js/pikaday.js"></script>
	<script type="text/javascript">
		var picker = new Pikaday({ field: document.getElementById('date') });
	</script>
</body>
</html>