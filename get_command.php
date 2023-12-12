<?php

// Lakukan koneksi ke database
$host = 'localhost';
$database = 'picture';
$user = 'root';
$password = '';

try {
    $conn = new PDO("mysql:host=$host;dbname=$database", $user, $password);

    // Periksa status "Take Picture" dari database
    $stmt = $conn->prepare("SELECT command FROM camera_command");
    $stmt->execute();
    $result = $stmt->fetch();

    // Kirim nilai command sebagai respons
    echo $result['command'];

    // Jika command adalah 'true', ubah menjadi 'false' setelah mengambil nilai
    if ($result['command'] === 'true') {
        $updateStmt = $conn->prepare("UPDATE camera_command SET command = 'false'");
        $updateStmt->execute();
    }

} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>
