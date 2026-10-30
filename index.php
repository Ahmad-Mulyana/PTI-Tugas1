<?php
require "koneksi.php";

if (!isset($_SESSION["nama"])) {
    header("Location: login.php");
    exit;
}

$error = [];
$ar_kategori = ["Elektronik", "Furnitur", "ATK"];

// OPERASI DELETE
if (isset($_GET["hapus"])) {
    $id_hapus = $_GET["hapus"];
    if (is_numeric($id_hapus)) {
        $stmt = $pdo->prepare("DELETE FROM barang WHERE id = :id");
        $stmt->execute([":id" => $id_hapus]);
        header("Location: index.php");
        exit;
    }
}

// OPERASI CREATE (Validasi & Sanitasi)
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $kode     = htmlspecialchars(trim($_POST["kode_barang"] ?? ""));
    $nama_brg = htmlspecialchars(trim($_POST["nama_barang"] ?? ""));
    $kategori = htmlspecialchars(trim($_POST["kategori"] ?? ""));
    $harga    = trim($_POST["harga"] ?? "");
    $kondisi  = htmlspecialchars(trim($_POST["kondisi"] ?? ""));

    if (empty($kode)) {
        $error[] = "Kode barang wajib diisi.";
    }
    if (empty($nama_brg)) {
        $error[] = "Nama barang wajib diisi.";
    }
    if (!in_array($kategori, $ar_kategori)) {
        $error[] = "Kategori tidak valid.";
    }
    if (!is_numeric($harga) || $harga < 0) {
        $error[] = "Harga harus berupa angka.";
    }

    // Validasi switch kondisi barang
    switch ($kondisi) {
        case "Baik":
        case "Perbaikan":
        case "Rusak":
            break;
        default:
            $error[] = "Kondisi tidak valid.";
            break;
    }

    if (empty($error)) {
        try {
            $stmt = $pdo->prepare("INSERT INTO barang (kode_barang, nama_barang, kategori, harga, kondisi) VALUES (:kode, :nama, :kategori, :harga, :kondisi)");
            $stmt->execute([":kode" => $kode, ":nama" => $nama_brg, ":kategori" => $kategori, ":harga" => $harga, ":kondisi" => $kondisi]);
            header("Location: index.php");
            exit;
        } catch (PDOException $e) {
            $error[] = "Gagal menyimpan data (Kode duplikat): " . $e->getMessage();
        }
    }
}

// OPERASI READ (fetchAll)
$stmt = $pdo->prepare("SELECT * FROM barang ORDER BY id DESC");
$stmt->execute();
$ar_barang = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="<?= $lang ?>">

