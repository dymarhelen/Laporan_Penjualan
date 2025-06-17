<?php

require 'koneksi.php';

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <title>Laporan Penjualan Tahunan</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: #f0f2f5;
            font-family: 'Segoe UI', sans-serif;
        }

        .wrapper-box {
            background: rgb(221, 221, 221);
            padding: 40px;
            border-radius: 16px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.08);
            max-width: 1200px;
            margin: 60px auto;
        }

        .card {
            border: none;
            border-radius: 12px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        h5 {
            color: #2c3e50;
        }

        .btn-primary {
            background-color: rgb(24, 144, 224);
            border: none;
        }

        .btn-success {
            background-color: rgb(5, 94, 42);
            border: none;
        }

        .table-dark th {
            background-color: rgb(28, 32, 37) !important;
        }

        .table-secondary th {
            background-color: #dfe6e9;
            color: rgb(10, 11, 11);
        }

        td.text-end {
            background-color: #ecf0f1;
        }

        .fw-bold {
            background-color: rgb(222, 171, 171) !important;
            color: rgb(3, 20, 10) !important;
        }

        tfoot tr {
            background-color: rgb(6, 29, 15);
            color: white;
        }
    </style>
</head>

<body>
    <div class="wrapper-box">
        <div class="container">
            <div class="card">
                <div class="card-body">
                    <h5 class="mb-3">📊 Laporan Penjualan Tahunan</h5>
                    <form method="GET">
                        <div class="d-flex gap-2 align-items-center">
                            <select name="tahun" class="form-select" style="width: 150px;">
                                <option value="2022" <?= ($_GET['tahun'] ?? '') == '2022' ? 'selected' : '' ?>>2022</option>
                                <option value="2021" <?= ($_GET['tahun'] ?? '') == '2021' ? 'selected' : '' ?>>2021</option>
                            </select>
                            <button type="submit" class="btn btn-primary">Tampilkan</button>
                            <a href="export.php?tahun=<?= $_GET['tahun'] ?? date('Y') ?>" class="btn btn-success">Download Database 📂</a>
                        </div>
                    </form>
                </div>
            </div>

            <div class="table-responsive mt-4">
                <table class="table table-bordered">
                    <thead class="table-dark text-center">
                        <tr>
                            <th rowspan="2">Menu</th>
                            <th colspan="12">Periode Pada <?= $_GET['tahun'] ?? date('Y') ?></th>
                            <th rowspan="2">Total</th>
                        </tr>
                        <tr>
                            <?php foreach (range(1, 12) as $b) echo "<th>" . date("M", mktime(0, 0, 0, $b, 1)) . "</th>"; ?>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $tahun = $_GET['tahun'] ?? date('Y');
                        $data = [];
                        $total_all = 0;

                        $result = $koneksi->query("
                        SELECT m.nama, m.kategori, MONTH(p.tanggal) AS bulan, SUM(d.total) AS jumlah
                        FROM t_pesanan_detail d
                        JOIN t_pesanan p ON d.t_pesanan_id = p.id
                        JOIN m_menu m ON d.m_menu_id = m.id
                        WHERE YEAR(p.tanggal) = '$tahun'
                        GROUP BY m.nama, m.kategori, MONTH(p.tanggal)
                    ");

                        while ($row = $result->fetch_assoc()) {
                            $data[$row['kategori']][$row['nama']][$row['bulan']] = $row['jumlah'];
                        }

                        foreach ($data as $kategori => $items) {
                            echo "<tr class='table-secondary'><th colspan='14'>🍽️ " . ucfirst($kategori) . "</th></tr>";
                            foreach ($items as $menu => $bulan_data) {
                                echo "<tr><td>$menu</td>";
                                $total_menu = 0;
                                for ($i = 1; $i <= 12; $i++) {
                                    $nilai = $bulan_data[$i] ?? 0;
                                    echo "<td class='text-end'>" . number_format($nilai, 0, ',', '.') . "</td>";
                                    $total_menu += $nilai;
                                }
                                $total_all += $total_menu;
                                echo "<td class='text-end fw-bold'>" . number_format($total_menu, 0, ',', '.') . "</td></tr>";
                            }
                        }
                        ?>
                    </tbody>
                    <tfoot class="text-end">
                        <tr>
                            <th colspan="13">Grand Total</th>
                            <th><?= number_format($total_all, 0, ',', '.') ?></th>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
</body>

</html>