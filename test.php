<?php
// Mengecek apakah request method adalah POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Mengambil nilai dari input 'nama' dan 'skema'
    $nama = $_POST['nama'];
    $skema = $_POST['skema'];

    // Menampilkan nilai input dengan htmlspecialchars untuk mencegah XSS
    echo "Nama: " . htmlspecialchars($nama) . "<br>";
    echo "Skema: " . htmlspecialchars($skema);
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Form Input</title>
</head>
<body>
    <!-- Form dengan method POST yang mengirimkan data ke halaman yang sama -->
    <form method="post" action="" enctype="multipart/form-data" style="border: 1px solid black; padding: 10px; width: 300px; margin: 0 auto;">
        <!-- Wrapper untuk Nama dengan border -->
        <div style="border: 1px solid black; padding: 5px; margin-bottom: 10px;">
            <label for="nama">Nama:</label>
            <input type="text" id="nama" name="nama" required><br>
        </div>
        <!-- Wrapper untuk Skema dengan border -->
        <div style="border: 1px solid black; padding: 5px; margin-bottom: 10px;">
            <label for="skema">Skema:</label>
            <select id="skema" name="skema">
                <option value="Junior Web Developer">Junior Web Developer</option>
                <option value="Senior Web Developer">Senior Web Developer</option>
                <option value="Full Stack Developer">Full Stack Developer</option>
            </select><br>
        </div>
        <!-- Wrapper untuk Foto dengan border -->
        <div style="border: 1px solid black; padding: 5px; margin-bottom: 10px;">
            <label for="foto">Foto:</label>
            <input type="file" id="foto" name="foto"><br>
        </div>
        <!-- Wrapper untuk tombol Daftar dengan border -->
        <div style="border: 1px solid black; padding: 5px;">
            <input type="submit" value="Daftar">
        </div>
    </form>
</body>
</html>
