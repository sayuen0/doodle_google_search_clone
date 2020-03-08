<?php
session_start();
ob_start();
ini_set("display_errors", "1");

try {
  $con = new PDO("mysql:dbname=doodle_google_search_clone;host=localhost", "root", "root");
  $con->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING);
} catch (PDOException $e) {
  echo $e->getMessage();
}
