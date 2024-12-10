<?php
session_start();  // เริ่ม session เพื่อใช้เก็บการตั้งค่าการแสดงผล
include 'db_connect.php';  // เชื่อมต่อกับฐานข้อมูล

// ตรวจสอบการลบข้อมูล
if (isset($_GET['delete'])) {
    $worker_id = $_GET['delete'];
    $current_date = date('Y-m-d');  // วันที่ปัจจุบัน

    // อัปเดตสถานะ employment_status ของคนงานเป็น 'deleted' และบันทึกวันที่ลบ
    $update_status_sql = "UPDATE workers SET employment_status = 'deleted', delete_date = '$current_date' WHERE worker_id = $worker_id";
    if ($mysqli->query($update_status_sql) === TRUE) {
        echo "<script>alert('เปลี่ยนสถานะคนงานเป็น deleted สำเร็จ!');</script>";
        header('Location: worker_list.php');  // กลับไปยังหน้ารายชื่อคนงานหลังจากลบสำเร็จ
        exit;
    } else {
        echo "<div class='error-message'>Error: " . $mysqli->error . "</div>";
    }
}


// ตรวจสอบการเปลี่ยนแปลงรูปแบบการแสดงผล
if (isset($_POST['view_type'])) {
    $_SESSION['view_type'] = $_POST['view_type'];  // บันทึกการตั้งค่าใน session
}

// ดึงข้อมูลคนงานทั้งหมดจากฐานข้อมูล
$workers = $mysqli->query("SELECT * FROM workers WHERE employment_status != 'deleted'");  // แสดงเฉพาะคนงานที่ไม่ถูกลบ

// กำหนดค่ารูปแบบการแสดงผลเริ่มต้น
$view_type = isset($_SESSION['view_type']) ? $_SESSION['view_type'] : 'card';  // ค่าเริ่มต้นเป็นการ์ด
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <title>รายชื่อคนงาน</title>
    <link rel="stylesheet" href="css/worker_list.css">  <!-- สไตล์ที่ใช้งาน -->
    <style>
        /* สไตล์สำหรับปุ่มเพิ่มคนงาน */
        .btn-add-worker {
            display: inline-block;
            padding: 12px 24px;
            background-color: #28a745;
            color: white;
            text-decoration: none;
            font-size: 16px;
            border-radius: 5px;
            border: none;
            cursor: pointer;
            position: absolute;
            right: 20px;
            top: 20px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            transition: background-color 0.3s ease, transform 0.3s ease;
        }

        /* เปลี่ยนสีและเพิ่มเอฟเฟกต์เมื่อ hover */
        .btn-add-worker:hover {
            background-color: #218838;
            transform: translateY(-2px);
        }

        /* สไตล์สำหรับไอคอนในปุ่ม */
        .btn-add-worker i {
            margin-right: 8px; /* ระยะห่างระหว่างไอคอนและข้อความ */
        }

        /* จัดตำแหน่งปุ่มให้อยู่ด้านขวาบน */
        .main-content {
            position: relative; /* กำหนด position เพื่อให้ปุ่มวางใน container นี้ */
            padding-top: 80px; /* เพิ่มพื้นที่ข้างบนให้กับปุ่ม */
        }
    </style>
</head>
<body>
    <?php include 'navbar.php'; ?>
    <div class="main-content">
        <h1>รายชื่อคนงาน</h1>
        <!-- ปุ่มเพิ่มคนงาน -->
        <a href="add_worker.php" class="btn-add-worker">เพิ่มคนงาน</a>
        <!-- ฟอร์มสำหรับเลือกการแสดงผล -->
        <form method="post" action="">
            <label for="view_type">เลือกรูปแบบการแสดงผล:</label>
            <select name="view_type" id="view_type" onchange="this.form.submit()">
                <option value="table" <?php if ($view_type == 'table') echo 'selected'; ?>>ตาราง</option>
                <option value="card" <?php if ($view_type == 'card') echo 'selected'; ?>>การ์ด</option>
            </select>
        </form>

        <?php if ($view_type == 'table') { ?>
            <!-- แสดงแบบตาราง -->
            <table>
                <tr>
                    <th>ชื่อ</th>
                    <th>อายุ</th>
                    <th>เพศ</th>
                    <th>ประเทศที่เกิด</th>
                    <th>เบอร์โทรติดต่อ</th>
                    <th>ค่าจ้างรายวัน</th>
                    <th>การกระทำ</th>
                </tr>
                <?php while ($row = $workers->fetch_assoc()) { ?>
                <tr>
                    <td><?php echo $row['name']; ?></td>
                    <td><?php echo $row['age']; ?></td>
                    <td><?php echo $row['gender']; ?></td>
                    <td><?php echo $row['country_of_birth']; ?></td>
                    <td><?php echo $row['contact_number']; ?></td>
                    <td><?php echo $row['daily_wage']; ?></td>
                    <td>
                        <a href="edit_worker.php?worker_id=<?php echo $row['worker_id']; ?>" class="edit-btn">แก้ไข</a>
                        <a href="worker_list.php?delete=<?php echo $row['worker_id']; ?>" class="delete-btn" onclick="return confirm('คุณแน่ใจว่าต้องการเปลี่ยนสถานะคนงานนี้หรือไม่?');">ลบ</a>
                        <a href="advance_payment.php?worker_id=<?php echo $row['worker_id']; ?>" class="advance-btn">เบิกเงิน</a>
                    </td>
                </tr>
                <?php } ?>
            </table>
        <?php } else { ?>
            <!-- แสดงแบบการ์ด -->
            <div class="card-container">
                <?php while ($row = $workers->fetch_assoc()) { ?>
                    <div class="card">
                        <!-- แสดงรูปคนงาน -->
                        <?php if ($row['profile_image']) { ?>
                            <img src="uploads/<?php echo $row['profile_image']; ?>" alt="รูปคนงาน">
                        <?php } else { ?>
                            <img src="uploads/no_img.jpg" alt="ไม่มีรูป">
                        <?php } ?>

                        <!-- แสดงข้อมูลคนงาน -->
                        <h3><?php echo htmlspecialchars($row['name']); ?></h3>
                        <p>อายุ: <?php echo htmlspecialchars($row['age']); ?></p>
                        <p>เพศ: <?php echo htmlspecialchars($row['gender']); ?></p>
                        <p>ประเทศ: <?php echo htmlspecialchars($row['country_of_birth']); ?></p>
                        <p>เบอร์โทร: <?php echo htmlspecialchars($row['contact_number']); ?></p>
                        <p>ค่าจ้างรายวัน: <?php echo htmlspecialchars($row['daily_wage']); ?> บาท</p>
                        <div>
                            <a href="edit_worker.php?worker_id=<?php echo $row['worker_id']; ?>" class="edit-btn">แก้ไข</a>
                            <a href="worker_list.php?delete=<?php echo $row['worker_id']; ?>" class="delete-btn" onclick="return confirm('คุณแน่ใจว่าต้องการเปลี่ยนสถานะคนงานนี้หรือไม่?');">ลบ</a>
                            <a href="advance_payment.php?worker_id=<?php echo $row['worker_id']; ?>" class="advance-btn">เบิกเงิน</a>
                        </div>
                    </div>
                <?php } ?>
            </div>
        <?php } ?>
    </div>
</body>
</html>
