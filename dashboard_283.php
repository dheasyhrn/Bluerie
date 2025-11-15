<?php
session_start();
if (!isset($_SESSION['username'])) {
    echo "akses ditolak. silahkan login dulu";
    exit;
}

require_once 'koneksi_283.php';
$db = new Database();
$conn = $db->conn;
$produk_result = $conn->query("SELECT COUNT(*) AS total FROM products");
$total_produk = $produk_result->fetch_assoc()['total'];

$user_result = $conn->query("SELECT COUNT(*) AS total FROM users");
$total_user = $user_result->fetch_assoc()['total'];

$harga_result = $conn->query("SELECT SUM(harga) AS total FROM products");
$total_harga = $harga_result->fetch_assoc()['total'] ?? 0;

$search = isset($_GET['search']) ? trim($conn->real_escape_string($_GET['search'])) : "";
$query = "SELECT * FROM products";
if (!empty($search)) {
    $query .= " WHERE nama_produk LIKE '%$search%' OR deskripsi LIKE '%$search%' OR CAST(harga AS CHAR) LIKE '%$search%'";
}
$query .= " ORDER BY id DESC LIMIT 5";
$produk_terbaru = $conn->query($query);
?>

<!DOCTYPE html><html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <style>
        body {
            background: linear-gradient(to right, #4facfe, #00f2fe);
            color: #fff;
            min-height: 100vh;
        }
        .navbar {
            background-color: #004aad;
        }
        .sidebar {
            width: 250px;
            background-color: #003580;
            position: fixed;
            top: 0;
            left: -250px;
            height: 100%;
            padding-top: 60px;
            transition: all 0.3s;
            z-index: 1000;
        }
        .sidebar a {
            display: block;
            color: #fff;
            margin: 10px 15px;
            padding: 12px 20px;
            text-decoration: none;
            border-radius: 8px;
            background-color: rgba(255, 255, 255, 0.1);
            transition: 0.3s;
        }
        .sidebar a:hover {
            background-color: rgba(255, 255, 255, 0.2);
        }
        #sidebarToggle:checked ~ .sidebar {
            left: 0;
        }
        .content {
            margin-top: 60px;
            padding: 30px;
        }
        .overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0,0,0,0.5);
            z-index: 900;
            display: none;
        }
        #sidebarToggle:checked ~ .overlay {
            display: block;
        }
        .card-box {
            background-color: rgba(255, 255, 255, 0.9);
            color: #003580;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
            text-align: center;
        }
        .circle-box {
            width: 120px;
            height: 120px;
            background-color: white;
            color: #004aad;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-direction: column;
            margin: auto;
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
        }
        .circle-box h4 {
            margin: 0;
            font-size: 14px;
        }
        .circle-box h2 {
            margin: 0;
        }
        .table-container {
            background-color: rgba(255, 255, 255, 0.9);
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
            color: #003580;
        }
        .hamburger {
            font-size: 24px;
            cursor: pointer;
            color: white;
            margin-left: 15px;
        }
        .brand-label {
            color: white;
            font-weight: bold;
            font-size: 20px;
            margin-left: 10px;
        }
        .logout-btn {
            position: absolute;
            right: 20px;
            top: 10px;
            color: white;
            text-decoration: none;
        }
    </style>
</head>
<body>
<input type="checkbox" id="sidebarToggle" hidden><nav class="navbar navbar-dark fixed-top shadow d-flex justify-content-between align-items-center px-3">
    <div class="d-flex align-items-center">
        <label for="sidebarToggle" class="hamburger me-2 mb-0">☰</label>
        <span class="brand-label mb-0">Bluerie</span>
    </div>
    <a class="logout-btn" href="logout_283.php">Logout</a>
</nav><div class="sidebar">
    <a href="dashboard.php">Dashboard</a>
    <a href="produk_283.php">Lihat Produk</a>
    <a href="tambah_produk_283.php">Tambah Produk</a>
    <a href="produk_pdf_283.php">Export PDF</a>
    <a href="login_283.php">Login</a>
    <a href="register_283.php">Register</a>
</div><label class="overlay" for="sidebarToggle"></label>

<div class="content container">
    <div class="mb-4 text-center">
        <h2>Halo, <?php echo $_SESSION['username']; ?>!</h2>
    </div><div class="row mb-4">
    <div class="col-md-4 mb-3">
        <div class="card-box">
            <h4>Total Produk</h4>
            <h2><?= $total_produk ?></h2>
        </div>
    </div>
    <div class="col-md-4 mb-3">
        <div class="card-box">
            <h4>Total User</h4>
            <h2><?= $total_user ?></h2>
        </div>
    </div>
    <div class="col-md-4 mb-3 d-flex align-items-center justify-content-center">
        <div class="circle-box">
            <h4>Total Harga</h4>
            <h2>Rp <?= number_format($total_harga, 0, ',', '.') ?></h2>
        </div>
    </div>
</div>

<div class="table-container">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="m-0">Produk Terbaru</h4>
        <form method="GET" class="d-flex">
            <input type="text" name="search" class="form-control me-2" placeholder="Cari produk..." value="<?= htmlspecialchars($search) ?>">
            <button class="btn btn-primary" type="submit">Cari</button>
        </form>
    </div>
    <table class="table table-bordered table-striped">
        <thead>
            <tr>
                <th>Nama Produk</th>
                <th>Deskripsi</th>
                <th>Harga</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $produk_terbaru->fetch_assoc()): ?>
            <tr>
                <td><?= htmlspecialchars($row['nama_produk']) ?></td>
                <td><?= htmlspecialchars($row['deskripsi']) ?></td>
                <td>Rp. <?= htmlspecialchars($row['harga']) ?></td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>

</div>
</body>
</html>
