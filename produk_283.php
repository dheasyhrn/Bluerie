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

    public function getAll() {
        return $this->conn->query("SELECT * FROM products");
    }

    public function search($keyword) {
        $keyword = $this->conn->real_escape_string($keyword);
        $query = "SELECT * FROM products 
                  WHERE nama_produk LIKE '%$keyword%' 
                  OR deskripsi LIKE '%$keyword%' 
                  OR CAST(harga AS CHAR) LIKE '%$keyword%'";
        return $this->conn->query($query);
    }

    public function delete($id) {
        $id = (int)$id;
        return $this->conn->query("DELETE FROM products WHERE id = $id");
    }
}

// === Proses ===
$product = new Product();

// Jika ada parameter delete
if (isset($_GET['delete'])) {
    $product->delete($_GET['delete']);
    header("Location: produk_283.php");
    exit;
}

// Ambil data produk
$keyword = isset($_GET['search']) ? $_GET['search'] : '';
$result = !empty($keyword) ? $product->search($keyword) : $product->getAll();
?>

<!-- === TAMPILAN HTML === -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Lihat Produk</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container mt-5">
    <h2 class="text-center text-primary mb-4">Lihat Produk</h2>

    <!-- Form Search -->
    <form method="get" action="" class="d-flex justify-content-center mb-4">
        <input type="text" name="search" class="form-control w-50 me-2" placeholder="Cari produk..." value="<?= htmlspecialchars($keyword); ?>">
        <button type="submit" class="btn btn-primary">Search</button>
    </form>

    <!-- Tombol Navigasi -->
    <div class="text-center mb-4">
        <a href="dashboard_283.php" class="btn btn-secondary me-2">← Kembali</a>
        <a href="tambah_produk_283.php" class="btn btn-success me-2">+ Tambah Produk</a>
        <a href="produk_pdf_283.php" class="btn btn-danger">Export PDF</a>
    </div>

    <!-- Tabel Produk -->
    <table class="table table-bordered table-hover shadow-sm">
        <thead class="table-primary text-center">
            <tr>
                <th>Nama Produk</th>
                <th>Deskripsi</th>
                <th>Harga</th>
                <th>Gambar</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody class="text-center">
        <?php if ($result && $result->num_rows > 0): ?>
            <?php while ($row = $result->fetch_assoc()): ?>
            <tr>
                <td><?= $row['nama_produk']; ?></td>
                <td><?= $row['deskripsi']; ?></td>
                <td>Rp<?= number_format($row['harga'], 0, ',', '.'); ?></td>
                <td><img src="gambar/<?= $row['gambar']; ?>" width="80"></td>
                <td>
                    <a href="edit_produk_283.php?id=<?= $row['id']; ?>" class="btn btn-sm btn-warning">Edit</a>
                    <a href="produk_283.php?delete=<?= $row['id']; ?>" onclick="return confirm('Yakin ingin hapus?')" class="btn btn-sm btn-danger">Hapus</a>
                </td>
            </tr>
            <?php endwhile; ?>
        <?php else: ?>
            <tr><td colspan="5">Tidak ada produk ditemukan.</td></tr>
        <?php endif; ?>
        </tbody>
    </table>
</div>

</body>
</html>
