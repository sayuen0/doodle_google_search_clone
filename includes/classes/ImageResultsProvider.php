<?php
class ImageResultsProvider
{
  private $con;

  public function __construct($con)
  {
    $this->con = $con;
  }

  public function getNumResults($term)
  {
    $query = $this->con->prepare(<<<SQL
    SELECT COUNT(*) as total  FROM  images
    WHERE( title like :term
    or alt like :term)
    AND broken=0
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
    SELECT *  FROM  images
    WHERE( title like :term
    or alt like :term)
    AND broken=0
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

    $count = 0;
    while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
      $count++;
      $id = $row["id"];
      $imageUrl = $row["imageUrl"];
      $siteUrl = $row["siteUrl"];
      $title = $row["title"];
      $alt = $row["alt"];

      if ($title) {
        $displayText = $title;
      } elseif ($alt) {
        $displayText  = $alt;
      } else {
        $displayText = $imageUrl;
      }

      $results .=  <<<HTML
        <div class="gridItem image$count">
          <a href="$imageUrl" data-fancybox data-caption="$displayText">
            <script>
              $(document).ready(function () {
                loadImage("$imageUrl","image$count")
              });

            </script>
            <!-- <img src="$imageUrl" alt=""> -->
            <span class="details">$displayText</span><!-- /.details -->
          </a>
        </div><!-- /.gridItem -->
      HTML;
    }

    $resultsContainer = <<<HTML
    <div class="imageResults">
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
