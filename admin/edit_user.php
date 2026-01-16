<?php
    session_start();
    include "../koneksi.php";
    include "../template.php";
    
    if (!isset($_SESSION['role'])) {
        header("Location: " . base_url('login.php'));
        exit;
    }
    $id = $_GET['id'];
    $role = $_SESSION['role'];
    $user = $_SESSION['nama_lengkap'];
    
    $users = mysqli_query($koneksi, "SELECT * FROM users WHERE id='".$id."'");
    if($data = mysqli_fetch_array($users)){
    
    
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add User</title>
    <style>
         .btn-tambah{
        background-color: var(--primary-color) !important;
        color: var(--text-color) !important;
        font-weight: 800;
        border-radius: 50px 50px 50px 50px;
       border: none;
    }
    .btn-tambah:hover{
        background-color: rgb(64, 51, 133) !important;
        color: var(--text-color) !important;
        font-weight: 800;
    }
         .btn-kembali{
             
             background-color: var(--primary-color) !important;
             color: var(--text-color) !important;
             font-weight: 800;
             border-radius: 50px 50px 50px 50px;
            
            }
            .btn-kembali:hover{
        
                background-color:rgb(88, 67, 193) !important;
        color: var(--text-color) !important;
        font-weight: 800;
        
    }
   
    </style>
</head>
<body>
    <?php
        include "../navbar.php";
    ?>

    <div class="container">
        <div class="col-12">
            <div class="row">
            <div class="col-lg-6 col-12 my-5">
                    <div class="col-lg-2 col-4 ">
                    <a href="<?= base_url('admin/kelola_user.php');?>" class="btn-kembali my-3 px-5 py-2 d-flex justify-content-center align-items-center gap-2 text-decoration-none p-3"><div class="icon-box d-flex justify-content-center align-items-center">
               
                </div>Kembali</a>
                    </div>
                    <form action="<?= base_url('admin/Controller_edit_user.php');?>" autocomplete="off" method="POST" onsubmit="return confirmSubmit();">
                        <div class="form-floating mb-3">
                            
                            <input type="hidden" class="form-control" name="id" id="id" required value="<?php echo $data['id'];?>" >
                            <input type="text" class="form-control" name="username" id="username" required value="<?php echo $data['username'];?>">
                            <label for="" class="form-label">Username</label>
                        </div>
                        <div class="form-floating mb-3">
                            
                            <input type="text" class="form-control" name="password" id="password" required value="<?php echo $data['password'];?>">
                            <label for="" class="form-label">Password</label>
                        </div>
                        <div class="form-floating mb-3">
                            
                            <select name="role" id="role" class="form-control form-select">
                                <option value=""  disabled <?php if(empty($data['role'])) echo 'selected'; ?>>--Pilih Role--</option>
                                <option value="admin" <?php if($data['role'] == 'admin') echo 'selected'; ?>>ADMIN</option>
                                <option value="dosen" <?php if($data['role'] == 'dosen') echo 'selected'; ?>>DOSEN</option>
                                <option value="mahasiswa" <?php if($data['role'] == 'mahasiswa') echo 'selected'; ?>>MAHASISWA</option>
                            </select>
                            <label for="" class="form-label ">Role</label>
                        </div>
                        <div class="form-floating mb-3">
                            
                            <input type="text" class="form-control" name="nama_lengkap" id="nama_lengkap" required value="<?php echo $data['nama_lengkap'];?>">
                            <label for="" class="form-label">Nama Lengkap</label>
                        </div>
                        <div class="form-floating mb-3">
                            
                            <input type="text" class="form-control" name="email" id="email" required value="<?php echo $data['email'];?>">
                            <label for="" class="form-label">Email</label>
                        </div>
                        <div class="form-floating">
                            <button type="submit"  class=" btn-tambah my-3 px-4 py-2 d-flex justify-content-center align-items-center">Edit</button>
                        </div>
                    </form>
                    
                </div>
                <div class="col-lg-6 col-12 my-5 d-flex justify-content-center align-items-center">

    <div class="text-center ">
        <img src="<?= base_url('assets/img/UNPAM_logo1.png');?>" alt="User Illustration" class="img-fluid " style="max-height: 320px;">
        <h5 class="mt-4 fw-bold" style="color: var(--primary-color); max-height: 320px; max-width: 100%;">Kelola Pengguna Lebih Mudah</h5>
        <p class="text-muted">Tambahkan admin, dosen, atau mahasiswa dengan cepat dan aman. Pastikan data valid sebelum menyimpan.</p>
    </div>
</div>

            </div>
        </div>
    </div>
    <?php
    }
    ?>
    <script>
document.addEventListener("DOMContentLoaded", function () {
    const roleSelect = document.getElementById("role");
    const usernameInput = document.getElementById("username");
    const passwordInput = document.getElementById("password");

    roleSelect.addEventListener("change", function () {
        const role = this.value;
        const randomNumber = Math.floor(Math.random() * 899999 + 100001); 

        if (role === "mahasiswa") {
            usernameInput.value = "251012" + randomNumber;
            passwordInput.value = "unpam#" + randomNumber;
        } else if (role === "dosen") {
            usernameInput.value = "201011" + randomNumber;
            passwordInput.value = "dosen#" + randomNumber;
        } else {
            usernameInput.value = "";
            passwordInput.value = "";
        }
    });
});

function confirmSubmit() {
    return confirm("Apakah data Anda sudah benar?");
}
</script>

</body>
</html>