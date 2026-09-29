# PRODUCT MANAGER

Aplikasi Product Manager merupakan aplikasi berbasis web untuk mengelola data produk menggunakan PHP dan MySQL.

## Teknologi

- PHP
- MySQL
- PDO
- HTML
- CSS
- XAMPP

## Fitur

1. Menampilkan data produk
2. Menambahkan produk
3. Mengedit produk
4. Menghapus produk
5. Validasi data
6. Pencarian produk
7. Perlindungan CSRF
8. Prepared Statement
9. Perlindungan XSS
10. Responsive UI

## Struktur Folder

ProductManager/
│
├── config/
│   ├── db.php
│   └── csrf.php
│
├── database/
│   └── store_db.sql
│
├── index.php
├── create.php
├── edit.php
├── delete.php
└── README.md

## Database

Nama database:

store_db

Nama tabel:

products

## Cara Menjalankan

1. Jalankan Apache dan MySQL melalui XAMPP.
2. Pastikan folder ProductManager berada di:
   C:\xampp\htdocs\ProductManager
3. Buka browser.
4. Masukkan alamat:

http://localhost/ProductManager/

## Validasi

Nama produk minimal 3 karakter.

Harga harus lebih dari 0.

Stok tidak boleh kurang dari 0.

Nama produk tidak boleh sama.

## Keamanan

Aplikasi menggunakan PDO Prepared Statement untuk query database.

Input ditampilkan menggunakan htmlspecialchars untuk mengurangi risiko XSS.

Aksi hapus menggunakan metode POST dan CSRF token.

## Fitur Pencarian

Pencarian produk menggunakan metode GET berdasarkan nama produk atau kategori.

## Alur Aplikasi

Pengguna membuka halaman utama.

Pengguna dapat melihat daftar produk.

Pengguna dapat menambahkan produk.

Pengguna dapat mengedit produk.

Pengguna dapat menghapus produk.

Pengguna dapat mencari produk berdasarkan nama atau kategori.