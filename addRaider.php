<?php
require_once('header.php');

if(isset($_POST['addRaider'])){
    
    if(empty(trim($_POST['name']))){
        echo "<script type='text/javascript'>alert('Invalid name!')</script>";
    }
    else{
        $raiderName = $_POST['name'];
        $raiderClass = $_POST['class'];
        
        $sqlInsert = $conn->prepare("INSERT INTO raider(raiderName, class) VALUES(?, ?)");
        $sqlInsert->bind_param('ss', $raiderName, $raiderClass);
        //End 'filtering'
        
        if($sqlInsert->execute() === true){
            echo "<script type='text/javascript'>alert('".$raiderName." has been added to the database!')</script>";
            $sqlInsert->close();
        } else {
            echo "<script type='text/javascript'>alert('Error: ".$sqlInsert->error."')</script>";
            $sqlInsert->close();
        }
    }
}

if(isset($_POST['mapAlt'])){
    
	list($mainName, $mainId) = explode("-", $_POST['mainName']);
	list($altName, $altId) = explode("-", $_POST['altId']);
	
	$sqlInsert = $conn->prepare("UPDATE raider SET mainName = NULL WHERE id = ?");
	$sqlInsert->bind_param('i', $mainId);
	$sqlInsert->execute();
	
	$sqlInsert = $conn->prepare("UPDATE raider SET mainName = ? WHERE id = ?");
	$sqlInsert->bind_param('si', $mainName, $altId);
	
	if($sqlInsert->execute() === true){
		echo "<script type='text/javascript'>alert('Alt ".$altName." has been mapped to main ".$mainName."!')</script>";
		$sqlInsert->close();
	} else {
		echo "<script type='text/javascript'>alert('Error: ".$sqlInsert->error."')</script>";
		$sqlInsert->close();
    }
}


$sqlRaiders = "SELECT * FROM raider ORDER BY raiderName ASC";
$raidersResults = $conn->query($sqlRaiders);

$raiders = [];
while($row = $raidersResults->fetch_assoc()){
	$raiders[$row['id']] = $row['raiderName'];
}

?>

<button type="button" class="btn btn-info" data-toggle="collapse" data-target="#addRaider">Add Raider to Database</button>
<div id="addRaider" class="collapse">
	<form action='#' method='post' enctype='multipart/form-data'>
		<div class='row'>
			<div class='col-sm-2'>
				<div class='form-group'>
					<br>
					<label for='name'>Raider Name:</label>
					<input type='text' class='form-control' name='name' id='name' placeholder='Name' required>
					<br>
					<label for='class'>Class:</label>
					<select class='form-control' id='class' name='class' required>
						<option value='Death Knight'>Death Knight</option>
						<option value='Demon Hunter'>Demon Hunter</option>
						<option value='Druid'>Druid</option>
						<option value='Hunter'>Hunter</option>
						<option value='Mage'>Mage</option>
						<option value='Monk'>Monk</option>
						<option value='Paladin'>Paladin</option>
						<option value='Priest'>Priest</option>
						<option value='Rogue'>Rogue</option>
						<option value='Shaman'>Shaman</option>
						<option value='Warlock'>Warlock</option>
						<option value='Warrior'>Warrior</option>
					</select>
					<br>
					<button type='submit' class='btn btn-info' name ='addRaider'>Submit</button>
				</div>
			</div>
		</div>
	</form>
</div>

<p></p>

<button type="button" class="btn btn-info" data-toggle="collapse" data-target="#mapAlt">Map Alt to Main</button>
<div id="mapAlt" class="collapse">
	<form action='#' method='post' enctype='multipart/form-data'>
		<div class='row'>
			<div class='col-sm-2'>
				<div class='form-group'>
					<br>
					<label for='mainName'>Main Name:</label>
					<select class='form-control' id='mainName' name='mainName' required>
						<?php
							foreach($raiders as $raiderId => $raiderName){
								echo "
									<option value='".$raiderName."-".$raiderId."'>".$raiderName."</option>
								";
							}
						?>
					</select>
					<br>
					<label for='altName'>Alt Name:</label>
					<select class='form-control' id='altId' name='altId' required>
						<?php
							foreach($raiders as $raiderAltId => $altName){
								echo "
									<option value='".$altName."-".$raiderAltId."'>".$altName."</option>
								";
							}
						?>
					</select>
					<br>
					<button type='submit' class='btn btn-info' name ='mapAlt'>Submit</button>
				</div>
			</div>
		</div>
	</form>
</div>


<?php
require_once('footer.php');
?>