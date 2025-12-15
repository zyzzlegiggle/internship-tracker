# Internship Placement Tracker

## 1. Project Overview
This web-based application tracks student internships, companies, and placement statuses. It uses PHP and MySQL (XAMPP environment) with Bootstrap for styling.

## 2. Database Design & Reasoning

### Database Structure
The system is built on a relational database with three core tables, designed to ensure data integrity and minimize redundancy through normalization.

**Table 1: `students`**
Stores student information.
- `id` (INT, PK): Unique identifier for each student.
- `name` (VARCHAR): Full name.
- `student_id_number` (VARCHAR): Unique university ID (e.g., S12345).
- `email`, `course`: Contact and academic info.

**Table 2: `companies`**
Stores company details.
- `id` (INT, PK): Unique identifier for each company.
- `name` (VARCHAR): Company name.
- `industry`: To categorize companies (e.g., Tech, Finance).
- `email`, `address`: Contact info.

**Table 3: `placements`**
This is the associative (junction) table that links Students and Companies.
- `id` (INT, PK): Unique ID for the placement record.
- `student_id` (INT, FK): Links to `students` table.
- `company_id` (INT, FK): Links to `companies` table.
- `status` (ENUM): Tracks the state ('Applied', 'Interview', 'Placed', 'Rejected').
- `placement_date`: Date of the record.

**Reasoning for Design:**
We separated Students and Companies into distinct tables to avoid data anomalies (Third Normal Form). If we stored company info in the student table, we would repeat the same company details for every student applying there, leading to inconsistencies if the company address changed. The `placements` table acts as a transaction log, allowing many-to-many relationships: one student can apply to multiple companies, and one company can hire multiple students. We enforced Referential Integrity using Foreign Keys (`ON DELETE CASCADE`) to ensure no orphan records exist if a student or company is deleted.

## 3. Dashboard Analytics & Metric Choices
The dashboard provides a high-level view of the placement process.

**Selected Metrics:**
1.  **Total Students:** Indicates the total pool of candidates available for internship. This is crucial for understanding the scale of the department's responsibility.
2.  **Registered Companies:** Shows the network of industry partners. A low number here would indicate a need for business development outreach.
3.  **Placed Students:** The primary success metric. It tracks the raw number of students who have secured a position.
4.  **Placement Rate (%):** This is a derived metric (`Placed / Total * 100`). It provides a normalized performance indicator, allowing comparison across different cohorts or years regardless of class size.

**SQL Logic Used:**
- Counts are aggregated using `SELECT COUNT(*)`.
- The 'Placed' count filters specifically for `status='Placed'`.
- This ensures the dashboard always reflects real-time data from the underlying tables without manual updates.

## 4. How to Run
1.  Ensure XAMPP is running (Apache and MySQL).
2.  Import `database.sql` into phpMyAdmin (create DB `internship_tracker` first if needed) OR visit `http://localhost/internship_tracker/setup_db.php`.
3.  Open the application at `http://localhost/internship_tracker/`.
4.  For detailed verification of database structure and rows, visit `http://localhost/internship_tracker/documentation.php`.
