<?php
require_once('header.php');

if(isset($_POST['password']) && $_POST['password'] == 'hypnotic'){
	$sqlDeleteOrders = "DELETE * FROM orders";
	$sqlDeleteBuyers = "DELETE * FROM buyers";
	$sqlDeleteServices = "DELETE * FROM services";
	
	$deleteOrdersResults = $conn->query($sqlDeleteOrders);
	//$conn->query($sqlDeleteBuyers);
	//$conn->query($sqlDeleteServices);
	echo "<script type='text/javascript'>alert('Everything has been deleted!')</script>";
}

else if(isset($_POST['password']) && $_POST['password'] != 'hypnotic'){
	echo "<script type='text/javascript'>alert('Incorrect password')</script>";
}
?>
<h3>Delete Everything</h3>
<p>Click the button to delete everything from the database.</p>
<div class='row'>
	<div class='col-sm-4'>
		<div class='alert alert-danger'>
			WARNING! This will delete EVERYTHING and NOTHING will be recoverable!
		</div>
	</div>
</div>
<form action='#' method='post'>
	<div class='row'>
		<div class='col-sm-2'>
			<div class='form-group'>
				<label for='password'>Password:</label>
				<input type='text' class='form-control' name='password' id='password' placeholder='Password' required>
			</div>
		</div>
	</div>
	<button type='submit' class='btn btn-danger' name ='delete'>Delete Everything</button>
</form>

<?php
require_once('footer.php');
?>