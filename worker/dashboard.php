<?php
include 'db_connect.php';

// ตรวจสอบว่าผู้ใช้งานเลือกเดือนหรือปีหรือไม่
$selected_month_year = isset($_POST['month_year']) ? $_POST['month_year'] : date('Y-m');
$selected_year = isset($_POST['year']) ? $_POST['year'] : date('Y');

// แยกเดือนและปีจาก $selected_month_year
$selected_month = date('m', strtotime($selected_month_year));
$selected_year_from_month_year = date('Y', strtotime($selected_month_year));

// ดึงข้อมูลกิจกรรมสำหรับการแสดงผลเป็นรายเดือนหรือรายปี
$activity_sql = "SELECT MONTH(date) as month, COUNT(*) as total_activities 
                 FROM work_records 
                 WHERE YEAR(date) = '$selected_year_from_month_year' 
                 AND MONTH(date) = '$selected_month'
                 GROUP BY MONTH(date)";
$activity_result = $mysqli->query($activity_sql);

$activity_labels = [];
$activity_data = [];

while ($row = $activity_result->fetch_assoc()) {
    $activity_labels[] = "เดือน " . $row['month'];
    $activity_data[] = $row['total_activities'];
}

// ดึงข้อมูลชั่วโมงการทำงานคนงาน
$worker_sql = "SELECT w.name, SUM(wr.work_duration) as total_hours 
               FROM workers w 
               LEFT JOIN work_records wr ON w.worker_id = wr.worker_id 
               WHERE YEAR(wr.date) = '$selected_year' 
               GROUP BY w.worker_id";
$worker_result = $mysqli->query($worker_sql);

$worker_labels = [];
$worker_data = [];

while ($row = $worker_result->fetch_assoc()) {
    $worker_labels[] = $row['name'];
    $worker_data[] = $row['total_hours'];
}

// ดึงข้อมูลค่าใช้จ่าย
$expense_sql = "SELECT SUM(wr.work_duration * w.daily_wage / 8) as total_wages, 
                       SUM(ap.amount) as total_advance 
                FROM work_records wr 
                LEFT JOIN workers w ON wr.worker_id = w.worker_id 
                LEFT JOIN advance_payments ap ON w.worker_id = ap.worker_id 
                WHERE YEAR(wr.date) = '$selected_year_from_month_year' 
                AND MONTH(wr.date) = '$selected_month'";
$expense_result = $mysqli->query($expense_sql);
$expense_data = $expense_result->fetch_assoc();

// ดึงข้อมูลสถานะการจ่ายเงิน
$payment_sql = "SELECT w.name, IFNULL(mp.paid, 0) as paid_status
                FROM workers w
                LEFT JOIN monthly_payments mp ON w.worker_id = mp.worker_id 
                AND mp.month_year = '$selected_month_year'";
$payment_result = $mysqli->query($payment_sql);

$paid = 0; 
$unpaid = 0;
while ($row = $payment_result->fetch_assoc()) {
    if ($row['paid_status']) $paid++; 
    else $unpaid++;
}

// ดึงข้อมูลกิจกรรมสำคัญ
$important_sql = "SELECT date, comments 
                  FROM work_records 
                  WHERE comments LIKE '%สำคัญ%' 
                  AND YEAR(date) = '$selected_year_from_month_year' 
                  AND MONTH(date) = '$selected_month'";
$important_result = $mysqli->query($important_sql);

// ประวัติการทำงานย้อนหลัง
$history_sql = "SELECT MONTH(date) as month, COUNT(*) as total_activities 
                FROM work_records 
                WHERE YEAR(date) = '$selected_year'
                GROUP BY MONTH(date)";
$history_result = $mysqli->query($history_sql);

$history_labels = [];
$history_data = [];

