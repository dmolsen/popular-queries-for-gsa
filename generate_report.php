<?php

## Name: Generate GSA Report
## Description: Logs into your GSA and creates a report. Pretty straightforward.
## Created by: dave olsen, wvu web services
## Created on: april 16, 2009

require("settings.inc.php"); # include the GSA settings
require("lib/logger.inc.php"); # include the logger functions
require("lib/auth.inc.php"); # include the GSA login & logout functions

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

# POST to generate the report
$report_post = array("collection" => $gsa_collection, "reportType" => $gsa_report_type, "reportName" => $gsa_report_name, "withResults" => $gsa_with_results, "dateFormat" => $gsa_date_format, "dateYear" => $gsa_date_year, "dateMonth" => $gsa_date_month, "dateDay" => $gsa_date_day, "topCount" => $gsa_top_count, "actionType" => "generateReport"); # fields that are POSTed
$report_url = "http://".$gsa_hostname.":8000/EnterpriseController";
$report_files = array();
$report_options = array("cookies" => $cookie->cookies);
$response = @http_post_fields($report_url, $report_post, $report_files, $report_options);
if (preg_match("/<title>   Search Reports   <\/title>/i",$response)) {
	logger("Report being generated.",$response);
}
else {
	logger("POST to generate report has failed. Check your settings and double-check your GSA admin proper.",$response);
}

gsa_logout(); # logout of GSA

?>