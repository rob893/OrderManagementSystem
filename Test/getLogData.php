<?php
require_once('header.php');

$apiKey = '5de109b057aa700ff06f77487c0dc3ca';

if(isset($_POST['getLogs'])){
	$date = $_POST['date']."  00:00:00";
	$dateTimestamp = strtotime($date);
	$dateTimestamp = $dateTimestamp * 1000;
	$logsLink = 'https://www.warcraftlogs.com/v1/reports/guild/Hypnotic/Turalyon/US?start='.$dateTimestamp.'&api_key='.$apiKey;
	
	$data = json_decode(file_get_contents($logsLink), true);
	
	?>
	<h3>Select log</h3>
	<form action='#' method='post' enctype='multipart/form-data'>
	<table class='table table-striped table-responsive'>
		<thead>
			<tr>
				<th>Log Name</th>
				<th>Owner</th>
			</tr>
		</thead>
		
		<tbody>
			<?php
				foreach($data as $log){
					echo "
						<tr>
							<td><a href=getLogData.php?logId=".$log['id']." class='text-dark'>".$log['title']."</td>
							<td>".$log['owner']."</td>
						";
				}
			?>
		</tbody>
	</table>
	
	<?php
}

else if(isset($_GET['logId'])){
	$logId = $_GET['logId'];

	$logsLink = 'https://www.warcraftlogs.com/v1/report/fights/'.$logId.'?api_key='.$apiKey;
	
	$data = json_decode(file_get_contents($logsLink), true);
	
	echo '<pre>';
	print_r($data);
	echo '</pre>';
	
	?>
	
	<?php
}

else {
	
?>

<form action='#' method='post' enctype='multipart/form-data'>
	<div class='row'>
		<div class='form-group col-sm-2'>
			<label for="date">Date of Log (all logs from this date to the current date will be obtained):</label>
			<input type='date' class='form-control' name='date' id='date' required>
		</div>
	</div>
	
	
	<button type='submit' class='btn btn-info' name ='getLogs'>Submit</button>
</form>


<?php
}
require_once('footer.php');
?>