<?php
    session_start();
    include "koneksi.php";
    include "template.php";
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="bootstrap/css/bootstrap.min.css">
    <title>Tugas Online Unpam</title>
    <style>
      .card-title{
        font-weight: 700;
        
      }
      .card{
        border: none;
        border-radius: 0px 50px 0px 50px;
      }

      .btn-login{
      border: 2px solid var(--primary-color);
      color: var(--primary-color);
      font-weight: 700;
      width: 100%;
    }
    .btn-login:hover{
      background-color:  var(--primary-color);
      color: #f7f6fc;
      font-weight: 700;
      width: 100%;
    }
    .card-title{
      color: var(--primary-color);
    }
    .line{
      width: 100%;
      height: 2px;
      background-color: var(--primary-color);
    }

    .form-control:hover,
    .form-control:focus{
      box-shadow: 0 0 10px var(--primary-color);
      transition: 0.5s;
    }
    .text{
      color: var(--black-text);
      font-weight: 500;
    }
  .img img{
    width: 40%;
  }
    </style>
</head>
<body>

  <div class="container">
    <div class="col-12">
      <div class="row ">
      <div class="col-12 d-flex justify-content-center align-items-center my-5">
            <div class="card p-4">
              <div class="card-body">
                <div class="img d-flex justify-content-center align-items-center">
                  <img src="<?= base_url('assets/img/UNPAM_logo1.png');?>" class="" alt="">
                </div>
                <div class="card-title text-center mb-3">
                  <span><h1>Tugas Online</h1></span>
                </div>
                <div class="line my-4">

                </div>
                <form class="form " action="cek_login.php" method="POST"> 
                  <div class="form-floating mb-3">
                    <input class="form-control custom-input" type="text" name="username" id="username" placeholder="Masukan Username..." required>
                    <label for="" class="form-label">Username</label>
                  </div>
                  <div class="form-floating mb-3">
                    <input class="form-control custom-input" type="password" name="password" id="password" placeholder="Masukan Password..." required>
                    <label for="" class="form-label">Password</label>
                  </div>
                  <div class="form-floating">
                  <button class="btn btn-login px-4 py-2" type="submit">Login</button>
                  </div>
                   
                </form>
                <div class="back d-inline-flex mt-3 p-1 ">
                <a class="text-decoration-none text d-flex justify-content-center align-items-center" href="<?= base_url('index.php'); ?>">
                <div class="icon-box d-flex justify-content-center align-items-center me-2">
                <ion-icon name="arrow-back-outline" class="icon"></ion-icon>
                </div>
                      Previous
                </a>
                </div>
               

              </div>
            </div>
        </div>
      </div>
    </div>
  </div>

</body>
<script src="bootstrap/js/bootstrap.bundle.min.js"></script>
</html>