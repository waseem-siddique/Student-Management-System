<?php
session_start();
include "db.php";

/* =========================
   GET SEARCH QUERY
========================= */

$q = $_GET['q'] ?? '';

/* =========================
   SEARCH STUDENTS (NEW DB)
========================= */

$stmt = $conn->prepare("
SELECT s.*,
       ROUND(AVG(sm.sgpa),2) AS cgpa,
       COALESCE(SUM(sm.obtained_marks),0) AS totalMarks
FROM students s
LEFT JOIN semester_marks sm
ON s.id = sm.student_id
WHERE s.name LIKE CONCAT('%', ?, '%')
   OR s.roll_no LIKE CONCAT('%', ?, '%')
   OR s.branch LIKE CONCAT('%', ?, '%')
GROUP BY s.id
ORDER BY s.id DESC
");

$stmt->bind_param("sss", $q, $q, $q);
$stmt->execute();

$result = $stmt->get_result();

while($row = $result->fetch_assoc()){
?>

<div class="card mb-3 shadow-sm">
<div class="card-body">

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
<?php echo htmlspecialchars($row['branch']); ?><br>

<b>Year:</b>
<?php echo htmlspecialchars($row['year']); ?><br>

<b>Total Marks:</b>
<?php echo $row['totalMarks']; ?> / 6000 |

<b>CGPA:</b>
<?php echo $row['cgpa'] ?? '0'; ?>

</p>

<?php if(isset($_SESSION['role']) && $_SESSION['role']=="admin"){ ?>
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
