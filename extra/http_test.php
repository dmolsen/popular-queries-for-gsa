<?php

## Name: PECL HTTP Test
## Description: Tests to see if you have PECL HTTP available to use. If not you'll have to install it.
## Created by: dave olsen, wvu web services
## Created on: april 16, 2009

if (extension_loaded('http')) {
	echo("HTTP Extension is loaded\r\n");
}
else {
	echo("HTTP Extension is NOT loaded. Install it, check php.ini, check permissions on http.so or check 32-bit vs. 64-bit.\r\n");
}

?>