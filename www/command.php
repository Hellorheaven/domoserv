<?php
require_once("./inc/ZiBase.php");
$zibase = new ZiBase("192.168.210.4");
$module =strtoupper($_GET['module']);
$protocol = constant(sprintf('ZbProtocol::%s',strtoupper($_GET['protocol'])));
$ordre = strtoupper($_GET['ordre']);
$dim = ($_GET['dim']);
$dimLevel = '45%';

switch ($ordre)
{
 case STATE:
  $etat = $zibase->getState($module);
  $dateinfo = $zibase->getZwaveSensorInfo($module,"ON");
  echo "ON: ".$dateinfo[0]."<br/>";
  //echo "Heure du dernier declenchement : ". $dateinfo->format("d/m/Y H:i:s") . "<br/>";
  $dateinfo = $zibase->getZwaveSensorInfo($module,"OFF");
  echo "OFF: ".$dateinfo[0]."<br/>";
  //echo "Heure de la derniere mise a zero : ". $dateinfo->format("d/m/Y H:i:s") . "<br/>";
  print $etat."<br/>";
  echo "Heure du releve ".$info[0]."<br/>";
  echo "Temperature:".$info[1]."C<br/>";
  echo "Humidite ".$info[2]."%<br/>";
  echo "Batterie ".$info[3]."<br/>";
  break;

 case ON:
  $zibase->sendCommand($module, ZbAction::ON, $protocol);
  break;

 case OFF:
  $zibase->sendCommand($module, ZbAction::OFF, $protocol);
  break;

 case DIM:
  $zibase->sendCommand($module, ZbAction::DIM_BRIGHT, $protocol, $dimLevel);
  break;
}
