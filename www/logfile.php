<?php
date_default_timezone_set('Europe/Paris');
$now = date("Y-m-d H:i:s");
//$filename = '/surveillance/log/locate.log';

/*$somecontent = $now." user:".$_GET["user"]." location:".$_GET["location"]."\n";
// Let's make sure the file exists and is writable first.
 if (is_writable($filename)) {
    // we're opening $filename in append mode.
    // The file pointer is at the bottom of the file hence
    // that's where $somecontent will go when we fwrite() it.
    if (!$handle = fopen($filename, 'a')) {
         echo "Cannot open file ($filename)";
         exit;
    }
    // Write $somecontent to our opened file.
    if (fwrite($handle, $somecontent) === FALSE) {
        echo "Cannot write to file ($filename)";
        exit;
    }
    echo "Success, wrote ($somecontent) to file ($filename)";
    fclose($handle);
} else {
    echo "The file $filename is not writable";
} */


?>