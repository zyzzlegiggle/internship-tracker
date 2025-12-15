<?php include 'db_connect.php'; ?>
<?php include 'header.php'; ?>

<div class="row mb-4">
    <div class="col-12">
        <h1 class="display-4">System Documentation</h1>
        <p class="lead">Database Structure and Data Records for Verification.</p>
        <div class="alert alert-info">
            <strong>Note for User:</strong> You can take screenshots of this page to satisfy the "Structure + Rows"
            requirements.
        </div>
    </div>
</div>

<?php
$tables = ['students', 'companies', 'placements'];

foreach ($tables as $table) {
    echo "<div class='card mb-5'>";
    echo "<div class='card-header bg-dark text-white'><h3>Table: $table</h3></div>";
    echo "<div class='card-body'>";

    // Structure
    echo "<h5 class='text-primary'>1. Table Structure</h5>";
    echo "<table class='table table-bordered table-sm mb-4'>";
    echo "<thead class='table-light'><tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th><th>Extra</th></tr></thead>";
    echo "<tbody>";
    if ($result = $conn->query("DESCRIBE $table")) {
        while ($row = $result->fetch_assoc()) {
            echo "<tr>";
            foreach ($row as $val)
                echo "<td>$val</td>";
            echo "</tr>";
        }
    }
    echo "</tbody></table>";

    // Data
    echo "<h5 class='text-success'>2. Table Data (First 10 Rows)</h5>";
    echo "<div class='table-responsive'>";
    echo "<table class='table table-striped table-hover table-sm border'>";

    if ($result = $conn->query("SELECT * FROM $table LIMIT 10")) {
        $fields = $result->fetch_fields();
        echo "<thead class='table-light'><tr>";
        foreach ($fields as $field) {
            echo "<th>{$field->name}</th>";
        }
        echo "</tr></thead>";

        echo "<tbody>";
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                echo "<tr>";
                foreach ($row as $cell) {
                    echo "<td>$cell</td>";
                }
                echo "</tr>";
            }
        } else {
            echo "<tr><td colspan='" . count($fields) . "' class='text-center'>No data found</td></tr>";
        }
        echo "</tbody>";
    }
    echo "</table>";
    echo "</div>"; // table-responsive
    echo "</div>"; // card-body
    echo "</div>"; // card
}
?>

<?php include 'footer.php'; ?>