<?php
// Memulai sesi
session_start();

// Menyertakan file database
include './admin/db.php';

// Mengecek apakah parameter 'id' ada di URL
if (!isset($_GET['id'])) {
    echo "ID Manga tidak ditemukan.";
    exit;
}

// Mendapatkan ID manga dari URL
$id = $_GET['id'];

// Query untuk mendapatkan detail manga berdasarkan ID
$sql = "SELECT m.*, GROUP_CONCAT(g.genre SEPARATOR ', ') as genres
        FROM manga m
        LEFT JOIN manga_genres mg ON m.id = mg.manga_id
        LEFT JOIN genres g ON mg.genre_id = g.id
        WHERE m.id = $id
        GROUP BY m.id, m.title, m.description, m.author, m.cover_image";
$result = $conn->query($sql);

// Mengecek apakah manga ditemukan
if ($result->num_rows == 0) {
    echo "Manga tidak ditemukan.";
    exit;
}

// Mengambil data manga
$manga = $result->fetch_assoc();

// Query untuk mendapatkan daftar chapter manga
$sql_chapters = "SELECT * FROM chapters WHERE manga_id = $id ORDER BY volume_number DESC";
$chapters_result = $conn->query($sql_chapters);

// Query untuk mendapatkan daftar komentar manga
$sql_comments = "SELECT * FROM comments WHERE manga_id = $id ORDER BY created_at DESC";
$comments_result = $conn->query($sql_comments);

?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Manga</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/navbars.css">
    <link rel="stylesheet" href="css/details.css">
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
    <div class="container mt-5">
        <div class="row">
            <div class="col-md-4">
                <!-- Menampilkan gambar cover manga -->
                <img src="<?php echo $manga['cover_image']; ?>" class="img-fluid manga-cover" alt="<?php echo $manga['title']; ?>">
            </div>
            <div class="col-md-8 details-content">
                <!-- Menampilkan detail manga -->
                <h1 class="text-warning"><?php echo $manga['title']; ?></h1>
                <p><strong>Genre:</strong> <?php echo $manga['genres']; ?></p>
                <p><strong>Author:</strong> <?php echo $manga['author']; ?></p>
                <p><strong>Synopsis:</strong> <?php echo $manga['description']; ?></p>
                
                <!-- Menampilkan daftar chapter manga -->
                <div class="chapter-list">
                    <h3 class="text-warning">Chapters</h3>
                    <div class="chapter-box">
                        <?php
                        if ($chapters_result->num_rows > 0) {
                            while($chapter = $chapters_result->fetch_assoc()) {
                                echo '<div class="chapter-item">';
                                echo '<a href="view_chapter.php?id=' . $chapter['id'] . '">Volume ' . $chapter['volume_number'] . ': ' . $chapter['title'] . '</a>';
                                echo '</div>';
                            }
                        } else {
                            echo '<p>Tidak ada chapter tersedia.</p>';
                        }
                        ?>
                    </div>
                </div>
                
                <!-- Menampilkan komentar manga -->
                <div class="comment-section">
                    <h3 class="text-warning">Comments</h3>
                    <div class="comments">
                        <?php
                        if ($comments_result->num_rows > 0) {
                            while($comment = $comments_result->fetch_assoc()) {
                                echo '<div class="comment-item">';
                                echo '<p><strong>' . $comment['username'] . ':</strong> ' . $comment['comment'] . '</p>';
                                echo '<small>' . $comment['created_at'] . '</small>';
                                echo '</div>';
                            }
                        } else {
                            echo '<p>No comments yet.</p>';
                        }
                        ?>
                    </div>
                    
                    <!-- Form untuk menambahkan komentar baru -->
                    <form action="add_comment.php" method="post">
                        <input type="hidden" name="manga_id" value="<?php echo $id; ?>">
                        <input type="text" name="username" class="form-control" placeholder="Enter your name" required><br>
                        <textarea name="comment" rows="3" class="form-control" required></textarea><br>
                        <button type="submit" class="btn btn-warning">Add Comment</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Footer -->
    <footer class="footer text-center mt-5">
        <p>&copy; 2024 by Tegar, Guntur, Dede</p>
    </footer>

    <!-- Script untuk Bootstrap -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
