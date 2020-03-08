<?php
require_once "includes/header.php";
require_once "includes/classes/SearchResultsProvider.php";
require_once "includes/classes/ImageResultsProvider.php";
require_once "includes/classes/PaginationProvider.php";

if (isset($_GET["term"])) {
  $term = $_GET["term"];
} else {
  exit("You must enter a search word");
}

$type = (isset($_GET["type"])) ? $_GET["type"] : "sites";
$page = (isset($_GET["page"])) ? $_GET["page"] : 1;



function isActive($type, $link)
{
  return $type == $link ? "active" : "";
}
?>

<div class="wrapper searchPage">
  <header class="header">
    <div class="headerContent">

      <div class="logoContainer">
        <a href="index.php">
          <img src="assets/images/logos/toaru2.png" alt="">
        </a>
      </div>

      <div class="searchContainer">
        <form action="search.php" method="GET">
          <div class="searchBarContainer">
            <input type="hidden" name="type" value="<?= $type ?>">
            <input type="text" name="term" class="searchBox" value="<?= $term ?>">
            <button type="submit" class="searchButton">
              <img src="assets/images/icons/search.png" alt="">
            </button>
          </div><!-- /.searchBarContainer -->
        </form>
      </div><!-- /.searchContainer -->

    </div><!-- /.headerContent -->
    <div class="tabsContainer">
      <ul class="tabsList">
        <li class="<?= isActive($type, "sites")  ?>"><a href="<?= "search.php?term=$term&type=sites" ?>">Sites</a></li>
        <li class="<?= isActive($type, "images")  ?>"><a href="<?= "search.php?term=$term&type=images" ?>">Images</a></li>
      </ul><!-- /.tabList -->
    </div><!-- /.tabsContainer -->
  </header>

  <section class="mainResultsSection">
    <?php
    if ($type == "images") {
      $resultProvider = new ImageResultsProvider($con);
      $pageSize = 30;
    } else {
      $resultProvider = new SearchResultsProvider($con);
      $pageSize = 20;
    }
    $numResults =  $resultProvider->getNumResults($term);

    echo <<<HTML
      <p class="resultsCount">$numResults results found</p>
    HTML;



    echo $resultProvider->getResultHtml($page, $pageSize, $term);
    ?>
  </section><!-- /.mainResultsSection -->


  <div class="paginationContainer">

    <div class="pageButtons">

      <div class="pageNumberContainer">
        <img src="assets/images/logos/pageStart.png" alt="">
      </div><!-- /.pageButtons -->

      <?php

      // todo: ここのロジックはよく読んで理解する
      PaginationProvider::createPagination($term, $type, $page, $numResults, $pageSize);
      ?>

      <div class="pageNumberContainer">
        <img src="assets/images/logos/pageEnd.png" alt="">
      </div><!-- /.pageButtons -->

    </div>


  </div><!-- /.paginationContainer -->
</div><!-- /.wrapper searchPage -->
<script src="https://unpkg.com/masonry-layout@4/dist/masonry.pkgd.min.js"></script>
<script type="text/javascript" src="/fancybox/jquery.fancybox-1.3.4.pack.js"></script>
<script src="assets/js/index.js"></script>

</body>

</html>
