<?php
require_once "includes/config.php";
require_once "includes/classes/DomDocumentParser.php";
require_once "includes/classes/Crawler.php";



$crawler = new Crawler($con);

// $startLink = "https://ja.wikipedia.org/wiki/%E3%83%A1%E3%82%A4%E3%83%B3%E3%83%9A%E3%83%BC%E3%82%B8";
// $startLink = "https://ja.wikipedia.org/wiki/%E5%88%9D%E9%9F%B3%E3%83%9F%E3%82%AF";
// $startLink ="https://ja.wikipedia.org/wiki/VOCALOID";
$startLink = "https://www.google.com/search?q=football";

$crawler->followLinks($startLink);
