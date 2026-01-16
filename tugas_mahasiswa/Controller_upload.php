<?php
session_start();
include "../koneksi.php";
include "../template.php";

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'mahasiswa') {
    header("Location: " . base_url('login.php'));
    exit;
}

$id_mahasiswa = $_SESSION['id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_tugas = mysqli_real_escape_string($koneksi, $_POST['id_tugas']);
    $pengumpulan_id = isset($_POST['pengumpulan_id']) ? mysqli_real_escape_string($koneksi, $_POST['pengumpulan_id']) : null;

    $target_dir = "../tugas/jawaban/";
    $file_jawaban = null;
    $upload_ok = 1;
    $file_size_limit = 10 * 1024 * 1024; 

    if (isset($_FILES["file_jawaban"]) && $_FILES["file_jawaban"]["error"] == 0) {
        $original_filename = basename($_FILES["file_jawaban"]["name"]);
        $file_extension = strtolower(pathinfo($original_filename, PATHINFO_EXTENSION));
        $new_filename = uniqid('jawaban_') . '.' . $file_extension;
        $target_file = $target_dir . $new_filename;

        if ($_FILES["file_jawaban"]["size"] > $file_size_limit) {
            echo "<script>alert('Ukuran file terlalu besar. Maksimal 10MB.'); window.location.href='" . base_url('tugas_mahasiswa/upload_tugas.php?id_tugas=' . $id_tugas) . "';</script>";
            $upload_ok = 0;
        }

    
        $allowed_types = ['pdf', 'doc', 'docx', 'zip'];
        if (!in_array($file_extension, $allowed_types)) {
            echo "<script>alert('Hanya file PDF, DOC, DOCX, dan ZIP yang diizinkan.'); window.location.href='" . base_url('tugas_mahasiswa/upload_tugas.php?id_tugas=' . $id_tugas) . "';</script>";
            $upload_ok = 0;
        }

        if ($upload_ok == 1) {
           
            if ($pengumpulan_id) {
                $query_old_file = mysqli_query($koneksi, "SELECT file_jawaban FROM pengumpulan WHERE id = '$pengumpulan_id'");
                $old_file_data = mysqli_fetch_assoc($query_old_file);
                if ($old_file_data && !empty($old_file_data['file_jawaban'])) {
                    $old_file_path = $target_dir . $old_file_data['file_jawaban'];
                    if (file_exists($old_file_path)) {
                        unlink($old_file_path);
                    }
                }
            }

            if (move_uploaded_file($_FILES["file_jawaban"]["tmp_name"], $target_file)) {
                $file_jawaban = $new_filename;
            } else {
                echo "<script>alert('Gagal mengunggah file. Silakan coba lagi.'); window.location.href='" . base_url('tugas_mahasiswa/upload_tugas.php?id_tugas=' . $id_tugas) . "';</script>";
                exit;
            }
        } else {
            exit; 
        }
    } else if (!$pengumpulan_id) { 
        echo "<script>alert('Silakan pilih file jawaban untuk diunggah.'); window.location.href='" . base_url('tugas_mahasiswa/upload_tugas.php?id_tugas=' . $id_tugas) . "';</script>";
        exit;
    }

    $tanggal_kumpul = date('Y-m-d H:i:s');

    if ($pengumpulan_id) {
    
        if ($file_jawaban) {
            $update_query = "UPDATE pengumpulan SET file_jawaban = ?, tanggal_kumpul = ? WHERE id = ? AND tugas_id = ? AND mahasiswa_id = ?";
            if ($stmt = mysqli_prepare($koneksi, $update_query)) {
                mysqli_stmt_bind_param($stmt, "sssii", $file_jawaban, $tanggal_kumpul, $pengumpulan_id, $id_tugas, $id_mahasiswa);
                if (mysqli_stmt_execute($stmt)) {
                    echo "<script>alert('Jawaban berhasil diperbarui!'); window.location.href='" . base_url('tugas_mahasiswa/lihat_tugas.php') . "';</script>";
                } else {
                    echo "<script>alert('Gagal memperbarui jawaban: " . mysqli_error($koneksi) . "'); window.location.href='" . base_url('tugas_mahasiswa/upload_tugas.php?id_tugas=' . $id_tugas) . "';</script>";
                }
                mysqli_stmt_close($stmt);
            } else {
                echo "<script>alert('Gagal menyiapkan statement: " . mysqli_error($koneksi) . "'); window.location.href='" . base_url('tugas_mahasiswa/upload_tugas.php?id_tugas=' . $id_tugas) . "';</script>";
            }
        } else {
     
            $update_query = "UPDATE pengumpulan SET tanggal_kumpul = ? WHERE id = ? AND tugas_id = ? AND mahasiswa_id = ?";
            if ($stmt = mysqli_prepare($koneksi, $update_query)) {
                mysqli_stmt_bind_param($stmt, "sii", $tanggal_kumpul, $pengumpulan_id, $id_tugas, $id_mahasiswa);
                if (mysqli_stmt_execute($stmt)) {
                    echo "<script>alert('Tanggal pengumpulan berhasil diperbarui!'); window.location.href='" . base_url('tugas_mahasiswa/lihat_tugas.php') . "';</script>";
                } else {
                    echo "<script>alert('Gagal memperbarui tanggal pengumpulan: " . mysqli_error($koneksi) . "'); window.location.href='" . base_url('tugas_mahasiswa/upload_tugas.php?id_tugas=' . $id_tugas) . "';</script>";
                }
                mysqli_stmt_close($stmt);
            } else {
                echo "<script>alert('Gagal menyiapkan statement: " . mysqli_error($koneksi) . "'); window.location.href='" . base_url('tugas_mahasiswa/upload_tugas.php?id_tugas=' . $id_tugas) . "';</script>";
            }
        }
    } else {
     
        if ($file_jawaban) {
            $insert_query = "INSERT INTO pengumpulan (tugas_id, mahasiswa_id, file_jawaban, tanggal_kumpul) VALUES (?, ?, ?, ?)";
            if ($stmt = mysqli_prepare($koneksi, $insert_query)) {
                mysqli_stmt_bind_param($stmt, "iiss", $id_tugas, $id_mahasiswa, $file_jawaban, $tanggal_kumpul);
                if (mysqli_stmt_execute($stmt)) {
                    echo "<script>alert('Jawaban berhasil diunggah!'); window.location.href='" . base_url('tugas_mahasiswa/lihat_tugas.php') . "';</script>";
                } else {
                    echo "<script>alert('Gagal mengunggah jawaban: " . mysqli_error($koneksi) . "'); window.location.href='" . base_url('tugas_mahasiswa/upload_tugas.php?id_tugas=' . $id_tugas) . "';</script>";
                }
                mysqli_stmt_close($stmt);
            } else {
                echo "<script>alert('Gagal menyiapkan statement: " . mysqli_error($koneksi) . "'); window.location.href='" . base_url('tugas_mahasiswa/upload_tugas.php?id_tugas=' . $id_tugas) . "';</script>";
            }
        } else {
            echo "<script>alert('Tidak ada file yang diunggah untuk pengumpulan baru.'); window.location.href='" . base_url('tugas_mahasiswa/upload_tugas.php?id_tugas=' . $id_tugas) . "';</script>";
        }
    }
} else {
    header("Location: " . base_url('mahasiswa/lihat_tugas.php'));
    exit;
}

mysqli_close($koneksi);
?>