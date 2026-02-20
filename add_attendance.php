<?php
session_start();
include "db.php";

if($_SESSION['role']!="admin"){
    header("Location: login.php");
    exit();
}

$student_id = (int)$_GET['id'];

if(isset($_POST['save'])){

    $attendance = $_POST['attendance'];

    $stmt=$conn->prepare("
    INSERT INTO attendance(student_id,attendance_percent)
    VALUES(?,?)
    ON DUPLICATE KEY UPDATE
    attendance_percent=VALUES(attendance_percent)
    ");

    $stmt->bind_param("id",$student_id,$attendance);
    $stmt->execute();

    header("Location: dashboard.php");
}
?>
<?php include "header.php"; ?>

<div class="container mt-5">
<div class="card p-4 shadow">

<h4>Add Attendance</h4>

<form method="POST">

<input type="number"
step="0.01"
name="attendance"
class="form-control mb-3"
placeholder="Attendance %"
required>

<button name="save" class="btn btn-warning">
Save Attendance
</button>

</form>

</div>
</div>

<?php include "footer.php"; ?>
