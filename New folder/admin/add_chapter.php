<?php
session_start();
include '../admin/db.php';

if (!isset($_SESSION['admin'])) {
    header("Location: /komik/admin");
    exit();
}

$sql = "SELECT id, title FROM manga";
$result = $conn->query($sql);
$manga_options = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $manga_options[] = $row;
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['add_chapter'])) {
        $manga_id = $_POST['manga_id'];
        $title = $_POST['title'];
        $volume_number = $_POST['volume_number'];

        $target_dir = "../uploads/";

        if (!is_dir($target_dir)) {
            mkdir($target_dir, 0777, true);
        }

        $target_file = $target_dir . basename($_FILES["pdf_file"]["name"]);

        if (file_exists($target_file)) {
            echo "Sorry, file already exists.";
            exit();
        }

        if (move_uploaded_file($_FILES["pdf_file"]["tmp_name"], $target_file)) {
            $sql = "INSERT INTO chapters (manga_id, title, volume_number, pdf_path) VALUES (?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("isis", $manga_id, $title, $volume_number, $target_file);
            $stmt->execute();
            $stmt->close();
            echo "Chapter berhasil diupload.";
        } else {
            echo "Maaf, file Anda tidak berhasil diunggah.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Chapter</title>
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
                <h2>Tambah Chapter</h2>
                <form action="add_chapter.php" method="post" enctype="multipart/form-data">
                    <div class="form-group">
                        <label for="manga_id">Pilih Manga:</label>
                        <select id="manga_id" name="manga_id" class="form-control" required>
                            <option value="" disabled selected>Pilih Manga</option>
                            <?php foreach ($manga_options as $manga): ?>
                                <option value="<?php echo $manga['id']; ?>"><?php echo $manga['title']; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="title">Judul Chapter:</label>
                        <input type="text" id="title" name="title" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label for="volume_number">Nomor Volume:</label>
                        <input type="number" id="volume_number" name="volume_number" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label for="pdf_file">Upload PDF:</label>
                        <input type="file" id="pdf_file" name="pdf_file" class="form-control-file" required>
                    </div>
                    <button type="submit" name="add_chapter" class="btn btn-primary">Tambah Chapter</button>
                </form>
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
