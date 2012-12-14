<?php

//yamlを読み込むライブラリ
require_once('spyc.php');

$csvRaw = file_get_contents('booklist.csv');
$tsvRaw = file_get_contents('booklist.tsv');
$ymlRaw = file_get_contents('booklist.yml');

function csvConverter($csv){

	$encodingList = "UTF-8, UTF-7, ASCII, EUC-JP, SJIS, eucJP-win, SJIS-win, JIS, ISO-2022-JP";

	//1行以上の改行で区切る。
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

		/*
			$csvData[0]["ISBN"] = 123876124
							   ["title"] = hoge
								 ["author"] = hoge
								 ["price"] = 2000
								 ["amazon-url"] = http://www.amazon.com
			みたいな形で格納する。
		*/
		for($i = 0; $i < count($csvHeader); $i++){
			$csvData[$counter][$csvHeader[$i]] = $data[$i];
		}

		$counter++;
	}

	unset($line);

	return $csvData;
}


function tsvConverter($tsv){
	
	$encodingList = "UTF-8, UTF-7, ASCII, EUC-JP, SJIS, eucJP-win, SJIS-win, JIS, ISO-2022-JP";

	//1行以上の改行で区切る
	$tsvLines = preg_split('/[\n]+/', mb_convert_encoding($tsv, 'UTF-8', $encodingList));

	//1行目に含まれるタブをカンマに変換して、str_getcsvに渡す。
	$tsvHeader = str_getcsv(preg_replace('/\t/', ',', $tsvLines[0]), ',', '"');
	//ヘッダーを削除
	unset($tsvLines[0]);

	$tsvData = array();
	$counter = 0;

	foreach($tsvLines as $line){
	
		$data = str_getcsv(preg_replace('/\t/', ',', $line), ',', '"');

		//1行のレコードの数が、$tsvHeaderの数と合わなかったらエラーを出す
		if(count($data) !== count($tsvHeader)){
			echo "TSVファイルが壊れています";
			return false;
		}

		for($i = 0; $i < count($tsvHeader); $i++){
			$tsvData[$counter][$tsvHeader[$i]] = $data[$i];
		}

		$counter++;
	}
	
	unset($line);

	return $tsvData;
}


function ymlConverter($yml){

	$rawYmls = Spyc::YAMLLoad($yml);

	$ymlData = array();

	foreach($rawYmls as $isbn => $rawYml){
		$data['ISBN'] = $isbn;
		$data['title'] = $rawYml['title'];
		$data['author'] = $rawYml['author'];
		$data['price'] = $rawYml['price'];
		$data['amazon-url'] = $rawYml['amazon-url'];

		$ymlData[] = $data;
	}

	unset($isbn, $rawYml);

	return $ymlData;
}

//var_dump(csvConverter($csvRaw));
//var_dump(tsvConverter($tsvRaw));
//var_dump(ymlConverter($ymlRaw));

function tsvMerger($csvData, $tsvData, $ymlData){

	$mergedData = array_merge($csvData, $tsvData, $ymlData);

//	var_dump($mergedData);

	$tsv = "";

	//tsvファイルのカラムのリストをmergedDataのキーから取得
	$columnList = array_keys($mergedData[0]);

	//tsvファイルの1行目に$columnListをタブで結合したものを格納
	$tsv .= implode("\t", $columnList);

	//tsvの2行目以降を$tsvに足し込んでいく
	foreach($mergedData as $data){
		$tsv .= implode("\t", $data) . "\n";
	}

	$tsvFile = fopen('merged_booklist.tsv', 'w');

	if(fwrite($tsvFile, $tsv)){
		fclose($tsvFile);
		echo "merged_booklist.tsvを作成しました。\n";
	}else{
		echo "merged_booklist.tsvの作成に失敗しました。\n";
	}
}


tsvMerger(csvConverter($csvRaw), tsvConverter($tsvRaw), ymlConverter($ymlRaw));


?>
