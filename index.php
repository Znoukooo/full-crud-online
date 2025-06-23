<?php
    session_start();
    include "koneksi.php";
    include "template.php";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <title>Tugas Online Unpam</title>

    <style>
        .car{
            margin-top: 110px;
        }
        .car h1{
            color: var(--primary-color);
            font-weight: 600;
        }
        .image img{
            width: 100%;
        }
    </style>
</head>
<body>
    <?php
        include "navbar.php";
    ?>

    <div class="container">
        <div class="col-lg-12">
            <div class="row d-flex align-items-center">
                <div class="col-lg-6 col-md-12 my-5">
                    <div class="car my-5">
                        
                        <h1>Selamat Datang!</h1>
                        <h5>Kumpulkan tugas mu sekarang!</h5>
                        <p style="text-align: justify;">Gunakan platform ini untuk melihat, mengerjakan, dan mengumpulkan tugas kuliah tepat waktu. Jangan lupa cek deadline tugas secara berkala!</p>
                    </div>
                </div>
                <div class="col-lg-6 col-md-12 d-flex justify-content-center align-items-center p-3 mt-4">
                    <div class="image  ">
                        <img src="assets/img/illustration01.png" alt="" srcset="">
                    </div>
                </div>
            </div>
        </div>
    </div>
   
</body>

</html>