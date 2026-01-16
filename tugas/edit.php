<?php
session_start();
include "../koneksi.php";
include "../template.php";

if (!isset($_SESSION['role']) || ($_SESSION['role'] !== 'dosen' && $_SESSION['role'] !== 'admin')) {
    header("Location: " . base_url('login.php'));
    exit;
}

$id_tugas = isset($_GET['id']) ? mysqli_real_escape_string($koneksi, $_GET['id']) : '';

if (empty($id_tugas)) {
    header("Location: " . base_url('tugas/index.php'));
    exit;
}

$peran = $_SESSION['role'];
$id_pengguna = $_SESSION['id'];
$nama_pengguna = $_SESSION['nama_lengkap'];

$pesan = '';
$data_tugas = null;

$queryTugasLama = null;
if ($peran === 'admin') {
    $queryTugasLama = mysqli_query($koneksi, "SELECT * FROM tugas WHERE id = '$id_tugas'");
} else if ($peran === 'dosen') {
    $queryTugasLama = mysqli_query($koneksi, "SELECT * FROM tugas WHERE id = '$id_tugas' AND dosen_id = '$id_pengguna'");
}

if ($queryTugasLama && mysqli_num_rows($queryTugasLama) > 0) {
    $data_tugas = mysqli_fetch_assoc($queryTugasLama);
} else {
    echo "<script>alert('Tugas tidak ditemukan atau Anda tidak memiliki izin untuk mengeditnya.'); window.location.href='" . base_url('tugas/list.php') . "';</script>";
    exit;
}

