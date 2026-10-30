<?php
require "koneksi.php";

if (!isset($_SESSION["nama"])) {
    header("Location: login.php");
    exit;
}

$id = $_GET["id"] ?? "";
if (!is_numeric($id)) {
    header("Location: index.php");
    exit;
}

$stmt = $pdo->prepare("SELECT * FROM barang WHERE id = :id");
$stmt->execute([":id" => $id]);
$brg = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$brg) {
    header("Location: index.php");
    exit;
}

$error = [];
$ar_kategori = ["Elektronik", "Furnitur", "ATK"];

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $kode     = htmlspecialchars(trim($_POST["kode_barang"] ?? ""));
    $nama_brg = htmlspecialchars(trim($_POST["nama_barang"] ?? ""));
    $kategori = htmlspecialchars(trim($_POST["kategori"] ?? ""));
    $harga    = trim($_POST["harga"] ?? "");
    $kondisi  = htmlspecialchars(trim($_POST["kondisi"] ?? ""));

    if (empty($kode) || empty($nama_brg) || !is_numeric($harga)) {
        $error[] = "Semua field wajib diisi dengan benar.";
    }

    if (empty($error)) {
        $stmt = $pdo->prepare("UPDATE barang SET kode_barang = :kode, nama_barang = :nama, kategori = :kategori, harga = :harga, kondisi = :kondisi WHERE id = :id");
        $stmt->execute([":kode" => $kode, ":nama" => $nama_brg, ":kategori" => $kategori, ":harga" => $harga, ":kondisi" => $kondisi, ":id" => $id]);
        header("Location: index.php");
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="<?= $lang ?>">

<head>
    <meta charset="UTF-8">
    <title><?= $t["ubah_data"] ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-slate-100 min-h-screen flex items-center justify-center p-4">
    <div class="bg-white p-6 rounded-xl shadow-md w-full max-w-md">
        <h2 class="text-lg font-bold text-slate-800 mb-4"><?= $t["ubah_data"] ?></h2>

        <?php if (!empty($error)) { ?>
            <div class="bg-red-100 text-red-700 p-3 rounded-lg mb-4 text-sm">
                <?php foreach ($error as $e) {
                    echo "- " . $e . "<br>";
                } ?>
            </div>
        <?php } ?>

        <form method="POST" action="edit.php?id=<?= $id ?>" class="space-y-3 text-sm">
            <div>
                <label class="block font-medium text-slate-700"><?= $t["kode"] ?></label>
                <input type="text" name="kode_barang" value="<?= htmlspecialchars($brg['kode_barang']) ?>" required class="w-full mt-1 p-2 border rounded-lg">
            </div>
            <div>
                <label class="block font-medium text-slate-700"><?= $t["nama_brg"] ?></label>
                <input type="text" name="nama_barang" value="<?= htmlspecialchars($brg['nama_barang']) ?>" required class="w-full mt-1 p-2 border rounded-lg">
            </div>
            <div>
                <label class="block font-medium text-slate-700"><?= $t["kategori"] ?></label>
                <select name="kategori" class="w-full mt-1 p-2 border rounded-lg">
                    <?php foreach ($ar_kategori as $kat) { ?>
                        <option value="<?= $kat ?>" <?= $brg["kategori"] == $kat ? "selected" : "" ?>><?= $kat ?></option>
                    <?php } ?>
                </select>
            </div>
            <div>
                <label class="block font-medium text-slate-700"><?= $t["harga"] ?></label>
                <input type="text" name="harga" value="<?= htmlspecialchars($brg['harga']) ?>" required class="w-full mt-1 p-2 border rounded-lg">
            </div>
            <div>
                <label class="block font-medium text-slate-700"><?= $t["kondisi"] ?></label>
                <select name="kondisi" class="w-full mt-1 p-2 border rounded-lg">
                    <option value="Baik" <?= $brg["kondisi"] == "Baik" ? "selected" : "" ?>>Baik</option>
                    <option value="Perbaikan" <?= $brg["kondisi"] == "Perbaikan" ? "selected" : "" ?>>Perbaikan</option>
                    <option value="Rusak" <?= $brg["kondisi"] == "Rusak" ? "selected" : "" ?>>Rusak</option>
                </select>
            </div>
            <div class="flex space-x-2 pt-2">
                <button type="submit" class="flex-1 bg-blue-600 text-white py-2 rounded-lg hover:bg-blue-700 font-medium"><?= $t["update"] ?></button>
                <a href="index.php" class="flex-1 bg-slate-200 text-slate-700 py-2 rounded-lg text-center hover:bg-slate-300 font-medium"><?= $t["batal"] ?></a>
            </div>
        </form>
    </div>
</body>

</html>