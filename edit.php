<?php

require_once "config/db.php";

$id = filter_input(
    INPUT_GET,
    "id",
    FILTER_VALIDATE_INT
);

if (!$id) {
    header("Location: index.php");
    exit;
}


/* Ambil data produk berdasarkan ID */

$stmt = $pdo->prepare(
    "SELECT * FROM products WHERE id = :id"
);

$stmt->execute([
    "id" => $id
]);

$product = $stmt->fetch();


if (!$product) {
    header("Location: index.php");
    exit;
}


/* Data awal */

$name = $product["name"];
$category = $product["category"];
$price = $product["price"];
$stock = $product["stock"];

$errors = [];


/* Proses update */

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $name = trim($_POST["name"] ?? "");
    $category = trim($_POST["category"] ?? "");

    $price = filter_input(
        INPUT_POST,
        "price",
        FILTER_VALIDATE_FLOAT
    );

    $stock = filter_input(
        INPUT_POST,
        "stock",
        FILTER_VALIDATE_INT
    );


    /* Validasi nama */

    if (mb_strlen($name) < 3) {

        $errors["name"] =
            "Nama produk minimal 3 karakter.";

    }


    /* Validasi kategori */

    if ($category === "") {

        $errors["category"] =
            "Kategori wajib diisi.";

    }


    /* Validasi harga */

    if (
        $price === false ||
        $price === null ||
        $price <= 0
    ) {

        $errors["price"] =
            "Harga harus lebih dari 0.";

    }


    /* Validasi stok */

    if (
        $stock === false ||
        $stock === null ||
        $stock < 0
    ) {

        $errors["stock"] =
            "Stok tidak boleh negatif.";

    }


    /* Cek nama produk yang sama */

    if (empty($errors)) {

        $check = $pdo->prepare(
            "SELECT COUNT(*)
             FROM products
             WHERE name = :name
             AND id != :id"
        );

        $check->execute([
            "name" => $name,
            "id" => $id
        ]);

        $jumlah = $check->fetchColumn();


        if ($jumlah > 0) {

            $errors["name"] =
                "Nama produk sudah digunakan.";

        }

    }


    /* Update database */

    if (empty($errors)) {

        $update = $pdo->prepare(
            "UPDATE products
             SET
                name = :name,
                category = :category,
                price = :price,
                stock = :stock
             WHERE id = :id"
        );

        $update->execute([
            "name" => $name,
            "category" => $category,
            "price" => $price,
            "stock" => $stock,
            "id" => $id
        ]);


        header("Location: index.php?status=updated");
        exit;
    }
}

?>

<!DOCTYPE html>
<html lang="id">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Edit Produk</title>


    <style>

        * {
            box-sizing: border-box;
        }


        body {
            font-family: Arial, sans-serif;
            background-color: #ffffff;
            margin: 0;
            padding: 40px;
            color: #222222;
        }


        .container {
            max-width: 600px;
            margin: auto;
        }


        /* HEADER */

        .header {
            background-color: #b91c1c;
            color: white;
            padding: 25px;
            border-radius: 15px 15px 0 0;
        }


        .header h1 {
            margin: 0;
            font-size: 28px;
        }


        .header p {
            margin-bottom: 0;
        }


        /* FORM */

        .form-box {
            background-color: white;
            padding: 30px;
            border: 2px solid #fecaca;
            border-top: none;
            border-radius: 0 0 15px 15px;
            box-shadow: 0 5px 15px rgba(185, 28, 28, 0.12);
        }


        label {
            display: block;
            margin-top: 15px;
            margin-bottom: 7px;
            font-weight: bold;
            color: #991b1b;
        }


        input,
        select {
            width: 100%;
            padding: 12px;
            border: 1px solid #fca5a5;
            border-radius: 8px;
            font-size: 15px;
            outline: none;
        }


        input:focus,
        select:focus {
            border-color: #dc2626;
        }


        /* ERROR */

        .error {
            color: #b91c1c;
            background-color: #fef2f2;
            border: 1px solid #fecaca;
            padding: 8px;
            border-radius: 6px;
            margin-top: 5px;
            font-size: 14px;
        }


        /* TOMBOL */

        .tombol {
            margin-top: 25px;
            display: flex;
            gap: 10px;
        }


        .kembali {
            display: inline-block;
            padding: 11px 18px;
            background-color: #ffffff;
            color: #b91c1c;
            border: 2px solid #dc2626;
            border-radius: 8px;
            text-decoration: none;
            font-weight: bold;
        }


        .kembali:hover {
            background-color: #fef2f2;
        }


        button {
            padding: 11px 18px;
            background-color: #dc2626;
            color: white;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-weight: bold;
            font-size: 15px;
        }


        button:hover {
            background-color: #991b1b;
        }


        /* RESPONSIVE */

        @media (max-width: 700px) {

            body {
                padding: 20px;
            }

            .form-box {
                padding: 20px;
            }

            .tombol {
                flex-direction: column;
            }

            .kembali,
            button {
                text-align: center;
                width: 100%;
            }

        }

    </style>

