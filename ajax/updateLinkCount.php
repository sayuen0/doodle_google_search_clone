<?php
include("../includes/config.php");

if (isset($_POST["linkId"])) {
  $id = $_POST["linkId"];
  $query = $con->prepare(<<<SQL
  UPDATE  sites
  SET clicks = clicks + 1 
  WHERE id = :id
  SQL);
  $query->bindParam(":id", $id);
  $query->execute();
} else {
  echo "No link passed to page";
}
