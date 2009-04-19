<?php

## Name: Google Search Appliance (GSA) Login
## Description: Centralizes the functions for logging into your GSA via PHP
##              I'm not thrilled with the checks via <title> tags but it works
## Created by: dave olsen, wvu web services
## Created on: april 16, 2009
function gsa_login() {
	
	# get the GSA settings from settings.inc.php
	global $gsa_hostname, $gsa_username, $gsa_password, $gsa_collection;
	
	# run a GET to set-up the session with your GSA
	$response = @http_get("http://".$gsa_hostname.":8000/EnterpriseController", array("cookiestore" => "cookie_store/"));
	$setup_headers = http_parse_headers($response);
	if (preg_match("/0;URL=\/EnterpriseController\?actionType=reload&lastcmd=login/i",$setup_headers["Refresh"])) {
		$cookie = http_parse_cookie($setup_headers["Set-Cookie"]);
	}
	else {
		logger("GSA Session Setup Failed. Make sure your GSA hostname is correct.",$response);
		exit;
	}

	# POST the login credentials to log into your GSA
	$login_post = array("userName" => $gsa_username, "password" => $gsa_password, "actionType" => "authenticateUser"); # fields that are POSTed
	$login_url = "http://".$gsa_hostname.":8000/EnterpriseController";
	$login_files = array();
	$login_options = array("cookies" => $cookie->cookies);
    $response = @http_post_fields($login_url, $login_post, $login_files, $login_options);
	if (!preg_match("/<title>  Home  <\/title>/i",$response)) {
		logger("GSA Login Failed. Check the admin credentials, cookie store path & cookie store permissions.",$response);
		exit;
	}

	# POST to make sure we're looking at the correct reports, fixes bug with GSA
	$report_post = array("actionType" => "listReports", "selectedCollection" => $gsa_collection); # fields that are POSTed
	$report_url = "http://".$gsa_hostname.":8000/EnterpriseController";
	$report_files = array();
	$report_options = array("cookies" => $cookie->cookies);
	$response = @http_post_fields($report_url, $report_post, $report_files, $report_options);
	if (preg_match('/\<option\ selected\ value\="'.$gsa_collection.'"\>/i',$response)) {
		logger("Switched to Correct Collection",$response);
	}
	else {
		logger("POST to switch to correct collection has failed. Check your settings and make sure collection name is correct.",$response);
		gsa_logout();
		exit;
	}
	
	return $cookie; # return cookie to be used for further authentication when generating or exporting report
}

## Name: Google Search Appliance (GSA) Logout
## Description: Just cleaning up stuff by allowing a logout of GSA
## Created by: dave olsen, wvu web services
## Created on: april 16, 2009
function gsa_logout() {
	global $gsa_hostname;
	http_get("http://".$gsa_hostname.":8000/EnterpriseController?actionType=logout");
}

?>