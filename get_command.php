<?php

// Lakukan koneksi ke database
$host = 'localhost';
$database = 'u0173409_color-image';
$user = 'u0173409_color';
$password = 'asdjaWF@3%^8Xvdssj:.';

try {
    $conn = new PDO("mysql:host=$host;dbname=$database", $user, $password);

    // Periksa status "Take Picture" dari database
    $stmt = $conn->prepare("SELECT command FROM camera_command");
    $stmt->execute();
    $result = $stmt->fetch();

    // Kirim nilai command sebagai respons
    echo $result['command'];

} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}

?>
