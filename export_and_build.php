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

# POST to make sure we're looking at the correct reports, fixes bug with GSA
$switch_report_post = array("actionType" => "listReports", "selectedCollection" => $gsa_collection); # fields that are POSTed
$switch_report_url = "http://".$gsa_hostname.":8000/EnterpriseController";
$switch_report_files = array();
$switch_report_options = array("cookies" => $cookie->cookies);
$response = @http_post_fields($switch_report_url, $switch_report_post, $switch_report_files, $switch_report_options);
if (preg_match('/\<option\ selected\ value\="'.$gsa_collection.'"\>/i',$response)) {
	logger("Switched to Correct Collection",$response);
}
else {
	logger("POST to switch to correct collection has failed. Check your settings and make sure collection name is correct.",$response);
	gsa_logout();
	exit;
}

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
	$email_message = "This is a report of the popular search queries for yesterday:\r\n\r\n"; # setting up email message
	if ($f = @fopen($include_write_path."recent_top_searches.js", "w")) {
		for($i = 0; $i < sizeof($xml->topQueries->topQuery); $i++) {
			$k = $i + 1;
			$query = $xml->topQueries->topQuery[$i]["query"];
			if (!is_bad_word($query)) {
				$email_message .= $k.": ".$query."\r\n";
				$output = "document.write(\"<a href='http://".$gsa_hostname."/search?q=".str_replace(" ", "+", $query)."&sort=date%3AD%3AL%3Ad1&output=xml_no_dtd&ie=UTF-8&oe=UTF-8&client=".$gsa_frontend."&proxystylesheet=".$gsa_frontend."&site=".$gsa_collection."'"; 
				if ($enable_ga) {
					$output .= " onClick='javascript: pageTracker._trackPageview(\\\"/popular_queries/".str_replace(" ", "+", $query)."\\\");'";
				}
				$output .= " class='recent_query'>".$query."</a> \");\r\n";
				fwrite($f, $output);
			}
			else {
				$email_message .= $k.": ".$query." [Bad Word: not included]\r\n";
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
if ($enable_delete) {
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

# send copy of results as an email
if ($enable_email) {
	$email_subject = "[GSA Popular Queries] ".$gsa_report_name;
	if (@mail($email_to,$email_subject,$email_message)) {
		logger("Results Email Sent","");
	}
	else {
		logger("Failed to Send Results Email. Check your mail configuration in php.ini.","");
	}
}

gsa_logout(); # logout of GSA

?>