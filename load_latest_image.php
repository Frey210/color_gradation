<?php
$conn = new mysqli('localhost', 'root', '', 'picture');

if ($conn->connect_error) {
    die("Koneksi database gagal: " . $conn->connect_error);
}

$sql = "SELECT image_data FROM images ORDER BY capture_time DESC LIMIT 1";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $image_data = $row["image_data"];
    $base64_image = base64_encode($image_data);
    echo "<img src='data:image/jpeg;base64," . $base64_image . "' width='640' height='480'>";
}

$conn->close();
?>
