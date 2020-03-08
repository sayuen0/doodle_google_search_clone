<?php
class SearchResultsProvider
{
  private $con;

  public function __construct($con)
  {
    $this->con = $con;
  }

  public function getNumResults($term)
  {
    $query = $this->con->prepare(<<<SQL
    SELECT COUNT(*) as total  FROM  sites
    WHERE title like :term
    or url like :term
    or keywords like :term
    or description like :term
    SQL);
    $searchTerm = "%" . $term . "%";
    $query->bindParam(":term", $searchTerm);
    $query->execute();
    $row = $query->fetch(PDO::FETCH_ASSOC);
    return $row["total"];
  }
  public function getResultHtml($page, $pageSize, $term)
  {
    $fromLimit = ($page - 1) * $pageSize;
    // page1 : (1 - 1) *20 = 0
    // page2 : (2 - 1) *20 = 20

    $query = $this->con->prepare(<<<SQL
    SELECT * FROM  sites
    WHERE title like :term
    or url like :term
    or keywords like :term
    or description like :term
    ORDER BY clicks DESC
    LIMIT :fromLimit, :pageSize
    SQL);
    // $encodedUrl = "%" . urlencode($term) . "%";
    $searchTerm = "%" . $term . "%";
    $query->bindParam(":term", $searchTerm);
    // $query->bindParam(":url", $encodedUrl);
    // $query->bindParam(":term", $searchTerm);
    $query->bindParam(":fromLimit", $fromLimit, PDO::PARAM_INT);
    $query->bindParam(":pageSize", $pageSize, PDO::PARAM_INT);
    $query->execute();
    $results = "";
    while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
      $id = $row["id"];
      $url = $row["url"];
      $title = $row["title"];
      $description = $row["description"];

      $title = $this->trimField($title, 55);
      $description = $this->trimField($description, 230);

      $results .=  <<<HTML
        <div class="resultContainer">
          <h3 class="title">
            <a href="$url" class="result" data-linkId="$id" >$title</a><!-- /.result -->
          </h3><!-- /.title -->
          <span class="url">$url</span><!-- /.url -->
          <span class="description">$description</span><!-- /.url -->
        </div><!-- /.resultContainer -->
      HTML;
    }

    $resultsContainer = <<<HTML
    <div class="siteResults">
      $results
    </div><!-- /.siteResults -->
    HTML;
    return $resultsContainer;
  }

  private function trimField($string, $characterLimit)
  {
    $dots = (strlen($string) > $characterLimit) ? "..." : "";
    return substr($string, 0, $characterLimit) . $dots;
  }
}
