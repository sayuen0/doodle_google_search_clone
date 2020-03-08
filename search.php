<?php
require_once "includes/header.php";
require_once "includes/classes/SearchResulstProvider.php";
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
        <li class="<?= isActive($type, "images")  ?>"><a href="<?= "search.php?term=$term&type=imagee" ?>">Images</a></li>
      </ul><!-- /.tabList -->
    </div><!-- /.tabsContainer -->
  </header>

  <section class="mainResultsSection">
    <?php
    $resultProvider = new SearchResulstProvider($con);
    $numResults =  $resultProvider->getNumResults($term);
    echo <<<HTML
      <p class="resultsCount">$numResults results found</p>
    HTML;

    $pageLimit = 20;

    echo $resultProvider->getResultHtml($page, $pageLimit, $term);


    ?>
  </section><!-- /.mainResultsSection -->
</div><!-- /.wrapper searchPage -->
