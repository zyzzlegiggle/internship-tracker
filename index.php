<?php include 'db_connect.php'; ?>
<?php include 'header.php'; ?>

<div class="row mb-5 align-items-center">
    <div class="col-md-8">
        <h1 class="display-4 fw-bold">Dashboard</h1>
        <p class="lead text-muted">Overview of internship analytics</p>
    </div>
    <div class="col-md-4 text-end">
        <span class="badge bg-light text-dark p-3 rounded-pill shadow-sm">
            <i class="bi bi-calendar-check me-2"></i> <?php echo date('l, F j, Y'); ?>
        </span>
    </div>
</div>

<div class="row g-4 mb-5">
    <?php
    // Queries
    $student_count = $conn->query("SELECT COUNT(*) as count FROM students")->fetch_assoc()['count'];
    $company_count = $conn->query("SELECT COUNT(*) as count FROM companies")->fetch_assoc()['count'];
    $placed_count = $conn->query("SELECT COUNT(*) as count FROM placements WHERE status='Placed'")->fetch_assoc()['count'];
    $placement_rate = ($student_count > 0) ? round(($placed_count / $student_count) * 100, 1) : 0;
    ?>

    <!-- Stat Card 1 -->
    <div class="col-xl-3 col-md-6 fade-in delay-1">
        <div class="card stat-card bg-gradient-primary text-white h-100 shadow-lg rounded-4 border-0">
            <div class="card-body p-4">
                <i class="bi bi-people stat-icon"></i>
                <h6 class="text-uppercase mb-2 opacity-75">Total Students</h6>
                <h2 class="display-5 fw-bold mb-0"><?php echo $student_count; ?></h2>
                <a href="students.php" class="text-white text-decoration-none small mt-3 d-inline-block">View All <i
                        class="bi bi-arrow-right"></i></a>
            </div>
        </div>
    </div>

    <!-- Stat Card 2 -->
    <div class="col-xl-3 col-md-6 fade-in delay-2">
        <div class="card stat-card bg-gradient-success text-white h-100 shadow-lg rounded-4 border-0">
            <div class="card-body p-4">
                <i class="bi bi-briefcase-fill stat-icon"></i>
                <h6 class="text-uppercase mb-2 opacity-75">Placed Students</h6>
                <h2 class="display-5 fw-bold mb-0"><?php echo $placed_count; ?></h2>
                <a href="placements.php" class="text-white text-decoration-none small mt-3 d-inline-block">View Details
                    <i class="bi bi-arrow-right"></i></a>
            </div>
        </div>
    </div>

    <!-- Stat Card 3 -->
    <div class="col-xl-3 col-md-6 fade-in delay-3">
        <div class="card stat-card bg-gradient-warning text-white h-100 shadow-lg rounded-4 border-0">
            <div class="card-body p-4">
                <i class="bi bi-building stat-icon"></i>
                <h6 class="text-uppercase mb-2 opacity-75">Companies</h6>
                <h2 class="display-5 fw-bold mb-0"><?php echo $company_count; ?></h2>
                <a href="companies.php" class="text-white text-decoration-none small mt-3 d-inline-block">View Directory
                    <i class="bi bi-arrow-right"></i></a>
            </div>
        </div>
    </div>

    <!-- Stat Card 4 -->
    <div class="col-xl-3 col-md-6 fade-in delay-4">
        <div class="card stat-card bg-gradient-info text-white h-100 shadow-lg rounded-4 border-0">
            <div class="card-body p-4">
                <i class="bi bi-graph-up stat-icon"></i>
                <h6 class="text-uppercase mb-2 opacity-75">Placement Rate</h6>
                <h2 class="display-5 fw-bold mb-0"><?php echo $placement_rate; ?>%</h2>
                <div class="progress mt-3" style="height: 6px; background: rgba(255,255,255,0.3);">
                    <div class="progress-bar bg-white" role="progressbar"
                        style="width: <?php echo $placement_rate; ?>%"></div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row g-4 mt-2">
    <!-- Recent Activity -->
    <div class="col-lg-7 fade-in delay-3">
        <div class="glass-card h-100 p-4">
            <h4 class="mb-4 fw-bold text-primary"><i class="bi bi-clock-history me-2"></i>Recent Placements</h4>
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Student</th>
                            <th>Company</th>
                            <th>Date</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        if (
                            $result = $conn->query("
                            SELECT p.placement_date, p.status, s.name as student_name, c.name as company_name 
                            FROM placements p 
                            JOIN students s ON p.student_id = s.id 
                            JOIN companies c ON p.company_id = c.id 
                            WHERE p.status = 'Placed' 
                            ORDER BY p.placement_date DESC LIMIT 5")
                        ) {

                            if ($result->num_rows > 0) {
                                while ($row = $result->fetch_assoc()) {
                                    echo "<tr>";
                                    echo "<td><div class='d-flex align-items-center'><div class='avatar-circle bg-primary text-white me-2 rounded-circle d-flex align-items-center justify-content-center' style='width:35px;height:35px'>" . substr($row['student_name'], 0, 1) . "</div><span class='fw-semibold'>" . htmlspecialchars($row['student_name']) . "</span></div></td>";
                                    echo "<td>" . htmlspecialchars($row['company_name']) . "</td>";
                                    echo "<td><small class='text-muted'>" . date('M j, Y', strtotime($row['placement_date'])) . "</small></td>";
                                    echo "<td><span class='badge bg-success rounded-pill px-3'>Placed</span></td>";
                                    echo "</tr>";
                                }
                            } else {
                                echo "<tr><td colspan='4' class='text-center py-4 text-muted'>No recent placements found.</td></tr>";
                            }
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="col-lg-5 fade-in delay-4">
        <div class="glass-card h-100 p-4">
            <h4 class="mb-4 fw-bold text-primary"><i class="bi bi-lightning-charge me-2"></i>Quick Actions</h4>
            <div class="d-grid gap-3">
                <a href="students.php" class="btn btn-outline-primary btn-lg text-start p-3 border-2 shadow-sm">
                    <i class="bi bi-person-plus-fill fs-4 me-3"></i> Add New Student
                </a>
                <a href="companies.php"
                    class="btn btn-outline-warning btn-lg text-start p-3 border-2 shadow-sm text-dark">
                    <i class="bi bi-building-add fs-4 me-3"></i> Register Company
                </a>
                <a href="placements.php" class="btn btn-outline-success btn-lg text-start p-3 border-2 shadow-sm">
                    <i class="bi bi-check-circle-fill fs-4 me-3"></i> Record Placement
                </a>
            </div>

            <div class="mt-4 p-3 bg-light rounded-3 border">
                <h6 class="text-muted text-uppercase small fw-bold mb-2">System Status</h6>
                <div class="d-flex justify-content-between align-items-center mb-1">
                    <span>Database</span>
                    <span class="badge bg-success">Connected</span>
                </div>
                <div class="d-flex justify-content-between align-items-center">
                    <span>Last Update</span>
                    <span class="small text-muted">Just now</span>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>