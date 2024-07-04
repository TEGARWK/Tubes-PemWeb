<?php
session_start(); // Memulai sesi PHP untuk menyimpan informasi sesi
include 'db.php'; // Mengikutsertakan file db.php untuk koneksi ke database

// Mengecek apakah variabel sesi 'admin' sudah diset, jika belum, pengguna akan dialihkan ke halaman login
if (!isset($_SESSION['admin'])) {
    header("Location: ../login/login.php"); // Mengarahkan ke halaman login
    exit(); // Menghentikan eksekusi script untuk memastikan pengguna tidak mengakses bagian di bawahnya
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8"> <!-- Mengatur karakter encoding halaman ke UTF-8 -->
    <meta name="viewport" content="width=device-width, initial-scale=1.0"> <!-- Mengatur viewport agar halaman bersifat responsif -->
    <title>Admin Dashboard</title> <!-- Menentukan judul halaman -->
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
                    <div class="col-md-12 text-center">
                        <!-- Judul utama halaman -->
                        <h1 class="my-4"><i class="fas fa-user-shield"></i> Selamat datang, Admin!</h1>
                        <p>Ini adalah halaman dashboard admin.</p>
                    </div>
                </div>
                <div class="row">
                    <!-- Tambah Genre -->
                    <div class="col-md-4">
                        <div class="card text-center mb-4">
                            <div class="card-header">
                                <i class="fas fa-tags"></i> Tambah Genre
                            </div>
                            <div class="card-body">
                                <h5 class="card-title">Tambah Genre Baru</h5>
                                <p class="card-text">Tambahkan genre baru ke dalam database.</p>
                                <a href="add_genre.php" class="btn btn-primary">Tambah Genre</a>
                            </div>
                        </div>
                    </div>
                    <!-- Tambah Manga -->
                    <div class="col-md-4">
                        <div class="card text-center mb-4">
                            <div class="card-header">
                                <i class="fas fa-book"></i> Tambah Manga
                            </div>
                            <div class="card-body">
                                <h5 class="card-title">Tambah Manga Baru</h5>
                                <p class="card-text">Tambahkan manga baru ke dalam database.</p>
                                <a href="add_manga.php" class="btn btn-primary">Tambah Manga</a>
                            </div>
                        </div>
                    </div>
                    <!-- Tambah Chapter -->
                    <div class="col-md-4">
                        <div class="card text-center mb-4">
                            <div class="card-header">
                                <i class="fas fa-book-open"></i> Tambah Chapter
                            </div>
                            <div class="card-body">
                                <h5 class="card-title">Tambah Chapter Baru</h5>
                                <p class="card-text">Tambahkan chapter baru ke dalam manga.</p>
                                <a href="add_chapter.php" class="btn btn-primary">Tambah Chapter</a>
                            </div>
                        </div>
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
