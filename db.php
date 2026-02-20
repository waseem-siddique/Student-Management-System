<?php
ini_set('display_errors',0);

$conn = new mysqli(
    "sql104.infinityfree.com",
    "if0_41199526",
    "iCPJkqjPfF",
    "if0_41199526_sms_project"
);

if($conn->connect_error){
    die("Connection Failed: " . $conn->connect_error);
}
?>
