<?php
// Memulai session di awal script sebelum ada output apapun ke browser
session_start();

// 1. Konfigurasi Koneksi Database Menggunakan PDO
$host     = "localhost";
$nama_db  = "db_inventaris";
$username = "root";
$password = "";

$dsn = "mysql:host=" . $host . ";dbname=" . $nama_db . ";charset=utf8";

try {
    $pdo = new PDO($dsn, $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Koneksi gagal: " . $e->getMessage()); // Menghentikan eksekusi jika koneksi gagal
}

// 2. Pengaturan Session Dual Bahasa (Struktur Kontrol Switch)
if (isset($_GET["lang"])) {
    switch ($_GET["lang"]) {
        case "en":
            $_SESSION["lang"] = "en";
            break;
        case "id":
        default:
            $_SESSION["lang"] = "id";
            break;
    }
    // Menyimpan preferensi bahasa ke Cookie (materi Cookies)
    $expire = time() + (7 * 24 * 60 * 60);
    setcookie("pref_lang", $_SESSION["lang"], $expire, "/");

    header("Location: " . strtok($_SERVER["REQUEST_URI"], '?'));
    exit;
}

// Cek session bahasa atau cookie
if (isset($_SESSION["lang"])) {
    $lang = $_SESSION["lang"];
} elseif (isset($_COOKIE["pref_lang"])) {
    $lang = $_COOKIE["pref_lang"];
    $_SESSION["lang"] = $lang;
} else {
    $lang = "id";
}

// 3. Array Multidimensi untuk Kamus Bahasa (ID / EN)
$ar_kamus = [
    "id" => [
        "judul_app"    => "Sistem Manajemen Inventaris Barang",
        "login"        => "Masuk Aplikasi",
        "register"     => "Daftar Akun Baru",
        "logout"       => "Keluar",
        "selamat"      => "Selamat datang",
        "nama"         => "Nama Lengkap",
        "email"        => "Alamat Email",
        "password"     => "Kata Sandi",
        "tambah_data"  => "Tambah Data Barang",
        "ubah_data"    => "Ubah Data Barang",
        "kode"         => "Kode Barang",
        "nama_brg"     => "Nama Barang",
        "kategori"     => "Kategori",
        "harga"        => "Harga Satuan (Rp)",
        "kondisi"      => "Kondisi",
        "aksi"         => "Aksi",
        "simpan"       => "Simpan Data",
        "update"       => "Perbarui Data",
        "batal"        => "Batal",
        "ubah"         => "Ubah",
        "hapus"        => "Hapus",
        "data_kosong"  => "Belum ada data tersimpan.",
        "ke_login"     => "Sudah punya akun? Masuk di sini",
        "ke_register"  => "Belum punya akun? Daftar di sini"
    ],
    "en" => [
        "judul_app"    => "Item Inventory Management System",
        "login"        => "Application Login",
        "register"     => "Register New Account",
        "logout"       => "Logout",
        "selamat"      => "Welcome",
        "nama"         => "Full Name",
        "email"        => "Email Address",
        "password"     => "Password",
        "tambah_data"  => "Add New Item",
        "ubah_data"    => "Edit Item Data",
        "kode"         => "Item Code",
        "nama_brg"     => "Item Name",
        "kategori"     => "Category",
        "harga"        => "Unit Price (IDR)",
        "kondisi"      => "Condition",
        "aksi"         => "Actions",
        "simpan"       => "Save Item",
        "update"       => "Update Item",
        "batal"        => "Cancel",
        "ubah"         => "Edit",
        "hapus"        => "Delete",
        "data_kosong"  => "No records found.",
        "ke_login"     => "Already have an account? Login here",
        "ke_register"  => "Don't have an account? Register here"
    ]
];

$t = $ar_kamus[$lang];
