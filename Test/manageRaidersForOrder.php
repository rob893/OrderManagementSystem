<?php
require_once('header.php');

if(isset($_GET['time']) && isset($_GET['date'])){
    $orderId = $_GET['orderId'];
    $time = $_GET['time'];
    $date = $_GET['date'];
    $dateTime = $date.' '.$time;
    $formattedTime = date("g:iA", strtotime($time));
} else{
    header("Location: index.php");
}


if(isset($_POST['addToGroup']) && isset($_POST['orderId']) && !empty($_POST['raiderId'])){
	$orderId = $_POST['orderId'];
	foreach($_POST['raiderId'] as $raiderId){
		$sqlInsert = $conn->prepare("INSERT INTO raiderOrderInvolved (raiderId, orderId) VALUES (?, ?)");
										
		$sqlInsert->bind_param('ii', $raiderId, $orderId);
		
		if($sqlInsert->execute() === true){
			//echo "<script type='text/javascript'>alert('Order has been reopened! Amount paid for this order set to: ".$amountPaid."')</script>";
			$sqlInsert->close();
		} else {
			echo "<script type='text/javascript'>alert('Error: ".$sqlInsert->error."')</script>";
			$sqlInsert->close();
		}
	}
}

if(isset($_POST['remove']) && isset($_POST['orderId']) && !empty($_POST['raiderId'])){
	$orderId = $_POST['orderId'];
	foreach($_POST['raiderId'] as $raiderId){
		$sqlInsert = $conn->prepare("DELETE FROM raiderOrderInvolved WHERE raiderId = ? AND orderId = ?");
										
		$sqlInsert->bind_param('ii', $raiderId, $orderId);
		
		if($sqlInsert->execute() === true){
			//echo "<script type='text/javascript'>alert('Order has been reopened! Amount paid for this order set to: ".$amountPaid."')</script>";
			$sqlInsert->close();
		} else {
			echo "<script type='text/javascript'>alert('Error: ".$sqlInsert->error."')</script>";
			$sqlInsert->close();
		}
	}
}

$sqlNumInGroup = "SELECT COUNT(raiderId) AS numRaiders FROM raiderOrderInvolved 
                    INNER JOIN orders ON raiderOrderInvolved.orderId = orders.id 
                    WHERE dateOfService = '$dateTime'";
$sqlNumInGroupResults = $conn->query($sqlNumInGroup);
$numInGroupArray = $sqlNumInGroupResults->fetch_assoc();
$numInGroup = $numInGroupArray['numRaiders'];

$sqlServiceCost = "SELECT serviceCost, serviceName, dateOfService, orders.id AS orderId, services.id AS serviceId FROM services 
                        INNER JOIN orders ON services.id = serviceId 
                        WHERE dateOfService = '$dateTime'";
$sqlServiceCostResults = $conn->query($sqlServiceCost);

$serviceCost = 0;
$orderId = [];
$i = 0;
while($row = $serviceCostArray = $sqlServiceCostResults->fetch_assoc()){
    $serviceCost += $row['serviceCost'];
    $serviceName = $row['serviceName'];
    $orderId[$i] = $row['orderId'];
    $i++;
}
$orderIdArrString = implode("," , $orderId);
echo $orderIdArrString;


if($numInGroup == 0){
	$amountOwedPerRaider = 0;
} else {
	$amountOwedPerRaider = $serviceCost / $numInGroup; 
}


$sqlRaidersInGroup = "SELECT * FROM raiderOrderInvolved 
						INNER JOIN raider ON raiderId = raider.id
                        INNER JOIN orders ON orderId = orders.id 
						WHERE dateOfService = '$dateTime'";
						
$sqlAvailableRaiders = "SELECT * FROM raider
							WHERE raiderName NOT 
							IN (
								SELECT raiderName FROM raiderOrderInvolved
								INNER JOIN raider ON raiderId = raider.id
                                INNER JOIN orders ON orderId = orders.id
								WHERE dateOfService =  '$dateTime'
							)";
							
$sqlGroupResults = $conn->query($sqlRaidersInGroup);
$sqlAvailableResults = $conn->query($sqlAvailableRaiders);
?>
<h2>Group for <?php echo $serviceName." on ".$date." at ".$formattedTime; ?></h2>
<br>
<h2>Raiders In Group</h2>
<form action='#' method='post'>
	<table class='table table-striped table-responsive'>
		<thead>
			<tr>
				<th>Name</th>
				<th>Amount Owed To Raider</th>
			</tr>
		</thead>
		
		<tbody>
			<?php
				while($row = $sqlGroupResults->fetch_assoc()){
					echo "
						<tr>
							<td>
								<input type='checkbox' value='".$row['raiderId']."' id='raiderId[]' name='raiderId[]>
								<label class='form-check-label' for='raiderId[]'>
								".$row["raiderName"]."
								</label>
							</td>
							<td>".number_format($amountOwedPerRaider)."</td>
						</tr>";
				}
			?>
		</tbody>
	</table>
	<input type='hidden' class='form-control' name='orderId' id='orderId' value='<?php echo $orderId; ?>"'>
	<button type='submit' class='btn btn-danger' name ='remove' value='1'>Remove Selected From Group</button>
</form>
<br>
<h2>Raiders Not In Group</h2>
<form action='#' method='post'>
	<table class='table table-striped table-responsive'>
		<thead>
			<tr>
				<th>Name</th>
			</tr>
		</thead>
		
		<tbody>
			<?php
				while($row = $sqlAvailableResults->fetch_assoc()){
					echo "
						<tr>
							<td>
								<input type='checkbox' value='".$row['id']."' id='raiderId[]' name='raiderId[]>
								<label class='form-check-label' for='raiderId[]'>
								".$row["raiderName"]."
								</label>	
							</td>
						</tr>";
				}
			?>
		</tbody>
	</table>
	<input type='hidden' class='form-control' name='orderId' id='orderId' value='<?php echo $orderId; ?>"'>
	<button type='submit' class='btn btn-danger' name ='addToGroup' value='1'>Add Selected To Group</button>
</form>

<?php
require_once('footer.php');
?>