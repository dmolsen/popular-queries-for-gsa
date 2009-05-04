<?php

##############################################################################################
## google search appliance settings
## revise this to match your set-up
##############################################################################################

## modify the following as appropriate
$gsa_username        = "xxxxxxxxxx"; # admin username
$gsa_password        = "xxxxxxxxxx"; # admin password
$gsa_collection      = "default_collection"; # collection you want results for
$gsa_frontend        = "default_frontend"; # layout you want to display results in
$gsa_hostname        = "search.wvu.edu"; # the hostname for your search appliance
$gsa_top_count       = "20"; # the number of results to show in final include
$gsa_with_results    = "true"; # use searches that returned results, can be 'false' for searches that didn't return results

$include_write_path  = "../test_path/"; # path to where the JS include is to be written too
$bad_words           = "shit,fuck,useyourimagination"; # separate bad words by commas to have search queries using those words removed

# if you want to add divs, bullets, list item tags this would be the place to do it
$pre_output          = " "; # HTML that goes before the popular query link
$post_output         = " "; # HTML that goes after the popular query link

## because bad words may still slip through you may want the report emailed to you each day just in case
$enable_email        = false; # turn daily email of top results on or off
$email_to            = "dmolsen@example.com"; # who you want to receive the daily email. separate by commas

$enable_ga           = false; # turn on GA tracking of clicks on popular queries links. requires latest version of GA code
$enable_delete       = false; # deletes report in GSA so the report interface doesn't get cluttered up
$enable_debug        = false; # turn verbose logging mode on or off, uses log/debug.log


## report defaults, don't change if you don't have to
$gsa_report_type = "1";
$gsa_date_format = "date";
$gsa_report_name = date("Ymd", mktime(0, 0, 0, date("n"), date("j")-1, date("Y")))."-report"; ## YYYYMMDD-report
$gsa_date_year   = date("Y", mktime(0, 0, 0, date("n"), date("j")-1, date("Y")));
$gsa_date_month  = date("n", mktime(0, 0, 0, date("n"), date("j")-1, date("Y"))); 
$gsa_date_day    = date("j", mktime(0, 0, 0, date("n"), date("j")-1, date("Y")));

?>