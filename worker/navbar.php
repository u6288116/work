<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <title>หน้า Navbar</title>

    <!-- ลิงก์ไปยัง FontAwesome สำหรับไอคอน -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    
    <!-- ลิงก์ไปยังไฟล์ CSS ของคุณ -->
    <link rel="stylesheet" href="css/navbar.css">
    <style>
        /* นำ CSS ที่ปรับปรุงมาไว้ที่นี่ */
        <?php include 'css/navbar.css'; ?>
    </style>
</head>
<body>

    <!-- Navbar -->
    <nav class="navbar">
        <a href="index.php"><i class="fas fa-home"></i> หน้าแรก</a>
        <a href="worker_list.php"><i class="fas fa-users"></i> รายชื่อคนงาน</a>
        <a href="calculate_payment.php"><i class="fas fa-money-check-alt"></i> ยอดเงินเดือน</a>
    </nav>
</body>
</html>
