<!DOCTYPE html>
<html>
<head>
    <title>Contoh Form</title>
    <script>
        // Fungsi untuk mendapatkan nilai dari input "Nama"
        function getNameValue() {
            // Ambil elemen dengan id "nama" dan dapatkan nilainya
            var nameValue = document.getElementById("nama").value;
            // Tampilkan nilai dalam sebuah alert
            alert("Nama: " + nameValue);
        }
    </script>
    <style>
        /* CSS untuk memberi tampilan seperti yang ada di soal */
        .form-container {
            border: 1px solid black;
            padding: 20px;
            width: 300px;
            margin: auto;
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        .form-container label,
        .form-container input,
        .form-container select,
        .form-container button {
            width: 100%;
        }

        .form-container button {
            margin-top: 10px;
        }
    </style>
</head>
<body>
    <div class="form-container">
        <!-- Label dan input untuk "Nama" -->
        <label for="nama">Nama:</label>
        <input type="text" id="nama" name="nama">

        <!-- Label dan dropdown untuk "Skema" -->
        <label for="skema">Skema:</label>
        <select id="skema" name="skema">
            <option value="junior-web-developer">Junior Web Developer</option>
            <option value="Senior Web Developer">Senior Web Developer</option>
            <option value="Full Stack Developer">Full Stack Developer</option>
        </select>

        <!-- Label dan input file untuk "Foto" -->
        <label for="foto">Foto:</label>
        <input type="file" id="foto" name="foto">

        <!-- Tombol untuk memicu fungsi getNameValue -->
        <button type="button" onclick="getNameValue()">Daftar</button>
    </div>
</body>
</html>
