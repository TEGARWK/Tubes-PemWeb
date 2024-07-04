<?php
// Memulai sesi
session_start();

// Menyertakan file database
include './admin/db.php';

// Mendapatkan daftar genre dari database
$genre_sql = "SELECT * FROM genres";
$genre_result = $conn->query($genre_sql);

// Mendapatkan parameter genre yang dipilih dari URL
$selected_genres = isset($_GET['genre']) ? $_GET['genre'] : [];

// Mendapatkan parameter pencarian judul dari URL
$search_title = isset($_GET['search']) ? $_GET['search'] : null;

// Query untuk mendapatkan data manga berdasarkan genre dan pencarian judul yang dipilih
$manga_sql = "SELECT m.*, GROUP_CONCAT(g.genre SEPARATOR ', ') as genres
            FROM manga m
            LEFT JOIN manga_genres mg ON m.id = mg.manga_id
            LEFT JOIN genres g ON mg.genre_id = g.id
            WHERE 1=1";

// Menambahkan kondisi pada query jika ada genre yang dipilih
if (!empty($selected_genres)) {
    $genre_ids = implode(',', array_map('intval', $selected_genres));
    $manga_sql .= " AND m.id IN (SELECT manga_id FROM manga_genres WHERE genre_id IN ($genre_ids))";
}

// Menambahkan kondisi pada query jika ada pencarian judul
if ($search_title) {
    $manga_sql .= " AND m.title LIKE '%$search_title%'";
}

// Mengelompokkan hasil query berdasarkan ID manga
$manga_sql .= " GROUP BY m.id";
$manga_result = $conn->query($manga_sql);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Homepage Manga</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/navbars.css">
    <link rel="stylesheet" href="css/indexs.css">
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg">
        <a class="navbar-brand" href="#">
            <img src="media/logo.png" alt="Logo">
            <h3 class="komikgpt">KOMIK GPT</h3>
        </a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ml-auto">
                <li class="nav-item"><a class="nav-link" href="index.php">HOME</a></li>
                <li class="nav-item"><a class="nav-link" href="index.php">DAFTAR MANGA</a></li>
                <!-- Menampilkan link Logout jika user telah login -->
                <?php if (isset($_SESSION['user_id']) || isset($_SESSION['admin_id'])): ?>
                    <li class="nav-item"><a class="nav-link" href="login/logout.php">LOGOUT</a></li>
                <?php else: ?>
                    <li class="nav-item"><a class="nav-link" href="login/login.php">LOGIN</a></li>
                <?php endif; ?>
            </ul>
        </div>
    </nav>
    
    <!-- Konten Utama -->
    <div class="container">
        <div class="row">
            <div class="col-md-9">
                <h1 class="text-center text-warning">Daftar Manga</h1>
                <div class="manga-list-box">
                    <div class="row">
                        <!-- Menampilkan daftar manga yang ditemukan -->
                        <?php
                        if ($manga_result->num_rows > 0) {
                            while ($row = $manga_result->fetch_assoc()) {
                                // Memotong judul dan deskripsi manga jika terlalu panjang
                                $title = strlen($row['title']) > 50 ? substr($row['title'], 0, 47) . '...' : $row['title'];
                                $description = strlen($row['description']) > 100 ? substr($row['description'], 0, 97) . '...' : $row['description'];
                                echo '<div class="col-6 col-sm-4 col-md-3">';
                                echo '<div class="manga-card card" onclick="location.href=\'detail_manga.php?id=' . $row['id'] . '\'">';
                                echo '<img src="' . $row['cover_image'] . '" class="card-img-top" alt="' . $row['title'] . '">';
                                echo '<div class="card-body">';
                                echo '<h5 class="card-title">' . $title . '</h5>';
                                echo '<p class="card-genre">' . $row['genres'] . '</p>';
                                echo '</div>';
                                echo '</div>';
                                echo '</div>';
                            }
                        } else {
                            // Pesan jika tidak ada manga yang ditemukan
                            echo '<p class="text-center">Tidak ada manga tersedia.</p>';
                        }
                        $conn->close();
                        ?>
                    </div>
                </div>
            </div>
            
            <!-- Sidebar -->
            <div class="col-md-3">
                <!-- Pencarian judul manga -->
                <div class="search-bar">
                    <form method="GET" action="index.php" class="d-flex">
                        <input type="text" name="search" class="form-control" placeholder="Masukan Judul Manga" value="<?php echo $search_title; ?>">
                        <button type="submit" class="btn btn-primary">Cari</button>
                    </form>
                </div>
                <!-- Filter genre -->
                <div class="sidebar mt-4">
                    <div class="genre-filter">
                        <h3 class="text-warning">Genre</h3>
                        <form method="GET" action="index.php">
                            <div class="genre-list">
                                <!-- Menampilkan daftar genre untuk filter -->
                                <?php
                                if ($genre_result->num_rows > 0) {
                                    while ($genre_row = $genre_result->fetch_assoc()) {
                                        $checked = in_array($genre_row['id'], $selected_genres) ? 'checked' : '';
                                        echo '<label><input type="checkbox" name="genre[]" value="' . $genre_row['id'] . '" ' . $checked . '> ' . $genre_row['genre'] . '</label><br>';
                                    }
                                }
                                ?>
                            </div>
                            <button type="submit" class="btn btn-primary mt-2">Filter</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Footer -->
    <footer class="text-center mt-5">
        <p>&copy; 2024 by Tegar, Guntur, Dede</p>
    </footer>

    <!-- Script untuk Bootstrap -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
