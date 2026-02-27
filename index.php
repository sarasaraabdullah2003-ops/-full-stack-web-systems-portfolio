<?php
// 1. إعدادات قاعدة البيانات والاتصال التلقائي
$host = "localhost";
$user = "root";
$pass = "";
$db_name = "smart_school_db";

$conn = new mysqli($host, $user, $pass);

// إنشاء قاعدة البيانات والجدول إذا لم يوجدوا
$conn->query("CREATE DATABASE IF NOT EXISTS $db_name");
$conn->select_db($db_name);
$conn->query("CREATE TABLE IF NOT EXISTS students (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100),
    phone VARCHAR(20),
    reg_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)");

// معالجة البيانات (PHP Logic)
$message = "";
if (isset($_POST['register'])) {
    $name = $_POST['name']; $email = $_POST['email']; $phone = $_POST['phone'];
    $conn->query("INSERT INTO students (name, email, phone) VALUES ('$name', '$email', '$phone')");
    $message = "تم تسجيل الطالب بنجاح! ✅";
}
if (isset($_POST['delete'])) {
    $id = $_POST['id'];
    $conn->query("DELETE FROM students WHERE id=$id");
}
if (isset($_POST['update'])) {
    $id = $_POST['id']; $name = $_POST['name'];
    $conn->query("UPDATE students SET name='$name' WHERE id=$id");
}
$students = $conn->query("SELECT * FROM students ORDER BY id DESC");
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>نظام إدارة المدرسة الذكي</title>
    <style>
        :root {
            --primary: #4e73df;
            --secondary: #1cc88a;
            --dark: #2c3e50;
            --light: #f8f9fc;
        }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #f0f2f5;
            margin: 0;
            display: flex;
            flex-direction: column;
            align-items: center;
        }
        /* الهيدر */
        nav {
            background: var(--dark);
            width: 100%;
            padding: 15px 0;
            text-align: center;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }
        nav button {
            background: none;
            border: 2px solid white;
            color: white;
            padding: 8px 20px;
            margin: 0 10px;
            cursor: pointer;
            border-radius: 20px;
            transition: 0.3s;
        }
        nav button:hover { background: white; color: var(--dark); }

        /* الحاويات */
        .page { display: none; width: 90%; max-width: 800px; margin-top: 30px; animation: fadeIn 0.5s; }
        .active { display: block; }

        .card {
            background: white;
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.05);
        }

        h1, h2 { color: var(--dark); text-align: center; }

        /* الفورم */
        input {
            width: 100%;
            padding: 12px;
            margin: 10px 0;
            border: 1px solid #ddd;
            border-radius: 8px;
            box-sizing: border-box;
        }
        .btn-main {
            width: 100%;
            padding: 12px;
            background: var(--primary);
            color: white;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-size: 16px;
        }
        .btn-main:hover { background: #2e59d9; }

        /* الجدول */
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th { background: var(--primary); color: white; padding: 15px; }
        td { padding: 12px; border-bottom: 1px solid #eee; text-align: center; }
        tr:hover { background: #f9f9f9; }

        .action-btns { display: flex; gap: 5px; justify-content: center; }
        .btn-del { background: #e74a3b; color: white; border: none; padding: 5px 10px; border-radius: 4px; cursor: pointer; }
        .btn-edit { background: #f6c23e; color: white; border: none; padding: 5px 10px; border-radius: 4px; cursor: pointer; }

        @keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
    </style>
</head>
<body>

<nav>
    <button onclick="showPage('home')">الرئيسية (التسجيل)</button>
    <button onclick="showPage('login')">دخول الإدارة</button>
    <button id="adminBtn" style="display:none;" onclick="showPage('admin')">لوحة التحكم</button>
</nav>

<div id="home" class="page active">
    <div class="card">
        <h1>🏫 نظام تسجيل الطلاب</h1>
        <p style="text-align:center; color:green;"><?php echo $message; ?></p>
        <form method="POST">
            <input type="text" name="name" placeholder="اسم الطالب الكامل" required>
            <input type="email" name="email" placeholder="البريد الإلكتروني" required>
            <input type="text" name="phone" placeholder="رقم الجوال">
            <button type="submit" name="register" class="btn-main">إرسال طلب التسجيل</button>
        </form>
    </div>
</div>

<div id="login" class="page">
    <div class="card" style="max-width: 400px; margin: auto;">
        <h2>🔐 دخول الإدارة</h2>
        <input type="text" id="user" placeholder="اسم المستخدم (admin)">
        <input type="password" id="pass" placeholder="كلمة المرور (123)">
        <button onclick="loginCheck()" class="btn-main">دخول</button>
        <p id="loginError" style="color:red; text-align:center;"></p>
    </div>
</div>

<div id="admin" class="page">
    <div class="card">
        <h2>📂 إدارة بيانات الطلاب</h2>
        <form method="POST" id="editForm" style="background: #f8f9fc; padding: 15px; border-radius: 10px; margin-bottom: 20px;">
            <input type="hidden" name="id" id="std_id">
            <input type="text" name="name" id="std_name" placeholder="الاسم للتعديل أو الإضافة" required>
            <div style="display:flex; gap:10px;">
                <button type="submit" name="update" class="btn-main" style="background:var(--secondary);">تحديث / حفظ</button>
                <button type="button" onclick="resetForm()" class="btn-main" style="background:#858796;">إلغاء</button>
            </div>
        </form>

        <table>
            <tr>
                <th>الاسم</th>
                <th>الهاتف</th>
                <th>إدارة</th>
            </tr>
            <?php while($row = $students->fetch_assoc()): ?>
            <tr>
                <td><?php echo $row['name']; ?></td>
                <td><?php echo $row['phone']; ?></td>
                <td class="action-btns">
                    <button class="btn-edit" onclick="fillEdit('<?php echo $row['id']; ?>', '<?php echo $row['name']; ?>')">تعديل</button>
                    <form method="POST" style="display:inline;">
                        <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
                        <button type="submit" name="delete" class="btn-del" onclick="return confirm('حذف؟')">حذف</button>
                    </form>
                </td>
            </tr>
            <?php endwhile; ?>
        </table>
    </div>
</div>

<script>
    function showPage(pageId) {
        document.querySelectorAll('.page').forEach(p => p.classList.remove('active'));
        document.getElementById(pageId).classList.add('active');
    }

    function loginCheck() {
        const u = document.getElementById('user').value;
        const p = document.getElementById('pass').value;
        if(u === 'admin' && p === '123') {
            document.getElementById('adminBtn').style.display = 'inline-block';
            showPage('admin');
        } else {
            document.getElementById('loginError').innerText = 'خطأ في البيانات!';
        }
    }

    function fillEdit(id, name) {
        document.getElementById('std_id').value = id;
        document.getElementById('std_name').value = name;
        document.getElementById('std_name').focus();
    }

    function resetForm() {
        document.getElementById('std_id').value = '';
        document.getElementById('std_name').value = '';
    }
</script>

</body>
</html>

