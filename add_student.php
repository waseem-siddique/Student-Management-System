<?php
header('Content-Type: application/json');
include "db.php";

/* =========================
   VALIDATE INPUT
========================= */

if(
    !isset($_POST['name']) ||
    !isset($_POST['roll']) ||
    !isset($_POST['branch']) ||
    !isset($_POST['year'])
){
    die("Invalid Request");
}

$name   = trim($_POST['name']);
$roll   = trim($_POST['roll']);
$branch = trim($_POST['branch']);
$year   = (int)$_POST['year'];


/* =========================
   AUTO CREATE LOGIN ACCOUNT
========================= */

/* generate username */
$username = strtolower(str_replace(" ","",$name));

/* DEFAULT STUDENT PASSWORD */
$default_password = "1234";

/* HASH PASSWORD (SECURE) */
$password = password_hash($default_password, PASSWORD_DEFAULT);


/* =========================
   ENSURE UNIQUE USERNAME
========================= */

$baseUsername = $username;
$count = 1;

while(true){

    $check = $conn->prepare(
        "SELECT id FROM users WHERE user=?"
    );

    $check->bind_param("s",$username);
    $check->execute();

    if($check->get_result()->num_rows == 0){
        break;
    }

    $username = $baseUsername . $count;
    $count++;
}


/* =========================
   INSERT USER ACCOUNT
========================= */

$userStmt = $conn->prepare("
    INSERT INTO users
    (user, pass, role, must_change_password)
    VALUES (?, ?, 'student', 1)
");

$userStmt->bind_param("ss", $username, $password);
$userStmt->execute();

$user_id = $conn->insert_id;


/* =========================
   INSERT STUDENT PROFILE
========================= */

$studentStmt = $conn->prepare("
    INSERT INTO students
    (user_id, name, roll_no, branch, year)
    VALUES (?,?,?,?,?)
");

$studentStmt->bind_param(
    "isssi",
    $user_id,
    $name,
    $roll,
    $branch,
    $year
);

$studentStmt->execute();

$student_id = $conn->insert_id;


/* =========================
   CREATE EMPTY SEMESTERS
========================= */

$semStmt = $conn->prepare("
    INSERT INTO semester_marks
    (student_id, semester, obtained_marks, sgpa)
    VALUES (?, ?, 0, 0)
");

for($i=1; $i<=6; $i++){
    $semStmt->bind_param("ii", $student_id, $i);
    $semStmt->execute();
}


/* =========================
   CREATE EMPTY ATTENDANCE
========================= */

$attStmt = $conn->prepare("
    INSERT INTO attendance
    (student_id, attendance_percent)
    VALUES (?, 0)
");

$attStmt->bind_param("i",$student_id);
$attStmt->execute();


/* =========================
   SUCCESS RESPONSE
========================= */

echo json_encode([
    "status" => "success",
    "username" => $username,
    "password" => "1234"
]);

?>
