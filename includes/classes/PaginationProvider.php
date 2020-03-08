<?php
class PaginationProvider
{
  public static function createPagination(
    String $term,
    String  $type,
    int $page,
    int $numResults,
    int $pageSize
  ) {
    $pagesToShow = 10;
    $numPages = ceil($numResults / $pageSize);
    $pagesLeft = min($pagesToShow, $numPages);

    $currentPage =  $page - floor($pagesToShow / 2);
    if ($currentPage <   1) {
      $currentPage  = 1;
    }

    if ($currentPage + $pagesLeft > $numPages + 1) {
      $currentPage = $numPages  + 1  - $pagesLeft;
    }
    while ($pagesLeft != 0 && $currentPage <= $numPages) {
      if ($currentPage == $page) {

        echo <<<HTML
        <div class="pageNumberContainer">
          <img src="assets/images/logos/pageSelected.png" alt="">
          <span class="pageNumber">$currentPage</span>
        </div>
      HTML;
      } else {
        echo <<<HTML
        <div class="pageNumberContainer">
          <a href="search.php?term=$term&type=$type&page=$currentPage">
            <img src="assets/images/logos/page.png" alt="">
            <span class="pageNumber">$currentPage</span>
          </a>
        </div>
      HTML;
      }

      $currentPage++;
      $pagesLeft--;
    }
  }
}
