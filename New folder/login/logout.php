
<?php
// Memulai sesi PHP
session_start();

// Menghancurkan semua data sesi yang ada
session_destroy();

// Mengalihkan pengguna ke halaman utama (index.php) di direktori /komik
header("Location: /komik/index.php");

// Menghentikan eksekusi skrip selanjutnya setelah pengalihan
exit();
?>
