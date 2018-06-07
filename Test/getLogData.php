<?php
require_once('header.php');

$apiKey = '5de109b057aa700ff06f77487c0dc3ca';

if(isset($_GET['date'])){
	$date = $_GET['date']."  00:00:00";
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
							<td><a href=getLogData.php?logId=".$log['id'].">".$log['title']."</td>
							<td>".$log['owner']."</td>
						</tr>";
				}
			?>
		</tbody>
	</table>
	
	<?php
}

else if(isset($_GET['logId'])){
	$logId = $_GET['logId'];

	$logsLink = 'https://www.warcraftlogs.com/v1/report/fights/'.$logId.'?api_key='.$apiKey;
	
	$totalAmountMade = 0;
	$data = json_decode(file_get_contents($logsLink), true);
	$fights = [];
	foreach($data['fights'] as $fight){
		if((isset($fight['kill']) && $fight['kill'] == 1) && (($fight['difficulty'] == 5 && $fight['name'] == "Gul'dan") || ($fight['difficulty'] == 4 && $fight['name'] == "Argus the Unmaker"))){
			$fights[$fight['id']] = $fight['name'];
			if($fight['name'] == "Gul'dan"){
				$totalAmountMade += 800000;
			}
			else if($fight['name'] == "Argus the Unmaker"){
				$totalAmountMade += 100000;
			}
		}
	}
	
	$guildCut = $totalAmountMade / 2;
	
	
	$playersInCarryFights = [];
	foreach($data['friendlies'] as $friendly){
		if(!($friendly['type'] == 'NPC' || $friendly['type'] == 'Pet')){
			foreach($fights as $fightId => $bossName){
				$numParticipants = 0;
				foreach($friendly['fights'] as $fightParticipated){
					if($fightParticipated['id'] == $fightId){
						$playersInCarryFights[$fightId]['NumParticipant'] = $numParticipants;
						$playersInCarryFights[$fightId]['BossName'] = $bossName;
						$playersInCarryFights[$fightId]['Participants'][$friendly['name']] = $friendly['type'];
					}
				}
			}
		}
	}
	foreach($playersInCarryFights as $fightId => $fight){
		foreach($fight['Participants'] as $player){
			if(!isset($playersInCarryFights[$fightId]['NumParticipants'])){
				$playersInCarryFights[$fightId]['NumParticipants'] = 1;
			} else {
				$playersInCarryFights[$fightId]['NumParticipants']++;
			}
		}
	}
	
	
	
	$masterTable = [];
	foreach($playersInCarryFights as $fight){
		foreach($fight['Participants'] as $player => $class){
			$masterTable[$player]['amountOwed'] = 0;
			if(!isset($masterTable[$player][$fight['BossName']])){
				$masterTable[$player][$fight['BossName']] = 1;
			} else {
				$masterTable[$player][$fight['BossName']]++;
			}
			if($fight['BossName'] == "Gul'dan"){
				$masterTable[$player]['amountOwed'] += 800000;
			}
			else if($fight['BossName'] == "Argus the Unmaker"){
				$masterTable[$player]['amountOwed'] += 100000;
			}
		}
	}
	//echo '<pre>';
	//print_r($masterTable);
	//print_r($playersInCarryFights);
	//print_r($data);
	//echo '</pre>';
	
	?>
	<h2>Players Participating in Sales Fights:</h2>
	<h3>Master Table</h3>
	<table class='table table-striped table-responsive'>
		<thead>
			<tr>
				<th>Participants</th>
				<th>Bosses In On</th>
				<th>Amount Owed To Raider</th>
			</tr>
		</thead>
		
		<tbody>
			<?php
				foreach($masterTable as $player => $bosses){
					//$color = GetClassColor($class);
					echo "
						<tr>
							<td>".$player."</td>
							<td>";
								foreach($bosses as $boss => $numberKilled){
									if($boss == 'amountOwed'){ //sloppy
										//do nothing
									} 
									else {
										echo $boss." x".$numberKilled."<br>";
									}
								}			
					echo "
							</td>
							<td>WIP ".$bosses['amountOwed']."</td>
						</tr>";
				}
			?>
		</tbody>
	</table>
		
	<?php
	foreach($playersInCarryFights as $fight){
		echo "<h3>".$fight['BossName']."</h3>";
		?>
		<table class='table table-striped table-responsive'>
			<thead>
				<tr>
					<th>Participants</th>
					<th>Class</th>
				</tr>
			</thead>
			
			<tbody>
				<?php
					foreach($fight['Participants'] as $player => $class){
						$color = GetClassColor($class);
						echo "
							<tr>
								<td><font color='".$color."'>".$player."</font></td>
								<td><font color='".$color."'>".$class."</font></td>
							</tr>";
					}
				?>
			</tbody>
		</table>
		
		<?php
	}
}

else {
	
?>

<form action='#' method='get' enctype='multipart/form-data'>
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