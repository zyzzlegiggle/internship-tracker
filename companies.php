<?php
include 'db_connect.php';

$message = "";

// Handle Add Company
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_company'])) {
    $name = $_POST['name'];
    $industry = $_POST['industry'];
    $email = $_POST['email'];
    $address = $_POST['address'];

    $sql = "INSERT INTO companies (name, industry, email, address) VALUES ('$name', '$industry', '$email', '$address')";

    if ($conn->query($sql) === TRUE) {
        $message = "<div class='alert alert-success'>Company added successfully</div>";
    } else {
        $message = "<div class='alert alert-danger'>Error: " . $conn->error . "</div>";
    }
}

// Handle Delete Company
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $sql = "DELETE FROM companies WHERE id=$id";
    if ($conn->query($sql) === TRUE) {
        $message = "<div class='alert alert-success'>Company deleted successfully</div>";
    } else {
        $message = "<div class='alert alert-danger'>Error deleting record: " . $conn->error . "</div>";
    }
}
?>

<?php include 'header.php'; ?>

<div class="row">
    <div class="col-md-12">
        <h2 class="mb-4">Companies Management</h2>
        <?php echo $message; ?>
    </div>
</div>

<div class="row">
    <!-- Add Company Form -->
    <div class="col-md-4">
        <div class="card mb-4">
            <div class="card-header bg-warning text-dark">
                Add New Company
            </div>
            <div class="card-body">
                <form method="post" action="companies.php">
                    <div class="mb-3">
                        <label for="name" class="form-label">Company Name</label>
                        <input type="text" class="form-control" id="name" name="name" required>
                    </div>
                    <div class="mb-3">
                        <label for="industry" class="form-label">Industry</label>
                        <input type="text" class="form-control" id="industry" name="industry" required>
                    </div>
                    <div class="mb-3">
                        <label for="email" class="form-label">Contact Email</label>
                        <input type="email" class="form-control" id="email" name="email" required>
                    </div>
                    <div class="mb-3">
                        <label for="address" class="form-label">Address</label>
                        <textarea class="form-control" id="address" name="address" rows="3"></textarea>
                    </div>
                    <button type="submit" name="add_company" class="btn btn-warning w-100">Add Company</button>
                </form>
            </div>
        </div>
    </div>

    <!-- Company List -->
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                Registered Companies
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Company Name</th>
                                <th>Industry</th>
                                <th>Email</th>
                                <th>Address</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $sql = "SELECT * FROM companies ORDER BY id DESC";
                            $result = $conn->query($sql);

                            if ($result->num_rows > 0) {
                                while ($row = $result->fetch_assoc()) {
                                    echo "<tr>";
                                    echo "<td>" . $row['id'] . "</td>";
                                    echo "<td>" . htmlspecialchars($row['name']) . "</td>";
                                    echo "<td>" . htmlspecialchars($row['industry']) . "</td>";
                                    echo "<td>" . htmlspecialchars($row['email']) . "</td>";
                                    echo "<td>" . htmlspecialchars($row['address']) . "</td>";
                                    echo "<td>
                                            <a href='companies.php?delete=" . $row['id'] . "' class='btn btn-sm btn-danger' onclick='return confirm(\"Are you sure?\")'>Delete</a>
                                          </td>";
                                    echo "</tr>";
                                }
                            } else {
                                echo "<tr><td colspan='6' class='text-center'>No companies found</td></tr>";
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