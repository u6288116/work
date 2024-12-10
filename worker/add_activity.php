<?php
include 'db_connect.php';

$workers = $mysqli->query("SELECT * FROM workers");
$locations = $mysqli->query("SELECT * FROM locations");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['worker_id']) && isset($_POST['location_id']) && !empty($_POST['worker_id']) && !empty($_POST['location_id']) && !empty($_POST['work_duration'])) {
        $worker_ids = $_POST['worker_id']; // รับข้อมูลเป็น array
        $date = $_POST['date'];
        $location_id = $_POST['location_id'];
        $work_duration = $_POST['work_duration'];
        $comments = $_POST['comments'];

        // วนลูปเพื่อบันทึกข้อมูลสำหรับคนงานแต่ละคน
        foreach ($worker_ids as $worker_id) {
            $sql = "INSERT INTO work_records (worker_id, date, location_id, work_duration, comments)
                    VALUES ('$worker_id', '$date', '$location_id', '$work_duration', '$comments')";

            if (!$mysqli->query($sql)) {
                echo "<div class='error-message'>Error: " . $sql . "<br>" . $mysqli->error . "</div>";
            }
        }

        echo "<div class='success-message'>บันทึกการทำงานสำเร็จ!</div>";
    } else {
        echo "<div class='error-message'>กรุณากรอกข้อมูลให้ครบถ้วนและเลือกคนงานและสถานที่ให้ครบถ้วน.</div>";
    }
}
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <title>บันทึกการทำงาน</title>
    <link rel="stylesheet" href="css/add_activity.css">
    <style>
        .worker-list {
            list-style-type: none;
            padding: 0;
        }
        .worker-list li {
            margin-bottom: 10px;
        }
        .select-all {
            margin-bottom: 15px;
            cursor: pointer;
            color: #007bff;
        }
        .error-message {
            color: red;
            margin-bottom: 10px;
        }
        .success-message {
            color: green;
            margin-bottom: 10px;
        }
    </style>
    <script>
        // ฟังก์ชันเลือกทั้งหมด
        function selectAllWorkers(selectAllCheckbox) {
            var checkboxes = document.getElementsByName('worker_id[]');
            for (var i = 0; i < checkboxes.length; i++) {
                checkboxes[i].checked = selectAllCheckbox.checked;
            }
        }
    </script>
</head>
<body>
    <?php include 'navbar.php'; ?>

    <div class="main-content">
        <h1>บันทึกการทำงาน</h1>
        <form method="post" action="">
            <label for="date">วันที่:</label>
            <input type="date" name="date" required>

            <label for="worker_id">คนงาน:</label>
            <div class="select-all">
                <input type="checkbox" id="select-all" onclick="selectAllWorkers(this)">
                เลือกทั้งหมด
            </div>
            <ul class="worker-list">
                <?php while ($row = $workers->fetch_assoc()) { ?>
                    <li>
                        <label>
                            <input type="checkbox" name="worker_id[]" value="<?php echo $row['worker_id']; ?>">
                            <?php echo $row['name']; ?>
                        </label>
                    </li>
                <?php } ?>
            </ul>

            <label for="location_id">สถานที่:</label>
            <select name="location_id" required>
                <option value="">--เลือกสถานที่--</option>
                <?php while ($row = $locations->fetch_assoc()) { ?>
                    <option value="<?php echo $row['location_id']; ?>"><?php echo $row['location_name']; ?></option>
                <?php } ?>
            </select>

            <label for="work_duration">ระยะเวลาการทำงาน (ชม.):</label>
            <select name="work_duration" required>
                <option value="">--เลือกชั่วโมงการทำงาน--</option>
                <option value="1">1 ชั่วโมง</option>
                <option value="2">2 ชั่วโมง</option>
                <option value="3">3 ชั่วโมง</option>
                <option value="4">4 ชั่วโมง</option>
                <option value="5">5 ชั่วโมง</option>
                <option value="6">6 ชั่วโมง</option>
                <option value="7">7 ชั่วโมง</option>
                <option value="8">8 ชั่วโมง</option>
            </select>

            <label for="comments">หมายเหตุ:</label>
            <textarea name="comments"></textarea>

            <input type="submit" value="บันทึก">
        </form>
    </div>
</body>
</html>
