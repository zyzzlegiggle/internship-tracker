<?php
include 'db_connect.php';

// Handle Add Student
$message = "";
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_student'])) {
    $name = $_POST['name'];
    $student_id = $_POST['student_id'];
    $email = $_POST['email'];
    $course = $_POST['course'];

    $sql = "INSERT INTO students (name, student_id_number, email, course) VALUES ('$name', '$student_id', '$email', '$course')";

    try {
        if ($conn->query($sql) === TRUE) {
            $message = "<div class='alert alert-success'>Student added successfully</div>";
        } else {
            $message = "<div class='alert alert-danger'>Error: " . $conn->error . "</div>";
        }
    } catch (Exception $e) {
        $message = "<div class='alert alert-danger'>Error: " . $e->getMessage() . "</div>";
    }
}

// Handle Delete Student
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $sql = "DELETE FROM students WHERE id=$id";
    if ($conn->query($sql) === TRUE) {
        $message = "<div class='alert alert-success'>Student deleted successfully</div>";
    } else {
        $message = "<div class='alert alert-danger'>Error deleting record: " . $conn->error . "</div>";
    }
}
?>

<?php include 'header.php'; ?>

<div class="row">
    <div class="col-md-12">
        <h2 class="mb-4">Students Management</h2>
        <?php echo $message; ?>
    </div>
</div>

<div class="row">
    <!-- Add Student Form -->
    <div class="col-md-4">
        <div class="card mb-4">
            <div class="card-header bg-primary text-white">
                Add New Student
            </div>
            <div class="card-body">
                <form method="post" action="students.php">
                    <div class="mb-3">
                        <label for="name" class="form-label">Full Name</label>
                        <input type="text" class="form-control" id="name" name="name" required>
                    </div>
                    <div class="mb-3">
                        <label for="student_id" class="form-label">Student ID Details</label>
                        <input type="text" class="form-control" id="student_id" name="student_id" required>
                    </div>
                    <div class="mb-3">
                        <label for="email" class="form-label">Email Address</label>
                        <input type="email" class="form-control" id="email" name="email" required>
                    </div>
                    <div class="mb-3">
                        <label for="course" class="form-label">Course/Major</label>
                        <input type="text" class="form-control" id="course" name="course" required>
                    </div>
                    <button type="submit" name="add_student" class="btn btn-primary w-100">Add Student</button>
                </form>
            </div>
        </div>
    </div>

    <!-- Student List -->
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                Registered Students
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>ID Number</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Course</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $sql = "SELECT * FROM students ORDER BY id DESC";
                            $result = $conn->query($sql);

                            if ($result->num_rows > 0) {
                                while ($row = $result->fetch_assoc()) {
                                    echo "<tr>";
                                    echo "<td>" . $row['id'] . "</td>";
                                    echo "<td>" . $row['student_id_number'] . "</td>";
                                    echo "<td>" . htmlspecialchars($row['name']) . "</td>";
                                    echo "<td>" . htmlspecialchars($row['email']) . "</td>";
                                    echo "<td>" . htmlspecialchars($row['course']) . "</td>";
                                    echo "<td>
                                            <a href='students.php?delete=" . $row['id'] . "' class='btn btn-sm btn-danger' onclick='return confirm(\"Are you sure?\")'>Delete</a>
                                          </td>";
                                    echo "</tr>";
                                }
                            } else {
                                echo "<tr><td colspan='6' class='text-center'>No students found</td></tr>";
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>