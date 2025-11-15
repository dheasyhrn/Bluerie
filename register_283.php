<?php
require_once 'koneksi_283.php';
$db = new Database();
$conn = $db->conn;

if (isset($_POST['register'])){
    $username = $_POST['username'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $email = $_POST['email'];

    $query = "INSERT INTO users (username, password, email) VALUES ('$username', '$password', '$email')";

    if ($conn->query($query) === TRUE){
        $success = "Registrasi berhasil! Silakan login.";
    } else {
        $error = "Registrasi gagal: " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrasi - Bluerie</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(to right, #007bff, #00bfff);
            font-family: 'Segoe UI', sans-serif;
        }
        .register-container {
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
        }
        .register-card {
            background-color: #fff;
            padding: 40px 30px;
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 450px;
        }
        .register-card h2 {
            font-weight: bold;
            color: #007bff;
            margin-bottom: 30px;
        }
        .btn-blue {
            background-color: #007bff;
            border: none;
        }
        .btn-blue:hover {
            background-color: #0056b3;
        }
        .brand {
            font-size: 24px;
            font-weight: bold;
            color: #007bff;
            margin-bottom: 20px;
            text-align: center;
        }
    </style>
</head>
<body>
<div class="register-container">
    <div class="register-card">
        <div class="brand">Bluerie</div>
        <h2 class="text-center">Registrasi</h2>
        <?php if (isset($success)): ?>
            <div class="alert alert-success text-center"><?= $success ?></div>
        <?php elseif (isset($error)): ?>
            <div class="alert alert-danger text-center"><?= $error ?></div>
        <?php endif; ?>
        <form method="post">
            <div class="mb-3">
                <label class="form-label">Username</label>
                <input type="text" name="username" class="form-control" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Password</label>
                <input type="password" name="password" class="form-control" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Email</label>
                <input type="email" name="email" class="form-control" required>
            </div>
            <input type="submit" name="register" value="Register" class="btn btn-blue w-100 text-white">
            <div class="text-center mt-3">
                <a href="login_283.php">Sudah punya akun? Login</a>
            </div>
        </form>
    </div>
</div>
</body>
</html>
