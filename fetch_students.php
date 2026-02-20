<?php
session_start();
include "db.php";

/*
   fetch_students.php
   Used by dashboard.php (AJAX)
   Shows ALL students (NEW DB STRUCTURE)
*/

/* =========================
   FETCH STUDENTS + CGPA
========================= */

$query = "
SELECT s.*,
       ROUND(AVG(sm.sgpa),2) AS cgpa,
       COALESCE(SUM(sm.obtained_marks),0) AS totalMarks
FROM students s
LEFT JOIN semester_marks sm
ON s.id = sm.student_id
GROUP BY s.id
ORDER BY s.id DESC
";

$result = $conn->query($query);

while($row = $result->fetch_assoc()){
?>

<div class="card mb-3 shadow-sm">
<div class="card-body">

<!-- CLICKABLE NAME -->
<h5 class="card-title">
<a href="student.php?id=<?php echo $row['id']; ?>"
style="text-decoration:none;color:#000;font-weight:600;">
<?php echo htmlspecialchars($row['name']); ?>
</a>
</h5>

<p class="card-text mb-2">

<b>Roll:</b>
<?php echo htmlspecialchars($row['roll_no']); ?><br>

<b>Branch:</b>
<?php echo htmlspecialchars($row['branch'] ?? 'N/A'); ?><br>

<b>Year:</b>
<?php echo htmlspecialchars($row['year'] ?? 'N/A'); ?><br>

<b>Total Marks:</b>
<?php echo $row['totalMarks']; ?> / 6000 |

<b>CGPA:</b>
<?php echo $row['cgpa'] ?? '0'; ?>

</p>

<?php if(isset($_SESSION['role']) && $_SESSION['role']=="admin"){ ?>

<a href="add_marks.php?id=<?php echo $row['id']; ?>"
class="btn btn-primary btn-sm">
Add Marks
</a>

<a href="add_attendance.php?id=<?php echo $row['id']; ?>"
class="btn btn-warning btn-sm">
Attendance
</a>

<a href="edit_student.php?id=<?php echo $row['id']; ?>"
class="btn btn-info btn-sm">
Edit
</a>

<a href="delete_student.php?id=<?php echo $row['id']; ?>"
class="btn btn-danger btn-sm"
onclick="return confirm('Delete this student?');">
Delete
</a>

<?php } ?>


</div>
</div>

<?php
}
?>
