<?php

## Name: Export Report, Build JavaScript Include, & Delete Report
## Description: Logs into GSA for you, exports selected report as XML, creates a javascript include
##              that you can use on your GSA web page, and deletes the report if required.
## Created by: dave olsen, wvu web services
## Created on: april 16, 2009

require("settings.inc.php"); # include the GSA settings
require("lib/logger.inc.php"); # include the logger functions
require("lib/auth.inc.php"); # include the GSA login & logout functions
require("lib/bad_words.inc.php"); # include the bad word checker

$cookie = gsa_login(); # log into GSA & get the session cookie

# GET the generated report from GSA
$export_url = "http://".$gsa_hostname.":8000/EnterpriseController?collection=".$gsa_collection."&reportName=".$gsa_report_name."&actionType=exportSummaryReport";
$export_options = array("cookies" => $cookie->cookies);
$response = @http_get($export_url, $export_options);

# load up the XML
$data = http_parse_message($response)->body;
$xml = new SimpleXMLElement($data); # if $data is not an xml doc it will blow up this script.

# write out the include
if (sizeof($xml->topQueries->topQuery) == 0) {
	logger("XML Not Properly Loaded. Make sure debugging is on and try again.",$data);
}
else {
	if ($f = @fopen($include_write_path."recent_top_searches.js", "w")) {
		for($i = 0; $i < sizeof($xml->topQueries->topQuery); $i++) {
			$query = $xml->topQueries->topQuery[$i]["query"];
			if (!is_bad_word($query)) {
				$output = "document.write(\"<a href='http://".$gsa_hostname."/search?q=".str_replace(" ", "+", $query)."&sort=date%3AD%3AL%3Ad1&output=xml_no_dtd&ie=UTF-8&oe=UTF-8&client=".$gsa_frontend."&proxystylesheet=".$gsa_frontend."&site=".$gsa_collection."' class='recent_query'>".$query."</a> \");\r\n";
				fwrite($f, $output);
			}	
		}
		fclose($f);
		logger("Javascript Include Written","");
	}
	else {
		logger("Javascript Include File Failed to Open. Check permissions. Path given was: ".$include_write_path."recent_top_searches.js.","");
		gsa_logout(); # logout of GSA
		exit; # this way report delete won't run and session is cleaned up
	}
}

# delete report to keep GSA report interface somewhat manageable
if ($delete_report) {
	$delete_url = "http://".$gsa_hostname.":8000/EnterpriseController?actionType=deleteReport&reportType=".$gsa_report_type."&collection=".$gsa_collection."&reportName=".$gsa_report_name;
	$delete_options = array("cookies" => $cookie->cookies);
	if (@http_get($delete_url, $delete_options)) {
		logger("GSA Report Deleted","");
	}
	else {
		logger("Failed to Delete GSA Report",""); 
		# this only fails if a connection can't be made. if connection is made but report doesn't delete it this won't catch it
		# yeah, kind of pointless
	}
}

gsa_logout(); # logout of GSA

?>