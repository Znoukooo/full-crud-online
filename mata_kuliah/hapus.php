<?php
    session_start();
    include "../koneksi.php";
    $id = $_GET['id'];
    

    $sql = mysqli_query($koneksi, "DELETE FROM mata_kuliah WHERE id='".$id."'");
    if($sql){
        header('location:list.php');
    }else{
        echo "gagal";
    }
   


?>