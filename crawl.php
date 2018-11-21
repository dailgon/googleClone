<?php

include "classes/DomDocumentParser.php";

$alreadyCrawled = array();
$crawling = array();

function createLink($src, $url){
    /*
    POSSIBILITIES:
    //www.google.com -> http://www.google.com
    /about/aboutus.php
    ./about/aboutUs.php
    ../about/ablutUs.php
    about/aboutUs.php
    */
    
    $scheme = parse_url($url)["scheme"]; //http
    $host = parse_url($url)["host"];  //www.google.com
    
    if(substr($src, 0,2) =="//"){
        $src = $scheme . ":" . $src;
    }
    elseif(substr($src, 0,1) =="/"){
        $src = $scheme . "://" . $host . $src;
    }
    elseif(substr($src, 0,2) =="./"){
        $src = $scheme . "://" . $host . dirname(parse_url($url)["path"]) . substr($src, 1);
    }
    elseif(substr($src, 0,3) =="../"){
        $src = $scheme . "://" . $host . "/" . $src;
    }
    elseif(substr($src, 0,5) != "https" && substr($src, 0,4) != "http"){
        $src = $scheme . "://" . $host . "/" . $src;
    }
    
    return $src; 
    
}

function getDetails($url){
    
    $parser = new DomDocumentParser($url);
    
    $titleArray = $parser->getTitletags();
    
    if(sizeof($titleArray) == 0 || $titleArray->item(0) == null){
        return;
    }
    
    $title = $titleArray->item(0)->nodeValue;
    //replace newline with blank space
    $title = str_replace("\n", "", $title);
    
    if($title = ""){
        return;
    }
    echo "URL $url, Title: $title <br />";
    
}

function followLinks($url){
    
    global $alreadyCrawled;
    global $crawling;
    
    $parser = new DomDocumentParser($url);
    
    $linkList = $parser->getLinks();
    
    foreach ($linkList as $link){
        $href = $link->getAttribute("href");
        
        if(strpos($href, '#') !== false){
            //don't want #
            continue;
        }
        else if(substr($href, 0, 11) == "javascript:"){
            //dont want javascript
            continue;
        }
        
        $href = createLink($href, $url);
        
        if(!in_array($href, $alreadyCrawled)){
            $alreadyCrawled[] = $href;
            $crawling[] = $href;
            
            getDetails($href);
        }
        
        // echo $href . "<br />";
    }
    
    array_shift($crawling);
    
    foreach($crawling as $site){
        followLinks($site);
    }
}



$startUrl = "http://www.bbc.com";
followLinks($startUrl);



?>