<?php
include 'db_connect.php';  // เชื่อมต่อกับฐานข้อมูล

// ตรวจสอบว่ามีการส่ง worker_id มาหรือไม่
if (isset($_GET['worker_id'])) {
    $worker_id = $_GET['worker_id'];

    // ดึงข้อมูลคนงานตาม worker_id
    $worker = $mysqli->query("SELECT * FROM workers WHERE worker_id = $worker_id")->fetch_assoc();

    // ตรวจสอบว่าคนงานมีอยู่หรือไม่
    if (!$worker) {
        die("ไม่พบข้อมูลคนงาน");
    }
}

// ตรวจสอบว่ามีการแก้ไขข้อมูลหรือไม่
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $age = $_POST['age'];
    $gender = $_POST['gender'];
    $country_of_birth = $_POST['country_of_birth'];
    $dob = $_POST['dob'];
    $id_number = $_POST['id_number'];
    $daily_wage = $_POST['daily_wage'];
    $contact_number = $_POST['contact_number'];
    $address = $_POST['address'];
    $hire_date = $_POST['hire_date'];

    // สำหรับรูปคนงาน
    $profile_image = $worker['profile_image'];  // เก็บรูปปัจจุบันไว้ก่อน

    // เช็คว่ามีการอัปโหลดรูปใหม่หรือไม่
    if (isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] === UPLOAD_ERR_OK) {
        if (!is_dir('uploads')) {
            mkdir('uploads', 0777, true);
        }

        // ลบรูปเก่า (ถ้ามี)
        if (!empty($worker['profile_image']) && file_exists('uploads/' . $worker['profile_image'])) {
            unlink('uploads/' . $worker['profile_image']);
        }

        // อัปโหลดรูปใหม่
        $image_name = $_FILES['profile_image']['name'];
        $image_tmp = $_FILES['profile_image']['tmp_name'];
        $image_ext = pathinfo($image_name, PATHINFO_EXTENSION);
        $image_new_name = uniqid() . '.' . $image_ext;

        move_uploaded_file($image_tmp, 'uploads/' . $image_new_name);

        $profile_image = $image_new_name;
    }

    // อัปเดตข้อมูลคนงานในฐานข้อมูล
    $sql = "UPDATE workers SET 
                name = '$name', 
                age = '$age', 
                gender = '$gender', 
                country_of_birth = '$country_of_birth', 
                dob = '$dob', 
                id_number = '$id_number', 
                daily_wage = '$daily_wage', 
                contact_number = '$contact_number', 
                address = '$address', 
                hire_date = '$hire_date',
                profile_image = '$profile_image'
            WHERE worker_id = $worker_id";

    if ($mysqli->query($sql) === TRUE) {
        echo "<div class='success-message'>แก้ไขข้อมูลคนงานสำเร็จ!</div>";
    } else {
        echo "<div class='error-message'>Error: " . $sql . "<br>" . $mysqli->error . "</div>";
    }
}
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <title>แก้ไขข้อมูลคนงาน</title>
    <link rel="stylesheet" href="css/add_worker.css"> <!-- ใช้ CSS เดียวกับการเพิ่มคนงาน -->
</head>
<body>
    <?php include 'navbar.php'; ?>

    <div class="main-content">
        <h1>แก้ไขข้อมูลคนงาน</h1>
        <form method="post" action="" enctype="multipart/form-data">
            <label for="name">ชื่อ:</label>
            <input type="text" name="name" value="<?php echo $worker['name']; ?>" required>

            <label for="age">อายุ:</label>
            <input type="text" name="age" value="<?php echo $worker['age']; ?>" required>

            <label for="gender">เพศ:</label>
            <select name="gender">
                <option value="ชาย" <?php if ($worker['gender'] == 'ชาย') echo 'selected'; ?>>ชาย</option>
                <option value="หญิง" <?php if ($worker['gender'] == 'หญิง') echo 'selected'; ?>>หญิง</option>
            </select>

            <label for="country_of_birth">ประเทศที่เกิด:</label>
            <input type="text" name="country_of_birth" value="<?php echo $worker['country_of_birth']; ?>" required>

            <label for="dob">วันเกิด:</label>
            <input type="date" name="dob" value="<?php echo $worker['dob']; ?>">

            <label for="id_number">เลขบัตร:</label>
            <input type="text" name="id_number" value="<?php echo $worker['id_number']; ?>">

            <label for="daily_wage">ค่าจ้างรายวัน:</label>
            <input type="text" name="daily_wage" value="<?php echo $worker['daily_wage']; ?>" required>

            <label for="contact_number">เบอร์โทรติดต่อ:</label>
            <input type="text" name="contact_number" value="<?php echo $worker['contact_number']; ?>">

            <label for="address">ที่อยู่:</label>
            <textarea name="address"><?php echo $worker['address']; ?></textarea>

            <label for="hire_date">วันที่เริ่มงาน:</label>
            <input type="date" name="hire_date" value="<?php echo $worker['hire_date']; ?>">

            <!-- ฟิลด์อัปโหลดรูป -->
            <label for="profile_image">รูปคนงาน:</label>
            <input type="file" name="profile_image" accept="image/*">

            <!-- แสดงรูปปัจจุบัน -->
            <?php if ($worker['profile_image']) { ?>
                <p>รูปปัจจุบัน:</p>
                <img src="uploads/<?php echo $worker['profile_image']; ?>" alt="รูปคนงาน" style="max-width: 150px;">
            <?php } else { ?>
                <p>ไม่มีรูปภาพ</p>
            <?php } ?>

            <input type="submit" value="บันทึกการเปลี่ยนแปลง">
        </form>
    </div>
</body>
</html>
