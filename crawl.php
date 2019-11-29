<?php
include("config.php");
include("classes/DomDocumentParser.php");

$alreadyCrawled = array();
$crawling = array();
$alreadyFoundImages = array();

function insertLink($url, $title, $description, $keywords) {
	global $con;

	$query = $con->prepare("INSERT INTO sites(url, title, description, keywords)
							VALUES(:url, :title, :description, :keywords)");

	$query->bindParam(":url", $url);
	$query->bindParam(":title", $title);
	$query->bindParam(":description", $description);
	$query->bindParam(":keywords", $keywords);

	return $query->execute();
}

function insertImage($url, $src, $alt, $title) {
	global $con;

	$query = $con->prepare("INSERT INTO images(site_url, image_url, alt, title)
							VALUES(:site_url, :image_url, :alt, :title)");

	$query->bindParam(":site_url", $url);
	$query->bindParam(":image_url", $src);
	$query->bindParam(":alt", $alt);
	$query->bindParam(":title", $title);

	return $query->execute();
}

function linkExists($url) {
	global $con;

	$query = $con->prepare("SELECT * FROM sites WHERE url = :url");

	$query->bindParam(":url", $url);

	$query->execute();
    return $query->rowCount() != 0;
}

function createLink($src, $url) {

	$scheme = parse_url($url)["scheme"]; // http
	$host = parse_url($url)["host"]; // www.reecekenney.com
	
	if(substr($src, 0, 2) == "//") {
		$src =  $scheme . ":" . $src;
	}
	else if(substr($src, 0, 1) == "/") {
		$src = $scheme . "://" . $host . $src;
	}
	else if(substr($src, 0, 2) == "./") {
		$src = $scheme . "://" . $host . dirname(parse_url($url)["path"]) . substr($src, 1);
	}
	else if(substr($src, 0, 3) == "../") {
		$src = $scheme . "://" . $host . "/" . $src;
	}
	else if(substr($src, 0, 5) != "https" && substr($src, 0, 4) != "http") {
		$src = $scheme . "://" . $host . "/" . $src;
	}

	array_shift($crawling);

	foreach($crawling as $site) {
		followLinks($site);
	}

}



    $startUrl = "https://dnevnik.hr/vijesti/";
    followLinks($startUrl);

?>
