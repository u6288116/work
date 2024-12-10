<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <title>ปฏิทินกิจกรรม</title>
    
    <!-- ลิงก์ไปยัง FullCalendar CSS -->
    <link href='https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.css' rel='stylesheet' />
    
    <!-- ลิงก์ไปยัง FullCalendar JS -->
    <script src='https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.js'></script>
    
    <!-- ลิงก์ไปยังไลบรารี FontAwesome สำหรับไอคอน -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    
    <!-- ลิงก์ไปยังไฟล์ CSS ที่กำหนดเอง -->
    <link rel="stylesheet" href="css/index.css">
</head>
<body>

    <!-- Sidebar -->
    <div class="sidebar">
        <?php include 'navbar.php'; ?>
    </div>

    <!-- Container ปฏิทิน -->
    <div id="calendar" style="margin-left: 260px;"></div>

    <!-- โค้ด FullCalendar -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var calendarEl = document.getElementById('calendar');

            var calendar = new FullCalendar.Calendar(calendarEl, {
                initialView: 'dayGridMonth',  // แสดงปฏิทินแบบเดือน
                headerToolbar: {
                    left: 'prev,next today',
                    center: 'title',
                    right: 'dayGridMonth,timeGridWeek,timeGridDay'
                },
                selectable: true,  // เปิดให้เลือกวันได้
                select: function(info) {
                    // เมื่อผู้ใช้เลือกวันที่ ให้เปลี่ยนเส้นทางไปยังหน้า add_activity.php
                    window.location.href = 'add_activity.php?date=' + info.startStr;  // ส่งวันที่ผ่าน URL
                },
                events: 'fetch_events.php',  // ดึงข้อมูลกิจกรรมจากฐานข้อมูล
                eventClick: function(info) {
                    info.jsEvent.preventDefault();  // ปิดการกระทำ default
                    if (info.event.url) {
                        window.location.href = info.event.url;  // เปลี่ยนเส้นทางไปหน้า view_day.php
                    }
                }
            });

            calendar.render();
        });
    </script>

</body>
</html>
