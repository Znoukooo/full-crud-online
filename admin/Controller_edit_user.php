<?php
    include "../koneksi.php";

    $id         = $_POST['id'];
    $username   = $_POST['username'];
    $password   = $_POST['password'];
    $role   = $_POST['role'];
    $nama_lengkap   = $_POST['nama_lengkap'];
    $email   = $_POST['email'];

    $sql = mysqli_query($koneksi, "UPDATE users SET username ='".$username."', password ='".$password."', role ='".$role."', nama_lengkap ='".$nama_lengkap."', email ='".$email."' WHERE id ='".$id."'");

    if ($sql) {
        header('Location:kelola_user.php');
        exit;
    } else {
        echo "Gagal input data: " . mysqli_error($koneksi);
    }
?>