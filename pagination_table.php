<?php
include_once('db/database.php');

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

$page_no = isset($_GET['page_no']) && $_GET['page_no'] !== "" ? intval($_GET['page_no']) : 1;
$page_no = max(1, $page_no);

$total_records_per_page = 10;
$offset = ($page_no - 1) * $total_records_per_page;
$previous_page = $page_no - 1;
$next_page = $page_no + 1;

$result_count = mysqli_query($conn, "SELECT COUNT(*) as total_records FROM tblstudentclass")
    or die(mysqli_error($conn));

$records = mysqli_fetch_array($result_count);
$total_records = $records['total_records'];
$total_no_of_page = ceil($total_records / $total_records_per_page);

$max_page_links = 5;
$start_page = max(1, $page_no - floor($max_page_links / 2));
$end_page = min($total_no_of_page, $start_page + $max_page_links - 1);

if ($end_page - $start_page + 1 < $max_page_links) {
    $start_page = max(1, $end_page - $max_page_links + 1);
}

$squery = "SELECT sc.id as sid, c.classname, yl.yearlevel, yl.description as strand, st.id as stid, 
           CONCAT(st.lname, ', ', st.fname, ' ', st.mname) as sname, ms.id as msid, sb.subjectname, 
           sb.description, CONCAT(sy.schoolyear, ' - ', sem.semname) as schoolyear_semname
           FROM tblstudentclass sc
           LEFT JOIN tblclass c ON sc.classid = c.id
           LEFT JOIN tblyearlevel yl ON c.yearlevelid = yl.id
           LEFT JOIN tblstudent st ON sc.studentid = st.id
           LEFT JOIN tblmanagesubject ms ON sc.managesubject_id = ms.id
           LEFT JOIN tblsubjects sb ON ms.subjectid = sb.id
           LEFT JOIN tblsemester sem ON ms.semester_id = sem.id
           LEFT JOIN tblschoolyear sy ON sem.schoolyear_id = sy.id
           LIMIT $offset, $total_records_per_page";

$result = mysqli_query($conn, $squery) or die(mysqli_error($conn));
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PHP Pagination</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h1>PHP Pagination</h1>
        
        <table class="table">
            <thead>
                <tr>
                    <th>SY & Semester</th>
                    <th>Yr & Sec</th>
                    <th>Student Name</th>
                    <th>Subject</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($total_records > 0): ?>
                    <?php while($row = mysqli_fetch_array($result)): ?>
                    <tr>
                        <td><?= htmlspecialchars($row['schoolyear_semname']); ?></td> 
                        <td><?= htmlspecialchars($row['strand'] . ' ' . $row['yearlevel']); ?></td>
                        <td><?= htmlspecialchars($row['sname']); ?></td> 
                        <td><?= htmlspecialchars($row['subjectname'] . ' ' . $row['description']); ?></td>
                    </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="4" class="text-center">No records found.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>

        <nav aria-label="Page navigation">
            <ul class="pagination">
                <li class="page-item <?= ($page_no <= 1) ? 'disabled' : ''; ?>">
                    <a class="page-link" href="<?= ($page_no > 1) ? '?page_no=' . $previous_page : '#'; ?>">Previous</a>
                </li>

                <?php if ($start_page > 1): ?>
                <li class="page-item disabled">
                    <span class="page-link">...</span>
                </li>
                <?php endif; ?>

                <?php for ($i = $start_page; $i <= $end_page; $i++): ?>
                <li class="page-item <?= ($i == $page_no) ? 'active' : ''; ?>">
                    <a class="page-link" href="?page_no=<?= $i; ?>"><?= $i; ?></a>
                </li>
                <?php endfor; ?>

                <?php if ($end_page < $total_no_of_page): ?>
                <li class="page-item disabled">
                    <span class="page-link">...</span>
                </li>
                <?php endif; ?>

                <li class="page-item <?= ($page_no >= $total_no_of_page) ? 'disabled' : ''; ?>">
                    <a class="page-link" href="<?= ($page_no < $total_no_of_page) ? '?page_no=' . $next_page : '#'; ?>">Next</a>
                </li>
            </ul>
        </nav>
        
        <div class="mt-3">
            <strong>Page <?= $page_no; ?> of <?= $total_no_of_page; ?></strong>
        </div>
    </div>
</body>
</html>

<?php mysqli_close($conn); ?>
