<?php

## Name: Logger
## Description: Logs activity of the various scripts. Set $debug to true in settings.inc.php
##              to get a more verbose activity log.
## Created by: dave olsen, wvu web services
## Created on: april 16, 2009
function logger($m,$extra) {
	global $debug;
	$m = date("Y-m-d h:i:s")." - ".$m."\r\n";
	if ($debug) {
		echo($m." See debug.log for more details.\r\n"); # write basic message to cmd prompt
		$m = $m.$extra."\r\n\r\n";
		$f = fopen('log/debug.log', 'a');
		fwrite($f, $m);
		fclose($f);
	}
	else {
		$f = fopen('log/production.log', 'a');
		fwrite($f, $m);
		fclose($f);
	}	
}

?>