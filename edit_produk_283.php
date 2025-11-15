<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: login_283.php");
    exit;
}

// === OOP Class Database ===
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

    public function getById($id) {
        $stmt = $this->conn->prepare("SELECT * FROM products WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    public function update($id, $nama, $deskripsi, $harga, $gambar) {
        if ($gambar != '') {
            $stmt = $this->conn->prepare("UPDATE products SET nama_produk=?, deskripsi=?, harga=?, gambar=? WHERE id=?");
            $stmt->bind_param("ssisi", $nama, $deskripsi, $harga, $gambar, $id);
        } else {
            $stmt = $this->conn->prepare("UPDATE products SET nama_produk=?, deskripsi=?, harga=? WHERE id=?");
            $stmt->bind_param("ssii", $nama, $deskripsi, $harga, $id);
        }
        return $stmt->execute();
    }
}

// === Ambil ID ===
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$product = new Product();
$data = $product->getById($id);

if (!$data) {
    echo "<div class='text-center mt-5'><h4>Produk tidak ditemukan.</h4></div>";
    exit;
}

// === Proses Edit ===
$msg = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama = $_POST['nama'];
    $deskripsi = $_POST['deskripsi'];
    $harga = $_POST['harga'];

    $gambar = $_FILES['gambar']['name'];
    $tmp = $_FILES['gambar']['tmp_name'];

    if ($gambar != '') {
        move_uploaded_file($tmp, 'gambar/' . $gambar);
    }

    if ($product->update($id, $nama, $deskripsi, $harga, $gambar)) {
        $msg = '<div class="alert alert-success">Produk berhasil diupdate!</div>';
        $data = $product->getById($id); // Refresh data
    } else {
        $msg = '<div class="alert alert-danger">Gagal update produk.</div>';
    }
}
?>

<!-- === Tampilan HTML === -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Produk</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container mt-5" style="max-width: 600px;">
    <h2 class="text-center text-primary mb-4">Edit Produk</h2>

    <?= $msg; ?>

    <form method="post" enctype="multipart/form-data">
        <div class="mb-3">
            <label>Nama Produk</label>
            <input type="text" name="nama" class="form-control" value="<?= htmlspecialchars($data['nama_produk']); ?>" required>
        </div>

        <div class="mb-3">
            <label>Deskripsi</label>
            <textarea name="deskripsi" class="form-control" required><?= htmlspecialchars($data['deskripsi']); ?></textarea>
        </div>

        <div class="mb-3">
            <label>Harga</label>
            <input type="number" name="harga" class="form-control" value="<?= $data['harga']; ?>" required>
        </div>

        <div class="mb-3">
            <label>Gambar (kosongkan jika tidak diganti)</label>
            <input type="file" name="gambar" class="form-control">
            <small class="text-muted">Gambar saat ini: <?= $data['gambar']; ?></small>
        </div>

        <div class="text-center">
            <button type="submit" class="btn btn-success">Update</button>
            <a href="produk_283.php" class="btn btn-secondary ms-2">← Kembali</a>
        </div>
    </form>
</div>

</body>
</html>
