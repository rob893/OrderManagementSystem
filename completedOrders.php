<?php
require_once('header.php');

if(isset($_POST['reopen'])){
	
	$orderId = $_POST['reopen'];
	$amountPaid = $_POST['amount'];
	$sqlInsert = $conn->prepare("UPDATE orders SET paidInFull = '0', amountPaid = ? WHERE id=?");
	$sqlInsert->bind_param('ii', $amountPaid, $orderId);
	//End 'filtering'
	
	if($sqlInsert->execute() === true){
		echo "<script type='text/javascript'>alert('Order has been reopened! Amount paid for this order set to: ".$amountPaid."')</script>";
		$sqlInsert->close();
	} else {
		echo "<script type='text/javascript'>alert('Error: ".$sqlInsert->error."')</script>";
		$sqlInsert->close();
	}
}

if(isset($_GET['buyerId'])){
	$buyerId = $_GET['buyerId'];
	$sqlOrders = "SELECT orders.id, dateOfService, buyerId, serviceId, paidInFull, amountPaid, buyerName, serviceName, serviceCost FROM orders 
					INNER JOIN buyers ON buyers.id = buyerId 
					INNER JOIN services ON services.id = serviceId
					WHERE paidInFull = '1' AND canceled ='0' AND buyerId = '$buyerId'
					ORDER BY dateOfService ASC, buyerName ASC";
}

else if(isset($_GET['date']) && isset($_GET['time'])){
	$date = $_GET['date']." ".$_GET['time'];
	$sqlOrders = "SELECT orders.id, dateOfService, buyerId, serviceId, paidInFull, amountPaid, buyerName, serviceName, serviceCost FROM orders 
					INNER JOIN buyers ON buyers.id = buyerId 
					INNER JOIN services ON services.id = serviceId
					WHERE paidInFull = '1' AND canceled ='0' AND dateOfService LIKE '$date%'
					ORDER BY dateOfService ASC, buyerName ASC";
} 

else if(isset($_GET['date'])){
	$date = $_GET['date'];
	$sqlOrders = "SELECT orders.id, dateOfService, buyerId, serviceId, paidInFull, amountPaid, buyerName, serviceName, serviceCost FROM orders 
					INNER JOIN buyers ON buyers.id = buyerId 
					INNER JOIN services ON services.id = serviceId
					WHERE paidInFull = '1' AND canceled ='0' AND dateOfService LIKE '$date%'
					ORDER BY dateOfService ASC, buyerName ASC";
}

else if(isset($_GET['serviceId'])){
	$serviceId = $_GET['serviceId'];
	$sqlOrders = "SELECT orders.id, dateOfService, buyerId, serviceId, paidInFull, amountPaid, buyerName, serviceName, serviceCost FROM orders 
					INNER JOIN buyers ON buyers.id = buyerId 
					INNER JOIN services ON services.id = serviceId
					WHERE paidInFull = '1' AND canceled ='0' AND serviceId = '$serviceId'
					ORDER BY dateOfService ASC, buyerName ASC";
}

else{
	$sqlOrders = "SELECT orders.id, dateOfService, buyerId, serviceId, paidInFull, amountPaid, buyerName, serviceName, serviceCost FROM orders 
					INNER JOIN buyers ON buyers.id = buyerId 
					INNER JOIN services ON services.id = serviceId
					WHERE paidInFull = '1' AND canceled ='0'
					ORDER BY dateOfService ASC, buyerName ASC";
}

$sqlOrdersResults = $conn->query($sqlOrders);
?>



<h2>Completed Orders</h2>
<table class='table table-striped table-responsive'>
	<thead>
		<tr>
			<th>Buyer Name</th>
			<th>Service Date</th>
			<th>Time</th>
			<th>Service</th>
			<th>Service Cost</th>
			<th>Amount Paid</th>
			<th>Amount Due</th>
			<th>Reopen</th>
		</tr>
	</thead>
	
	<tbody>
		<?php
			$totalAmountMade = 0;
			$totalAmountOwed = 0;
			$totalServiceCost = 0;
			while($row = $sqlOrdersResults->fetch_assoc()){
				list($date, $time) = explode(" ", $row["dateOfService"], 2);
				$formattedTime = date("g:iA", strtotime($time));
				$amountDue = $row["serviceCost"] - $row["amountPaid"];
				$totalAmountMade += $row['amountPaid'];
				$totalAmountOwed += $amountDue;
				$totalServiceCost += $row['serviceCost'];
				echo "
					<tr>
						<td><a href=completedOrders.php?buyerId=".$row["buyerId"]." class='text-dark'>".$row["buyerName"]."</td>
						<td><a href=completedOrders.php?date=".$date." class='text-dark'>".$date."</td>
						<td><a href=completedOrders.php?date=".$date."&time=".$time." class='text-dark'>".$formattedTime."</td>
						<td><a href=completedOrders.php?serviceId=".$row["serviceId"]." class='text-dark'>".$row["serviceName"]."</td>
						<td>".number_format($row["serviceCost"])."</td>
						<td class='text-success'>".number_format($row["amountPaid"])."</td>
						<td class='text-danger'>".number_format($amountDue)."</td>
						<td>
							<form action='#' method='post'>
								<input type='number' min='0' max='100000000' onkeydown='javascript: return event.keyCode !== 69' class='col-sm-4' name='amount' id='amount' placeholder='Amount' required>
								<button type='submit' class='btn btn-danger' name ='reopen' value='".$row['id']."'>Reopen</button>
							</form>
						</td>
					</tr>";
			}
			echo "
				<tr>
					<td><b>Totals:</b></td>
					<td></td>
					<td></td>
					<td></td>
					<td><b>".number_format($totalServiceCost)."</b></td>
					<td class='text-success'><b>".number_format($totalAmountMade)."</b></td>
					<td class='text-danger'><b>".number_format($totalAmountOwed)."</b></td>
					<td></td>
				</tr>";
		?>
	</tbody>
</table>

<?php
require_once('footer.php');
?>