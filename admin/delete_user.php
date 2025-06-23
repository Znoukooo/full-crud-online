<?php
    session_start();
    include "../koneksi.php";

    $id = $_GET['id'];

    $sql = mysqli_query($koneksi, "DELETE FROM users WHERE id='".$id."' ");
    if($sql){
        header('location:kelola_user.php');
    }else{
        header('gagal hapus');
    }
?>