<?php
include 'db_connect.php';

if (isset($_GET['date'])) {
    $date = $_GET['date'];
    
    // ดึงข้อมูลคนงานจากฐานข้อมูลที่ทำงานในวันที่เลือก
    $sql = "
        SELECT wr.worker_id, wr.work_duration, wr.comments, w.name, l.location_name
        FROM work_records wr
        LEFT JOIN workers w ON wr.worker_id = w.worker_id
        LEFT JOIN locations l ON wr.location_id = l.location_id
        WHERE wr.date = '$date'
    ";
    $result = $mysqli->query($sql);
}

?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <title>ข้อมูลการทำงานของวันที่ <?php echo htmlspecialchars($date); ?></title>
    <link rel="stylesheet" href="css/view_day.css">
</head>
<body>
    <?php include 'navbar.php'; ?>
    
    <div class="main-content">
        <h1>ข้อมูลการทำงานของวันที่ <?php echo htmlspecialchars($date); ?></h1>

        <?php if ($result->num_rows > 0) { ?>
            <table>
                <tr>
                    <th>ชื่อคนงาน</th>
                    <th>สถานที่</th>
                    <th>จำนวนชั่วโมง</th>
                    <th>หมายเหตุ</th>
                </tr>
                <?php while ($row = $result->fetch_assoc()) { ?>
                <tr>
                    <td><?php echo htmlspecialchars($row['name']); ?></td>
                    <td><?php echo htmlspecialchars($row['location_name']); ?></td>
                    <td><?php echo htmlspecialchars($row['work_duration']); ?> ชั่วโมง</td>
                    <td><?php echo htmlspecialchars($row['comments']); ?></td>
                </tr>
                <?php } ?>
            </table>
        <?php } else { ?>
            <p>ไม่มีข้อมูลการทำงานสำหรับวันนี้</p>
        <?php } ?>
    </div>
</body>
</html>
