<?php
include("../includes/config.php");

if (isset($_POST["src"])) {
  $src = $_POST["src"];
  $query = $con->prepare(<<<SQL
  UPDATE  images
  SET broken = 1 
  WHERE imageUrl = :src
  SQL);
  $query->bindParam(":src", $src);
  $query->execute();
} else {
  echo "No src passed to page";
}
