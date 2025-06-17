<?php

$Host = "localhost";
$Username = "root";
$Password = "";
$database = "penjualan";

$koneksi = mysqli_connect($Host, $Username, $Password, $database);

if (!$koneksi) {
  die ("gagal");
}

?>

