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

function GetClassColor($class){
    
    $color = '#FFFFFF';
    switch ($class){
        case "Death Knight":
            $color = '#C41F3B';
            break;
        case "Demon Hunter":
            $color = '#A330C9';
            break;
        case "Druid":
            $color = '#FF7D0A';
            break;
        case "Hunter":
            $color = '#ABD473';
            break;
        case "Mage":
            $color = '#69CCF0';
            break;
        case "Monk":
            $color = '#00FF96';
            break;
        case "Paladin":
            $color = '#F58CBA';
            break;
        case "Priest":
            $color = '#FFFFFF';
            break;
        case "Rogue":
            $color = '#FFF569';
            break;
        case "Shaman":
            $color = '#0070DE';
            break;
        case "Warlock":
            $color = '#9482C9';
            break;
        case "Warrior":
            $color = '#C79C6E';
            break;
    }
    
    return $color;
}

if(isset($_POST['splitWithGuild'])){
    $sqlInsert = $conn->prepare("UPDATE orders SET splitWithGuild = ? WHERE dateOfService = ?");
    $sqlInsert->bind_param('is', $_POST['splitWithGuild'], $dateTime);
    //End 'filtering'
    
    if($sqlInsert->execute() === true){
        //echo "<script type='text/javascript'>alert('".$$raiderName." has been added to the database!')</script>";
        $sqlInsert->close();
    } else {
        echo "<script type='text/javascript'>alert('Error: ".$sqlInsert->error."')</script>";
        $sqlInsert->close();
    }
}


if(isset($_POST['addRaider'])){
    
    //The following takes the POST data and 'filters' it before inserting into the database to prevent SQL injection attacks.
    //All user input should be 'filtered' before inserting into the database.
    if(empty(trim($_POST['name']))){
        echo "<script type='text/javascript'>alert('Invalid name!')</script>";
    }
    else{
        $raiderName = strip_tags($_POST['name']);
        $raiderClass = strip_tags($_POST['class']);
        
        $raiderName = stripslashes($raiderName);
        $raiderClass = stripslashes($raiderClass);
        
        $raiderName = mysqli_real_escape_string($conn, $raiderName);
        $raiderClass = mysqli_real_escape_string($conn, $raiderClass);
        
        $sqlInsert = $conn->prepare("INSERT INTO raider(raiderName, class) VALUES(?, ?)");
        $sqlInsert->bind_param('ss', $raiderName, $raiderClass);
        //End 'filtering'
        
        if($sqlInsert->execute() === true){
            //echo "<script type='text/javascript'>alert('".$$raiderName." has been added to the database!')</script>";
            $sqlInsert->close();
        } else {
            echo "<script type='text/javascript'>alert('Error: ".$sqlInsert->error."')</script>";
            $sqlInsert->close();
        }
    }
}

