<?php
session_start();
include "db.php";

/* =========================
   ADMIN PROTECTION
========================= */

if(!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin'){
    header("Location: login.php");
    exit();
}

/* =========================
   VALIDATE STUDENT ID
========================= */

if(!isset($_GET['id']) || !is_numeric($_GET['id'])){
    die("Invalid student ID");
}

$student_id = (int)$_GET['id'];


/* =========================
   GET LINKED USER ID
========================= */

$getUser = $conn->prepare(
    "SELECT user_id FROM students WHERE id=?"
);

$getUser->bind_param("i",$student_id);
$getUser->execute();

$res = $getUser->get_result();
$data = $res->fetch_assoc();

$user_id = $data['user_id'] ?? null;


/* =========================
   DELETE RELATED DATA FIRST
========================= */

/* delete semester marks */
$delMarks = $conn->prepare(
    "DELETE FROM semester_marks WHERE student_id=?"
);
$delMarks->bind_param("i",$student_id);
$delMarks->execute();

/* delete attendance */
$delAttendance = $conn->prepare(
    "DELETE FROM attendance WHERE student_id=?"
);
$delAttendance->bind_param("i",$student_id);
$delAttendance->execute();


/* =========================
   DELETE STUDENT PROFILE
========================= */

$delStudent = $conn->prepare(
    "DELETE FROM students WHERE id=?"
);

$delStudent->bind_param("i",$student_id);
$delStudent->execute();


/* =========================
   DELETE LOGIN ACCOUNT
========================= */

if($user_id){
    $delUser = $conn->prepare(
        "DELETE FROM users WHERE id=? AND role='student'"
    );
    $delUser->bind_param("i",$user_id);
    $delUser->execute();
}


/* =========================
   REDIRECT BACK
========================= */

header("Location: dashboard.php");
exit();
?>