<head>
    <meta charset="UTF-8">
    <title><?= $t["judul_app"] ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-slate-50 min-h-screen">
    <nav class="bg-white shadow-sm px-6 py-4 flex justify-between items-center">
        <h1 class="text-lg font-bold text-slate-800"><?= $t["judul_app"] ?></h1>
        <div class="flex items-center space-x-4 text-sm">
            <span class="text-slate-600"><?= $t["selamat"] ?>, <b><?= htmlspecialchars($_SESSION["nama"]) ?></b></span>
            <div class="border-l pl-4 space-x-1">
                <a href="?lang=id" class="<?= $lang == 'id' ? 'font-bold text-blue-600' : 'text-slate-500' ?>">ID</a> |
                <a href="?lang=en" class="<?= $lang == 'en' ? 'font-bold text-blue-600' : 'text-slate-500' ?>">EN</a>
            </div>
            <a href="logout.php" class="bg-red-500 text-white px-3 py-1.5 rounded-lg hover:bg-red-600"><?= $t["logout"] ?></a>
        </div>
    </nav>

    <div class="max-w-7xl mx-auto p-6 grid grid-cols-1 md:grid-cols-3 gap-6">
        <!-- Form Create -->
        <div class="bg-white p-6 rounded-xl shadow-sm h-fit">
            <h2 class="text-md font-bold text-slate-800 mb-4"><?= $t["tambah_data"] ?></h2>

            <?php if (!empty($error)) { ?>
                <div class="bg-red-100 text-red-700 p-3 rounded-lg mb-4 text-sm">
                    <?php foreach ($error as $e) {
                        echo "- " . $e . "<br>";
                    } ?>
                </div>
            <?php } ?>

            <form method="POST" action="<?php echo $_SERVER['PHP_SELF']; ?>" class="space-y-3 text-sm">
                <div>
                    <label class="block font-medium text-slate-700"><?= $t["kode"] ?></label>
                    <input type="text" name="kode_barang" placeholder="BRG-001" required class="w-full mt-1 p-2 border rounded-lg">
                </div>
                <div>
                    <label class="block font-medium text-slate-700"><?= $t["nama_brg"] ?></label>
                    <input type="text" name="nama_barang" placeholder="Laptop Asus" required class="w-full mt-1 p-2 border rounded-lg">
                </div>
                <div>
                    <label class="block font-medium text-slate-700"><?= $t["kategori"] ?></label>
                    <select name="kategori" class="w-full mt-1 p-2 border rounded-lg">
                        <?php foreach ($ar_kategori as $kat) { ?>
                            <option value="<?= $kat ?>"><?= $kat ?></option>
                        <?php } ?>
                    </select>
                </div>
                <div>
                    <label class="block font-medium text-slate-700"><?= $t["harga"] ?></label>
                    <input type="text" name="harga" placeholder="7500000" required class="w-full mt-1 p-2 border rounded-lg">
                </div>
                <div>
                    <label class="block font-medium text-slate-700"><?= $t["kondisi"] ?></label>
                    <select name="kondisi" class="w-full mt-1 p-2 border rounded-lg">
                        <option value="Baik">Baik</option>
                        <option value="Perbaikan">Perbaikan</option>
                        <option value="Rusak">Rusak</option>
                    </select>
                </div>
                <button type="submit" class="w-full bg-blue-600 text-white py-2 rounded-lg hover:bg-blue-700 font-medium">
                    <?= $t["simpan"] ?>
                </button>
            </form>
        </div>

        <!-- Tabel Read -->
        <div class="md:col-span-2 bg-white p-6 rounded-xl shadow-sm overflow-x-auto">
            <table class="w-full text-left border-collapse text-sm">
                <thead>
                    <tr class="border-b bg-slate-50 text-slate-700">
                        <th class="p-3"><?= $t["kode"] ?></th>
                        <th class="p-3"><?= $t["nama_brg"] ?></th>
                        <th class="p-3"><?= $t["kategori"] ?></th>
                        <th class="p-3"><?= $t["harga"] ?></th>
                        <th class="p-3"><?= $t["kondisi"] ?></th>
                        <th class="p-3"><?= $t["aksi"] ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (count($ar_barang) === 0) { ?>
                        <tr>
                            <td colspan="6" class="p-4 text-center text-slate-500"><?= $t["data_kosong"] ?></td>
                        </tr>
                    <?php } else { ?>
                        <?php foreach ($ar_barang as $brg) { ?>
                            <tr class="border-b hover:bg-slate-50">
                                <td class="p-3 font-mono text-xs"><?= htmlspecialchars($brg["kode_barang"]) ?></td>
                                <td class="p-3 font-medium"><?= htmlspecialchars($brg["nama_barang"]) ?></td>
                                <td class="p-3"><?= htmlspecialchars($brg["kategori"]) ?></td>
                                <td class="p-3">Rp <?= number_format($brg["harga"], 0, ',', '.') ?></td>
                                <td class="p-3">
                                    <span class="px-2 py-1 rounded text-xs bg-blue-50 text-blue-700 font-medium">
                                        <?= htmlspecialchars($brg["kondisi"]) ?>
                                    </span>
                                </td>
                                <td class="p-3 space-x-2">
                                    <a href="edit.php?id=<?= $brg['id'] ?>" class="text-amber-600 hover:underline font-medium"><?= $t["ubah"] ?></a>
                                    <a href="index.php?hapus=<?= $brg['id'] ?>" onclick="return confirm('Hapus data ini?')" class="text-red-600 hover:underline font-medium"><?= $t["hapus"] ?></a>
                                </td>
                            </tr>
                        <?php } ?>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
</body>

</html>