<?php
include 'db_connect.php';

$message = "";

// Handle Add Placement
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_placement'])) {
    $student_id = $_POST['student_id'];
    $company_id = $_POST['company_id'];
    $status = $_POST['status'];
    $placement_date = $_POST['placement_date'];

    $sql = "INSERT INTO placements (student_id, company_id, status, placement_date) VALUES ('$student_id', '$company_id', '$status', '$placement_date')";

    if ($conn->query($sql) === TRUE) {
        $message = "<div class='alert alert-success'>Placement record added successfully</div>";
    } else {
        $message = "<div class='alert alert-danger'>Error: " . $conn->error . "</div>";
    }
}

// Handle Update Status
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_status'])) {
    $placement_id = $_POST['placement_id'];
    $new_status = $_POST['new_status'];

    $sql = "UPDATE placements SET status='$new_status' WHERE id=$placement_id";
    if ($conn->query($sql) === TRUE) {
        $message = "<div class='alert alert-success'>Status updated successfully</div>";
    } else {
        $message = "<div class='alert alert-danger'>Error: " . $conn->error . "</div>";
    }
}

// Handle Delete Placement
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $sql = "DELETE FROM placements WHERE id=$id";
    if ($conn->query($sql) === TRUE) {
        $message = "<div class='alert alert-success'>Placement deleted successfully</div>";
    } else {
        $message = "<div class='alert alert-danger'>Error deleting record: " . $conn->error . "</div>";
    }
}

// Fetch Students for Dropdown
$students = $conn->query("SELECT id, name, student_id_number FROM students ORDER BY name");

// Fetch Companies for Dropdown
$companies = $conn->query("SELECT id, name FROM companies ORDER BY name");
?>

<?php include 'header.php'; ?>

<div class="row">
    <div class="col-md-12">
        <h2 class="mb-4">Placement Management</h2>
        <?php echo $message; ?>
    </div>
</div>

<div class="row">
    <!-- Add Placement Form -->
    <div class="col-md-4">
        <div class="card mb-4">
            <div class="card-header bg-success text-white">
                Add New Placement
            </div>
            <div class="card-body">
                <form method="post" action="placements.php">
                    <div class="mb-3">
                        <label for="student_id" class="form-label">Student</label>
                        <select class="form-control" id="student_id" name="student_id" required>
                            <option value="">Select Student</option>
                            <?php
                            if ($students->num_rows > 0) {
                                while ($row = $students->fetch_assoc()) {
                                    echo "<option value='" . $row['id'] . "'>" . $row['name'] . " (" . $row['student_id_number'] . ")</option>";
                                }
                            }
                            ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="company_id" class="form-label">Company</label>
                        <select class="form-control" id="company_id" name="company_id" required>
                            <option value="">Select Company</option>
                            <?php
                            $companies->data_seek(0); // Reset pointer if needed
                            if ($companies->num_rows > 0) {
                                while ($row = $companies->fetch_assoc()) {
                                    echo "<option value='" . $row['id'] . "'>" . $row['name'] . "</option>";
                                }
                            }
                            ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="status" class="form-label">Status</label>
                        <select class="form-control" id="status" name="status" required>
                            <option value="Applied">Applied</option>
                            <option value="Interview">Interview</option>
                            <option value="Placed">Placed</option>
                            <option value="Rejected">Rejected</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="placement_date" class="form-label">Date</label>
                        <input type="date" class="form-control" id="placement_date" name="placement_date"
                            value="<?php echo date('Y-m-d'); ?>" required>
                    </div>
                    <button type="submit" name="add_placement" class="btn btn-success w-100">Add Placement</button>
                </form>
            </div>
        </div>
    </div>

    <!-- Placements List -->
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                Placement Records
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Student</th>
                                <th>Company</th>
                                <th>Status</th>
                                <th>Date</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $sql = "SELECT p.id, p.status, p.placement_date, s.name as student_name, c.name as company_name 
                                    FROM placements p
                                    JOIN students s ON p.student_id = s.id
                                    JOIN companies c ON p.company_id = c.id
                                    ORDER BY p.placement_date DESC";
                            $result = $conn->query($sql);

                            if ($result && $result->num_rows > 0) {
                                while ($row = $result->fetch_assoc()) {
                                    // Status Badge Color
                                    $badge = "secondary";
                                    if ($row['status'] == 'Placed')
                                        $badge = "success";
                                    if ($row['status'] == 'Interview')
                                        $badge = "info";
                                    if ($row['status'] == 'Rejected')
                                        $badge = "danger";

                                    echo "<tr>";
                                    echo "<td>" . $row['id'] . "</td>";
                                    echo "<td>" . htmlspecialchars($row['student_name']) . "</td>";
                                    echo "<td>" . htmlspecialchars($row['company_name']) . "</td>";
                                    echo "<td><span class='badge bg-$badge'>" . $row['status'] . "</span></td>";
                                    echo "<td>" . $row['placement_date'] . "</td>";
                                    echo "<td>
                                            <form method='post' action='placements.php' style='display:inline-block'>
                                                <input type='hidden' name='placement_id' value='" . $row['id'] . "'>
                                                <select name='new_status' class='form-select form-select-sm d-inline-block w-auto' onchange='this.form.submit()'>
                                                    <option value='Applied' " . ($row['status'] == 'Applied' ? 'selected' : '') . ">Applied</option>
                                                    <option value='Rejected' " . ($row['status'] == 'Rejected' ? 'selected' : '') . ">Rejected</option>
                                                    <option value='Interview' " . ($row['status'] == 'Interview' ? 'selected' : '') . ">Interview</option>
                                                    <option value='Placed' " . ($row['status'] == 'Placed' ? 'selected' : '') . ">Placed</option>
                                                </select>
                                                <input type='hidden' name='update_status' value='1'>
                                            </form>
                                            <a href='placements.php?delete=" . $row['id'] . "' class='btn btn-sm btn-danger ms-2' onclick='return confirm(\"Are you sure?\")'>Delete</a>
                                          </td>";
                                    echo "</tr>";
                                }
                            } else {
                                echo "<tr><td colspan='6' class='text-center'>No placements found</td></tr>";
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