<?php
require('fpdf/fpdf.php');

// === KONEKSI ===
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

// === OOP CLASS PRODUCT ===
class Product {
    private $conn;

    public function __construct() {
        $db = new Database();
        $this->conn = $db->conn;
    }

    public function getAll() {
        return $this->conn->query("SELECT * FROM products");
    }
}

// === AMBIL DATA ===
$product = new Product();
$data = $product->getAll();

// === BUAT PDF ===
$pdf = new FPDF();
$pdf->AddPage();
$pdf->SetFont('Arial','B',16);
$pdf->Cell(0,10,'Daftar Produk Bluerie',0,1,'C');

$pdf->SetFont('Arial','B',12);
$pdf->Cell(10,10,'No',1);
$pdf->Cell(50,10,'Nama Produk',1);
$pdf->Cell(80,10,'Deskripsi',1);
$pdf->Cell(30,10,'Harga',1);
$pdf->Ln();

$no = 1;
$pdf->SetFont('Arial','',11);
while($row = $data->fetch_assoc()) {
    $pdf->Cell(10,10,$no++,1);
    $pdf->Cell(50,10,$row['nama_produk'],1);
    $pdf->Cell(80,10,substr($row['deskripsi'],0,35).'...',1);
    $pdf->Cell(30,10,'Rp '.number_format($row['harga']),1);
    $pdf->Ln();
}

$pdf->Output('I', 'produk_bluerie.pdf'); //MENAMPILKAN PDF
?>
