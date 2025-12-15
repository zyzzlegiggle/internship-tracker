<?php
include 'db_connect.php';

$message = "";

// Handle Add Placement
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_placement'])) {
    $student_id = $_POST['student_id'];
    $company_id = $_POST['company_id'];
    $status = $_POST['status'];
    $placement_date = $_POST['placement_date'];

    $stmt = $conn->prepare("INSERT INTO placements (student_id, company_id, status, placement_date) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("iiss", $student_id, $company_id, $status, $placement_date);

    try {
        if ($stmt->execute()) {
            $message = "<div class='alert alert-success alert-dismissible fade show' role='alert'><i class='bi bi-check-circle-fill me-2'></i>Placement recorded successfully<button type='button' class='btn-close' data-bs-dismiss='alert'></button></div>";
        } else {
            $message = "<div class='alert alert-danger alert-dismissible fade show' role='alert'><i class='bi bi-exclamation-triangle-fill me-2'></i>Error: " . $conn->error . "<button type='button' class='btn-close' data-bs-dismiss='alert'></button></div>";
        }
    } catch (Exception $e) {
        $message = "<div class='alert alert-danger alert-dismissible fade show' role='alert'>Error: " . $e->getMessage() . "<button type='button' class='btn-close' data-bs-dismiss='alert'></button></div>";
    }
    $stmt->close();
}

// Handle Update Status
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_status'])) {
    $placement_id = $_POST['placement_id'];
    $new_status = $_POST['new_status'];

    $stmt = $conn->prepare("UPDATE placements SET status=? WHERE id=?");
    $stmt->bind_param("si", $new_status, $placement_id);

    if ($stmt->execute()) {
        $message = "<div class='alert alert-success alert-dismissible fade show' role='alert'><i class='bi bi-check-circle-fill me-2'></i>Status updated successfully<button type='button' class='btn-close' data-bs-dismiss='alert'></button></div>";
    } else {
        $message = "<div class='alert alert-danger alert-dismissible fade show' role='alert'><i class='bi bi-exclamation-triangle-fill me-2'></i>Error: " . $conn->error . "<button type='button' class='btn-close' data-bs-dismiss='alert'></button></div>";
    }
    $stmt->close();
}

// Handle Delete Placement
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $sql = "DELETE FROM placements WHERE id=$id";
    if ($conn->query($sql) === TRUE) {
        $message = "<div class='alert alert-success alert-dismissible fade show' role='alert'><i class='bi bi-trash-fill me-2'></i>Record deleted successfully<button type='button' class='btn-close' data-bs-dismiss='alert'></button></div>";
    } else {
        $message = "<div class='alert alert-danger alert-dismissible fade show' role='alert'><i class='bi bi-exclamation-triangle-fill me-2'></i>Error deleting record: " . $conn->error . "<button type='button' class='btn-close' data-bs-dismiss='alert'></button></div>";
    }
}

// Fetch Students for Dropdown
$students = $conn->query("SELECT id, name, student_id_number FROM students ORDER BY name");

// Fetch Companies for Dropdown
$companies = $conn->query("SELECT id, name FROM companies ORDER BY name");
?>

<?php include 'header.php'; ?>

<div class="row fade-in">
    <div class="col-md-12 mb-4">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h2 class="fw-bold mb-0">Placement Management</h2>
                <p class="text-muted">Track applications and outcomes</p>
            </div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="index.php">Dashboard</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Placements</li>
                </ol>
            </nav>
        </div>
        <?php echo $message; ?>
    </div>
</div>

