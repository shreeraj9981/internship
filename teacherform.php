<?php
session_start();
require 'connect.php';

if ($_SESSION['role'] != 'teacher') {
    header("Location: dashboard.php");
    exit();
}

// Fetch classes and subjects data from database or define statically
$classes = [
    "Class 1" => ["Subject 1-1", "Subject 1-2", "Subject 1-3", "Subject 1-4", "Subject 1-5"],
    "Class 2" => ["Subject 2-1", "Subject 2-2", "Subject 2-3", "Subject 2-4", "Subject 2-5"],
    "Class 3" => ["Subject 3-1", "Subject 3-2", "Subject 3-3", "Subject 3-4", "Subject 3-5"],
    "Class 4" => ["Subject 4-1", "Subject 4-2", "Subject 4-3", "Subject 4-4", "Subject 4-5"],
    "Class 5" => ["Subject 5-1", "Subject 5-2", "Subject 5-3", "Subject 5-4", "Subject 5-5"]
];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $department = $_POST['department'];
    $mobile = $_POST['mobile'];
    $gender = $_POST['gender'];
    $address = $_POST['address'];
    $user_id = $_POST['user_id'];
    $class_subjects = isset($_POST['class_subjects']) ? $_POST['class_subjects'] : [];

    $stmt = $conn->prepare("INSERT INTO teachers (name, email, department, mobile, gender, address, user_id) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssssi", $name, $email, $department, $mobile, $gender, $address, $user_id);
    $stmt->execute();
    $teacher_id = $conn->insert_id;

    foreach ($class_subjects as $class_subject) {
        list($class, $subject) = explode('-', $class_subject);
        $stmt = $conn->prepare("INSERT INTO teacher_classes (teacher_id, class, subject) VALUES (?, ?, ?)");
        $stmt->bind_param("iss", $teacher_id, $class, $subject);
        $stmt->execute();
    }

    header("Location: viewtecdetails.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>New Teacher</title>
    <link rel="stylesheet" href="teacherform.css">
    <script>
        // Function to update subjects checkboxes based on selected class
        function updateSubjects() {
            var classesContainer = document.getElementById("classes_container");
            var subjectsContainer = document.getElementById("subjects_container");
            var selectedClasses = document.querySelectorAll('input[name="class[]"]:checked');

            // Clear existing subjects checkboxes
            subjectsContainer.innerHTML = '';

            // Populate subjects checkboxes based on selected classes
            selectedClasses.forEach(function(classCheckbox) {
                var classValue = classCheckbox.value;
                var subjects = <?php echo json_encode($classes); ?>;

                subjects[classValue].forEach(function(subject) {
                    var label = document.createElement("label");
                    var checkbox = document.createElement("input");
                    checkbox.type = "checkbox";
                    checkbox.name = "class_subjects[]";
                    checkbox.value = classValue + '-' + subject;
                    label.appendChild(checkbox);
                    label.appendChild(document.createTextNode(subject));
                    subjectsContainer.appendChild(label);
                });
            });
        }

        // Call updateSubjects() initially to populate subjects based on default classes
        window.onload = updateSubjects;
    </script>
</head>
<body>
<div class="form-container">
    <h2>New Teacher</h2>
    <form method="POST" action="teacherform.php">
        <div class="form-group">
            <label for="name">Name</label>
            <input type="text" id="name" name="name" placeholder="Name" required>
        </div>
        <div class="form-group">
            <label for="email">Email</label>
            <input type="email" id="email" name="email" placeholder="Email" required>
        </div>
        <div class="form-group">
            <label for="department">Department</label>
            <input type="text" id="department" name="department" placeholder="Department" required>
        </div>
        <div class="form-group">
            <label for="mobile">Mobile</label>
            <input type="text" id="mobile" name="mobile" placeholder="Mobile" required>
        </div>
        <div class="form-group">
            <label for="gender">Gender</label>
            <select id="gender" name="gender" required>
                <option value="" disabled selected>Select Gender</option>
                <option value="Male">Male</option>
                <option value="Female">Female</option>
                <option value="Other">Other</option>
            </select>
        </div>
        <div class="form-group">
            <label for="address">Address</label>
            <input type="text" id="address" name="address" placeholder="Address" required>
        </div>
        <div class="form-group" id="classes_container">
            <label>Select Classes</label><br>
            <?php foreach ($classes as $class => $subjects) { ?>
                <input type="checkbox" name="class[]" value="<?php echo $class; ?>" onchange="updateSubjects()">
                <label><?php echo $class; ?></label><br>
            <?php } ?>
        </div>
        <div class="form-group" id="subjects_container">
            <!-- Subjects checkboxes will be dynamically populated based on selected classes -->
        </div>
        <input type="hidden" name="user_id" value="<?php echo $_SESSION['user_id']; ?>">

        <button type="submit">Submit</button>
    </form>
</div>
</body>
</html>
