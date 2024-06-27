<?php
require 'connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];
    $email = $_POST['id'];
    $role = $_POST['role'];

    $stmt = $conn->prepare("INSERT INTO users (username, password, id, role) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssis", $username, $password, $email, $role);
    
    if ($stmt->execute()) {
        // Determine where to redirect based on role
        if ($role === 'teacher') {
            header("Location: teacherform.php");
        } elseif ($role === 'student') {
            header("Location: studentform.php");
        } else {
            // Handle unknown role scenario
            header("Location: login.php?signup=success");
        }
        exit();
    } else {
        echo "Error: " . $stmt->error;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Sign Up</title>
    <link rel="stylesheet" href="signup.css">
</head>
<body>
    <h2>Sign Up</h2>
    <form action="signup.php" method="POST">
        <div>
            <label for="id">ID:</label>
            <input type="text" id="id" name="id" required>
        </div>
        <div>
            <label for="username">Username:</label>
            <input type="text" id="username" name="username" required>
        </div>
        <div>
            <label for="password">Password:</label>
            <input type="password" id="password" name="password" required>
        </div>
        <div>
            <label for="role">Role:</label>
            <select id="role" name="role" required>
                <option value="teacher">Teacher</option>
                <option value="student">Student</option>
            </select>
        </div>
        <button type="submit">Sign Up</button>
    </form>
</body>
</html>
