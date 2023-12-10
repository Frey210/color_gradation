<?php
$conn = new mysqli('localhost', 'root', '', 'picture');

if ($conn->connect_error) {
    die("Koneksi database gagal: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['take_picture'])) {
    $sql = "UPDATE camera_command SET command = 'true'";
    $conn->query($sql);
    exit(); // Keluar dari skrip PHP setelah mengirim perintah "Take Picture"
}

$sql = "SELECT image_data, capture_time FROM images ORDER BY capture_time DESC LIMIT 1";
$result = $conn->query($sql);

$image_data = "";
$capture_time = "";

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $image_data = $row["image_data"];
    $capture_time = $row["capture_time"];
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Take Picture</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap/dist/css/bootstrap.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap/dist/js/bootstrap.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
</head>
<body>
    <div class="container mt-5">
        <div class="row">
            <div class="col-md-6">
                <h1>Take Picture</h1>
                <form id="take-picture-form" method="post">
                    <button type="submit" name="take_picture" class="btn btn-primary"><i class="fa fa-camera"></i> Take Picture</button>
                </form>
                <h2><button class="btn btn-primary"> <a href="riwayat.php" style="color: white; text-decoration:none"><i class="fa fa-history" aria-hidden="true"></i>  History</a></button></h2>
            </div>
            <div class="col-md-6">
                <h2>Gambar Terbaru:</h2>
                <div id="image-container">
                    <?php
                    if (!empty($image_data)) {
                        $base64_image = base64_encode($image_data);
                        echo "<img src='data:image/jpeg;base64," . $base64_image . "' class='img-fluid'>";
                    }
                    ?>
                </div>
                <!-- <p>Waktu Pengambilan: <?php echo $capture_time; ?></p> -->
            </div>
        </div>
    </div>

    <script>
        // Fungsi untuk memuat gambar terbaru secara otomatis
        function loadLatestImage() {
            $.ajax({
                url: 'load_latest_image.php', // Ganti dengan nama file PHP yang sesuai
                success: function (data) {
                    $('#image-container').html(data);
                }
            });
        }

        // Perbarui gambar secara otomatis setiap 5 detik
        setInterval(loadLatestImage, 1000);

        // Kode untuk mengirim perintah "Take Picture" tanpa mengirim ulang saat halaman di-refresh
        $('#take-picture-form').submit(function (e) {
            e.preventDefault(); // Mencegah pengiriman form bawaan
            $.ajax({
                type: 'POST',
                url: 'index.php', // Ganti dengan nama file PHP yang sesuai
                data: { take_picture: true },
                success: function () {
                    console.log('Perintah "Take Picture" dikirim.');
                }
            });
        });
    </script>
</body>
</html>
