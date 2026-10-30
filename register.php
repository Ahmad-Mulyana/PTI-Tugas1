<?php
require "koneksi.php";

if (isset($_SESSION["nama"])) {
    header("Location: index.php");
    exit;
}

$error = [];

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $nama     = htmlspecialchars(trim($_POST["nama"] ?? ""));
    $email    = filter_var(trim($_POST["email"] ?? ""), FILTER_SANITIZE_EMAIL);
    $password = $_POST["password"] ?? "";

    if (empty($nama)) {
        $error[] = ($lang == "id") ? "Nama wajib diisi." : "Name is required.";
    }
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error[] = ($lang == "id") ? "Format email tidak valid." : "Invalid email format.";
    }
    if (empty($password)) {
        $error[] = ($lang == "id") ? "Password wajib diisi." : "Password is required.";
    }

    if (empty($error)) {
        try {
            $hash_pass = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("INSERT INTO users (nama, email, password) VALUES (:nama, :email, :password)");
            $stmt->execute([":nama" => $nama, ":email" => $email, ":password" => $hash_pass]);
            header("Location: login.php");
            exit;
        } catch (PDOException $e) {
            $error[] = ($lang == "id") ? "Email sudah terdaftar!" : "Email is already registered!";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="<?= $lang ?>">

<head>
    <meta charset="UTF-8">
    <title><?= $t["register"] ?> - <?= $t["judul_app"] ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-slate-100 min-h-screen flex items-center justify-center p-4">
    <div class="bg-white p-8 rounded-xl shadow-md w-full max-w-md">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-xl font-bold text-slate-800"><?= $t["register"] ?></h1>
            <div class="text-sm space-x-1">
                <a href="?lang=id" class="<?= $lang == 'id' ? 'font-bold text-blue-600' : 'text-slate-500' ?>">ID</a> |
                <a href="?lang=en" class="<?= $lang == 'en' ? 'font-bold text-blue-600' : 'text-slate-500' ?>">EN</a>
            </div>
        </div>

        <?php if (!empty($error)) { ?>
            <div class="bg-red-100 text-red-700 p-3 rounded-lg mb-4 text-sm">
                <?php foreach ($error as $e) {
                    echo "- " . $e . "<br>";
                } ?>
            </div>
        <?php } ?>

        <form method="POST" action="<?php echo $_SERVER['PHP_SELF']; ?>" class="space-y-4">
            <div>
                <label class="block text-sm font-medium text-slate-700"><?= $t["nama"] ?></label>
                <input type="text" name="nama" required class="w-full mt-1 p-2 border rounded-lg">
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700"><?= $t["email"] ?></label>
                <input type="email" name="email" required class="w-full mt-1 p-2 border rounded-lg">
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700"><?= $t["password"] ?></label>
                <input type="password" name="password" required class="w-full mt-1 p-2 border rounded-lg">
            </div>
            <button type="submit" class="w-full bg-blue-600 text-white py-2 rounded-lg hover:bg-blue-700 font-medium">
                <?= $t["register"] ?>
            </button>
        </form>
        <p class="text-center text-sm text-slate-600 mt-4">
            <a href="login.php" class="text-blue-600 hover:underline"><?= $t["ke_login"] ?></a>
        </p>
    </div>
</body>

</html>