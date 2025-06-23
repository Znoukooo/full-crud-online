<?php 
session_start();
include "../koneksi.php";
include "../template.php";

if (!isset($_SESSION['role'])) {
    header("Location: " . base_url('login.php'));
    exit;
}

$role = $_SESSION['role'];
$user = $_SESSION['nama_lengkap'];

// Ambil data pengguna dari database
$query = mysqli_query($koneksi, "SELECT id, username, role, nama_lengkap, email FROM users ORDER BY nama_lengkap, role ASC");
$pengguna = mysqli_fetch_all($query, MYSQLI_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Kelola User - Admin</title>
  <link rel="stylesheet" href="<?= base_url('bootstrap/css/bootstrap.min.css') ?>">
  <style>
    .btn-tambah{
        background-color: var(--primary-color) !important;
        color: var(--text-color) !important;
        font-weight: 800;
       border-radius: 50px 50px 50px 50px !important;

    }
    .btn-tambah:hover{
        background-color: rgb(64, 51, 133) !important;
        color: var(--text-color) !important;
        font-weight: 800;
        border-radius: 50px 50px 50px 50px !important;
    }
    .title{
        color: var(--black-text);
    }
    .line{
        width: 100%;
        height: 2px;
        background-color: var(--primary-color) !important;
    }
    .form-control:hover,
    .form-control:focus,
    .form-select:hover,
    .form-select:focus{
      box-shadow: 0 0 10px var(--primary-color) !important;
      transition: 0.5s;
     
    }
    .form-select option:checked{
        background-color: var(--secondary) !important;
        color: var(--black-text) !important;
    }
    .table thead tr th{
      background-color: var(--primary-color) !important;
      color: var(--text-color);
    }
    .aksi{
        color: var(--text-color) !important;
      
    }
  </style>
</head>
<body>
<?php include "../navbar.php"; ?>

<div class="container mt-4">
  <h2 class="mb-3 title">Kelola Pengguna</h2>
  <div class="col-lg-5 col-8 line my-3"></div>
  <div class="button col-5 col-lg-2">
    <a href="<?= base_url('admin/add_user.php');?>" class="btn btn-tambah my-3 p-2 py-2 px-2 d-flex justify-content-center align-items-center gap-2">
  <ion-icon name="add-outline" size="small"></ion-icon>
  <span class="fw-bold">Tambah Pengguna</span>
</a>

  </div>
  <div class="row mb-3">
  
    <div class="col-4">
      <input type="text" id="searchName" class="form-control custom-input" placeholder="Cari berdasarkan Nama">
    </div>
    <div class="col-4">
      <input type="text" id="searchUsername" class="form-control custom-input" placeholder="Cari berdasarkan Username">
    </div>
    <div class="col-4">
      <select class="form-select custom-input" id="filterRole">
        <option value="">Semua Role</option>
        <option value="admin">Admin</option>
        <option value="dosen">Dosen</option>
        <option value="mahasiswa">Mahasiswa</option>
      </select>
    </div>
  </div>

  <table class="table table-bordered" id="userTable">
    <thead class="text-center">
      <tr>
        <th>No</th>
        <th>Nama Lengkap</th>
        <th>Username</th>
        <th>Role</th>
        <th>Aksi</th>
      </tr>
    </thead>
    <tbody>
      <?php $no = 1; foreach ($pengguna as $row): ?>
      <tr>
        <td><?= $no++ ?></td>
        <td class="nama"><?= htmlspecialchars($row['nama_lengkap']) ?></td>
        <td class="username"><?= htmlspecialchars($row['username']) ?></td>
        <td class="role"><?= htmlspecialchars($row['role']) ?></td>
        <td>
          <div class="d-flex gap-2 justify-content-center">
          <a href="<?= base_url('admin/edit_user.php?id=' . $row['id']); ?>" class="btn btn-warning aksi">Edit</a>
          <a href="<?= base_url('admin/delete_user.php?id=' . $row['id']);?>" class="btn btn-danger aksi" onclick="return confirmSubmit();">Delete</a>
          </div>
      </td>
      </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</div>

<script>
  const searchName = document.getElementById("searchName");
  const searchUsername = document.getElementById("searchUsername");
  const filterRole = document.getElementById("filterRole");

  searchName.addEventListener("keyup", filterTable);
  searchUsername.addEventListener("keyup", filterTable);
  filterRole.addEventListener("change", filterTable);

  function filterTable() {
    const nameVal = searchName.value.toLowerCase();
    const usernameVal = searchUsername.value.toLowerCase();
    const roleVal = filterRole.value;

    const rows = document.querySelectorAll("#userTable tbody tr");

    rows.forEach(row => {
      const nama = row.querySelector(".nama").textContent.toLowerCase();
      const username = row.querySelector(".username").textContent.toLowerCase();
      const role = row.querySelector(".role").textContent.toLowerCase();

      const matchName = nama.includes(nameVal);
      const matchUsername = username.includes(usernameVal);
      const matchRole = roleVal === "" || role === roleVal;

      if (matchName && matchUsername && matchRole) {
        row.style.display = "";
      } else {
        row.style.display = "none";
      }
    });
  }

  function confirmSubmit() {
        return confirm("Apakah datanya ingin si hapus?");
    }
</script>

<script src="<?= base_url('bootstrap/js/bootstrap.bundle.min.js') ?>"></script>
</body>
</html>
