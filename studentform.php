<?php
session_start();
require 'connect.php';

if ($_SESSION['role'] != 'teacher') {
    header("Location: dashboard.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $rollno = $_POST['Rollno'];
    $class = $_POST['Class'];
    $mobile = $_POST['Mobile'];
    $gender = $_POST['Gender'];
    $address = $_POST['Address'];
    $user_id = $_POST['user_id'];

    $stmt = $conn->prepare("INSERT INTO students (name, email, rollno, class, mobile, gender, address, user_id) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sssssssi", $name, $email, $rollno, $class, $mobile, $gender, $address, $user_id);
    $stmt->execute();

    header("Location: viewstddetails.php");
    exit(); // Always exit after header redirect
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>New Student</title>
    <link rel="stylesheet" href="studentform.css">
</head>
<body>
<div class="form-container">
    <h2>New Student</h2>
    <form method="POST" action="studentform.php">
        <label for="name">Name:</label><br>
        <input type="text" id="name" name="name" placeholder="Enter your name" required><br><br>
        
        <label for="email">Email:</label><br>
        <input type="email" id="email" name="email" placeholder="Enter your email" required><br><br>
        
        <label for="Rollno">Roll Number:</label><br>
        <input type="text" id="Rollno" name="Rollno" placeholder="Enter your roll number" required><br><br>
        
        <label for="Class">Class:</label><br>
        <select id="Class" name="Class" required>
            <option value="Class A">Class A</option>
            <option value="Class B">Class B</option>
            <option value="Class C">Class C</option>
            <option value="Class D">Class D</option>
            <option value="Class E">Class E</option>
        </select><br><br>
        
        <label for="Mobile">Mobile:</label><br>
        <input type="text" id="Mobile" name="Mobile" placeholder="Enter your mobile number" required><br><br>
        
        <label for="Address">Address:</label><br>
        <textarea id="Address" name="Address" placeholder="Enter your address" required></textarea><br><br>
        
        <label for="Gender">Gender:</label><br>
        <select id="Gender" name="Gender" required>
            <option value="Male">Male</option>
            <option value="Female">Female</option>
            <option value="Other">Other</option>
        </select><br><br>
        
        <input type="hidden" name="user_id" value="<?php echo $_SESSION['user_id']; ?>">
        <button type="submit">Submit</button>
    </form>
</div>
</body>
</html>
