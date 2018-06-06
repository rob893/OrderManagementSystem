<?php
require_once('header.php');

if(isset($_POST['delete'])){
	
	$serviceId = $_POST['delete'];
	$sqlInsert = $conn->prepare("UPDATE services SET active = '0' WHERE id = ?");
	$sqlInsert->bind_param('i', $serviceId);
	//End 'filtering'
	
	if($sqlInsert->execute() === true){
		echo "<script type='text/javascript'>alert('Success!')</script>";
		$sqlInsert->close();
	} else {
		echo "<script type='text/javascript'>alert('Error: ".$sqlInsert->error."')</script>";
		$sqlInsert->close();
	}
}

if(isset($_POST['addService'])){
	
	//The following takes the POST data and 'filters' it before inserting into the database to prevent SQL injection attacks. 
	//All user input should be 'filtered' before inserting into the database.
	if(empty(trim($_POST['name']))){
		echo "<script type='text/javascript'>alert('Invalid name!')</script>";
	} 
	else{
		$serviceName = strip_tags($_POST['name']);
		$serviceCost = strip_tags($_POST['cost']);
		
		$serviceName = stripslashes($serviceName);
		$serviceCost = stripslashes($serviceCost);

		$serviceName = mysqli_real_escape_string($conn, $serviceName);
		$serviceCost = mysqli_real_escape_string($conn, $serviceCost);

		$sqlInsert = $conn->prepare("INSERT INTO services(serviceCost, serviceName) VALUES(?, ?)");
		$sqlInsert->bind_param('is', $serviceCost, $serviceName);
		//End 'filtering'
		
		if($sqlInsert->execute() === true){
			echo "<script type='text/javascript'>alert('".$serviceName." has been added to the database!')</script>";
			$sqlInsert->close();
		} else {
			echo "<script type='text/javascript'>alert('Error: ".$sqlInsert->error."')</script>";
			$sqlInsert->close();
		}
	}
}

$sqlService = "SELECT * FROM services WHERE active = '1' ORDER BY serviceName ASC";

$sqlServicesResults = $conn->query($sqlService);

?>
<h2>Add Service</h2>

<button type="button" class="btn btn-info" data-toggle="collapse" data-target="#addService">Add Service</button>
<div id="addService" class="collapse">
	<form action='#' method='post' enctype='multipart/form-data'>
		<div class='row'>
			<div class='col-sm-2'>
				<div class='form-group'>
					<br>
					<label for='name'>Service Name:</label>
					<input type='text' class='form-control' name='name' id='name' placeholder='Name' required>
					<label for='cost'>Service Cost:</label>
					<input type='number' min='0' max='100000000' onkeydown='javascript: return event.keyCode !== 69' class='form-control' name='cost' id='cost' placeholder='Cost' required>
					<br>
					<button type='submit' class='btn btn-info' name ='addService'>Submit</button>
				</div>
			</div>
		</div>
	</form>
</div>

<p></p>
<h2>Currently Offered Services</h2>
<table class='table table-striped table-responsive'>
	<thead>
		<tr>
			<th>Service</th>
			<th>Cost</th>
			<th>Delete</th>
		</tr>
	</thead>
	
	<tbody>
		<?php
			while($row = $sqlServicesResults->fetch_assoc()){
				echo "
					<tr>
						<td><a href=serviceProfile.php?serviceId=".$row['id']." class='text-dark'>".$row["serviceName"]."</a></td>
						<td>".number_format($row["serviceCost"])."</td>
						<td>
							<form action='#' method='post'>
								<button type='submit' class='btn btn-danger' name ='delete' value='".$row['id']."'>Delete</button>
							</form>
						</td>
					</tr>";
			}
		?>
	</tbody>
</table>

<?php
require_once('footer.php');
?>