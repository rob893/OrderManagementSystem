<?php
require_once('header.php');

if(isset($_POST['paidInFull'])){
	
	$orderId = $_POST['paidInFull'];
	$amountPaid = $_POST['serviceCost'];
	$sqlInsert = $conn->prepare("UPDATE orders SET paidInFull = '1', amountPaid = ? WHERE id=?");
	$sqlInsert->bind_param('ii', $amountPaid, $orderId);
	//End 'filtering'
	
	if($sqlInsert->execute() === true){
		echo "<script type='text/javascript'>alert('Order has been paid in full!')</script>";
		$sqlInsert->close();
	} else {
		echo "<script type='text/javascript'>alert('Error: ".$sqlInsert->error."')</script>";
		$sqlInsert->close();
	}
}

if(isset($_POST['cancel'])){
	
	$orderId = $_POST['cancel'];
	$sqlInsert = $conn->prepare("UPDATE orders SET canceled = '1' WHERE id=?");
	$sqlInsert->bind_param('i', $orderId);
	//End 'filtering'
	
	if($sqlInsert->execute() === true){
		echo "<script type='text/javascript'>alert('Order was canceled!')</script>";
		$sqlInsert->close();
	} else {
		echo "<script type='text/javascript'>alert('Error: ".$sqlInsert->error."')</script>";
		$sqlInsert->close();
	}
}

if(isset($_POST['partialPayment'])){
	
	$orderId = $_POST['partialPayment'];
	$amount = $_POST['amount'];
	$amountPaid = $_POST['amountPaid'];
	$serviceCost = $_POST['serviceCost'];
	
	if($amount + $amountPaid >= $serviceCost){
		$sqlInsert = $conn->prepare("UPDATE orders SET paidInFull = '1', amountPaid = amountPaid + ? WHERE id=?");
		$sqlInsert->bind_param('ii', $amount, $orderId);
		
		if($sqlInsert->execute() === true){
			echo "<script type='text/javascript'>alert('With this payment, the customer has paid in full. Removing from the list of active orders.')</script>";
			$sqlInsert->close();
		} else {
			echo "<script type='text/javascript'>alert('error: ".$sqlinsert->error."')</script>";
			$sqlInsert->close();
		}
	} else {
		$sqlInsert = $conn->prepare("UPDATE orders SET amountPaid = amountPaid + ? WHERE id=?");
		$sqlInsert->bind_param('ii', $amount, $orderId);
	//end 'filtering'
	
		if($sqlInsert->execute() === true){
			echo "<script type='text/javascript'>alert('Partial payment applied to this order!')</script>";
			$sqlInsert->close();
		} else {
			echo "<script type='text/javascript'>alert('error: ".$sqlinsert->error."')</script>";
			$sqlInsert->close();
		}
	}
}

if(isset($_GET['buyerId'])){
	$buyerId = $_GET['buyerId'];
	$sqlOrders = "SELECT orders.id, dateOfService, buyerId, serviceId, paidInFull, amountPaid, buyerName, serviceName, serviceCost FROM orders 
					INNER JOIN buyers ON buyers.id = buyerId 
					INNER JOIN services ON services.id = serviceId
					WHERE paidInFull = '0' AND canceled ='0' AND buyerId = '$buyerId'
					ORDER BY dateOfService ASC, buyerName ASC";
} 

else if(isset($_GET['date']) && isset($_GET['time'])){
	$date = $_GET['date']." ".$_GET['time'];
	$sqlOrders = "SELECT orders.id, dateOfService, buyerId, serviceId, paidInFull, amountPaid, buyerName, serviceName, serviceCost FROM orders 
					INNER JOIN buyers ON buyers.id = buyerId 
					INNER JOIN services ON services.id = serviceId
					WHERE paidInFull = '0' AND canceled ='0' AND dateOfService LIKE '$date%'
					ORDER BY dateOfService ASC, buyerName ASC";
} 