</head>


<body>


<div class="container">


    <!-- HEADER -->

    <div class="header">

        <h1>
            Edit Produk
        </h1>

        <p>
            Ubah data produk yang dipilih
        </p>

    </div>


    <!-- FORM -->

    <div class="form-box">


        <form method="POST">


            <!-- NAMA -->

            <label for="name">
                Nama Produk
            </label>

            <input
                type="text"
                id="name"
                name="name"
                value="<?= htmlspecialchars(
                    $name,
                    ENT_QUOTES,
                    "UTF-8"
                ) ?>"
                minlength="3"
                required
            >


            <?php if (isset($errors["name"])): ?>

                <div class="error">

                    <?= htmlspecialchars(
                        $errors["name"],
                        ENT_QUOTES,
                        "UTF-8"
                    ) ?>

                </div>

            <?php endif; ?>


            <!-- KATEGORI -->

            <label for="category">
                Kategori
            </label>

            <select
                id="category"
                name="category"
                required
            >

                <option value="">
                    -- Pilih Kategori --
                </option>


                <option
                    value="Sepatu"
                    <?= $category === "Sepatu"
                        ? "selected"
                        : "" ?>
                >
                    Sepatu
                </option>


                <option
                    value="Sandal"
                    <?= $category === "Sandal"
                        ? "selected"
                        : "" ?>
                >
                    Sandal
                </option>


                <option
                    value="Umum"
                    <?= $category === "Umum"
                        ? "selected"
                        : "" ?>
                >
                    Umum
                </option>

            </select>


            <?php if (isset($errors["category"])): ?>

                <div class="error">

                    <?= htmlspecialchars(
                        $errors["category"],
                        ENT_QUOTES,
                        "UTF-8"
                    ) ?>

                </div>

            <?php endif; ?>


            <!-- HARGA -->

            <label for="price">
                Harga
            </label>

            <input
                type="number"
                id="price"
                name="price"
                value="<?= htmlspecialchars(
                    (string)$price,
                    ENT_QUOTES,
                    "UTF-8"
                ) ?>"
                min="1"
                step="0.01"
                required
            >


            <?php if (isset($errors["price"])): ?>

                <div class="error">

                    <?= htmlspecialchars(
                        $errors["price"],
                        ENT_QUOTES,
                        "UTF-8"
                    ) ?>

                </div>

            <?php endif; ?>


            <!-- STOK -->

            <label for="stock">
                Stok
            </label>

            <input
                type="number"
                id="stock"
                name="stock"
                value="<?= htmlspecialchars(
                    (string)$stock,
                    ENT_QUOTES,
                    "UTF-8"
                ) ?>"
                min="0"
                required
            >


            <?php if (isset($errors["stock"])): ?>

                <div class="error">

                    <?= htmlspecialchars(
                        $errors["stock"],
                        ENT_QUOTES,
                        "UTF-8"
                    ) ?>

                </div>

            <?php endif; ?>


            <!-- TOMBOL -->

            <div class="tombol">

                <a
                    href="index.php"
                    class="kembali"
                >
                    Kembali
                </a>


                <button type="submit">
                    Simpan Perubahan
                </button>

            </div>


        </form>


    </div>


</div>


</body>

</html>