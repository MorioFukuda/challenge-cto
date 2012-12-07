<?php

$words = preg_split('/[\s]+/', preg_replace('/\.|,|\(|\)|\[|\]|\?/s', ' ', file_get_contents('beating_the_averages.txt')));

$results =array();
$maxLength = 0;

foreach($words as $word){
	if(strlen($word) > $maxLength){
		$maxLength = strlen($word);
		unset($results);
		$results[] = $word;
	}elseif(strlen($word) == $maxLength){
		$results[] = $word;
	}
}

echo implode(", ", $results) . "\n";

?>