else if(isset($_GET['date'])){
	$date = $_GET['date'];
	$sqlOrders = "SELECT orders.id, dateOfService, buyerId, serviceId, paidInFull, amountPaid, buyerName, serviceName, serviceCost FROM orders 
					INNER JOIN buyers ON buyers.id = buyerId 
					INNER JOIN services ON services.id = serviceId
					WHERE paidInFull = '0' AND canceled ='0' AND dateOfService LIKE '$date%'
					ORDER BY dateOfService ASC, buyerName ASC";
}

else if(isset($_GET['serviceId'])){
	$serviceId = $_GET['serviceId'];
	$sqlOrders = "SELECT orders.id, dateOfService, buyerId, serviceId, paidInFull, amountPaid, buyerName, serviceName, serviceCost FROM orders 
					INNER JOIN buyers ON buyers.id = buyerId 
					INNER JOIN services ON services.id = serviceId
					WHERE paidInFull = '0' AND canceled ='0' AND serviceId = '$serviceId'
					ORDER BY dateOfService ASC, buyerName ASC";
} 

else {
	$sqlOrders = "SELECT orders.id, dateOfService, buyerId, serviceId, paidInFull, amountPaid, buyerName, serviceName, serviceCost FROM orders 
					INNER JOIN buyers ON buyers.id = buyerId 
					INNER JOIN services ON services.id = serviceId
					WHERE paidInFull = '0' AND canceled ='0'
					ORDER BY dateOfService ASC, buyerName ASC";
}

$sqlOrdersResults = $conn->query($sqlOrders);

?>
<h2>Currently Open Orders</h2>
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
			<th>Paid in Full</th>
			<th>Partial Payment</th>
			<th>Cancel</th>
		</tr>
	</thead>
	
	<tbody>
		<?php
			$totalAmountOwed = 0;
			while($row = $sqlOrdersResults->fetch_assoc()){
				list($date, $time) = explode(" ", $row["dateOfService"], 2);
				$formattedTime = date("g:iA", strtotime($time));
				$amountDue = $row["serviceCost"] - $row["amountPaid"];
				$totalAmountOwed += $amountDue;
				echo "
					<tr>
						<td><a href=index.php?buyerId=".$row["buyerId"].">".$row["buyerName"]."</td>
						<td><a href=index.php?date=".$date.">".$date."</td>
						<td><a href=index.php?date=".$date."&time=".$time.">".$formattedTime."</td>
						<td><a href=index.php?serviceId=".$row["serviceId"].">".$row["serviceName"]."</td>
						<td>".number_format($row["serviceCost"])."</td>
						<td>".number_format($row["amountPaid"])."</td>
						<td>".number_format($amountDue)."</td>
						<td>
							<form action='#' method='post'>
								<input type='hidden' class='form-control' name='serviceCost' id='serviceCost' value='".$row['serviceCost']."'>
								<button type='submit' class='btn btn-info' name ='paidInFull' value='".$row['id']."'>Paid In Full</button>
							</form>
						</td>
						<td>
							<form action='#' method='post'>
								<input type='hidden' class='form-control' name='amountPaid' id='amountPaid' value='".$row['amountPaid']."'>
								<input type='hidden' class='form-control' name='serviceCost' id='serviceCost' value='".$row['serviceCost']."'>
								<input type='number' class='col-sm-4' name='amount' id='amount' placeholder='Amount' required>
								<button type='submit' class='btn btn-info' name ='partialPayment' value='".$row['id']."'>Apply Payment</button>
							</form>
						</td>
						<td>
							<form action='#' method='post'>
								<button type='submit' class='btn btn-danger' name ='cancel' value='".$row['id']."'>Cancel</button>
							</form>
						</td>
					</tr>";
			}
			echo "
				<tr>
					<td><b>Total Amount Due:</b></td>
					<td></td>
					<td></td>
					<td></td>
					<td></td>
					<td><b>".number_format($totalAmountOwed)."</b></td>
					<td></td>
					<td></td>
					<td></td>
				</tr>";
		?>
	</tbody>
</table>

<?php
require_once('footer.php');
?>