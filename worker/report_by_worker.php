<?php
include 'db_connect.php';
include 'navbar.php';  // เพิ่มบรรทัดนี้เพื่อเรียกใช้งาน navbar
$month = isset($_GET['month']) ? $_GET['month'] : date('m');
$year = isset($_GET['year']) ? $_GET['year'] : date('Y');

$sql = "SELECT workers.name, SUM(work_records.work_duration) AS total_hours
        FROM work_records
        JOIN workers ON work_records.worker_id = workers.worker_id
        WHERE MONTH(work_records.date) = '$month' AND YEAR(work_records.date) = '$year'
        GROUP BY work_records.worker_id";
$result = $mysqli->query($sql);
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <title>รายงานการทำงานตามคนงาน</title>
    <link rel="stylesheet" href="css/report_by_worker.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
    <?php include 'navbar.php'; ?>

    <div class="main-content">
        <h1>รายงานการทำงานตามคนงาน</h1>
        <form method="get" action="">
            <label for="month">เดือน:</label>
            <select name="month">
                <?php for ($m=1; $m<=12; $m++) { ?>
                    <option value="<?php echo $m; ?>" <?php if ($m == $month) echo 'selected'; ?>><?php echo $m; ?></option>
                <?php } ?>
            </select>

            <label for="year">ปี:</label>
            <select name="year">
                <?php for ($y=date('Y')-5; $y<=date('Y'); $y++) { ?>
                    <option value="<?php echo $y; ?>" <?php if ($y == $year) echo 'selected'; ?>><?php echo $y; ?></option>
                <?php } ?>
            </select>

            <input type="submit" value="กรอง">
        </form>

        <canvas id="workerChart"></canvas>

        <script>
        var ctx = document.getElementById('workerChart').getContext('2d');
        var workerChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: [
                    <?php while ($row = $result->fetch_assoc()) { echo '"' . $row['name'] . '",'; } ?>
                ],
                datasets: [{
                    label: 'จำนวนชั่วโมงทำงาน',
                    data: [
                        <?php
                        $result->data_seek(0);
                        while ($row = $result->fetch_assoc()) { echo $row['total_hours'] . ','; }
                        ?>
                    ],
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
        </script>
    </div>
</body>
</html>
