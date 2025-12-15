<?php
include 'db_connect.php';

$message = "";

// Handle Add Company
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_company'])) {
    $name = $_POST['name'];
    $industry = $_POST['industry'];
    $email = $_POST['email'];
    $address = $_POST['address'];

    $stmt = $conn->prepare("INSERT INTO companies (name, industry, email, address) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $name, $industry, $email, $address);

    try {
        if ($stmt->execute()) {
            $message = "<div class='alert alert-success alert-dismissible fade show' role='alert'><i class='bi bi-check-circle-fill me-2'></i>Company added successfully<button type='button' class='btn-close' data-bs-dismiss='alert'></button></div>";
        } else {
            $message = "<div class='alert alert-danger alert-dismissible fade show' role='alert'><i class='bi bi-exclamation-triangle-fill me-2'></i>Error: " . $conn->error . "<button type='button' class='btn-close' data-bs-dismiss='alert'></button></div>";
        }
    } catch (Exception $e) {
        $message = "<div class='alert alert-danger alert-dismissible fade show' role='alert'>Error: " . $e->getMessage() . "<button type='button' class='btn-close' data-bs-dismiss='alert'></button></div>";
    }
    $stmt->close();
}

// Handle Delete Company
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $sql = "DELETE FROM companies WHERE id=$id";
    if ($conn->query($sql) === TRUE) {
        $message = "<div class='alert alert-success alert-dismissible fade show' role='alert'><i class='bi bi-trash-fill me-2'></i>Company deleted successfully<button type='button' class='btn-close' data-bs-dismiss='alert'></button></div>";
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
                <h2 class="fw-bold mb-0">Companies Management</h2>
                <p class="text-muted">Manage industry partners and organizations</p>
            </div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="index.php">Dashboard</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Companies</li>
                </ol>
            </nav>
        </div>
        <?php echo $message; ?>
    </div>
</div>

<div class="row g-4 fade-in delay-1">
    <!-- Add Company Form -->
    <div class="col-lg-4">
        <div class="glass-card p-4 h-100 border-top border-4 border-warning">
            <h4 class="mb-4 text-warning fw-bold"><i class="bi bi-building-add me-2"></i>Register Company</h4>
            <form method="post" action="companies.php">
                <div class="mb-3">
                    <label for="name" class="form-label fw-bold small text-uppercase text-muted">Company Name</label>
                    <input type="text" class="form-control" id="name" name="name" placeholder="e.g. Acme Corp" required>
                </div>
                <div class="mb-3">
                    <label for="industry" class="form-label fw-bold small text-uppercase text-muted">Industry</label>
                    <input type="text" class="form-control" id="industry" name="industry" placeholder="e.g. Technology"
                        required>
                </div>
                <div class="mb-3">
                    <label for="email" class="form-label fw-bold small text-uppercase text-muted">Contact Email</label>
                    <input type="email" class="form-control" id="email" name="email" placeholder="contact@company.com"
                        required>
                </div>
                <div class="mb-3">
                    <label for="address" class="form-label fw-bold small text-uppercase text-muted">Address</label>
                    <textarea class="form-control" id="address" name="address" rows="3"
                        placeholder="Company Headquarters..."></textarea>
                </div>
                <button type="submit" name="add_company" class="btn btn-warning w-100 mt-2 py-2 text-dark fw-bold">
                    <i class="bi bi-plus-lg me-1"></i> Register Company
                </button>
            </form>
        </div>
    </div>

    <!-- Company List -->
    <div class="col-lg-8">
        <div class="glass-card p-4 h-100">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h4 class="mb-0 text-dark fw-bold"><i class="bi bi-buildings-fill me-2"></i>Partner Companies</h4>
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
                            <th>Company Name</th>
                            <th>Industry</th>
                            <th>Email</th>
                            <th>Address</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $sql = "SELECT * FROM companies ORDER BY id DESC";
                        $result = $conn->query($sql);

                        if ($result->num_rows > 0) {
                            while ($row = $result->fetch_assoc()) {
                                echo "<tr>";
                                echo "<td class='fw-semibold'>" . htmlspecialchars($row['name']) . "</td>";
                                echo "<td><span class='badge bg-light text-dark border'>" . htmlspecialchars($row['industry']) . "</span></td>";
                                echo "<td class='text-muted'>" . htmlspecialchars($row['email']) . "</td>";
                                echo "<td class='small text-muted'>" . htmlspecialchars(substr($row['address'], 0, 30)) . (strlen($row['address']) > 30 ? '...' : '') . "</td>";
                                echo "<td class='text-end'>
                                        <button class='btn btn-sm btn-outline-primary me-1'><i class='bi bi-pencil'></i></button>
                                        <a href='companies.php?delete=" . $row['id'] . "' class='btn btn-sm btn-outline-danger' onclick='return confirm(\"Are you sure you want to delete this company?\")'><i class='bi bi-trash'></i></a>
                                      </td>";
                                echo "</tr>";
                            }
                        } else {
                            echo "<tr><td colspan='5' class='text-center py-5 text-muted'>
                                <i class='bi bi-building-slash display-4 d-block mb-3'></i>
                                No companies found. Add a partner to see them here.
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