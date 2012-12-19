<?php
require_once("./function/notify.php");
date_default_timezone_set('Europe/Paris');
$now = date('H \h\e\u\r\e\ i');

switch ($_GET["sonde"]){
  case "VS419920642":
    $title = "Urgence Bracelet Telepresence";
    $message = "Activation bracelet FLORINDA a ".$now;
    break;
  case "VS1394965250":
    $title = "Urgence Bracelet Telepresence";
    $message = "Activation bracelet JOAO a ".$now;
    break;
  case "192.168.210.30":
    $title = "Urgence Camera Dependance";
    $message = "La camera est arrete depuis ".$now;
    break;
  case "ZA3":
    break;
  default:
    $sonde=substr($_GET["sonde"],0,12);
    if ($sonde<>"192.168.210.") {
      $title="Test de notification ".$_GET["sonde"];
      $message="Activation Sonde Inconnu ".$_GET["sonde"]." ".$sonde." a ".$now;
    }
    else {
    }
}

$result = notify('4dd36e38ad3ad27e9f40091916dbbab8',$title, $message, $url);
if( $result['success'] == FALSE )
{
  print $result['error'];
}
else
{
  print $result['message'];
}



?>
