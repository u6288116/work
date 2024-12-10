<?php
include 'db_connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $worker_id = $_POST['worker_id'];
    $amount = $_POST['amount'];
    $date = date('Y-m-d'); // วันที่ปัจจุบัน

    $sql = "INSERT INTO advance_payments (worker_id, amount, date) VALUES ('$worker_id', '$amount', '$date')";
    if ($mysqli->query($sql) === TRUE) {
        echo "<div class='success-message'>บันทึกการเบิกเงินสำเร็จ!</div>";
    } else {
        echo "<div class='error-message'>Error: " . $sql . "<br>" . $mysqli->error . "</div>";
    }
}

// ดึงข้อมูลคนงานเพื่อแสดงในแบบฟอร์ม
$worker_id = isset($_GET['worker_id']) ? $_GET['worker_id'] : null;
$worker = null;
if ($worker_id) {
    $worker = $mysqli->query("SELECT * FROM workers WHERE worker_id = $worker_id")->fetch_assoc();
}

?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <title>เบิกเงินคนงาน</title>
    <link rel="stylesheet" href="css/advance_payment.css">
</head>
<body>
    <?php include 'navbar.php'; ?>

    <div class="main-content">
        <h1>เบิกเงินคนงาน</h1>

        <?php if ($worker): ?>
            <h2>เบิกเงินสำหรับ <?php echo $worker['name']; ?></h2>
            <form method="post" action="">
                <input type="hidden" name="worker_id" value="<?php echo $worker['worker_id']; ?>">
                
                <label for="amount">จำนวนเงิน:</label>
                <input type="text" name="amount" required>
                
                <input type="submit" value="บันทึกการเบิกเงิน">
            </form>
        <?php else: ?>
            <p>ไม่พบข้อมูลคนงาน</p>
        <?php endif; ?>

    </div>
</body>
</html>
