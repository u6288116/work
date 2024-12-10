<?php
include 'db_connect.php';
include 'navbar.php';  // เรียกใช้ navbar

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // ตรวจสอบว่ากรอกอายุเป็นตัวเลขหรือไม่
    if (isset($_POST['age']) && is_numeric($_POST['age'])) {
        $name = $_POST['name'];
        $age = $_POST['age'];
        $gender = $_POST['gender'];
        $country_of_birth = $_POST['country_of_birth'];
        $dob = $_POST['dob'];
        $id_number = $_POST['id_number'];
        $daily_wage = $_POST['daily_wage'];
        $contact_number = $_POST['contact_number'];
        $hire_date = $_POST['hire_date'];

        // สำหรับรูปคนงาน
        $profile_image = '';
        if (isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] === UPLOAD_ERR_OK) {
            // ตรวจสอบว่ามีโฟลเดอร์ uploads หรือไม่ หากไม่มีให้สร้างใหม่
            if (!is_dir('uploads')) {
                mkdir('uploads', 0777, true);
            }

            $image_name = $_FILES['profile_image']['name'];
            $image_tmp = $_FILES['profile_image']['tmp_name'];
            $image_ext = pathinfo($image_name, PATHINFO_EXTENSION);
            $image_new_name = uniqid() . '.' . $image_ext;

            move_uploaded_file($image_tmp, 'uploads/' . $image_new_name);

            $profile_image = $image_new_name;
        }

        // ตรวจสอบกรณีไม่มีการอัปโหลดรูป
        if (empty($profile_image)) {
            $profile_image = 'default.jpg'; // ใช้ default.jpg หากไม่ได้อัปโหลดรูป
        }

        // เพิ่มข้อมูลลงในฐานข้อมูล
        $sql = "INSERT INTO workers (name, age, gender, country_of_birth, dob, id_number, daily_wage, contact_number, hire_date, profile_image)
                VALUES ('$name', '$age', '$gender', '$country_of_birth', '$dob', '$id_number', '$daily_wage', '$contact_number', '$hire_date', '$profile_image')";

        if ($mysqli->query($sql) === TRUE) {
            // เปลี่ยนเส้นทางกลับไปยังหน้ารายชื่อคนงานหลังจากเพิ่มสำเร็จ
            header("Location: worker_list.php");
            exit();  // หยุดการทำงานเพื่อป้องกันไม่ให้โค้ดหลังจากนี้ทำงาน
        } else {
            echo "<div class='error-message'>Error: " . $sql . "<br>" . $mysqli->error . "</div>";
        }
    } else {
        echo "<div class='error-message'>กรุณากรอกอายุให้เป็นตัวเลข.</div>";
    }
}
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <title>เพิ่มคนงาน</title>
    <link rel="stylesheet" href="css/add_worker.css">
    <script>
        // ฟังก์ชันคำนวณอายุจากวันเกิด
        function calculateAge() {
            var dob = document.getElementById('dob').value;
            var dobDate = new Date(dob);
            var diff = Date.now() - dobDate.getTime();
            var ageDate = new Date(diff);
            var age = Math.abs(ageDate.getUTCFullYear() - 1970);

            if (!isNaN(age)) {
                document.getElementById('age').value = age; // กรอกอายุลงในช่องอายุ
            }
        }
    </script>
</head>
<body>
    <?php include 'navbar.php'; ?>

    <div class="main-content">
        <h1>เพิ่มคนงาน</h1>
        <form method="post" action="" enctype="multipart/form-data">
            <label for="name">ชื่อ:</label>
            <input type="text" name="name" required>

            <label for="gender">เพศ:</label>
            <select name="gender">
                <option value="ชาย">ชาย</option>
                <option value="หญิง">หญิง</option>
            </select>

            <label for="dob">วันเกิด:</label>
            <input type="date" name="dob" id="dob" onchange="calculateAge()">

            <label for="age">อายุ:</label>
            <input type="text" name="age" id="age" required readonly>

            <label for="country_of_birth">ประเทศที่เกิด:</label>
            <input type="text" name="country_of_birth" required>

            <label for="id_number">เลขบัตร:</label>
            <input type="text" name="id_number">

            <label for="daily_wage">ค่าจ้างรายวัน:</label>
            <input type="text" name="daily_wage" required>

            <label for="contact_number">เบอร์โทรติดต่อ:</label>
            <input type="text" name="contact_number">

            <label for="hire_date">วันที่เริ่มงาน:</label>
            <input type="date" name="hire_date">

            <!-- ฟิลด์อัปโหลดรูป -->
            <label for="profile_image">รูปคนงาน:</label>
            <input type="file" name="profile_image" accept="image/*">

            <input type="submit" value="เพิ่มคนงาน">
        </form>
    </div>
</body>
</html>
