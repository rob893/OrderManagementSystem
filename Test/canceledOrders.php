<?php
require_once('header.php');

if(isset($_POST['reopenCanceled'])){
	
	$orderId = $_POST['reopenCanceled'];
	$sqlInsert = $conn->prepare("UPDATE orders SET canceled = '0' WHERE id=?");
	$sqlInsert->bind_param('i', $orderId);
	//End 'filtering'
	
	if($sqlInsert->execute() === true){
		echo "<script type='text/javascript'>alert('Canceled order was reopened!')</script>";
		$sqlInsert->close();
	} else {
		echo "<script type='text/javascript'>alert('Error: ".$sqlInsert->error."')</script>";
		$sqlInsert->close();
	}
}

if(isset($_POST['delete'])){
	
	$orderId = $_POST['delete'];
	$sqlInsert = $conn->prepare("DELETE FROM orders WHERE id=?");
	$sqlInsert->bind_param('i', $orderId);
	//End 'filtering'
	
	if($sqlInsert->execute() === true){
		echo "<script type='text/javascript'>alert('Canceled order was deleted permanently!')</script>";
		$sqlInsert->close();
	} else {
		echo "<script type='text/javascript'>alert('Error: ".$sqlInsert->error."')</script>";
		$sqlInsert->close();
	}
}

if(isset($_GET['buyerId'])){
	$buyerId = $_GET['buyerId'];
	$sqlCanceledOrders = "SELECT orders.id, dateOfService, buyerId, serviceId, paidInFull, amountPaid, buyerName, serviceName, serviceCost FROM orders 
					INNER JOIN buyers ON buyers.id = buyerId 
					INNER JOIN services ON services.id = serviceId
					WHERE canceled ='1' AND buyerId = '$buyerId'
					ORDER BY dateOfService ASC, buyerName ASC";
}

else if(isset($_GET['date']) && isset($_GET['time'])){
	$date = $_GET['date']." ".$_GET['time'];
	$sqlCanceledOrders = "SELECT orders.id, dateOfService, buyerId, serviceId, paidInFull, amountPaid, buyerName, serviceName, serviceCost FROM orders 
					INNER JOIN buyers ON buyers.id = buyerId 
					INNER JOIN services ON services.id = serviceId
					WHERE canceled ='1' AND dateOfService LIKE '$date%'
					ORDER BY dateOfService ASC, buyerName ASC";
} 

else if(isset($_GET['date'])){
	$date = $_GET['date'];
	$sqlCanceledOrders = "SELECT orders.id, dateOfService, buyerId, serviceId, paidInFull, amountPaid, buyerName, serviceName, serviceCost FROM orders 
					INNER JOIN buyers ON buyers.id = buyerId 
					INNER JOIN services ON services.id = serviceId
					WHERE canceled ='1' AND dateOfService LIKE '$date%'
					ORDER BY dateOfService ASC, buyerName ASC";
}

else if(isset($_GET['serviceId'])){
	$serviceId = $_GET['serviceId'];
	$sqlCanceledOrders = "SELECT orders.id, dateOfService, buyerId, serviceId, paidInFull, amountPaid, buyerName, serviceName, serviceCost FROM orders 
					INNER JOIN buyers ON buyers.id = buyerId 
					INNER JOIN services ON services.id = serviceId
					WHERE canceled ='1' AND serviceId = '$serviceId'
					ORDER BY dateOfService ASC, buyerName ASC";
}

else{
	$sqlCanceledOrders = "SELECT orders.id, dateOfService, buyerId, serviceId, paidInFull, amountPaid, buyerName, serviceName, serviceCost FROM orders 
							INNER JOIN buyers ON buyers.id = buyerId 
							INNER JOIN services ON services.id = serviceId
							WHERE canceled = '1'
							ORDER BY dateOfService ASC, buyerName ASC";
}

$sqlCanceledResults = $conn->query($sqlCanceledOrders);
?>


<h2>Canceled Orders</h2>
<table class='table table-striped'>
	<thead>
		<tr>
			<th>Buyer Name</th>
			<th>Service Date</th>
			<th>Time</th>
			<th>Service</th>
			<th>Service Cost</th>
			<th>Amount Paid</th>
			<th>Amount Due</th>
			<th>Delete Permanently</th>
			<th>Reopen</th>
		</tr>
	</thead>
	
	<tbody>
		<?php
			while($row = $sqlCanceledResults->fetch_assoc()){
				list($date, $time) = explode(" ", $row["dateOfService"], 2);
				$formattedTime = date("g:iA", strtotime($time));
				$amountDue = $row["serviceCost"] - $row["amountPaid"];
				echo "
					<tr>
						<td><a href=canceledOrders.php?buyerId=".$row["buyerId"].">".$row["buyerName"]."</td>
						<td><a href=canceledOrders.php?date=".$date.">".$date."</td>
						<td><a href=canceledOrders.php?date=".$date."&time=".$time.">".$formattedTime."</td>
						<td><a href=canceledOrders.php?serviceId=".$row["serviceId"].">".$row["serviceName"]."</td>
						<td>".number_format($row["serviceCost"])."</td>
						<td>".number_format($row["amountPaid"])."</td>
						<td>".number_format($amountDue)."</td>
						<td>
							<form action='#' method='post'>
								<button type='submit' class='btn btn-danger' name ='delete' value='".$row['id']."'>Delete</button>
							</form>
						</td>
						<td>
							<form action='#' method='post'>
								<input type='number' class='col-sm-4' name='amount' id='amount' placeholder='Amount' required>
								<button type='submit' class='btn btn-danger' name ='reopenCanceled' value='".$row['id']."'>Reopen</button>
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