$queryMatkul = mysqli_query($koneksi, "SELECT id, nama_matkul FROM mata_kuliah ORDER BY nama_matkul ASC");
$opsi_mata_kuliah = [];
if ($queryMatkul) {
    while ($baris = mysqli_fetch_assoc($queryMatkul)) {
        $opsi_mata_kuliah[] = $baris;
    }
} else {
    $pesan = '<div class="alert alert-danger" role="alert">Gagal mengambil data mata kuliah: ' . mysqli_error($koneksi) . '</div>';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $judul = $_POST['judul'];
    $deskripsi = $_POST['deskripsi'];
    $batas_waktu = $_POST['deadline'];
    $id_matkul = $_POST['matkul_id'];
    $berkas_tugas_lama = $data_tugas['file_tugas'];
    $berkas_tugas_baru = $berkas_tugas_lama;

    $hapus_berkas_lama = isset($_POST['hapus_file_tugas']);

    if (empty($judul) || empty($deskripsi) || empty($batas_waktu) || empty($id_matkul)) {
        $pesan = '<div class="alert alert-danger" role="alert">Semua kolom (Judul, Deskripsi, Batas Waktu, Mata Kuliah) harus diisi!</div>';
    } else {
        if ($hapus_berkas_lama && !empty($berkas_tugas_lama)) {
            $path_file_lama = '../tugas/file_tugas/' . $berkas_tugas_lama;
            if (file_exists($path_file_lama)) {
                unlink($path_file_lama);
            }
            $berkas_tugas_baru = null;
        }

        if (isset($_FILES['file_tugas']) && $_FILES['file_tugas']['error'] === UPLOAD_ERR_OK) {
            $nama_berkas = $_FILES['file_tugas']['name'];
            $nama_sementara_berkas = $_FILES['file_tugas']['tmp_name'];
            $ukuran_berkas = $_FILES['file_tugas']['size'];
            $ekstensi_berkas = strtolower(pathinfo($nama_berkas, PATHINFO_EXTENSION));

            $ekstensi_diizinkan = ['pdf', 'doc', 'docx', 'ppt', 'pptx', 'xls', 'xlsx', 'jpg', 'jpeg', 'png', 'gif'];
            $ukuran_maksimal_berkas = 5 * 1024 * 1024;

            if (!in_array($ekstensi_berkas, $ekstensi_diizinkan)) {
                $pesan = '<div class="alert alert-danger" role="alert">Format berkas tidak diizinkan. Hanya PDF, DOC, PPT, XLS, JPG, PNG, GIF yang diizinkan.</div>';
            } elseif ($ukuran_berkas > $ukuran_maksimal_berkas) {
                $pesan = '<div class="alert alert-danger" role="alert">Ukuran berkas terlalu besar. Maksimal 5 MB.</div>';
            } else {
                $direktori_unggah = '../tugas/file_tugas/';
                if (!is_dir($direktori_unggah)) {
                    if (!mkdir($direktori_unggah, 0777, true)) {
                        $pesan = '<div class="alert alert-danger" role="alert">Gagal membuat direktori unggah.</div>';
                    }
                }
                
                if (empty($pesan)) {
                    if (!empty($berkas_tugas_lama) && !$hapus_berkas_lama) {
                         $path_file_lama = '../tugas/file_tugas/' . $berkas_tugas_lama;
                         if (file_exists($path_file_lama)) {
                             unlink($path_file_lama);
                         }
                    }
                    $nama_berkas_baru = uniqid('tugas_', true) . '.' . $ekstensi_berkas;
                    $jalur_berkas = $direktori_unggah . $nama_berkas_baru;

                    if (move_uploaded_file($nama_sementara_berkas, $jalur_berkas)) {
                        $berkas_tugas_baru = $nama_berkas_baru;
                    } else {
                        $pesan = '<div class="alert alert-danger" role="alert">Gagal mengunggah berkas.</div>';
                    }
                }
            }
        }

        if (empty($pesan)) {
            $stmt = mysqli_prepare($koneksi, "UPDATE tugas SET judul = ?, deskripsi = ?, deadline = ?, file_tugas = ?, matkul_id = ? WHERE id = ? AND dosen_id = ?");

            if ($stmt === false) {
                $pesan = '<div class="alert alert-danger" role="alert">Error menyiapkan pernyataan: ' . mysqli_error($koneksi) . '</div>';
            } else {
                mysqli_stmt_bind_param($stmt, "ssssiii", $judul, $deskripsi, $batas_waktu, $berkas_tugas_baru, $id_matkul, $id_tugas, $id_pengguna);

                if (mysqli_stmt_execute($stmt)) {
                    $pesan = '<div class="alert alert-success" role="alert">Tugas berhasil diperbarui!</div>';
                    $data_tugas['judul'] = $judul;
                    $data_tugas['deskripsi'] = $deskripsi;
                    $data_tugas['deadline'] = $batas_waktu;
                    $data_tugas['matkul_id'] = $id_matkul;
                    $data_tugas['file_tugas'] = $berkas_tugas_baru;
                } else {
                    $pesan = '<div class="alert alert-danger" role="alert">Error: ' . mysqli_stmt_error($stmt) . '</div>';
                }
                mysqli_stmt_close($stmt);
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Edit Tugas</title>
    <link rel="stylesheet" href="<?= base_url('bootstrap/css/bootstrap.min.css') ?>">
    <style>
        :root {
            --primary-color: #6a5acd;
            --text-color: #ffffff;
            --secondary: #e0e0e0;
            --dark-purple-hover: #403385;
        }

        .header-section {
            background-color: var(--primary-color);
            color: var(--text-color);
            padding: 20px;
            margin-bottom: 30px;
            border-radius: 15px;
            text-align: center;
        }
        .header-section h2 {
            color: var(--text-color);
        }

        .form-container {
            background-color: #f8f9fa;
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .form-group label {
            font-weight: bold;
            color: #343a40;
            margin-bottom: 8px;
        }

        .form-control {
            border-radius: 8px;
            border: 1px solid #ced4da;
            padding: 10px 15px;
        }
        .form-control:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.25rem rgba(106, 90, 205, 0.25);
        }

        .action-btn {
            background-color: var(--primary-color) !important;
            color: var(--text-color) !important;
            font-weight: 900 !important;
            border: none;
            padding: 10px 25px;
            border-radius: 50px;
            transition: background-color 0.2s ease-in-out, color 0.2s ease-in-out;
        }
        .action-btn:hover {
            background-color: var(--dark-purple-hover) !important;
            color: white !important;
        }
        .back-btn {
            background-color: #6c757d !important;
            color: white !important;
            font-weight: 900 !important;
            border: none;
            padding: 10px 25px;
            border-radius: 50px;
            transition: background-color 0.2s ease-in-out;
        }
        .back-btn:hover {
            background-color: #5a6268 !important;
        }
    </style>
</head>
<body>
<?php include "../navbar.php"; ?>

<div class="container mt-4">
    <div class="header-section">
        <h2>Edit Tugas</h2>
        <p>Perbarui informasi tugas ini.</p>
    </div>

    <div class="form-container">
        <?= $pesan; ?>
        <form id="formEditTugas" action="" method="POST" enctype="multipart/form-data">
            <div class="mb-3">
                <label for="judul" class="form-label">Judul Tugas:</label>
                <input type="text" class="form-control" id="judul" name="judul" value="<?= htmlspecialchars($data_tugas['judul']); ?>" required>
            </div>
            <div class="mb-3">
                <label for="deskripsi" class="form-label">Deskripsi:</label>
                <textarea class="form-control" id="deskripsi" name="deskripsi" rows="5" required><?= htmlspecialchars($data_tugas['deskripsi']); ?></textarea>
            </div>
            <div class="mb-3">
                <label for="deadline" class="form-label">Batas Waktu:</label>
                <input type="datetime-local" class="form-control" id="deadline" name="deadline" value="<?= date('Y-m-d\TH:i', strtotime($data_tugas['deadline'])); ?>" required>
            </div>
            <div class="mb-3">
                <label for="matkul_id" class="form-label">Mata Kuliah:</label>
                <select class="form-control" id="matkul_id" name="matkul_id" required>
                    <option value="">Pilih Mata Kuliah</option>
                    <?php foreach ($opsi_mata_kuliah as $matkul): ?>
                        <option value="<?= htmlspecialchars($matkul['id']); ?>" <?= ($matkul['id'] == $data_tugas['matkul_id']) ? 'selected' : ''; ?>>
                            <?= htmlspecialchars($matkul['nama_matkul']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="mb-3">
                <label for="current_file" class="form-label">Berkas Tugas Saat Ini:</label>
                <?php if (!empty($data_tugas['file_tugas'])): ?>
                    <p>
                        <a href="<?= base_url('tugas/file_tugas/' . $data_tugas['file_tugas']) ?>" target="_blank">
                            <?= htmlspecialchars($data_tugas['file_tugas']); ?>
                        </a>
                        <br>
                        <div class="form-check mt-2">
                            <input class="form-check-input" type="checkbox" id="hapus_file_tugas" name="hapus_file_tugas" value="1">
                            <label class="form-check-label" for="hapus_file_tugas">
                                Hapus berkas ini
                            </label>
                        </div>
                    </p>
                <?php else: ?>
                    <p>Tidak ada berkas terlampir.</p>
                <?php endif; ?>
            </div>
            <div class="mb-3">
                <label for="file_tugas" class="form-label">Ganti Berkas Tugas (Opsional):</label>
                <input type="file" class="form-control" id="file_tugas" name="file_tugas">
                <small class="form-text text-muted">Maksimal 5MB (PDF, DOC, PPT, XLS, Gambar)</small>
            </div>
            <div class="d-flex justify-content-between align-items-center mt-4">
                <button type="button" class="btn action-btn" onclick="konfirmasiEditTugas()">Perbarui Tugas</button>
                <a href="<?= base_url('tugas/list.php') ?>" class="btn back-btn">Kembali ke Daftar Tugas</a>
            </div>
        </form>
    </div>
</div>

<script src="<?= base_url('bootstrap/js/bootstrap.bundle.min.js') ?>"></script>
<script>
    function konfirmasiEditTugas() {
        const judul = document.getElementById('judul').value;
        const deskripsi = document.getElementById('deskripsi').value;
        const batasWaktu = document.getElementById('deadline').value;
        const idMatkul = document.getElementById('matkul_id').value;
        const berkasTugasBaru = document.getElementById('file_tugas').files[0];
        const hapusFileLama = document.getElementById('hapus_file_tugas') ? document.getElementById('hapus_file_tugas').checked : false;

        if (judul === '' || deskripsi === '' || batasWaktu === '' || idMatkul === '') {
            alert('Judul, Deskripsi, Batas Waktu, dan Mata Kuliah harus diisi!');
            return;
        }

        if (berkasTugasBaru) {
            const ekstensiDiizinkan = ['pdf', 'doc', 'docx', 'ppt', 'pptx', 'xls', 'xlsx', 'jpg', 'jpeg', 'png', 'gif'];
            const ukuranMaksimalBerkas = 5 * 1024 * 1024;

            const namaBerkas = berkasTugasBaru.name;
            const ekstensiBerkas = namaBerkas.split('.').pop().toLowerCase();

            if (!ekstensiDiizinkan.includes(ekstensiBerkas)) {
                alert('Format berkas tidak diizinkan. Hanya PDF, DOC, PPT, XLS, JPG, PNG, GIF yang diizinkan.');
                return;
            }

            if (berkasTugasBaru.size > ukuranMaksimalBerkas) {
                alert('Ukuran berkas terlalu besar. Maksimal 5 MB.');
                return;
            }
        }

        const dikonfirmasi = confirm("Anda yakin ingin memperbarui tugas ini?");

        if (dikonfirmasi) {
            document.getElementById('formEditTugas').submit();
        } else {
            alert("Pembaharuan tugas dibatalkan.");
        }
    }
</script>
</body>
</html>