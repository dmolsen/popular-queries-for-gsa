<?php

##############################################################################################
## google search appliance settings
## revise this to match your set-up
##############################################################################################

## modify the following as appropriate
$gsa_username        = "xxxxxxx"; # admin username
$gsa_password        = "xxxxxxx"; # admin password
$gsa_collection		 = "default_collection"; # collection you want results for
$gsa_frontend		 = "default_frontend"; # layout you want to display results in
$gsa_hostname        = "search.wvu.edu"; # the hostname for your search appliance
$gsa_top_count       = "20"; # the number of results to show in final include
$gsa_with_results    = "true"; # use searches that returned results, can be 'false' for searches that didn't return results

$debug               = false; # turn verbose logging mode on or off, uses log/debug.log
$delete_report		 = false; # deletes report in GSA so the report interface doesn't get cluttered up
$include_write_path  = "../test_path/"; # path to where the JS include is to be written too
$bad_words			 = "shit,fuck,useyourimagination"; # separate bad words by commas to have search queries using those words removed

## report defaults, don't change if you don't have to
$gsa_report_type = "1";
$gsa_date_format = "date";
$gsa_report_name = date("Ymd", mktime(0, 0, 0, date("n"), date("j")-1, date("Y")))."-report"; ## YYYYMMDD-report
$gsa_date_year   = date("Y", mktime(0, 0, 0, date("n"), date("j")-1, date("Y")));
$gsa_date_month  = date("n", mktime(0, 0, 0, date("n"), date("j")-1, date("Y"))); 
$gsa_date_day    = date("j", mktime(0, 0, 0, date("n"), date("j")-1, date("Y")));

?>