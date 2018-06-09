<?php

function GetClassColor($class){
    
    $color = '#FFFFFF';
    switch ($class){
        case "Death Knight":
            $color = '#C41F3B';
            break;
        case "Demon Hunter":
            $color = '#A330C9';
            break;
		case "DeathKnight":
            $color = '#C41F3B';
            break;
        case "DemonHunter":
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
            $color = '#7E7E7E';
            break;
        case "Rogue":
            $color = '#FFDD08';
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

function GetClassNameFromId($classId){
	$className = 'Unknown';
    switch ($classId){
        case 1:
            $className = 'Warrior';
            break;
        case 2:
            $className = 'Paladin';
            break;
        case 3:
            $className = 'Hunter';
            break;
        case 4:
            $className = 'Rogue';
            break;
        case 5:
            $className = 'Priest';
            break;
        case 6:
            $className = 'Death Knight';
            break;
        case 7:
            $className = 'Shaman';
            break;
        case 8:
            $className = 'Mage';
            break;
        case 9:
            $className = 'Warlock';
            break;
        case 10:
            $className = 'Monk';
            break;
        case 11:
            $className = 'Druid';
            break;
        case 12:
            $className = 'Demon Hunter';
            break;
    }
    
    return $className;
}

?>