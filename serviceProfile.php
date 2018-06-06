<?php
require_once('header.php');

if(isset($_GET['serviceId'])){
	$serviceId = $_GET['serviceId'];
	$sqlName = "SELECT orders.id buyerId, serviceId, paidInFull, SUM(amountPaid) AS totalPaid, buyerName, serviceName, serviceCost FROM orders 
					INNER JOIN buyers ON buyers.id = buyerId 
					INNER JOIN services ON services.id = serviceId
					WHERE canceled ='0' AND serviceId = '$serviceId'
					ORDER BY dateOfService ASC, buyerName ASC";
	$sqlOpenOrders = "SELECT orders.id, dateOfService, buyerId, serviceId, paidInFull, amountPaid, buyerName, serviceName, serviceCost FROM orders 
					INNER JOIN buyers ON buyers.id = buyerId 
					INNER JOIN services ON services.id = serviceId
					WHERE paidInFull = '0' AND canceled ='0' AND serviceId = '$serviceId'
					ORDER BY dateOfService ASC, buyerName ASC";
	
	$sqlCompletedOrders = "SELECT orders.id, dateOfService, buyerId, serviceId, paidInFull, amountPaid, buyerName, serviceName, serviceCost FROM orders 
					INNER JOIN buyers ON buyers.id = buyerId 
					INNER JOIN services ON services.id = serviceId
					WHERE paidInFull = '1' AND canceled ='0' AND serviceId = '$serviceId'
					ORDER BY dateOfService ASC, buyerName ASC";
	
	$sqlCanceledOrders = "SELECT orders.id, dateOfService, buyerId, serviceId, paidInFull, amountPaid, buyerName, serviceName, serviceCost FROM orders 
					INNER JOIN buyers ON buyers.id = buyerId 
					INNER JOIN services ON services.id = serviceId
					WHERE canceled ='1' AND serviceId = '$serviceId'
					ORDER BY dateOfService ASC, buyerName ASC";
					
	$sqlOpenOrdersResults = $conn->query($sqlOpenOrders);
	$sqlCompletedOrdersResults = $conn->query($sqlCompletedOrders);
	$sqlCanceledOrdersResults = $conn->query($sqlCanceledOrders);
	$nameResults = $conn->query($sqlName);
	$name = $nameResults->fetch_assoc();
	?>
	<h2>Service Profile for <?php echo $name['serviceName']; ?></h2>
	<p>Total money earned from this service: <?php echo number_format($name['totalPaid']); ?></p>
	<br>
	<h3>Currently Open Orders</h3>
	<a href='index.php?serviceId=<?php echo $serviceId; ?>' class='btn btn-info' role='button'>Manage open orders for this service.</a>
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
			</tr>
		</thead>
		
		<tbody>
			<?php
				$totalAmountOwed = 0;
				$totalAmountPaid = 0;
				$totalServiceCost = 0;
				while($row = $sqlOpenOrdersResults->fetch_assoc()){
					list($date, $time) = explode(" ", $row["dateOfService"], 2);
					$formattedTime = date("g:iA", strtotime($time));
					$amountDue = $row["serviceCost"] - $row["amountPaid"];
					$totalAmountOwed += $amountDue;
					$totalAmountPaid += $row['amountPaid'];
					$totalServiceCost += $row['serviceCost'];
					echo "
						<tr>
							<td><a href=index.php?buyerId=".$row["buyerId"]." class='text-dark'>".$row["buyerName"]."</td>
							<td><a href=index.php?date=".$date." class='text-dark'>".$date."</td>
							<td><a href=index.php?date=".$date."&time=".$time." class='text-dark'>".$formattedTime."</td>
							<td><a href=index.php?serviceId=".$row["serviceId"]." class='text-dark'>".$row["serviceName"]."</td>
							<td>".number_format($row["serviceCost"])."</td>
							<td class='text-success'>".number_format($row["amountPaid"])."</td>
							<td class='text-danger'>".number_format($amountDue)."</td>
						</tr>";
				}
				echo "
					<tr>
						<td><b>Totals:</b></td>
						<td></td>
						<td></td>
						<td></td>
						<td><b>".number_format($totalServiceCost)."</b></td>
						<td class='text-success'><b>".number_format($totalAmountPaid)."</b></td>
						<td class='text-danger'><b>".number_format($totalAmountOwed)."</b></td>
					</tr>";
			?>
		</tbody>
	</table>
	
	<br>
	<h3>Completed Orders</h3>
	<a href='completedOrders.php?serviceId=<?php echo $serviceId; ?>' class='btn btn-info' role='button'>Manage completed orders for this service.</a>
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
			</tr>
		</thead>
		
		<tbody>
			<?php
				$totalAmountMade = 0;
				$totalAmountOwed = 0;
				$totalServiceCost = 0;
				while($row = $sqlCompletedOrdersResults->fetch_assoc()){
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
					</tr>";
			?>
		</tbody>
	</table>
	
	<br>
	<h3>Canceled Orders</h3>
	<a href='canceledOrders.php?serviceId=<?php echo $serviceId; ?>' class='btn btn-info' role='button'>Manage canceled orders for this service.</a>
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
			</tr>
		</thead>
		
		<tbody>
			<?php
				while($row = $sqlCanceledOrdersResults->fetch_assoc()){
					list($date, $time) = explode(" ", $row["dateOfService"], 2);
					$formattedTime = date("g:iA", strtotime($time));
					$amountDue = $row["serviceCost"] - $row["amountPaid"];
					echo "
						<tr>
							<td><a href=canceledOrders.php?buyerId=".$row["buyerId"]." class='text-dark'>".$row["buyerName"]."</td>
							<td><a href=canceledOrders.php?date=".$date." class='text-dark'>".$date."</td>
							<td><a href=canceledOrders.php?date=".$date."&time=".$time." class='text-dark'>".$formattedTime."</td>
							<td><a href=canceledOrders.php?serviceId=".$row["serviceId"]." class='text-dark'>".$row["serviceName"]."</td>
							<td>".number_format($row["serviceCost"])."</td>
							<td class='text-success'>".number_format($row["amountPaid"])."</td>
							<td class='text-danger'>".number_format($amountDue)."</td>
						</tr>";
				}
			?>
		</tbody>
	</table>
	
	<?php
}

else {
	header('Location: services.php');
}

require_once('footer.php');
?>