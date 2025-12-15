<?php
include 'db_connect.php';

// Handle Add Student
$message = "";
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_student'])) {
    $name = $_POST['name'];
    $student_id = $_POST['student_id'];
    $email = $_POST['email'];
    $course = $_POST['course'];

    $stmt = $conn->prepare("INSERT INTO students (name, student_id_number, email, course) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $name, $student_id, $email, $course);

    try {
        if ($stmt->execute()) {
            $message = "<div class='alert alert-success alert-dismissible fade show' role='alert'><i class='bi bi-check-circle-fill me-2'></i>Student added successfully<button type='button' class='btn-close' data-bs-dismiss='alert'></button></div>";
        } else {
            $message = "<div class='alert alert-danger alert-dismissible fade show' role='alert'><i class='bi bi-exclamation-triangle-fill me-2'></i>Error: " . $conn->error . "<button type='button' class='btn-close' data-bs-dismiss='alert'></button></div>";
        }
    } catch (Exception $e) {
        $message = "<div class='alert alert-danger alert-dismissible fade show' role='alert'>Error: " . $e->getMessage() . "<button type='button' class='btn-close' data-bs-dismiss='alert'></button></div>";
    }
    $stmt->close();
}

// Handle Delete Student
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $sql = "DELETE FROM students WHERE id=$id";
    if ($conn->query($sql) === TRUE) {
        $message = "<div class='alert alert-success alert-dismissible fade show' role='alert'><i class='bi bi-trash-fill me-2'></i>Student deleted successfully<button type='button' class='btn-close' data-bs-dismiss='alert'></button></div>";
    } else {
        $message = "<div class='alert alert-danger alert-dismissible fade show' role='alert'><i class='bi bi-exclamation-triangle-fill me-2'></i>Error deleting record: " . $conn->error . "<button type='button' class='btn-close' data-bs-dismiss='alert'></button></div>";
    }
}
?>

<?php include 'header.php'; ?>

<div class="row fade-in">
    <div class="col-md-12 mb-4">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h2 class="fw-bold mb-0">Students Management</h2>
                <p class="text-muted">Manage student records and details</p>
            </div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="index.php">Dashboard</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Students</li>
                </ol>
            </nav>
        </div>
        <?php echo $message; ?>
    </div>
</div>

<div class="row g-4 fade-in delay-1">
    <!-- Add Student Form -->
    <div class="col-lg-4">
        <div class="glass-card p-4 h-100">
            <h4 class="mb-4 text-primary fw-bold"><i class="bi bi-person-plus-fill me-2"></i>Add New Student</h4>
            <form method="post" action="students.php">
                <div class="mb-3">
                    <label for="name" class="form-label fw-bold small text-uppercase text-muted">Full Name</label>
                    <input type="text" class="form-control" id="name" name="name" placeholder="e.g. John Doe" required>
                </div>
                <div class="mb-3">
                    <label for="student_id" class="form-label fw-bold small text-uppercase text-muted">Student
                        ID</label>
                    <input type="text" class="form-control" id="student_id" name="student_id" placeholder="e.g. S12345"
                        required>
                </div>
                <div class="mb-3">
                    <label for="email" class="form-label fw-bold small text-uppercase text-muted">Email Address</label>
                    <input type="email" class="form-control" id="email" name="email" placeholder="john@example.com"
                        required>
                </div>
                <div class="mb-3">
                    <label for="course" class="form-label fw-bold small text-uppercase text-muted">Course/Major</label>
                    <select class="form-select" id="course" name="course" required>
                        <option value="">Select Course...</option>
                        <option value="Computer Science">Computer Science</option>
                        <option value="Business Administration">Business Administration</option>
                        <option value="Marketing">Marketing</option>
                        <option value="Engineering">Engineering</option>
                        <option value="Data Science">Data Science</option>
                        <option value="Other">Other</option>
                    </select>
                </div>
                <button type="submit" name="add_student" class="btn btn-primary w-100 mt-2 py-2">
                    <i class="bi bi-plus-lg me-1"></i> Add Record
                </button>
            </form>
        </div>
    </div>

    <!-- Student List -->
    <div class="col-lg-8">
        <div class="glass-card p-4 h-100">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h4 class="mb-0 text-dark fw-bold"><i class="bi bi-people-fill me-2"></i>Registered Students</h4>
                <div class="input-group w-auto">
                    <span class="input-group-text bg-white border-end-0"><i class="bi bi-search"></i></span>
                    <input type="text" class="form-control border-start-0 ps-0" placeholder="Search..."
                        style="max-width: 200px;">
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>ID Number</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Course</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $sql = "SELECT * FROM students ORDER BY id DESC";
                        $result = $conn->query($sql);

                        if ($result->num_rows > 0) {
                            while ($row = $result->fetch_assoc()) {
                                echo "<tr>";
                                echo "<td><span class='badge bg-light text-dark border'>" . $row['student_id_number'] . "</span></td>";
                                echo "<td class='fw-semibold'>" . htmlspecialchars($row['name']) . "</td>";
                                echo "<td class='text-muted'>" . htmlspecialchars($row['email']) . "</td>";
                                echo "<td>" . htmlspecialchars($row['course']) . "</td>";
                                echo "<td class='text-end'>
                                        <button class='btn btn-sm btn-outline-primary me-1'><i class='bi bi-pencil'></i></button>
                                        <a href='students.php?delete=" . $row['id'] . "' class='btn btn-sm btn-outline-danger' onclick='return confirm(\"Are you sure you want to delete this student?\")'><i class='bi bi-trash'></i></a>
                                      </td>";
                                echo "</tr>";
                            }
                        } else {
                            echo "<tr><td colspan='5' class='text-center py-5 text-muted'>
                                <i class='bi bi-folder2-open display-4 d-block mb-3'></i>
                                No students found. Add one to get started!
                            </td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>