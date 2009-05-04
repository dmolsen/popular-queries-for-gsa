<?php

## Name: Logger
## Description: Logs activity of the various scripts. Set $debug to true in settings.inc.php
##              to get a more verbose activity log.
## Created by: dave olsen, wvu web services
## Created on: april 16, 2009
function logger($m,$extra) {
	global $enable_debug, $path_to_pq;
	$m = date("Y-m-d h:i:s")." - ".$m."\r\n";
	if ($enable_debug) {
		echo($m." See debug.log for more details.\r\n"); # write basic message to cmd prompt
		$m = $m.$extra."\r\n\r\n";
		$f = fopen($path_to_pq."log/debug.log", "a");
		fwrite($f, $m);
		fclose($f);
	}
	else {
		$f = fopen($path_to_pq."log/production.log", "a");
		fwrite($f, $m);
		fclose($f);
	}	
}

?>