<div class="row g-4 fade-in delay-1">
    <!-- Add Placement Form -->
    <div class="col-lg-4">
        <div class="glass-card p-4 h-100 border-top border-4 border-success">
            <h4 class="mb-4 text-success fw-bold"><i class="bi bi-bookmark-plus-fill me-2"></i>New Placement</h4>
            <form method="post" action="placements.php">
                <div class="mb-3">
                    <label for="student_id" class="form-label fw-bold small text-uppercase text-muted">Select
                        Student</label>
                    <select class="form-select" id="student_id" name="student_id" required>
                        <option value="">Choose Student...</option>
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
                    <label for="company_id" class="form-label fw-bold small text-uppercase text-muted">Select
                        Company</label>
                    <select class="form-select" id="company_id" name="company_id" required>
                        <option value="">Choose Company...</option>
                        <?php
                        $companies->data_seek(0); // Reset pointer
                        if ($companies->num_rows > 0) {
                            while ($row = $companies->fetch_assoc()) {
                                echo "<option value='" . $row['id'] . "'>" . $row['name'] . "</option>";
                            }
                        }
                        ?>
                    </select>
                </div>
                <div class="mb-3">
                    <label for="status" class="form-label fw-bold small text-uppercase text-muted">Application
                        Status</label>
                    <select class="form-select" id="status" name="status" required>
                        <option value="Applied">📄 Applied</option>
                        <option value="Interview">🗣️ Interview</option>
                        <option value="Placed">✅ Placed</option>
                        <option value="Rejected">❌ Rejected</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label for="placement_date" class="form-label fw-bold small text-uppercase text-muted">Date</label>
                    <input type="date" class="form-control" id="placement_date" name="placement_date"
                        value="<?php echo date('Y-m-d'); ?>" required>
                </div>
                <button type="submit" name="add_placement" class="btn btn-success w-100 mt-2 py-2 fw-bold">
                    <i class="bi bi-check-lg me-1"></i> Save Placement
                </button>
            </form>
        </div>
    </div>

    <!-- Placements List -->
    <div class="col-lg-8">
        <div class="glass-card p-4 h-100">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h4 class="mb-0 text-dark fw-bold"><i class="bi bi-list-check me-2"></i>Records</h4>
                <div class="input-group w-auto">
                    <span class="input-group-text bg-white border-end-0"><i class="bi bi-filter"></i></span>
                    <select class="form-select border-start-0 ps-0" style="max-width: 150px;">
                        <option>All Statuses</option>
                        <option>Placed</option>
                        <option>Interview</option>
                    </select>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Student</th>
                            <th>Company</th>
                            <th>Status</th>
                            <th>Date</th>
                            <th class="text-end">Actions</th>
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
                                // Status styling
                                $statusClass = "bg-secondary";
                                $statusIcon = "";
                                if ($row['status'] == 'Placed') {
                                    $statusClass = "bg-success";
                                    $statusIcon = "<i class='bi bi-check-circle-fill me-1'></i>";
                                }
                                if ($row['status'] == 'Interview') {
                                    $statusClass = "bg-info text-dark";
                                    $statusIcon = "<i class='bi bi-chat-text-fill me-1'></i>";
                                }
                                if ($row['status'] == 'Rejected') {
                                    $statusClass = "bg-danger";
                                    $statusIcon = "<i class='bi bi-x-circle-fill me-1'></i>";
                                }
                                if ($row['status'] == 'Applied') {
                                    $statusClass = "bg-primary";
                                    $statusIcon = "<i class='bi bi-send-fill me-1'></i>";
                                }

                                echo "<tr>";
                                echo "<td class='fw-semibold'>" . htmlspecialchars($row['student_name']) . "</td>";
                                echo "<td>" . htmlspecialchars($row['company_name']) . "</td>";
                                echo "<td><span class='badge $statusClass rounded-pill px-3 py-2'>$statusIcon " . $row['status'] . "</span></td>";
                                echo "<td class='text-muted small'>" . $row['placement_date'] . "</td>";
                                echo "<td class='text-end'>
                                        <form method='post' action='placements.php' style='display:inline-block'>
                                            <input type='hidden' name='placement_id' value='" . $row['id'] . "'>
                                            <select name='new_status' class='form-select form-select-sm d-inline-block w-auto border-0 bg-light' onchange='this.form.submit()' style='width: 110px;'>
                                                <option value='Applied' " . ($row['status'] == 'Applied' ? 'selected' : '') . ">Applied</option>
                                                <option value='Interview' " . ($row['status'] == 'Interview' ? 'selected' : '') . ">Interview</option>
                                                <option value='Placed' " . ($row['status'] == 'Placed' ? 'selected' : '') . ">Placed</option>
                                                <option value='Rejected' " . ($row['status'] == 'Rejected' ? 'selected' : '') . ">Rejected</option>
                                            </select>
                                            <input type='hidden' name='update_status' value='1'>
                                        </form>
                                        <a href='placements.php?delete=" . $row['id'] . "' class='btn btn-sm btn-outline-danger ms-1' onclick='return confirm(\"Are you sure?\")'><i class='bi bi-trash'></i></a>
                                      </td>";
                                echo "</tr>";
                            }
                        } else {
                            echo "<tr><td colspan='5' class='text-center py-5 text-muted'>
                                <i class='bi bi-inbox display-4 d-block mb-3'></i>
                                No placement records found yet.
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