<?php
$mysqli = new mysqli("localhost", "root", "root", "ระบบเช็คการทำงาน");

// ตรวจสอบการเชื่อมต่อ
if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
}
?>
