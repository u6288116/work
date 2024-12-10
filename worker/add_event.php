<?php
include 'db_connect.php';

$title = $_POST['title'];
$start = $_POST['start'];
$end = $_POST['end'];

// ตรวจสอบความถูกต้องของข้อมูลก่อนบันทึก
if (!empty($title) && !empty($start)) {
    $sql = "INSERT INTO events (title, start_date, end_date) VALUES ('$title', '$start', '$end')";
    if ($mysqli->query($sql)) {
        echo "กิจกรรมถูกเพิ่มสำเร็จ!";
    } else {
        echo "เกิดข้อผิดพลาด: " . $mysqli->error;
    }
}
?>
