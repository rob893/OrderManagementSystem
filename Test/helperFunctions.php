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


?>