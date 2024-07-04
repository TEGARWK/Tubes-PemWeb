<?php
// Menyertakan file database
include './admin/db.php';

// Mendapatkan ID chapter dari URL
$chapter_id = $_GET['id'];

// Query untuk mendapatkan detail chapter berdasarkan ID
$chapter_sql = "SELECT * FROM chapters WHERE id = $chapter_id";
$chapter_result = $conn->query($chapter_sql);

// Mengecek apakah chapter ditemukan
if ($chapter_result->num_rows > 0) {
    $chapter_row = $chapter_result->fetch_assoc();
    $pdf_path = $chapter_row['pdf_path'];
} else {
    echo "Chapter tidak ditemukan.";
    exit;
}

// Menghilangkan '../' dari path PDF jika ada
$pdf_path = str_replace('../uploads/', 'uploads/', $pdf_path);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo 'Volume ' . $chapter_row['volume_number'] . ': ' . $chapter_row['title']; ?></title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/navbars.css">
    <link rel="stylesheet" href="css/chapter.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/2.10.377/pdf_viewer.min.css">
</head>
<body>
<script type="text/javascript">
    // Mencegah klik kanan dan drag
    function mousedwn(e){try{if(event.button==2||event.button==3)return false}catch(e){if(e.which==3)return false}}document.oncontextmenu=function(){return false};
    document.ondragstart=function(){return false};
    document.onmousedown=mousedwn
</script>
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
        <h1><?php echo 'Volume ' . $chapter_row['volume_number'] . ': ' . $chapter_row['title']; ?></h1>
        <div class="pdf-container">
            <div id="pdf-viewer"></div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="text-center mt-5">
        <p>&copy; 2024 by Tegar, Guntur, Dede</p>
    </footer>

    <!-- jQuery, Popper.js, and Bootstrap JS -->
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <!-- PDF.js Scripts -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/2.10.377/pdf.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/2.10.377/pdf_viewer.min.js"></script>
    <script>
        const url = '<?php echo $pdf_path; ?>';

        // Menggunakan PDF.js untuk merender PDF
        pdfjsLib.getDocument(url).promise.then(function(pdf) {
            const viewer = document.getElementById('pdf-viewer');
            
            for (let pageNum = 1; pageNum <= pdf.numPages; pageNum++) {
                pdf.getPage(pageNum).then(function(page) {
                    const scale = 1.5;
                    const viewport = page.getViewport({ scale: scale });

                    const canvas = document.createElement('canvas');
                    const context = canvas.getContext('2d');
                    canvas.height = viewport.height;
                    canvas.width = viewport.width;

                    const renderContext = {
                        canvasContext: context,
                        viewport: viewport
                    };

                    viewer.appendChild(canvas);

                    page.render(renderContext).promise.then(function() {
                        console.log('Page rendered');
                    });
                });
            }
        }).catch(function(error) {
            console.error('Error while rendering PDF:', error);
        });
    </script>
</body>
</html>
