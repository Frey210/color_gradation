<?php
// Koneksi ke database
$conn = mysqli_connect("localhost", "root", "", "picture");

// Periksa koneksi
if (!$conn) {
    die("Koneksi gagal: " . mysqli_connect_error());
}

// Query untuk mendapatkan riwayat gambar
$query = "SELECT image_data, capture_time, description, hex_color, population_category FROM images";
$result = mysqli_query($conn, $query);

// Tutup koneksi
mysqli_close($conn);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Riwayat Gambar</title>
</head>
<body>
    <h1>Riwayat Gambar</h1>
    <table border="1">
        <tr>
            <th>Gambar</th>
            <th>Waktu Pengambilan</th>
            <th>Warna (Hex)</th>
            <th>Kategori Populasi</th>
        </tr>

        <?php
        // Tampilkan data dari query
        while ($row = mysqli_fetch_assoc($result)) {
            echo "<tr>";
            $image_data = $row["image_data"];
            $base64_image = base64_encode($image_data);
            echo "<td><img src='data:image/jpeg;base64," . $base64_image . "' class='img-fluid'>";
            echo "<td>{$row['capture_time']}</td>";
            echo "<td>{$row['hex_color']}</td>";
            echo "<td>{$row['population_category']}</td>";
            echo "</tr>";
        }
        ?>
    </table>
</body>
</html>
