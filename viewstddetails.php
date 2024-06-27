<?php
session_start();
require 'connect.php';

// Redirect to login page if user is not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Determine the SQL query based on user role
if ($_SESSION['role'] == 'teacher') {
    $stmt = $conn->prepare("SELECT * FROM students");
} else {
    $stmt = $conn->prepare("SELECT * FROM students WHERE user_id = ?");
    $stmt->bind_param("i", $_SESSION['user_id']);
}
$stmt->execute();
$result = $stmt->get_result();

// Handle delete request
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['delete'])) {
    $name = $_POST['name'];
    $stmt = $conn->prepare("DELETE FROM students WHERE name = ?");
    $stmt->bind_param("s", $name);
    $stmt->execute();
    // Redirect to avoid resubmission on page refresh
    header("Location: viewstddetails.php");
    exit();
}

// Handle edit submission
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['edit'])) {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $class = $_POST['Class']; // Ensure 'Class' matches the input name
    $mobile = $_POST['mobile'];
    $gender = $_POST['gender'];
    $address = $_POST['address'];

    $stmt = $conn->prepare("UPDATE students SET email = ?, class = ?, mobile = ?, gender = ?, address = ? WHERE name = ?");
    $stmt->bind_param("ssssss", $email, $class, $mobile, $gender, $address, $name);
    $stmt->execute();
    // Redirect to avoid resubmission on page refresh
    header("Location: viewstddetails.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Student Details</title>
    <link rel="stylesheet" href="studdetails.css">
</head>
<body>
<div class="details-container">
    <h2>Student Details</h2>
    
    <!-- Logout button -->
    <form action="logout.php" method="post" style="text-align:right;">
        <button type="submit">Logout</button>
    </form>
    
    <table>
        <tr>
            <th>Name</th>
            <th>Email</th>
            <th>Rollno</th>
            <th>Class</th>
            <th>Mobile</th>
            <th>Gender</th>
            <th>Address</th>
            <?php if ($_SESSION['role'] == 'teacher') { ?>
                <th>Actions</th>
            <?php } ?>
        </tr>
        <?php while ($row = $result->fetch_assoc()) { ?>
            <tr>
                <td><?php echo htmlspecialchars($row['name']); ?></td>
                <td><?php echo htmlspecialchars($row['email']); ?></td>
                <td><?php echo htmlspecialchars($row['rollno']); ?></td>
                <td><?php echo htmlspecialchars($row['class']); ?></td>
                <td><?php echo htmlspecialchars($row['mobile']); ?></td>
                <td><?php echo htmlspecialchars($row['gender']); ?></td>
                <td><?php echo htmlspecialchars($row['address']); ?></td>
                <td>
                    <?php if ($_SESSION['role'] == 'teacher') { ?>
                        <!-- Edit button -->
                        <button onclick="editStudent(<?php echo htmlspecialchars(json_encode($row)); ?>)">Edit</button>
                        
                        <!-- Delete form -->
                        <form action="viewstddetails.php" method="POST" style="display:inline;">
                            <input type="hidden" name="name" value="<?php echo $row['name']; ?>">
                            <input type="hidden" name="delete" value="1">
                            <input type="submit" value="Delete" class="delete-button">
                        </form>
                    <?php } ?>
                </td>
            </tr>
        <?php } ?>
    </table>
    
    <!-- Edit Student Form (Initially hidden) -->
    <div id="editFormContainer" style="display: none;">
        <h3>Edit Student</h3>
        <form id="editForm" action="viewstddetails.php" method="POST">
            <input type="hidden" name="edit" value="1">
            <input type="hidden" name="name" id="editName" value="">
            <div>
                <label for="editEmail">Email:</label>
                <input type="email" id="editEmail" name="email" required>
            </div>
            <div>
                <label for="editClass">Class:</label>
                <input type="text" id="editClass" name="Class" required>
            </div>
            <div>
                <label for="editMobile">Mobile:</label>
                <input type="text" id="editMobile" name="mobile" required>
            </div>
            <div>
                <label for="editGender">Gender:</label>
                <select id="editGender" name="gender" required>
                    <option value="Male">Male</option>
                    <option value="Female">Female</option>
                    <option value="Other">Other</option>
                </select>
            </div>
            <div>
                <label for="editAddress">Address:</label>
                <input type="text" id="editAddress" name="address" required>
            </div>
            <button type="submit">Update</button>
            <button type="button" onclick="cancelEdit()">Cancel</button>
        </form>
    </div>
    
    <script>
        function editStudent(student) {
            // Populate the edit form with student data
            document.getElementById('editName').value = student.name;
            document.getElementById('editEmail').value = student.email;
            document.getElementById('editClass').value = student.class;
            document.getElementById('editMobile').value = student.mobile;
            document.getElementById('editGender').value = student.gender;
            document.getElementById('editAddress').value = student.address;

            // Show the edit form
            document.getElementById('editFormContainer').style.display = 'block';
        }

        function cancelEdit() {
            // Hide the edit form
            document.getElementById('editFormContainer').style.display = 'none';
        }
    </script>
</div>
</body>
</html>
