<?php
session_start();
require 'connect.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Fetch teacher details for all roles
$stmt = $conn->prepare("
    SELECT t.*, GROUP_CONCAT(tc.class ORDER BY tc.class SEPARATOR ', ') AS classes, GROUP_CONCAT(tc.subject ORDER BY tc.subject SEPARATOR ', ') AS subjects
    FROM teachers t
    LEFT JOIN teacher_classes tc ON t.id = tc.teacher_id
    GROUP BY t.id
");
$stmt->execute();
$result = $stmt->get_result();

// Handle delete request (only for teachers)
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['delete']) && $_SESSION['role'] == 'teacher') {
    $id = $_POST['id'];
    $stmt = $conn->prepare("DELETE FROM teachers WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    // Redirect to avoid resubmission on page refresh
    header("Location: viewtecdetails.php");
    exit();
}

// Handle edit submission (only for teachers)
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['edit']) && $_SESSION['role'] == 'teacher') {
    $id = $_POST['id'];
    $email = $_POST['email'];
    $department = $_POST['department'];
    $mobile = $_POST['mobile'];
    $gender = $_POST['gender'];
    $address = $_POST['address'];
    $classes = $_POST['classes'];
    $subjects = $_POST['subjects'];

    $stmt = $conn->prepare("UPDATE teachers SET email = ?, department = ?, mobile = ?, gender = ?, address = ? ,classes = ?,subjects = ? WHERE id = ?");
    $stmt->bind_param("sssssssi", $email, $department, $mobile, $gender, $address,$classes,$subjects, $id);
    $stmt->execute();
    // Redirect to avoid resubmission on page refresh
    header("Location: viewtecdetails.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Teacher Details</title>
    <link rel="stylesheet" href="teacdetails.css">
</head>
<body>
<div class="details-container">
    <h2>Teacher Details</h2>
    
    <!-- Logout button -->
    <form action="logout.php" method="post" style="text-align:right;">
        <button type="submit">Logout</button>
    </form>
    
    <table>
        <tr>
            <th>Name</th>
            <th>Email</th>
            <th>Mobile</th>
            <th>Gender</th>
            <th>Department</th>
            <th>Address</th>
            <th>Classes</th>
            <th>Subjects</th>
            <?php if ($_SESSION['role'] == 'teacher') { ?>
                <th>Actions</th>
            <?php } ?>
        </tr>
        <?php while ($row = $result->fetch_assoc()) { ?>
            <tr>
                <td><?php echo htmlspecialchars($row['name']); ?></td>
                <td><?php echo htmlspecialchars($row['email']); ?></td>
                <td><?php echo htmlspecialchars($row['mobile']); ?></td>
                <td><?php echo htmlspecialchars($row['gender']); ?></td>
                <td><?php echo htmlspecialchars($row['department']); ?></td>
                <td><?php echo htmlspecialchars($row['address']); ?></td>
                <td><?php echo htmlspecialchars($row['classes']); ?></td>
                <td><?php echo htmlspecialchars($row['subjects']); ?></td>
                <td>
                    <?php if ($_SESSION['role'] == 'teacher') { ?>
                        <!-- Edit button -->
                        <button onclick="editTeacher(<?php echo htmlspecialchars(json_encode($row)); ?>)">Edit</button>
                        
                        <!-- Delete form -->
                        <form action="viewtecdetails.php" method="POST" style="display:inline;">
                            <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
                            <input type="hidden" name="delete" value="1">
                            <input type="submit" value="Delete" class="delete-button">
                        </form>
                    <?php } ?>
                </td>
            </tr>
        <?php } ?>
    </table>
    
    <!-- Edit Teacher Form (Initially hidden) -->
    <div id="editFormContainer" style="display: none;">
        <h3>Edit Teacher</h3>
        <form id="editForm" action="viewtecdetails.php" method="POST">
            <input type="hidden" name="edit" value="1">
            <input type="hidden" name="id" id="editId" value="">
            <div>
                <label for="editEmail">Email:</label>
                <input type="email" id="editEmail" name="email" required>
            </div>
            <div>
                <label for="editDepartment">Department:</label>
                <input type="text" id="editDepartment" name="department" required>
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
            <div>
                <label for="editClasses">Classes:</label>
                <input type="text" id="editClasses" name="classes" required>
            </div>
            <div>
                <label for="editSubjects">Subjects:</label>
                <input type="text" id="editSubjects" name="subjects" required>
            </div>
            <button type="submit">Update</button>
            <button type="button" onclick="cancelEdit()">Cancel</button>
        </form>
    </div>
    
    <script>
        function editTeacher(teacher) {
            // Populate the edit form with teacher data
            document.getElementById('editId').value = teacher.id;
            document.getElementById('editEmail').value = teacher.email;
            document.getElementById('editDepartment').value = teacher.department;
            document.getElementById('editMobile').value = teacher.mobile;
            document.getElementById('editGender').value = teacher.gender;
            document.getElementById('editAddress').value = teacher.address;
            document.getElementById('editClasses').value = teacher.classes;
            document.getElementById('editSubjects').value = teacher.subjects;

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
