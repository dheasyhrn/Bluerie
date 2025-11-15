<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: login_283.php");
    exit;
}

// === Class Database ===
class Database {
    private $host = "localhost";
    private $user = "root";
    private $pass = "";
    private $dbname = "latihan";
    public $conn;

    public function __construct() {
        $this->conn = new mysqli($this->host, $this->user, $this->pass, $this->dbname);
        if ($this->conn->connect_error) {
            die("Koneksi gagal: " . $this->conn->connect_error);
        }
    }
}

// === OOP Class Product ===
class Product {
    private $conn;

    public function __construct() {
        $db = new Database();
        $this->conn = $db->conn;
    }

    public function add($nama, $deskripsi, $harga, $gambar) {
        $stmt = $this->conn->prepare("INSERT INTO products (nama_produk, deskripsi, harga, gambar) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssis", $nama, $deskripsi, $harga, $gambar);
        return $stmt->execute();
    }
}

// === Proses Tambah Produk ===
$msg = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama = $_POST['nama'];
    $deskripsi = $_POST['deskripsi'];
    $harga = $_POST['harga'];

    $gambar = $_FILES['gambar']['name'];
    $tmp = $_FILES['gambar']['tmp_name'];

    if ($gambar != '') {
        $target = 'gambar/' . basename($gambar);
        move_uploaded_file($tmp, $target);
    }

    $product = new Product();
    if ($product->add($nama, $deskripsi, $harga, $gambar)) {
        $msg = '<div class="alert alert-success">Produk berhasil ditambahkan!</div>';
    } else {
        $msg = '<div class="alert alert-danger">Gagal menambahkan produk.</div>';
    }
}
?>

<!-- === Tampilan HTML === -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Tambah Produk</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container mt-5" style="max-width: 600px;">
    <h2 class="text-center text-primary mb-4">Tambah Produk</h2>

    <?= $msg; ?>

    <form method="post" enctype="multipart/form-data">
        <div class="mb-3">
            <label>Nama Produk</label>
            <input type="text" name="nama" class="form-control" required>
        </div>

        <div class="mb-3">
            <label>Deskripsi</label>
            <textarea name="deskripsi" class="form-control" required></textarea>
        </div>

        <div class="mb-3">
            <label>Harga</label>
            <input type="number" name="harga" class="form-control" required>
        </div>

        <div class="mb-3">
            <label>Upload Gambar</label>
            <input type="file" name="gambar" class="form-control" required>
        </div>

        <div class="text-center">
            <button type="submit" class="btn btn-success">Simpan</button>
            <a href="produk_283.php" class="btn btn-secondary ms-2">← Kembali</a>
        </div>
    </form>
</div>

</body>
</html>
