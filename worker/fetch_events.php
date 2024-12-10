<?php
include 'db_connect.php';  // เชื่อมต่อฐานข้อมูล

// ดึงข้อมูลจากตาราง work_records โดยจัดกลุ่มตามวันที่และหมายเหตุ
$sql = "
    SELECT wr.date, wr.comments, COUNT(*) as count
    FROM work_records wr
    WHERE wr.comments IS NOT NULL  -- เฉพาะหมายเหตุที่ไม่ใช่ NULL
    GROUP BY wr.date, wr.comments
";
$result = $mysqli->query($sql);

$events = [];  // เก็บข้อมูล events

// วนลูปดึงข้อมูลจากฐานข้อมูลแล้วเพิ่มเข้าใน array $events
while ($row = $result->fetch_assoc()) {
    // ตรวจสอบว่ามีคนทำงานกี่คน และเพิ่มในหมายเหตุถ้ามีมากกว่า 1 คน
    $title = $row['comments'];
    if ($row['count'] > 1) {
        $title .= " (มีคนทำงาน $row[count] คน)";
    }

    $events[] = [
        'title' => $title,  // แสดงหมายเหตุ
        'start' => $row['date'],  // วันที่ทำงาน
        'allDay' => true,  // ตั้งค่าเป็น allDay ให้แสดงทั้งวัน
        'url' => 'view_day.php?date=' . $row['date'],  // เมื่อคลิก จะนำไปสู่หน้า view_day.php พร้อมวันที่
    ];
}

// ส่งข้อมูลเป็น JSON กลับไปยัง FullCalendar
header('Content-Type: application/json');
echo json_encode($events);
?>
