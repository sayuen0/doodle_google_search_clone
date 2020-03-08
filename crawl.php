<?php
require_once "includes/config.php";
require_once "includes/classes/DomDocumentParser.php";
require_once "includes/classes/Crawler.php";



if (!isset($_GET["term"])) {
  echo "set the term";
  exit();
}else {
  $term = urlEncode($_GET["term"]);

}


// $startLink = "https://ja.wikipedia.org/wiki/%E3%83%A1%E3%82%A4%E3%83%B3%E3%83%9A%E3%83%BC%E3%82%B8";
// $startLink = "https://ja.wikipedia.org/wiki/%E5%88%9D%E9%9F%B3%E3%83%9F%E3%82%AF";
// $startLink ="https://ja.wikipedia.org/wiki/VOCALOID";


// $s = "https://www.ynu.ac.jp/";

$crawler = new Crawler($con);
// $s = "https://www.google.com/search?q=$term";
$s ="https://wiki.xn--rckteqa2e.com/wiki/%E3%83%A1%E3%82%A4%E3%83%B3%E3%83%9A%E3%83%BC%E3%82%B8";
$crawler->followLinks($s);
