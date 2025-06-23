<?php
 include "template.php";
 $role = isset($_SESSION['role']) ? $_SESSION['role'] : null;
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Document</title>
  <link href="<?= base_url('bootstrap/css/bootstrap-grid.min.css');?>" rel="stylesheet">
  <style>
    .navbar-brand{
      font-weight: 700;
      color: var(--primary-color);
    }
    .navbar-brand:hover{
      color:var(--secondary);
    }
    .btn-login{
      border: 2px solid var(--primary-color);
      color: var(--primary-color);
      font-weight: 700;
    }
    .btn-login:hover{
      background-color:  var(--primary-color);
      color: #f7f6fc;
      font-weight: 700;
    }
    .navbar{
 
      background-color: #f7f6fc;
      border-radius: 0px 0px 0px 0px;
    }
  </style>
</head>
<body>

<nav class="navbar navbar-expand-lg py-2 sticky-top">
  <div class="container">
    <a class="navbar-brand" href="#">
      <img src="<?= base_url('assets/img/UNPAM_logo1.png');?>" alt="Logo" width="52" height="40">
      Universitas Pamulang
    </a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarContent">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarContent">
      <ul class="navbar-nav ms-auto mb-2 mb-lg-0">
        <?php if ($role === 'admin'): ?>
          <li class="nav-item">
            <a class="nav-link" href="<?= base_url('dashboard/dashboard.php');?>">Dashboard Admin</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="<?= base_url('admin/kelola_user.php');?>">Kelola Pengguna</a>
          </li>
        <?php elseif ($role === 'dosen'): ?>
          <li class="nav-item">
            <a class="nav-link" href="dosen/tugas.php">Tugas Saya</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="dosen/input_nilai.php">Input Nilai</a>
          </li>
        <?php elseif ($role === 'mahasiswa'): ?>
          <li class="nav-item">
            <a class="nav-link" href="mahasiswa/tugas.php">Tugas</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="<?= base_url('mahasiswa/nilai.php');?>">Nilai</a>
          </li>
        <?php endif; ?>
      </ul>
      <?php if ($role): ?>
        <a class="btn btn-login ms-lg-3 mt-2 mt-lg-0 px-4" href="<?= base_url('logout.php');?>">Logout</a>
      <?php else: ?>
        <a class="btn btn-login ms-lg-3 mt-2 mt-lg-0 px-4" href="<?= base_url('login.php');?>">Login</a>
      <?php endif; ?>
    </div>
  </div>
</nav>
<script src="bootstrap/js/bootstrap.bundle.min.js"></script>
</body>
</html>
