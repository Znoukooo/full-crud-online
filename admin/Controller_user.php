<?php
    include "../koneksi.php";

    $username   = $_POST['username'];
    $password   = $_POST['password'];
    $role   = $_POST['role'];
    $nama_lengkap   = $_POST['nama_lengkap'];
    $email   = $_POST['email'];

    $sql = mysqli_query($koneksi, "INSERT INTO users (username, password, role, nama_lengkap, email)VALUES('$username', '$password', '$role', '$nama_lengkap', '$email')");

    if ($sql) {
        header('Location: ../admin/kelola_user.php');
        exit;
    } else {
        echo "Gagal input data: " . mysqli_error($koneksi);
    }
?>