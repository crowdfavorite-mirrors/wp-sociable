<?php
require_once("../../../../wp-config.php");

global $skyscraper_options;

$url_shares = $_POST["link"];
$title_shared = $_POST["title"];

global $url_shares;
global $title_shared;

$display = true;
//echo $url_shares;
if (strpos($url_shares, "wp-admin") && !strpos($url_shares, "page=skyscraper_options")){
	
	$display = false;
}

if ($display){
//	echo "entrea aca!";
	require_once("skyscraper_output.php");
	auto_skyscraper('', $display );
}
?>