<?php

require_once "config/db.php";

$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $name = trim($_POST["name"] ?? "");
    $category = trim($_POST["category"] ?? "");
    $price = filter_var($_POST["price"] ?? "", FILTER_VALIDATE_FLOAT);
    $stock = filter_var($_POST["stock"] ?? "", FILTER_VALIDATE_INT);

    if (strlen($name) < 3) {

        $error = "Nama produk minimal 3 karakter.";

    } elseif ($category === "") {

        $error = "Kategori wajib diisi.";

    } elseif ($price === false || $price <= 0) {

        $error = "Harga harus lebih dari 0.";

    } elseif ($stock === false || $stock < 0) {

        $error = "Stok tidak boleh kurang dari 0.";

    } else {

        $check = $pdo->prepare("
            SELECT id
            FROM products
            WHERE name = :name
        ");

        $check->execute([
            "name" => $name
        ]);

        if ($check->fetch()) {

            $error = "Nama produk sudah digunakan.";

        } else {

            $stmt = $pdo->prepare("
                INSERT INTO products
                (name, category, price, stock)
                VALUES
                (:name, :category, :price, :stock)
            ");

            $stmt->execute([
                "name" => $name,
                "category" => $category,
                "price" => $price,
                "stock" => $stock
            ]);

            header("Location: index.php?status=created");
            exit;
        }
    }
}

?>

<!DOCTYPE html>
<html lang="id">

<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Tambah Produk</title>

    <style>

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background: #f5f5f5;
        }

        .container {
            width: 90%;
            max-width: 600px;
            margin: 50px auto;
        }

        .box {
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 3px 10px rgba(0,0,0,0.1);
        }

        h1 {
            color: #d90429;
        }

        label {
            display: block;
            margin-top: 15px;
            margin-bottom: 5px;
            font-weight: bold;
        }

        input, select {
            width: 100%;
            padding: 11px;
            border: 1px solid #ccc;
            border-radius: 6px;
        }

        button, a {
            margin-top: 20px;
            padding: 11px 18px;
            border-radius: 6px;
            border: none;
            text-decoration: none;
            cursor: pointer;
        }

        button {
            background: #d90429;
            color: white;
        }

        .back {
            display: inline-block;
            background: #222;
            color: white;
        }

        .error {
            background: #ffe5e5;
            color: #b00020;
            padding: 12px;
            border-radius: 6px;
        }

    </style>

</head>

<body>

<div class="container">

    <div class="box">

        <h1>Tambah Produk</h1>

        <?php if ($error !== ""): ?>

            <div class="error">
                <?= htmlspecialchars($error, ENT_QUOTES, "UTF-8"); ?>
            </div>

        <?php endif; ?>

        <form method="POST">

            <label>Nama Produk</label>

            <input
                type="text"
                name="name"
                required
                value="<?= htmlspecialchars($_POST["name"] ?? "", ENT_QUOTES, "UTF-8"); ?>"
            >

            <label>Kategori</label>

            <select name="category" required>

                <option value="">-- Pilih Kategori --</option>

                <option value="Sepatu"
                    <?= ($_POST["category"] ?? "") === "Sepatu" ? "selected" : ""; ?>>
                    Sepatu
                </option>

                <option value="Sandal"
                    <?= ($_POST["category"] ?? "") === "Sandal" ? "selected" : ""; ?>>
                    Sandal
                </option>

                <option value="Umum"
                    <?= ($_POST["category"] ?? "") === "Umum" ? "selected" : ""; ?>>
                    Umum
                </option>

            </select>

            <label>Harga</label>

            <input
                type="number"
                name="price"
                min="1"
                step="0.01"
                required
                value="<?= htmlspecialchars($_POST["price"] ?? "", ENT_QUOTES, "UTF-8"); ?>"
            >

            <label>Stok</label>

            <input
                type="number"
                name="stock"
                min="0"
                required
                value="<?= htmlspecialchars($_POST["stock"] ?? "", ENT_QUOTES, "UTF-8"); ?>"
            >

            <button type="submit">
                Simpan Produk
            </button>

            <a href="index.php" class="back">
                Kembali
            </a>

        </form>

    </div>

</div>

</body>
</html>