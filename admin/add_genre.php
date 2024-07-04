<?php
session_start();
include 'db.php';

if (!isset($_SESSION['admin'])) {
    header("Location: /komik/admin");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['add_genre'])) {
        $name = $_POST['name'];
        $sql = "INSERT INTO genres (genre) VALUES (?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $name);
        $stmt->execute();
        $stmt->close();
        header("Location: add_genre.php");
        exit();
    }
}

if (isset($_GET['delete'])) {
    $genre_id = $_GET['delete'];
    $sql = "DELETE FROM genres WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $genre_id);
    $stmt->execute();
    $stmt->close();
    header("Location: add_genre.php");
    exit();
}

$genre_sql = "SELECT * FROM genres";
$genre_result = $conn->query($genre_sql);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Genre</title>
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
                    <div class="col-md-12">
                        <h2>Tambah Genre</h2>
                        <form action="add_genre.php" method="post">
                            <div class="form-group">
                                <label for="name">Nama Genre:</label>
                                <input type="text" id="name" name="name" class="form-control" required>
                            </div>
                            <button type="submit" name="add_genre" class="btn btn-primary">Tambah Genre</button>
                        </form>
                        <hr>
                        <h2>Daftar Genre</h2>
                        <table class="table table-bordered table-hover bg-dark">
                            <thead class="thead-dark">
                                <tr>
                                    <th>ID</th>
                                    <th>Nama Genre</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                if ($genre_result->num_rows > 0) {
                                    while ($row = $genre_result->fetch_assoc()) {
                                        echo '<tr>';
                                        echo '<td>' . $row['id'] . '</td>';
                                        echo '<td>' . $row['genre'] . '</td>';
                                        echo '<td>';
                                        echo '<a href="add_genre.php?delete=' . $row['id'] . '" class="btn btn-danger btn-sm">Hapus</a>';
                                        echo '</td>';
                                        echo '</tr>';
                                    }
                                } else {
                                    echo '<tr><td colspan="3">Tidak ada genre yang tersedia.</td></tr>';
                                }
                                ?>
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
