<?php
class Crawler
{

  private $alreadyCrawled;
  private $crawling;
  private $con;
  private $alreadyFoundImages;

  public function __construct(PDO $con)
  {
    $this->con = $con;
    $this->alreadyCrawled = array();
    $this->crawling = array();
    $this->alreadyFoundImages = array();
  }


  /**
   * そのURLのデータがすでに挿入されていたらtrueを返す
   *
   * @param string $url
   * @return boolean
   */
  public function linkExists($url)
  {
    $query = $this->con->prepare(<<<SQL
    SELECT *  FROM  sites
    WHERE url = :url
    SQL);
    $query->bindParam(":url", $url);
    $query->execute();
    return $query->rowCount() != 0;
  }
  /**
   * データベースにurl, title, description, keywordを挿入して成功したらtrueを返す
   *
   * @param string $url
   * @param string $title
   * @param string $description
   * @param string $keywords
   * @return boolean
   */
  public function insertLink($url, $title, $description, $keywords)
  {
    $query = $this->con->prepare(<<<SQL
    INSERT INTO sites(url, title, description, keywords)
    VALUES (:url, :title, :description, :keywords) 
    SQL);
    $query->bindParam(":url", $url);
    $query->bindParam(":title", $title);
    $query->bindParam(":description", $description);
    $query->bindParam(":keywords", $keywords);
    if (!$query->execute()) {
      echo $this->con->errorInfo();
      return false;
    }
    return true;
  }
  /**
   * データベースにurl, title, description, keywordを挿入して成功したらtrueを返す
   *
   * @param string $url
   * @param string $title
   * @param string $description
   * @param string $keywords
   * @return boolean
   */
  public function insertImage($siteUrl, $src, $alt, $title)
  {

    $query = $this->con->prepare(<<<SQL
    INSERT INTO images(siteUrl,imageUrl, alt ,title)
    VALUES (:siteUrl, :imageUrl, :alt, :title);
    SQL);
    $query->bindParam(":siteUrl", $siteUrl);
    $query->bindParam(":imageUrl", $src);
    $query->bindParam(":alt", $alt);
    $query->bindParam(":title", $title);

    if (!$query->execute()) {
      echo $this->con->errorInfo();
      return false;
    }
    return $siteUrl;
  }

  /**
   * リンクをパースして返す
   *
   * @param string $src
   * @param string $url
   * @return string
   */
  public function createLink($src, $url)
  {
    echo "SRC: $src<br>";
    echo "URL: $url<br>";

    $scheme = parse_url($url)["scheme"]; //http
    $host = parse_url($url)["host"];  //www.reecekenney.com/about.php

    //     //www.reecekenney.com -> http://www.reecekenney.com
    if (substr($src, 0, 2) == "//") {
      $src = $scheme . ":" . $src;

      //  /about/aboutUs.php -> http://www.reecekenney.com
    } elseif (substr($src, 0, 1) == "/") {
      $src = $scheme . "://" . $host . $src;
    } elseif (substr($src, 0, 2) == "/") {
      $src = $scheme  . "://" . $host . dirname(parse_url($url["path"])) . substr($src, 1);
    } elseif (substr($src, 0, 3) == "../") {
      $src = $scheme . "://" . $host . "/" . $src;
    } elseif (substr($src, 0, 5) != "https" && substr($src, 0, 4) != "http") {
      $src = $scheme . "://" . $host . "/" . $src;
    }
    return $src;

    # code...
  }

  /**
   * ページ情報を取得する
   *
   * @param [type] $url
   * @return void
   */
  public function getDetails($url)
  {
    $parser = new DomDocumentParser($url);

    $titleArray = $parser->getTitleTags();

    if (sizeof($titleArray) == 0 || $titleArray->item(0) == NULL) {
      return;
    }

    $title = $titleArray->item(0)->nodeValue;
    $title = str_replace("\n", "", $title);

    if ($title == "") {
      return;
    }
    $description = "";
    $keywords = "";

    $metasArray = $parser->getMetaTags();

    foreach ($metasArray as $meta) {
      if ($meta->getAttribute("name") == "description") {
        $description = $meta->getAttribute("content");
      }
      if ($meta->getAttribute("name") == "keywords") {
        $keywords = $meta->getAttribute("content");
      }
    }

    $description = str_replace("\n", "", $description);
    $keywords = str_replace("\n", "", $keywords);
    if ($this->linkExists($url)) {
      echo "URL already exists :  $url";
      echo "<br>";
    } else if ($this->insertLink($url, $title, $description, $keywords)) {
      echo "SUCESS: $url";
    } else {
      echo "ERROR: $url cannot be inserted";
      echo "<br>";
      echo $this->con->errorInfo();
    }
    $imageArray = $parser->getImages();
    foreach ($imageArray as $image) {
      $src = $image->getAttribute("src");
      $alt = $image->getAttribute("alt");
      $title = $image->getAttribute("title");
      if (!$title && !$alt) {
        continue;
      }
      $src = $this->createLink($src, $url);
      if (!in_array($src, $this->alreadyFoundImages)) {
        $this->alreadyFoundImages[] = $src;
        echo " INSERTED : " . $this->insertImage($url, $src, $alt, $title);
      }
    }
  }

  /**
   * 再帰的にリンクを取得し続ける
   *
   * @param string $url
   * @return void
   */
  public function followLinks($url)
  {
    $parser = new DomDocumentParser($url);
    $linkList = $parser->getLinks();

    foreach ($linkList as $link) {
      $href = $link->getAttribute("href");

      // process link
      if (strpos($href, "#") !== false) {
        continue;
      } elseif (substr($href, 0, 11) == "javascript") {
        continue;
      }

      $href = $this->createLink($href, $url);
      if (!in_array($href, $this->alreadyCrawled)) {
        $this->alreadyCrawled[] = $href;
        $this->crawling[]  = $href;
        $this->getDetails($href);
      } else {
        return;
      }
      echo $href . "<br>";
    }
    array_shift($this->crawling);

    foreach ($this->crawling as $site) {
      // TODO: 再帰の処理を精査して、めっちゃクローリングさせる
      $this->followLinks($site);
    }
  }
}
