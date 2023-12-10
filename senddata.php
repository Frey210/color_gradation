<?php

// Terima data dari Python
$image_data = $_POST['image_data'];
$hex_color = $_POST['hex_color'];
$population_category = $_POST['population_category'];

// Lakukan koneksi ke database
$host = 'localhost';
$database = 'u0173409_color-image';
$user = 'u0173409_color';
$password = 'asdjaWF@3%^8Xvdssj:.';

try {
    $conn = new PDO("mysql:host=$host;dbname=$database", $user, $password);

    $query = "INSERT INTO images (image_data, capture_time, hex_color, population_category) VALUES (?, NOW(), ?, ?)";
    $stmt = $conn->prepare($query);

    $stmt->bindParam(1, $image_data, PDO::PARAM_LOB);
    $stmt->bindParam(2, $hex_color);
    $stmt->bindParam(3, $population_category);

    $stmt->execute();

    echo "Data berhasil disimpan ke database.";

} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}

?>
