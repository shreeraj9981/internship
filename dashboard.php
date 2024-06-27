<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Include your database connection file
require 'connect.php';

// Function to fetch student details
function fetchStudentDetails($conn) {
    $user_id = $_SESSION['user_id'];
    $query = "SELECT * FROM students WHERE user_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->fetch_assoc();
}

// Function to fetch all teachers' details
function fetchAllTeachers($conn) {
    $query = "SELECT * FROM teachers";
    $result = $conn->query($query);
    return $result->fetch_all(MYSQLI_ASSOC);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Dashboard</title>
    <link rel="stylesheet" href="dashboard.css">
</head>
<body>
<div class="dashboard-container">
    <h1>ENGINEERING COLLEGE</h1>
    <nav>
        <ul>
            <?php if ($_SESSION['role'] == 'teacher') { ?>
                <li><a href="viewstddetails.php">Student Details</a></li>
                <li><a href="viewtecdetails.php">Teacher Details</a></li>
            <?php } elseif ($_SESSION['role'] == 'student') { ?>
                <li><a href="#">Your Details</a></li>
                <li><a href="viewtecdetails.php">Teachers Details</a></li>
            <?php } ?>
        </ul>
    </nav>
    <div class="dashboard-content">
        <?php if ($_SESSION['role'] == 'teacher') {
            // Fetch and display all teachers
            $teachers = fetchAllTeachers($conn);
            if ($teachers) {
                echo "<h2>Teachers Details</h2>";
                echo "<ul>";
                foreach ($teachers as $teacher) {
                    echo "<li>{$teacher['name']} - {$teacher['email']} - {$teacher['mobile']} - {$teacher['gender']} - {$teacher['department']} - {$teacher['address']}</li>";
                }
                echo "</ul>";
            }
        } elseif ($_SESSION['role'] == 'student') {
            // Fetch and display student details
            $student = fetchStudentDetails($conn);
            if ($student) {
                echo "<h2>Your Details</h2>";
                echo "<p>Name: {$student['name']}</p>";
                echo "<p>Roll Number: {$student['rollno']}</p>";
                echo "<p>Class: {$student['class']}</p>";
                echo "<p>:Mobile {$student['mobile']}</p>";
                echo "<p>:Gender {$student['gender']}</p>";
                echo "<p>:Address {$student['address']}</p>";
                // Add more fields as per your database structure
            }
            
            // Fetch and display all teachers (for students)
            $teachers = fetchAllTeachers($conn);
            if ($teachers) {
                echo "<h2>Teachers Details</h2>";
                echo "<ul>";
                foreach ($teachers as $teacher) {
                    echo "<li>{$teacher['name']} - {$teacher['email']} - {$teacher['mobile']} - {$teacher['gender']} - {$teacher['department']} - {$teacher['address']} </li>";
                }
                echo "</ul>";
            }
        }
        ?>
    </div>
</div>
</body>
</html>
