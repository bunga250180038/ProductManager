<?php

require_once "config/db.php";
require_once "config/csrf.php";

$search = trim($_GET["search"] ?? "");
$status = $_GET["status"] ?? "";

if ($search !== "") {

    $stmt = $pdo->prepare("
        SELECT *
        FROM products
        WHERE name LIKE :search
        OR category LIKE :search
        ORDER BY id DESC
    ");

    $stmt->execute([
        "search" => "%" . $search . "%"
    ]);

} else {

    $stmt = $pdo->query("
        SELECT *
        FROM products
        ORDER BY id DESC
    ");
}

$products = $stmt->fetchAll();

?>

<!DOCTYPE html>
<html lang="id">

<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Product Manager</title>

    <style>

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background: #f5f5f5;
            color: #222;
        }

        .header {
            background: #d90429;
            color: white;
            padding: 25px;
            text-align: center;
        }

        .header h1 {
            margin: 0;
        }

        .container {
            width: 90%;
            max-width: 1100px;
            margin: 30px auto;
        }

        .top {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 15px;
            margin-bottom: 20px;
            flex-wrap: wrap;
        }

        .btn {
            display: inline-block;
            padding: 10px 16px;
            text-decoration: none;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-size: 14px;
        }

        .btn-add {
            background: #d90429;
            color: white;
        }

        .btn-edit {
            background: #222;
            color: white;
        }

        .btn-delete {
            background: #d90429;
            color: white;
        }

        .search-box {
            background: white;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 25px;
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }

        .search-box input {
            flex: 1;
            min-width: 200px;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 6px;
        }

        .search-box button {
            background: #222;
            color: white;
            border: none;
            padding: 10px 18px;
            border-radius: 6px;
            cursor: pointer;
        }

        .reset {
            background: #777;
            color: white;
            padding: 10px 18px;
            border-radius: 6px;
            text-decoration: none;
        }

        .message {
            padding: 12px;
            background: #d8f3dc;
            color: #1b4332;
            border-radius: 6px;
            margin-bottom: 20px;
        }

        .products {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
        }

        .card {
            background: white;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 3px 10px rgba(0,0,0,0.08);
        }

        .card h2 {
            margin-top: 0;
            color: #d90429;
        }

        .category {
            display: inline-block;
            background: #ffe5e9;
            color: #d90429;
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 13px;
        }

        .price {
            font-size: 20px;
            font-weight: bold;
            margin: 15px 0;
        }

        .actions {
            display: flex;
            gap: 8px;
            margin-top: 20px;
        }

        .actions form {
            margin: 0;
        }

        .empty {
            background: white;
            padding: 30px;
            text-align: center;
            border-radius: 8px;
        }

        footer {
            margin-top: 40px;
            background: #222;
            color: white;
            text-align: center;
            padding: 20px;
        }

        @media (max-width: 600px) {

            .container {
                width: 94%;
            }

            .actions {
                flex-direction: column;
            }

            .actions .btn {
                width: 100%;
                text-align: center;
            }

        }

    </style>

</head>

<body>

<div class="header">
    <h1>PRODUCT MANAGER</h1>
    <p>Manajemen Data Produk</p>
</div>

<div class="container">

    <?php if ($status === "created"): ?>

        <div class="message">
            Produk berhasil ditambahkan.
        </div>

    <?php elseif ($status === "updated"): ?>

        <div class="message">
            Produk berhasil diubah.
        </div>

    <?php elseif ($status === "deleted"): ?>

        <div class="message">
            Produk berhasil dihapus.
        </div>

    <?php endif; ?>


    <div class="top">

        <h2>Daftar Produk</h2>

        <a href="create.php" class="btn btn-add">
            + Tambah Produk
        </a>

    </div>


    <form method="GET" class="search-box">

        <input
            type="text"
            name="search"
            placeholder="Cari nama atau kategori produk..."
            value="<?= htmlspecialchars($search, ENT_QUOTES, 'UTF-8'); ?>"
        >

        <button type="submit">
            Cari
        </button>

        <a href="index.php" class="reset">
            Reset
        </a>

    </form>


    <?php if (count($products) > 0): ?>

        <div class="products">

            <?php foreach ($products as $product): ?>

                <div class="card">

                    <h2>
                        <?= htmlspecialchars($product["name"], ENT_QUOTES, "UTF-8"); ?>
                    </h2>

                    <span class="category">
                        <?= htmlspecialchars($product["category"], ENT_QUOTES, "UTF-8"); ?>
                    </span>

                    <div class="price">
                        Rp <?= number_format($product["price"], 0, ",", "."); ?>
                    </div>

                    <p>
                        Stok:
                        <strong>
                            <?= htmlspecialchars($product["stock"], ENT_QUOTES, "UTF-8"); ?>
                        </strong>
                    </p>

                    <div class="actions">

                        <a
                            href="edit.php?id=<?= $product["id"]; ?>"
                            class="btn btn-edit"
                        >
                            Edit
                        </a>

                        <form
                            method="POST"
                            action="delete.php"
                        >

                            <input
                                type="hidden"
                                name="id"
                                value="<?= $product["id"]; ?>"
                            >

                            <input
                                type="hidden"
                                name="csrf_token"
                                value="<?= htmlspecialchars(csrf_token(), ENT_QUOTES, "UTF-8"); ?>"
                            >

                            <button
                                type="submit"
                                class="btn btn-delete"
                            >
                                Hapus
                            </button>

                        </form>

                    </div>

                </div>

            <?php endforeach; ?>

        </div>

    <?php else: ?>

        <div class="empty">
            <h3>Produk tidak ditemukan</h3>
            <p>Belum ada produk yang sesuai dengan pencarian.</p>
        </div>

    <?php endif; ?>

</div>

<footer>
    Product Manager &copy; 2026
</footer>

</body>
</html>