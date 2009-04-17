<?php

## Name: Is Bad Word?
## Description: Checks to see if the word matches or contains a "bad word" which
##              should exclude it from being listed.
## Created by: dave olsen, wvu web services
## Created on: april 16, 2009
function is_bad_word($word) {
	
	global $bad_words; # load $bad_words from settings.inc.php
	$is_bad = false;
	
	# examine word vs. bad words list
    $bad_words_a = explode(',', $bad_words);
    for ($i = 0; $i < count($bad_words_a); $i++) {
	    if (preg_match('/'.$bad_words_a[$i].'/i', $word)) {
			$is_bad = true; # match, ding the word
		}
    }

	return $is_bad;
}

?>