<?php
header("Content-Disposition: attachment; filename=penjualan.sql");
header("Content-Type: application/sql");

// pastikan path file sesuai
readfile("penjualan.sql");
exit;
?>
