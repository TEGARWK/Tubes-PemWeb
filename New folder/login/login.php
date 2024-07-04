<?php
// Memulai sesi PHP
session_start();

// Menginclude file koneksi database
include '../admin/db.php';

// Memeriksa apakah form telah dikirim menggunakan metode POST
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Mengambil data username dan password dari form
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Membuat query SQL untuk mencari user berdasarkan username
    $sql = "SELECT * FROM admin WHERE username = ?";
    // Mempersiapkan statement SQL untuk mencegah SQL injection
    $stmt = $conn->prepare($sql);
    // Mengikat parameter ke statement SQL
    $stmt->bind_param('s', $username);
    // Menjalankan statement SQL
    $stmt->execute();
    // Mendapatkan hasil dari statement SQL
    $result = $stmt->get_result();

    // Memeriksa apakah user ditemukan
    if ($result->num_rows > 0) {
        // Mengambil data user dari hasil query
        $row = $result->fetch_assoc();
        // Mengambil password yang sudah di-hash dari database
        $hashed_password = $row['password'];

        // Memverifikasi password yang dimasukkan dengan password yang di-hash
        if (password_verify($password, $hashed_password)) {
            // Jika verifikasi password berhasil
            echo "Password verification succeeded.<br>";
            // Menyimpan ID admin ke sesi
            $_SESSION['admin'] = $row['id'];
            // Mengalihkan pengguna ke dashboard admin
            header("Location: ../admin/dashboard.php");
            exit();
        } else {
            // Jika verifikasi password gagal
            echo "Password verification failed.<br>";
            // Menampilkan password yang dimasukkan dan password yang di-hash untuk debugging
            echo "Entered Password: " . htmlspecialchars($password) . "<br>";
            echo "Hashed Password: " . htmlspecialchars($hashed_password) . "<br>";
        }
    } else {
        // Jika username tidak ditemukan di database
        echo "Invalid username or password.";
    }
    // Menutup statement SQL
    $stmt->close();
}
// Menutup koneksi database
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <!-- Menginclude file CSS custom -->
    <link rel="stylesheet" href="../css/logins.css">
    <!-- Menginclude file CSS Bootstrap untuk styling -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <!-- Kontainer untuk form login -->
    <div class="login-container">
        <h1>Login</h1>
        <!-- Form login dengan metode POST -->
        <form method="POST" action="login.php">
            <div class="form-group">
                <!-- Input untuk username -->
                <label for="username">Username:</label>
                <input type="text" id="username" name="username" class="form-control" required>
            </div>
            <div class="form-group">
                <!-- Input untuk password -->
                <label for="password">Password:</label>
                <input type="password" id="password" name="password" class="form-control" required>
            </div>
            <!-- Tombol submit untuk login -->
            <button type="submit" class="btn btn-warning">Login</button>
        </form>
        <!-- Link untuk kembali ke halaman utama -->
        <div class="mt-3">
            <a href="../index.php" class="btn btn-secondary">Back</a>
        </div>
    </div>
</body>
</html>
