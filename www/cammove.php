<?php
require_once("./function/camera.php");
/*
http://ipcam_url/live.htm
http://ipcam_url/videostream.cgi
http://ipcam_url/videostream.asf?user=username&pwd=password
http://ipcam_url/snapshot.cgi
http://ipcam_url/videostream.asf?user=username&pwd=password
http://ipcam_url/decoder_control.cgi?command=1(STOP)
http://ipcam_url/decoder_control.cgi?command=6(LEFT)
http://ipcam_url/decoder_control.cgi?command=4(RIGHT)
http://ipcam_url/decoder_control.cgi?command=2(UP)
http://ipcam_url/decoder_control.cgi?command=0(DOWN)
http://ipcam_url/decoder_control.cgi?command=93(UP-LEFT)
http://ipcam_url/decoder_control.cgi?command=92(UP-RIGHT)
http://ipcam_url/decoder_control.cgi?command=91(DOWN-LEFT)
http://ipcam_url/decoder_control.cgi?command=90(DOWN-RIGHT)
http://ipcam_url/decoder_control.cgi?command=25(CENTER)
http://ipcam_url/decoder_control.cgi?command=26(Vertical-PATROL)
http://ipcam_url/decoder_control.cgi?command=27(Vertical-PATROL-STOP)
http://ipcam_url/decoder_control.cgi?command=28(Horizontal-PATROL)
http://ipcam_url/decoder_control.cgi?command=29(Horizontal-PATROL-STOP)
http://ipcam_url/decoder_control.cgi?command=16(Zoom-In, Disabled on somemodel)
http://ipcam_url/decoder_control.cgi?command=18(Zoom-Out, Disabled on somemodel)
http://ipcam_url/decoder_control.cgi?command=94(IRSwitch-ON)
http://ipcam_url/decoder_control.cgi?command=95(IRSwitch-OFF)
http://ipcam_url/camera_control.cgi?param=5&value=0(normal image)
http://ipcam_url/camera_control.cgi?param=5&value=1(Vertical flip image)
http://ipcam_url/camera_control.cgi?param=5&value=2(mirror image)
*/

$result = CamCommand($_GET["cam"], $_GET["com"], $_GET["step"], $_GET["deg"]);
if( $result['success'] == FALSE )
{
  print $result['error'];
}
else
{
  print $result['message'];
}


?>