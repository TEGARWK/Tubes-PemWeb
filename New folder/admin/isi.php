<?php
session_start();
include '../admin/db.php'; // Sesuaikan dengan lokasi file db.php

if (!isset($_SESSION['admin'])) {
    header("Location: /komik/admin");
    exit();
}

// Mengambil data manga dan genres dari database
$sql = "SELECT m.id, m.title, m.description, m.author, m.cover_image, GROUP_CONCAT(g.genre SEPARATOR ', ') as genres
        FROM manga m
        LEFT JOIN manga_genres mg ON m.id = mg.manga_id
        LEFT JOIN genres g ON mg.genre_id = g.id
        GROUP BY m.id, m.title, m.description, m.author, m.cover_image";
$result = $conn->query($sql);
$manga_data = [];
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $manga_data[] = $row;
    }
}

// Mengambil data genres dari database
$sql = "SELECT id, genre FROM genres";
$result = $conn->query($sql);
$genres = [];
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $genres[] = $row;
    }
}

// CRUD operations
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['delete_manga'])) {
        $id = $_POST['id'];

        // Menghapus komentar terkait
        $sql_delete_comments = "DELETE FROM comments WHERE manga_id = ?";
        $stmt_comments = $conn->prepare($sql_delete_comments);
        $stmt_comments->bind_param("i", $id);
        $stmt_comments->execute();
        $stmt_comments->close();

        // Menghapus chapters terkait
        $sql_delete_chapters = "DELETE FROM chapters WHERE manga_id = ?";
        $stmt_chapters = $conn->prepare($sql_delete_chapters);
        $stmt_chapters->bind_param("i", $id);
        $stmt_chapters->execute();
        $stmt_chapters->close();

        // Mengambil path gambar sebelum menghapus data
        $sql = "SELECT cover_image FROM manga WHERE id=?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $stmt->bind_result($cover_image);
        $stmt->fetch();
        $stmt->close();

        // Menghapus file gambar jika ada
        if ($cover_image && file_exists(__DIR__ . '/../' . $cover_image)) {
            unlink(__DIR__ . '/../' . $cover_image);
        }

        // Menghapus data dari tabel manga
        $sql = "DELETE FROM manga WHERE id=?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $stmt->close();

        // Menghapus data dari tabel manga_genres
        $sql = "DELETE FROM manga_genres WHERE manga_id=?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $stmt->close();

        header("Location: isi.php");
    } elseif (isset($_POST['edit_manga'])) {

        $id = $_POST['id'];
        $title = $_POST['title'];
        $description = $_POST['description'];
        $author = $_POST['author'];
        $genres_input = isset($_POST['type']) ? $_POST['type'] : [];
        $cover_image = $_FILES['cover_image']['name'] ? 'uploads/' . basename($_FILES['cover_image']['name']) : null;

        // Update manga table
        $sql = "UPDATE manga SET title=?, description=?, author=?";
        if ($cover_image) {
            $sql .= ", cover_image=?";
        }
        $sql .= " WHERE id=?";
        
        $stmt = $conn->prepare($sql);
        if ($cover_image) {
            $stmt->bind_param("ssssi", $title, $description, $author, $cover_image, $id);
        } else {
            $stmt->bind_param("sssi", $title, $description, $author, $id);
        }
        $stmt->execute();

        // Upload new cover image if exists
        if ($cover_image && move_uploaded_file($_FILES['cover_image']['tmp_name'], __DIR__ . '/../' . $cover_image)) {
            echo "File " . htmlspecialchars(basename($_FILES['cover_image']['name'])) . " telah diunggah.";
        }

        // Update manga_genres table
        // First, delete existing genres
        $sql = "DELETE FROM manga_genres WHERE manga_id=?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $id);
        $stmt->execute();

        // Then, insert new genres
        foreach ($genres_input as $genre) {
            $sql = "INSERT INTO manga_genres (manga_id, genre_id) VALUES (?, (SELECT id FROM genres WHERE genre = ?))";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("is", $id, $genre);
            $stmt->execute();
        }

        header("Location: isi.php");
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Manga</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <link rel="stylesheet" href="../css/dashboards.css">
</head>
<body>
    <div class="wrapper">
        <?php include 'navbar.php'; ?>
        <div id="content">
                <div class="container-fluid">
                    <button type="button" id="sidebarCollapse" class="btn btn-info">
                        <i class="fas fa-align-left"></i>
                    </button>
                </div> 
            <div class="container">
                <div class="row">
                    <div class="col-md-12 bg-dark">
                        <h2>Data Manga</h2>
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Judul</th>
                                    <th>Deskripsi</th>
                                    <th>Penulis</th>
                                    <th>Genre</th>
                                    <th>Gambar Sampul</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach($manga_data as $manga): ?>
                                <tr>
                                    <td><?= $manga['id']; ?></td>
                                    <td><?= $manga['title']; ?></td>
                                    <td><?= $manga['description']; ?></td>
                                    <td><?= $manga['author']; ?></td>
                                    <td><?= $manga['genres']; ?></td>
                                    <td><img src="../<?= $manga['cover_image']; ?>" alt="Cover Image" style="width: 50px;"></td>
                                    <td>
                                        <form method="POST" style="display:inline-block;" onsubmit="return confirm('Anda yakin ingin menghapus data ini?');">
                                            <input type="hidden" name="id" value="<?= $manga['id']; ?>">
                                            <button type="submit" name="delete_manga" class="btn btn-danger">Delete</button>
                                        </form>
                                        <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#editModal<?= $manga['id']; ?>">Edit</button>
                                    </td>
                                </tr>

                                <!-- Modal Edit -->
                                <div class="modal fade" id="editModal<?= $manga['id']; ?>" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <form method="POST" enctype="multipart/form-data">
                                                <div class="modal-header">
                                                    <h5 class="modal-title" id="editModalLabel">Edit Manga</h5>
                                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                        <span aria-hidden="true">&times;</span>
                                                    </button>
                                                </div>
                                                <div class="modal-body">
                                                    <input type="hidden" name="id" value="<?= $manga['id']; ?>">
                                                    <div class="form-group">
                                                        <label for="title">Judul:</label>
                                                        <input type="text" id="title" name="title" class="form-control" value="<?= $manga['title']; ?>" required>
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="description">Deskripsi:</label>
                                                        <textarea id="description" name="description" class="form-control" required><?= $manga['description']; ?></textarea>
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="author">Penulis:</label>
                                                        <input type="text" id="author" name="author" class="form-control" value="<?= $manga['author']; ?>" required>
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="type">Genre:</label>
                                                        <?php 
                                                        $selected_type = isset($manga['genres']) ? explode(', ', $manga['genres']) : [];
                                                        foreach($genres as $genre): ?>
                                                            <div class="form-check">
                                                                <input class="form-check-input" type="checkbox" name="type[]" id="type<?= $genre['id']; ?>" value="<?= $genre['genre']; ?>" <?= in_array($genre['genre'], $selected_type) ? 'checked' : ''; ?>>
                                                                <label class="form-check-label" for="type<?= $genre['id']; ?>">
                                                                    <?= $genre['genre']; ?>
                                                                </label>
                                                            </div>
                                                        <?php endforeach; ?>
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="cover_image">Gambar Sampul:</label>
                                                        <input type="file" id="cover_image" name="cover_image" class="form-control">
                                                        <small class="form-text text-muted">Biarkan kosong jika tidak ingin mengubah gambar sampul.</small>
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                                    <button type="submit" name="edit_manga" class="btn btn-primary">Save changes</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>   
    </div>                                                
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script>
        $(document).ready(function () {
            $('#sidebarCollapse').on('click', function () {
                $('#sidebar').toggleClass('active');
            });
        });
    </script>
</body>
</html>

<?php
$conn->close(); // Menutup koneksi database
?>
