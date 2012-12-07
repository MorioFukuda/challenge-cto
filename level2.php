<?php

$csvRaw = file_get_contents('booklist.csv');
$tsvRaw = file_get_contents('booklist.tsv');
$ymlRaw = file_get_contents('booklist.yml');

function csvConverter($csv){

	$encodingList = "UTF-8, UTF-7, ASCII, EUC-JP, SJIS, eucJP-win, SJIS-win, JIS, ISO-2022-JP";
	$csvLines = preg_split('/[\n]+/', mb_convert_encoding($csv, 'UTF-8', $encodingList)); 

	//csvのヘッダーを$csvHeaderに格納し、ヘッダーを削除
	$csvHeader = str_getcsv($csvLines[0], ',', '"');
	unset($csvLines[0]);

	$csvData = array();
	$counter = 0;

	foreach($csvLines as $line){

		$data = str_getcsv($line, ',', '"');

		//1行のレコードの数が、$csvHeaderの数と合わなかったら、エラーを出す
		if(count($data) !== count($csvHeader)){
			echo "CSVファイルが壊れています。";
			return false;
		}

		for($i = 0; $i < count($csvHeader); $i++){
			$csvData[$counter][$csvHeader[$i]] = $data[$i];
		}

		$counter++;
	}

	return $csvData;
}

var_dump( csvConverter($csvRaw) );

?>
