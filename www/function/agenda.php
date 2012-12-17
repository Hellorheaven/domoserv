<?php
require_once 'GoogleAgenda.php';
require_once 'GoogleAgendaEvent.php';
require_once 'GoogleAgendaException.php';

// Recupere les variables passes en parametre de l'URL
$url_cal=$_GET['cal'];
$decode=$_GET['decode'];
$defaut=$_GET['defaut'];


// On remplit le tableau qui permet le decodage
$items = explode(",", $decode); 
            
            for($n = 0, $m = count($items); $n < $m; $n=$n+2){ 
                $tableau_decode[$items[$n]] = $items[$n+1]; 
            } 
            
               

// Lecture de l'agenda
try {
    $oAgendaConges = new GoogleAgenda("https://www.google.com/calendar/feeds/".$url_cal."/basic");  // Compléter ici par l'url privée de l'agenda Google
   
    $aAujourdhui = $oAgendaConges->getEvents(array(
      'startmin' => date('Y-m-d'),
        'startmax' => date('Y-m-d',strtotime("+24 hours")),
        'sortorder' => 'ascending',
        'orderby' => 'starttime',
        'maxresults' => '1',
        'startindex' => '1',
        'search' => '',
        'singleevents' => 'true',
        'futureevents' => 'false',
        'timezone' => 'Europe/Paris',
        'showdeleted' => 'false'
    ));
   $aDemain = $oAgendaConges->getEvents(array(
      'startmin' => date('Y-m-d',strtotime("+24 hours")),
      'startmax' => date('Y-m-d',strtotime("+48 hours")),
      'sortorder' => 'ascending',
      'orderby' => 'starttime',
      'maxresults' => '1',
      'startindex' => '1',
      'search' => '',
      'singleevents' => 'true',
      'futureevents' => 'false',
      'timezone' => 'Europe/Paris',
      'showdeleted' => 'false'
    ));
   
   echo '<?xml version="1.0" encoding="utf8" ?>';
	echo '<calendrier>';
// Lecture de l'agenda du jour

if ($aAujourdhui) { 
   foreach ($aAujourdhui as $oAujourdhui) {
         // transforme l'intitule en code pour qu'il soit plus facilement utilisable sous la zibase
         echo '<aujourdhui>'.array_search(($oAujourdhui->getTitle()),$tableau_decode).'</aujourdhui>';
    }
} else {
		 echo '<aujourdhui>' . $defaut . '</aujourdhui>'; 
}


// Lecture de l'agenda de demain  
if ($aDemain) {  
   foreach ($aDemain as $oDemain) {
         // transforme l'intitule en code pour qu'il soit plus facilement utilisable sous la zibase
         echo '<demain>'. array_search(($oDemain->getTitle()),$tableau_decode) . '</demain>';
    }
} else {
		 echo '<demain>' . $defaut . '</demain>'; 
}	
	echo '</calendrier>';
}
	
	
	
catch (GoogleAgendaException $e) {
    echo $e->getMessage();
}



?>
