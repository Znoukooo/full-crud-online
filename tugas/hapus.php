<?php

    session_start();

    $id = $_GET['id'];

    $stmt = mysqli_prepare($koneksi, "DELETE FROM tugas WHERE id='".$id."'");

    if($stmt){
        header('location:list.php');
    }else{
        echo "gagal";
    }

?>