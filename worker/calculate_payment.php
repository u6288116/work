<?php
include 'db_connect.php';  // เชื่อมต่อฐานข้อมูล

// ตรวจสอบว่ามีการเลือกเดือนและปีหรือไม่
$selected_month_year = isset($_POST['month_year']) ? $_POST['month_year'] : date('Y-m');
$selected_year = date('Y', strtotime($selected_month_year));
$selected_month = date('m', strtotime($selected_month_year));
$total_amount = 0;

// ดึงข้อมูลการทำงานและการเบิกเงินล่วงหน้าของคนงานในเดือนที่เลือก
$sql = "
    SELECT 
        w.worker_id, 
        w.name,
        IFNULL(SUM(wr.work_duration)/8, 0) AS total_duration,
        IFNULL(w.daily_wage, 0) AS daily_wage,
        IFNULL((SELECT SUM(amount) FROM advance_payments ap WHERE ap.worker_id = w.worker_id AND DATE_FORMAT(ap.date, '%Y-%m') = '$selected_month_year'), 0) as total_advance,
        IFNULL(((w.daily_wage / 8) * SUM(wr.work_duration)) - 
        IFNULL((SELECT SUM(amount) FROM advance_payments ap WHERE ap.worker_id = w.worker_id AND DATE_FORMAT(ap.date, '%Y-%m') = '$selected_month_year'), 0), 0) as net_salary,
        w.employment_status,
        w.delete_date
    FROM workers w
    LEFT JOIN work_records wr ON w.worker_id = wr.worker_id AND DATE_FORMAT(wr.date, '%Y-%m') = '$selected_month_year'
    WHERE (w.employment_status != 'deleted' OR (w.employment_status = 'deleted' AND (w.delete_date IS NULL OR (YEAR(w.delete_date) = '$selected_year' AND MONTH(w.delete_date) = '$selected_month'))))
    GROUP BY w.worker_id
";

// รัน query
$result = $mysqli->query($sql);

if (!$result) {
    die("Query Error: " . $mysqli->error);  // แสดงข้อผิดพลาดถ้า query ไม่ทำงาน
}

$payment_data = [];  // เก็บข้อมูลผลลัพธ์

// ตรวจสอบผลลัพธ์ที่ได้จาก query
while ($row = $result->fetch_assoc()) {
    $payment_data[] = $row;
    $total_amount += $row['net_salary'];  // คำนวณยอดรวมทั้งหมด
}
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <title>คำนวณเงินเดือนคนงาน</title>
    <link rel="stylesheet" href="css/calculate_payment.css">
</head>
<body>
    <?php include 'navbar.php'; ?>

    <div class="main-content">
        <h1>คำนวณเงินเดือนคนงาน</h1>

        <form method="post" action="">
            <label for="month_year">เลือกเดือนและปี:</label>
            <input type="month" name="month_year" id="month_year" value="<?php echo $selected_month_year; ?>" required>
            <input type="submit" value="แสดงข้อมูล">
        </form>

        <h2>ยอดรวมสำหรับ <?php echo $selected_month_year; ?>: <?php echo number_format($total_amount, 2); ?> บาท</h2>

        <table>
            <tr>
                <th>ชื่อคนงาน</th>
                <th>ชั่วโมงการทำงานทั้งหมด</th>
                <th>ค่าจ้างรายวัน</th>
                <th>ยอดเบิกเงิน</th>
                <th>ยอดสุทธิ</th>
                <th>สถานะการจ่าย</th>
            </tr>
            <?php foreach ($payment_data as $payment): ?>
                <tr>
                    <td><?php echo htmlspecialchars($payment['name']); ?></td>
                    <td><?php echo number_format($payment['total_duration'], 2); ?> วัน</td>
                    <td><?php echo number_format($payment['daily_wage'], 2); ?> บาท</td>
                    <td><?php echo number_format($payment['total_advance'], 2); ?> บาท</td>
                    <td><?php echo number_format($payment['net_salary'], 2); ?> บาท</td>
                    <td>
                        <form method="post" action="pay_worker.php">
                            <input type="hidden" name="worker_id" value="<?php echo $payment['worker_id']; ?>">
                            <input type="hidden" name="amount" value="<?php echo $payment['net_salary']; ?>">
                            <input type="submit" value="จ่ายเงิน">
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        </table>
    </div>
</body>
</html>
