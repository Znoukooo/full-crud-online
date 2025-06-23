<?php 
session_start();
include "koneksi.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    $username = $_POST['username'];
    $password = $_POST['password'];

   
    $sql = mysqli_prepare($koneksi, "SELECT * FROM users WHERE username = ? AND password = ?");
    mysqli_stmt_bind_param($sql, "ss", $username, $password);
    mysqli_stmt_execute($sql);

    $result = mysqli_stmt_get_result($sql);

 
    if(mysqli_num_rows($result) > 0){
        $data = mysqli_fetch_array($result);

    
        $_SESSION['login'] = "sudah login";
        $_SESSION['id'] = $data['id'];
        $_SESSION['username'] = $data['username'];
        $_SESSION['password'] = $data['password'];
        $_SESSION['role'] = $data['role'];
        $_SESSION['nama_lengkap'] = $data['nama_lengkap'];
        $_SESSION['email'] = $data['email'];

        header("location:dashboard/dashboard.php");
    } else {
        $_SESSION['alert'] = "Username tidak ditemukan. Silakan coba lagi.";
        header("location:login.php");
    }
    mysqli_stmt_close($sql);
}

mysqli_close($koneksi);
?>