while ($row = $history_result->fetch_assoc()) {
    $history_labels[] = "เดือน " . $row['month'];
    $history_data[] = $row['total_activities'];
}
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <title>Dashboard</title>
    <link rel="stylesheet" href="css/dashboard.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
    <?php include 'navbar.php'; ?>
    
    <div class="dashboard-container">
        <h1>Dashboard</h1>

        <form method="post" action="">
            <label for="month_year">เลือกเดือนและปี:</label>
            <input type="month" name="month_year" id="month_year" value="<?php echo $selected_month_year; ?>">
            
            <label for="year">เลือกปี:</label>
            <select name="year" id="year">
                <option value="2023" <?php if ($selected_year == '2023') echo 'selected'; ?>>2023</option>
                <option value="2024" <?php if ($selected_year == '2024') echo 'selected'; ?>>2024</option>
                <!-- สามารถเพิ่มปีอื่น ๆ ได้ที่นี่ -->
            </select>

            <input type="submit" value="แสดงข้อมูล">
        </form>

        <!-- กราฟสรุปกิจกรรมประจำเดือน -->
        <div class="chart-container">
            <h2>สรุปกิจกรรมประจำเดือน</h2>
            <canvas id="activityChart"></canvas>
        </div>

        <!-- กราฟสถิติการทำงานของคนงาน -->
        <div class="chart-container">
            <h2>สถิติการทำงานของคนงาน</h2>
            <canvas id="workerChart"></canvas>
        </div>

        <!-- กราฟสรุปค่าใช้จ่าย -->
        <div class="chart-container">
            <h2>สรุปค่าใช้จ่าย</h2>
            <canvas id="expenseChart"></canvas>
        </div>

        <!-- สถานะการจ่ายเงิน -->
        <div class="chart-container">
            <h2>สถานะการจ่ายเงิน</h2>
            <canvas id="paymentChart"></canvas>
        </div>

        <!-- ประสิทธิภาพการทำงานของคนงาน -->
        <div class="chart-container">
            <h2>ประสิทธิภาพของคนงาน</h2>
            <canvas id="efficiencyChart"></canvas>
        </div>

        <!-- กิจกรรมสำคัญ -->
        <div class="important-events">
            <h2>การแจ้งเตือนกิจกรรมสำคัญ</h2>
            <ul>
                <?php while ($important_row = $important_result->fetch_assoc()) { ?>
                    <li><?php echo $important_row['date'] . ": " . $important_row['comments']; ?></li>
                <?php } ?>
            </ul>
        </div>

        <!-- ประวัติการทำงานย้อนหลัง -->
        <div class="chart-container">
            <h2>ประวัติการทำงานย้อนหลัง</h2>
            <canvas id="historyChart"></canvas>
        </div>
    </div>

    <script>
        // สรุปกิจกรรมประจำเดือน
        var activityCtx = document.getElementById('activityChart').getContext('2d');
        var activityChart = new Chart(activityCtx, {
            type: 'bar',
            data: {
                labels: <?php echo json_encode($activity_labels); ?>,
                datasets: [{
                    label: 'จำนวนกิจกรรม',
                    data: <?php echo json_encode($activity_data); ?>,
                    backgroundColor: 'rgba(75, 192, 192, 0.2)',
                    borderColor: 'rgba(75, 192, 192, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });

        // สถิติการทำงานของคนงาน
        var workerCtx = document.getElementById('workerChart').getContext('2d');
        var workerChart = new Chart(workerCtx, {
            type: 'bar',
            data: {
                labels: <?php echo json_encode($worker_labels); ?>,
                datasets: [{
                    label: 'ชั่วโมงการทำงาน',
                    data: <?php echo json_encode($worker_data); ?>,
                    backgroundColor: 'rgba(153, 102, 255, 0.2)',
                    borderColor: 'rgba(153, 102, 255, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });

        // สรุปค่าใช้จ่าย
        var expenseCtx = document.getElementById('expenseChart').getContext('2d');
        var expenseChart = new Chart(expenseCtx, {
            type: 'pie',
            data: {
                labels: ['ค่าแรง', 'ยอดเบิกเงินล่วงหน้า'],
                datasets: [{
                    label: 'ค่าใช้จ่าย',
                    data: [<?php echo $expense_data['total_wages'] . "," . $expense_data['total_advance']; ?>],
                    backgroundColor: [
                        'rgba(255, 99, 132, 0.2)',
                        'rgba(54, 162, 235, 0.2)'
                    ],
                    borderColor: [
                        'rgba(255, 99, 132, 1)',
                        'rgba(54, 162, 235, 1)'
                    ],
                    borderWidth: 1
                }]
            }
        });

        // สถานะการจ่ายเงิน
        var paymentCtx = document.getElementById('paymentChart').getContext('2d');
        var paymentChart = new Chart(paymentCtx, {
            type: 'pie',
            data: {
                labels: ['จ่ายแล้ว', 'ยังไม่ได้จ่าย'],
                datasets: [{
                    label: 'สถานะการจ่ายเงิน',
                    data: [<?php echo $paid . "," . $unpaid; ?>],
                    backgroundColor: [
                        'rgba(75, 192, 192, 0.2)',
                        'rgba(255, 99, 132, 0.2)'
                    ],
                    borderColor: [
                        'rgba(75, 192, 192, 1)',
                        'rgba(255, 99, 132, 1)'
                    ],
                    borderWidth: 1
                }]
            }
        });

        // ประวัติการทำงานย้อนหลัง
        var historyCtx = document.getElementById('historyChart').getContext('2d');
        var historyChart = new Chart(historyCtx, {
            type: 'line',
            data: {
                labels: <?php echo json_encode($history_labels); ?>,
                datasets: [{
                    label: 'จำนวนกิจกรรม',
                    data: <?php echo json_encode($history_data); ?>,
                    borderColor: 'rgba(255, 206, 86, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    </script>

</body>
</html>
