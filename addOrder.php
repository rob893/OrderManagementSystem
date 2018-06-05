<?php
require_once('header.php');

if(isset($_POST['addOrder'])){
	
	$buyerId = $_POST['buyer'];
	$serviceId = $_POST['service'];
	$date = $_POST['date']." ".$_POST['time'];
	//$time = $_POST['time'];
	
	//echo $date;
	$sqlInsert = $conn->prepare("INSERT INTO orders(buyerId, serviceId, dateOfService) VALUES(?, ?, ?)");
	$sqlInsert->bind_param('iis', $buyerId, $serviceId, $date);
	//End 'filtering'
	
	if($sqlInsert->execute() === true){
		echo "<script type='text/javascript'>alert('Order has been placed!')</script>";
		$sqlInsert->close();
	} else {
		echo "<script type='text/javascript'>alert('Error: ".$sqlInsert->error."')</script>";
		$sqlInsert->close();
	}
}


$sqlServices = "SELECT * FROM services WHERE active = '1' ORDER BY serviceName ASC";
$sqlBuyers = "SELECT * FROM buyers WHERE active = '1' ORDER BY buyerName ASC";

$sqlServicesResults = $conn->query($sqlServices);
$sqlBuyersResults = $conn->query($sqlBuyers);
?>
<h3>Add An Order</h3>
<form action='#' method='post' enctype='multipart/form-data'>
	<div class='row'>
		<div class="form-group col-sm-2">
			<label for="service">Service:</label>
			<select class='form-control' id='service' name='service'>
				<?php
				while($row = $sqlServicesResults->fetch_assoc()){
					$serviceName = $row['serviceName'];
					$serviceId = $row['id'];
					echo "
						<option value='".$serviceId."'>".$serviceName."</option>
					";
				}
				?>
			</select>
		</div>
	</div>
		
	<div class='row'>
		<div class='form-group col-sm-2'>
			<label for="buyer">Buyer:</label>
			<select class='form-control' id='buyer' name='buyer'>
				<?php
				while($row = $sqlBuyersResults->fetch_assoc()){
					$buyerName = $row['buyerName'];
					$buyerId = $row['id'];
					echo "
						<option value='".$buyerId."'>".$buyerName."</option>
					";
				}
				?>
			</select>
		</div>
	</div>
	
	<div class='row'>
		<div class='form-group col-sm-2'>
			<label for="date">Date of Service:</label>
			<input type='date' class='form-control' name='date' id='date' required>
		</div>
	</div>
	
	<div class='row'>
		<div class='form-group col-sm-2'>
			<label for="time">Time of Service:</label>
			<input type='time' class='form-control' name='time' id='time' required>
		</div>
	</div>
	<button type='submit' class='btn btn-info' name ='addOrder'>Submit</button>
</form>

<?php
require_once('footer.php');
?>