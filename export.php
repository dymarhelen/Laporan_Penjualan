<?php
require 'koneksi.php';

// Ambil tahun dari parameter GET
$tahun = $_GET['tahun'] ?? date('Y');

// Set header untuk download Excel
header("Content-type: application/vnd-ms-excel");
header("Content-Disposition: attachment; filename=Laporan_Penjualan_Tahun_$tahun.xls");

echo "<table border='1'>";
echo "<thead>
<tr>
    <th>Menu</th>
    <th>Kategori</th>
    <th>Jan</th><th>Feb</th><th>Mar</th><th>Apr</th><th>Mei</th><th>Jun</th>
    <th>Jul</th><th>Agu</th><th>Sep</th><th>Okt</th><th>Nov</th><th>Des</th>
    <th>Total</th>
</tr>
</thead><tbody>";

$data = [];
$result = $koneksi->query("
    SELECT m.nama, m.kategori, MONTH(p.tanggal) AS bulan, SUM(d.total) AS jumlah
    FROM t_pesanan_detail d
    JOIN t_pesanan p ON d.t_pesanan_id = p.id
    JOIN m_menu m ON d.m_menu_id = m.id
    WHERE YEAR(p.tanggal) = '$tahun'
    GROUP BY m.nama, m.kategori, MONTH(p.tanggal)
");

// Susun data
while ($row = $result->fetch_assoc()) {
    $data[$row['kategori']][$row['nama']][$row['bulan']] = $row['jumlah'];
}

$grandTotal = 0;

foreach ($data as $kategori => $items) {
    foreach ($items as $menu => $bulan_data) {
        echo "<tr><td>$menu</td><td>$kategori</td>";
        $total = 0;
        for ($i = 1; $i <= 12; $i++) {
            $nilai = $bulan_data[$i] ?? 0;
            echo "<td>$nilai</td>";
            $total += $nilai;
        }
        $grandTotal += $total;
        echo "<td>$total</td></tr>";
    }
}

// Tambahkan baris Grand Total
echo "<tr>
    <td colspan='14' style='text-align:right; font-weight:bold;'>Grand Total</td>
    <td><strong>$grandTotal</strong></td>
</tr>";

echo "</tbody></table>";
?>
