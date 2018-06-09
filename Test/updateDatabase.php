<?php
require_once('header.php');

$apiGetMembersLink = 'https://us.api.battle.net/wow/guild/Turalyon/Hypnotic?fields=members&locale=en_US&apikey=mup5b7wnrf4rtxd5ykb6q9vj8bsxzap4';

$data = json_decode(file_get_contents($apiGetMembersLink), true);

foreach($data['members'] as $index => $characterInfo){
	$characterName = $characterInfo['character']['name'];
	$class = GetClassNameFromId($characterInfo['character']['class']);
	
	$sqlInsert = $conn->prepare('INSERT IGNORE INTO raider (raiderName, class) VALUES (?, ?) ');
	$sqlInsert->bind_param('ss', $characterName, $class);
	$sqlInsert->execute();
}


require_once('footer.php');
?>