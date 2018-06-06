<?php
require_once('header.php');

if(isset($_POST['delete'])){
	
	$buyerId = $_POST['delete'];
	$sqlInsert = $conn->prepare("UPDATE buyers SET active = '0' WHERE id = ?");
	$sqlInsert->bind_param('i', $buyerId);
	//End 'filtering'
	
	if($sqlInsert->execute() === true){
		echo "<script type='text/javascript'>alert('Buyer deleted!')</script>";
		$sqlInsert->close();
	} else {
		echo "<script type='text/javascript'>alert('Error: ".$sqlInsert->error."')</script>";
		$sqlInsert->close();
	}
}

if(isset($_POST['addBuyer'])){
	
	//The following takes the POST data and 'filters' it before inserting into the database to prevent SQL injection attacks. 
	//All user input should be 'filtered' before inserting into the database.
	if(empty(trim($_POST['name']))){
		echo "<script type='text/javascript'>alert('Invalid name!')</script>";
	} 
	else{
		$$raiderName = strip_tags($_POST['name']);
		
		$$raiderName = stripslashes($$raiderName);

		$$raiderName = mysqli_real_escape_string($conn, $$raiderName);

		$sqlInsert = $conn->prepare("INSERT INTO buyers(buyerName) VALUES(?)");
		$sqlInsert->bind_param('s', $$raiderName);
		//End 'filtering'
		
		if($sqlInsert->execute() === true){
			echo "<script type='text/javascript'>alert('".$$raiderName." has been added to the database!')</script>";
			$sqlInsert->close();
		} else {
			echo "<script type='text/javascript'>alert('Error: ".$sqlInsert->error."')</script>";
			$sqlInsert->close();
		}
	}
}

$sqlBuyers = "SELECT * FROM buyers WHERE active = '1' ORDER BY buyerName ASC";

$sqlBuyersResults = $conn->query($sqlBuyers);
?>

<h2>Add Buyer</h2>
<button type="button" class="btn btn-info" data-toggle="collapse" data-target="#addBuyer">Add Buyer</button>
<div id="addBuyer" class="collapse">
	<form action='#' method='post' enctype='multipart/form-data'>
		<div class='row'>
			<div class='col-sm-2'>
				<div class='form-group'>
					<br>
					<label for='name'>Buyer Name:</label>
					<input type='text' class='form-control' name='name' id='name' placeholder='Name' required>
					<br>
					<button type='submit' class='btn btn-info' name ='addBuyer'>Submit</button>
				</div>
			</div>
		</div>
	</form>
</div>

<p></p>
<h2>Saved Buyers</h2>
<table class='table table-striped table-responsive'>
	<thead>
		<tr>
			<th>Name</th>
			<th>Delete</th>
		</tr>
	</thead>
	
	<tbody>
		<?php
			while($row = $sqlBuyersResults->fetch_assoc()){
				echo "
					<tr>
						<td>".$row["buyerName"]."</td>
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