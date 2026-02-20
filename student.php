<?php
include "db.php";

/* =========================
   VALIDATE STUDENT ID
========================= */

if(!isset($_GET['id']) || !is_numeric($_GET['id'])){
    die("Invalid student ID");
}

$id = (int)$_GET['id'];

/* =========================
   FETCH STUDENT PROFILE
========================= */

$stmt = $conn->prepare(
    "SELECT * FROM students WHERE id=?"
);

$stmt->bind_param("i",$id);
$stmt->execute();

$result = $stmt->get_result();
$row = $result->fetch_assoc();

if(!$row){
    die("Student not found");
}

/* =========================
   FETCH SEMESTER DATA
========================= */

$semData = [];
$totalMarks = 0;

$semQuery = $conn->prepare("
    SELECT semester, obtained_marks, sgpa
    FROM semester_marks
    WHERE student_id=?
    ORDER BY semester
");

$semQuery->bind_param("i",$id);
$semQuery->execute();

$semResult = $semQuery->get_result();

while($sem = $semResult->fetch_assoc()){
    $semData[$sem['semester']] = $sem;
    $totalMarks += (int)$sem['obtained_marks'];
}

/* =========================
   CGPA
========================= */

$cgpaQuery = $conn->prepare("
    SELECT ROUND(AVG(sgpa),2) AS cgpa
    FROM semester_marks
    WHERE student_id=?
");

$cgpaQuery->bind_param("i",$id);
$cgpaQuery->execute();

$cgpaResult = $cgpaQuery->get_result()->fetch_assoc();
$cgpa = $cgpaResult['cgpa'] ?? 0;

?>

<!DOCTYPE html>
<html>
<head>

<title>Student Profile</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

</head>

<body>

<?php include "header.php"; ?>

<div class="container mt-5">

<div class="card shadow p-4">

<h3><?php echo htmlspecialchars($row['name']); ?></h3>
<hr>

<p><b>Roll Number:</b> <?php echo htmlspecialchars($row['roll_no']); ?></p>
<p><b>Branch:</b> <?php echo htmlspecialchars($row['branch']); ?></p>
<p><b>Year:</b> <?php echo htmlspecialchars($row['year']); ?></p>

<hr>

<h5>Semester Performance</h5>

<?php for($i=1;$i<=6;$i++): 
    $marks = $semData[$i]['obtained_marks'] ?? 0;
    $sgpa  = $semData[$i]['sgpa'] ?? 0;
?>

<p>
Semester <?php echo $i; ?> :
<?php echo $marks; ?>/1000
&nbsp; | SGPA: <?php echo $sgpa; ?>
</p>

<?php endfor; ?>

<hr>

<h5>Total Marks: <?php echo $totalMarks; ?> / 6000</h5>
<h4 class="text-success">Final CGPA: <?php echo $cgpa; ?></h4>

<a href="dashboard.php" class="btn btn-primary mt-3">
Back to Dashboard
</a>

</div>

</div>

<?php include "footer.php"; ?>

</body>
</html>
