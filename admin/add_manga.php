<?php
include 'db.php'; // Mengikutsertakan file db.php untuk koneksi ke database

// Mengambil data genres dari database
$genres = []; // Mendefinisikan array kosong untuk menyimpan genre
$sql = "SELECT id, genre FROM genres"; // Query SQL untuk mengambil id dan genre dari tabel genres
$result = $conn->query($sql); // Menjalankan query SQL
if ($result->num_rows > 0) { // Mengecek apakah ada hasil dari query
    while ($row = $result->fetch_assoc()) { // Mengambil setiap baris hasil query sebagai array asosiatif
        $genres[] = $row; // Menambahkan baris ke array genres
    }
}

// Mengecek apakah form telah disubmit dengan metode POST dan tombol 'add_manga' ditekan
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_manga'])) {
    $title = $_POST['title']; // Mengambil judul manga dari input form
    $description = $_POST['description']; // Mengambil deskripsi manga dari input form
    $author = $_POST['author']; // Mengambil penulis manga dari input form
    $genre_id = $_POST['genre_id']; // Mengambil genre_id dari input form

    // Mengatur direktori untuk mengunggah file
    $uploadDir = __DIR__ . '/../uploads/';
    $uploadFile = $uploadDir . basename($_FILES['cover_image']['name']); // Mengatur path lengkap file yang akan diunggah
    $uploadOk = 1; // Flag untuk menentukan apakah file dapat diunggah
    $imageFileType = strtolower(pathinfo($uploadFile, PATHINFO_EXTENSION)); // Mendapatkan tipe file

    // Membuat direktori jika belum ada
    if (!file_exists($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }

    // Mengecek apakah file adalah gambar
    $check = getimagesize($_FILES['cover_image']['tmp_name']);
    if ($check !== false) {
        $uploadOk = 1;
    } else {
        $uploadOk = 0;
    }

    // Mengecek apakah file sudah ada
    if (file_exists($uploadFile)) {
        $uploadOk = 0;
    }

    // Mengecek ukuran file
    if ($_FILES['cover_image']['size'] > 500000) {
        $uploadOk = 0;
    }

    // Mengecek tipe file
    if ($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg" && $imageFileType != "gif") {
        $uploadOk = 0;
    }

    // Mengecek apakah file dapat diunggah
    if ($uploadOk == 0) {
        echo "Maaf, file Anda tidak berhasil diunggah.";
    } else {
        // Mengunggah file
        if (move_uploaded_file($_FILES['cover_image']['tmp_name'], $uploadFile)) {
            $coverImage = 'uploads/' . basename($_FILES['cover_image']['name']); // Menyimpan path file yang diunggah
            // Query SQL untuk memasukkan data manga ke database
            $sql = "INSERT INTO manga (title, description, author, cover_image) 
                    VALUES ('$title', '$description', '$author', '$coverImage')";
            if ($conn->query($sql) === TRUE) { // Mengecek apakah query berhasil
                $manga_id = $conn->insert_id; // Mendapatkan id manga yang baru ditambahkan

                // Query SQL untuk memasukkan genre yang dipilih ke tabel manga_genres
                $genre_sql = "INSERT INTO manga_genres (manga_id, genre_id) VALUES ('$manga_id', '$genre_id')";
                if ($conn->query($genre_sql) === TRUE) {
                    echo "Manga berhasil ditambahkan.";
                } else {
                    echo "Kesalahan: " . $genre_sql . "<br>" . $conn->error;
                }
            } else {
                echo "Kesalahan: " . $sql . "<br>" . $conn->error;
            }
        } else {
            echo "Maaf, terjadi kesalahan saat mengunggah file Anda.";
        }
    }

    $conn->close(); // Menutup koneksi ke database
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8"> <!-- Mengatur karakter encoding halaman ke UTF-8 -->
    <meta name="viewport" content="width=device-width, initial-scale=1.0"> <!-- Mengatur viewport agar halaman bersifat responsif -->
    <title>Tambah Manga</title> <!-- Menentukan judul halaman -->
    <!-- Menyertakan CSS Bootstrap dari CDN untuk styling -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <!-- Menyertakan Font Awesome CSS dari CDN untuk ikon -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <!-- Menyertakan stylesheet custom -->
    <link rel="stylesheet" href="../css/dashboards.css">
</head>
<body>
    <div class="wrapper">
        <?php include 'navbar.php'; ?> <!-- Mengikutsertakan file navbar.php untuk navigasi -->

        <div id="content">
            <div class="container-fluid">
                <!-- Tombol untuk menyembunyikan/menampilkan sidebar -->
                <button type="button" id="sidebarCollapse" class="btn btn-info">
                    <i class="fas fa-align-left"></i>
                </button>
            </div>
            <div class="container">
                <div class="row">
                    <div class="col-md-12">
                        <h2>Tambah Manga</h2>
                        <!-- Form untuk menambahkan manga baru -->
                        <form action="add_manga.php" method="post" enctype="multipart/form-data">
                            <div class="form-group">
                                <label for="title">Judul:</label> <!-- Label untuk input judul -->
                                <input type="text" id="title" name="title" class="form-control" required> <!-- Input judul -->
                            </div>
                            <div class="form-group">
                                <label for="description">Deskripsi:</label> <!-- Label untuk input deskripsi -->
                                <textarea id="description" name="description" class="form-control" required></textarea> <!-- Input deskripsi -->
                            </div>
                            <div class="form-group">
                                <label for="author">Penulis:</label> <!-- Label untuk input penulis -->
                                <input type="text" id="author" name="author" class="form-control" required> <!-- Input penulis -->
                            </div>
                            <div class="form-group">
                                <label for="type">Genre:</label> <!-- Label untuk input genre -->
                                <!-- Loop untuk menampilkan semua genre yang diambil dari database -->
                                <?php foreach ($genres as $genre): ?>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="genre_id" id="genre<?= $genre['id'] ?>" value="<?= $genre['id'] ?>" required> <!-- Input radio untuk genre -->
                                        <label class="form-check-label" for="genre<?= $genre['id'] ?>">
                                            <?= $genre['genre'] ?> <!-- Menampilkan nama genre -->
                                        </label>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                            <div class="form-group">
                                <label for="cover_image">Gambar Sampul:</label> <!-- Label untuk input gambar sampul -->
                                <input type="file" id="cover_image" name="cover_image" class="form-control-file" required> <!-- Input file gambar sampul -->
                            </div>
                            <button type="submit" name="add_manga" class="btn btn-primary">Tambah Manga</button> <!-- Tombol submit -->
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Menyertakan jQuery dari CDN untuk interaksi JavaScript -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <!-- Menyertakan Popper.js dari CDN untuk Bootstrap tooltips dan popovers -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
    <!-- Menyertakan JavaScript Bootstrap dari CDN -->
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script>
        // Menambahkan fungsi JavaScript untuk menyembunyikan/menampilkan sidebar
        $(document).ready(function () {
            $('#sidebarCollapse').on('click', function () {
                $('#sidebar').toggleClass('active'); // Menambahkan/menghapus class 'active' pada elemen dengan id 'sidebar'
            });
        });
    </script>
</body>
</html>