if(isset($_POST['addToGroup']) && isset($_POST['orderId']) && !empty($_POST['raiderId'])){
	$orderIds = explode(",", $_POST['orderId']);
	foreach($orderIds as $orderId){
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

}

if(isset($_POST['remove']) && isset($_POST['orderId']) && !empty($_POST['raiderId'])){
    $orderIds = explode(",", $_POST['orderId']);
    foreach($orderIds as $orderId){
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
}

$sqlNumInGroup = "SELECT COUNT( DISTINCT raiderId) AS numRaiders FROM raiderOrderInvolved 
                    INNER JOIN orders ON raiderOrderInvolved.orderId = orders.id 
                    WHERE dateOfService = '$dateTime'";
$sqlNumInGroupResults = $conn->query($sqlNumInGroup);
$numInGroupArray = $sqlNumInGroupResults->fetch_assoc();
$numInGroup = $numInGroupArray['numRaiders'];

$sqlServiceCost = "SELECT serviceCost, serviceName, dateOfService, orders.id AS orderId, splitWithGuild, services.id AS serviceId FROM services 
                        INNER JOIN orders ON services.id = serviceId 
                        WHERE dateOfService = '$dateTime'";
$sqlServiceCostResults = $conn->query($sqlServiceCost);

$splitWithGuild = false;
$serviceCost = 0;
$orderId = [];
$services = [];
$numOrders = 0;
while($row = $serviceCostArray = $sqlServiceCostResults->fetch_assoc()){
    $serviceCost += $row['serviceCost'];
    $serviceName = $row['serviceName'];
    
    if(!isset($services[$serviceName])){
        $services[$serviceName] = 1;
    } else {
        $services[$serviceName]++;
    }
    
    if($row['splitWithGuild'] == 1){
        $splitWithGuild = true;
    } else {
        $splitWithGuild = false;
    }
    
    $orderId[$numOrders] = $row['orderId'];
    $numOrders++;
}

$orderIdArrString = implode("," , $orderId);

$amountOwedToGuild = $serviceCost / 2;

if($numInGroup == 0){
	$amountOwedPerRaider = 0;
} else {
    if($splitWithGuild){
        $amountOwedPerRaider = ($serviceCost / 2) / $numInGroup; 
    } else {
        $amountOwedPerRaider = $serviceCost / $numInGroup; 
    }
}


$sqlRaidersInGroup = "SELECT DISTINCT raiderName, class, raiderId FROM raiderOrderInvolved 
						INNER JOIN raider ON raiderId = raider.id
                        INNER JOIN orders ON orderId = orders.id 
						WHERE dateOfService = '$dateTime'
                        ORDER BY class ASC, raiderName ASC";
						
$sqlAvailableRaiders = "SELECT * FROM raider
							WHERE raiderName NOT 
							IN (
								SELECT raiderName FROM raiderOrderInvolved
								INNER JOIN raider ON raiderId = raider.id
                                INNER JOIN orders ON orderId = orders.id
								WHERE dateOfService =  '$dateTime'
							)
                            ORDER BY class ASC, raiderName ASC";
							
$sqlGroupResults = $conn->query($sqlRaidersInGroup);
$sqlAvailableResults = $conn->query($sqlAvailableRaiders);
?>
<h2>Raid Group for <?php echo $date." at ".$formattedTime; ?></h2>

<p>
    <b>Services for this time slot:</b>
    <br>
    <?php 
        foreach($services as $serviceName => $numService){
            echo $serviceName." x".$numService;
        }
    ?>
</p>

<p>
	<b>Total cost of services for this time solt:</b>
	<br>
    <?php 
        echo number_format($serviceCost);
    ?>
</p>

<form action='#' method='post' enctype='multipart/form-data'>
    <p>
    	<b>Split half the earnings with the guild?</b>
    	<br>
    		<?php 
        		if($splitWithGuild){
        		    echo "
                            Earnings owed to guild: ".number_format($amountOwedToGuild)."
                            <br>
                            <input type='radio' onChange='this.form.submit()' value='1' id='splitWithGuild' name='splitWithGuild' checked='checked'/> Yes
        		            <input type='radio' onChange='this.form.submit()' value='0' id='splitWithGuild' name='splitWithGuild'  /> No
                    ";
        		} else {
        		    echo "
            		    <input type='radio' onChange='this.form.submit()' value='1' id='splitWithGuild' name='splitWithGuild' /> Yes
            		    <input type='radio' onChange='this.form.submit()' value='0' id='splitWithGuild' name='splitWithGuild' checked='checked' /> No
                    ";
        		}
    		?>
    </p>
</form>

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

<h3>Raiders In Group: <?php echo $numInGroup; ?></h3>
<form action='#' method='post'>
	<table class='table table-striped table-responsive'>
		<thead>
			<tr>
				<th>Name</th>
				<th>Class</th>
				<th>Amount Owed To Raider</th>
			</tr>
		</thead>
		
		<tbody>
			<?php
				while($row = $sqlGroupResults->fetch_assoc()){
				    $classColor = GetClassColor($row['class']);
					echo "
						<tr>
							<td>
								<input type='checkbox' value='".$row['raiderId']."' id='raiderId[]' name='raiderId[]'>
								<label class='form-check-label' for='raiderId[]'>
                                <font color='".$classColor."'>".$row["raiderName"]."</font>
								</label>
							</td>
                            <td><font color='".$classColor."'>".$row['class']."</font></td>
							<td>".number_format($amountOwedPerRaider)."</td>
						</tr>";
				}
			?>
		</tbody>
	</table>
	<input type='hidden' class='form-control' name='orderId' id='orderId' value='<?php echo $orderIdArrString; ?>"'>
	<button type='submit' class='btn btn-danger' name ='remove' value='1'>Remove Selected From Group</button>
</form>
<br>
<h3>Raiders Not In Group</h3>
<form action='#' method='post'>
	<table class='table table-striped table-responsive'>
		<thead>
			<tr>
				<th>Name</th>
				<th>Class</th>
			</tr>
		</thead>
		
		<tbody>
			<?php
				while($row = $sqlAvailableResults->fetch_assoc()){
				    $classColor = GetClassColor($row['class']);
					echo "
						<tr>
							<td>
								<input type='checkbox' value='".$row['id']."' id='raiderId[]' name='raiderId[]>
								<label class='form-check-label' for='raiderId[]'>
								<font color='".$classColor."'>".$row["raiderName"]."</font>
								</label>	
							</td>
                            <td><font color='".$classColor."'>".$row['class']."</font></td>
						</tr>";
				}
			?>
		</tbody>
	</table>
	<input type='hidden' class='form-control' name='orderId' id='orderId' value='<?php echo $orderIdArrString; ?>"'>
	<button type='submit' class='btn btn-danger' name ='addToGroup' value='1'>Add Selected To Group</button>
</form>

<?php
require_once('footer.php');
?>