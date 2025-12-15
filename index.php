<?php include 'db_connect.php'; ?>
<?php include 'header.php'; ?>

<div class="row mb-4">
    <div class="col-12">
        <h1 class="display-4">Dashboard</h1>
        <p class="lead">Welcome to the Internship Placement Tracker.</p>
    </div>
</div>

<div class="row">
    <?php
    // Get Total Students
    $student_count = 0;
    if ($result = $conn->query("SELECT COUNT(*) as count FROM students")) {
        $student_count = $result->fetch_assoc()['count'];
    }

    // Get Total Companies
    $company_count = 0;
    if ($result = $conn->query("SELECT COUNT(*) as count FROM companies")) {
        $company_count = $result->fetch_assoc()['count'];
    }

    // Get Placed Students
    $placed_count = 0;
    if ($result = $conn->query("SELECT COUNT(*) as count FROM placements WHERE status='Placed'")) {
        $placed_count = $result->fetch_assoc()['count'];
    }

    // Get Placement Rate
    $placement_rate = ($student_count > 0) ? round(($placed_count / $student_count) * 100, 1) : 0;
    ?>

    <div class="col-md-3 mb-4">
        <div class="card bg-primary text-white h-100">
            <div class="card-body">
                <h5 class="card-title">Total Students</h5>
                <h2 class="display-3"><?php echo $student_count; ?></h2>
                <a href="students.php" class="text-white stretched-link">View Details</a>
            </div>
        </div>
    </div>

    <div class="col-md-3 mb-4">
        <div class="card bg-success text-white h-100">
            <div class="card-body">
                <h5 class="card-title">Placed Students</h5>
                <h2 class="display-3"><?php echo $placed_count; ?></h2>
                <a href="placements.php" class="text-white stretched-link">View Placements</a>
            </div>
        </div>
    </div>

    <div class="col-md-3 mb-4">
        <div class="card bg-warning text-dark h-100">
            <div class="card-body">
                <h5 class="card-title">Registered Companies</h5>
                <h2 class="display-3"><?php echo $company_count; ?></h2>
                <a href="companies.php" class="text-dark stretched-link">View Companies</a>
            </div>
        </div>
    </div>

    <div class="col-md-3 mb-4">
        <div class="card bg-info text-white h-100">
            <div class="card-body">
                <h5 class="card-title">Placement Rate</h5>
                <h2 class="display-3"><?php echo $placement_rate; ?>%</h2>
                <p class="card-text">of students placed</p>
            </div>
        </div>
    </div>
</div>

<div class="row mt-4">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                Recent Placements
            </div>
            <ul class="list-group list-group-flush">
                <?php
                if (
                    $result = $conn->query("
                    SELECT p.placement_date, s.name as student_name, c.name as company_name 
                    FROM placements p 
                    JOIN students s ON p.student_id = s.id 
                    JOIN companies c ON p.company_id = c.id 
                    WHERE p.status = 'Placed' 
                    ORDER BY p.placement_date DESC LIMIT 5")
                ) {

                    if ($result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                            echo "<li class='list-group-item d-flex justify-content-between align-items-center'>";
                            echo "<div><strong>" . htmlspecialchars($row['student_name']) . "</strong> at " . htmlspecialchars($row['company_name']) . "</div>";
                            echo "<span class='badge bg-light text-dark'>" . $row['placement_date'] . "</span>";
                            echo "</li>";
                        }
                    } else {
                        echo "<li class='list-group-item'>No recent placements found.</li>";
                    }
                }
                ?>
            </ul>
        </div>
    </div>

    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                Quick Actions
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <a href="students.php" class="btn btn-outline-primary">Add New Student</a>
                    <a href="companies.php" class="btn btn-outline-warning">Add New Company</a>
                    <a href="placements.php" class="btn btn-outline-success">Record Placement</a>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>