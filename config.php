
<?php
$host = "localhost";
$user = "root";
$pass = "";

$conn = new mysqli($host, $user, $pass);

// إنشاء قاعدة البيانات
$conn->query("CREATE DATABASE IF NOT EXISTS luxury_store CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
$conn->select_db("luxury_store");

// إنشاء جدول المنتجات
$table_products = "CREATE TABLE IF NOT EXISTS products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    price DECIMAL(10,2) NOT NULL,
    description TEXT,
    image VARCHAR(255) NOT NULL
)";
$conn->query($table_products);

// إنشاء جدول الطلبات
$table_orders = "CREATE TABLE IF NOT EXISTS orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    product_name VARCHAR(255),
    order_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";
$conn->query($table_orders);
?>
