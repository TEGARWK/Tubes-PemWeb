<?php
// Menyertakan file database
include './admin/db.php';

// Mengecek apakah metode request adalah POST
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Mendapatkan data dari form
    $manga_id = $_POST['manga_id'];
    $username = $_POST['username'];
    $comment = $_POST['comment'];

    // Menyiapkan query untuk memasukkan komentar baru ke dalam database
    $stmt = $conn->prepare("INSERT INTO comments (manga_id, username, comment, created_at) VALUES (?, ?, ?, NOW())");
    $stmt->bind_param("iss", $manga_id, $username, $comment);

    // Menjalankan query dan mengecek apakah berhasil
    if ($stmt->execute()) {
        // Jika berhasil, redirect kembali ke halaman detail manga
        header("Location: detail_manga.php?id=$manga_id");
    } else {
        // Jika gagal, tampilkan pesan error
        echo "Error: " . $stmt->error;
    }

    // Menutup statement
    $stmt->close();
}
?>
