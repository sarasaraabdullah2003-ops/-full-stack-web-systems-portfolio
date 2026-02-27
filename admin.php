
<?php
include 'config.php';

// رفع منتج جديد
if(isset($_POST['add_product'])){
    $name = $_POST['name'];
    $price = $_POST['price'];
    $desc = $_POST['desc'];
    $img = $_FILES['img']['name'];
    move_uploaded_file($_FILES['img']['tmp_name'], "uploads/".$img);
    
    $conn->query("INSERT INTO products (name, price, description, image) VALUES ('$name', '$price', '$desc', '$img')");
}

// حذف منتج
if(isset($_GET['delete'])){
    $id = $_GET['delete'];
    $conn->query("DELETE FROM products WHERE id=$id");
}
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <style>
        body { font-family: sans-serif; padding: 20px; background: #f4f4f4; }
        .admin-panel { background: white; padding: 20px; border-radius: 8px; box-shadow: 0 0 10px rgba(0,0,0,0.1); }
        input, textarea { width: 100%; margin: 10px 0; padding: 10px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 12px; text-align: center; }
        .btn-delete { color: red; text-decoration: none; font-weight: bold; }
    </style>
</head>
<body>

<div class="admin-panel">
    <h2>لوحة تحكم المتجر - إضافة منتجات جديدة</h2>
    <form method="POST" enctype="multipart/form-data">
        <input type="text" name="name" placeholder="اسم المنتج (عطر/زهور)" required>
        <input type="number" name="price" placeholder="السعر" required>
        <textarea name="desc" placeholder="وصف المنتج وفخامته"></textarea>
        <input type="file" name="img" required>
        <button type="submit" name="add_product" style="background: green; color: white; padding: 10px 20px; border: none;">حفظ المنتج</button>
    </form>

    <h3>المنتجات الحالية</h3>
    <table>
        <tr>
            <th>الصورة</th>
            <th>الاسم</th>
            <th>السعر</th>
            <th>إدارة</th>
        </tr>
        <?php
        $res = $conn->query("SELECT * FROM products");
        while($row = $res->fetch_assoc()): ?>
        <tr>
            <td><img src="uploads/<?php echo $row['image']; ?>" width="50"></td>
            <td><?php echo $row['name']; ?></td>
            <td><?php echo $row['price']; ?> $</td>
            <td><a href="?delete=<?php echo $row['id']; ?>" class="btn-delete">حذف</a></td>
        </tr>
        <?php endwhile; ?>
    </table>
</div>

</body>
</html>

