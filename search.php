<?php
include_once('db/database.php');

// Check connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Get the search term and page number from the AJAX request
$search_term = isset($_GET['search']) ? trim($_GET['search']) : '';
$page_no = isset($_GET['page_no']) && $_GET['page_no'] !== "" ? intval($_GET['page_no']) : 1;
$page_no = max(1, $page_no);

$total_records_per_page = 10;
$offset = ($page_no - 1) * $total_records_per_page;

// Build the search SQL query
$search_sql = $search_term ? "WHERE name LIKE '%" . mysqli_real_escape_string($conn, $search_term) . "%' OR 
                               company LIKE '%" . mysqli_real_escape_string($conn, $search_term) . "%' OR
                               number LIKE '%" . mysqli_real_escape_string($conn, $search_term) . "%' OR
                               gmail LIKE '%" . mysqli_real_escape_string($conn, $search_term) . "%'" : "";

// Get the total count of records with or without the search filter
$query_count = "SELECT COUNT(*) as total_records FROM contact $search_sql";
$result_count = mysqli_query($conn, $query_count) or die(mysqli_error($conn));
$records = mysqli_fetch_array($result_count);
$total_records = $records['total_records'];
$total_no_of_page = ceil($total_records / $total_records_per_page);

// Query to get the results with pagination
$squery = "SELECT * FROM contact
           $search_sql
           LIMIT $offset, $total_records_per_page";

$result = mysqli_query($conn, $squery) or die(mysqli_error($conn));

// Output table rows
$tableRows = "";
if ($total_records > 0) {
    while ($row = mysqli_fetch_array($result)) {
        $tableRows .= "<tr>
                        <td>" . htmlspecialchars($row['id']) . "</td> 
                        <td>" . htmlspecialchars($row['name']) . "</td>
                        <td>" . htmlspecialchars($row['company']) . "</td> 
                        <td>" . htmlspecialchars($row['number']) . "</td>
                        <td>" . htmlspecialchars($row['gmail']) . "</td>
                      </tr>";
    }
} else {
    $tableRows = "<tr>
                    <td colspan='5' class='text-center'>No records found.</td>
                  </tr>";
}

// Output pagination links
$paginationLinks = "";
$max_page_links = 5;
$start_page = max(1, $page_no - floor($max_page_links / 2));
$end_page = min($total_no_of_page, $start_page + $max_page_links - 1);

if ($end_page - $start_page + 1 < $max_page_links) {
    $start_page = max(1, $end_page - $max_page_links + 1);
}

$previous_page = $page_no - 1;
$next_page = $page_no + 1;

$paginationLinks .= "<li class='page-item " . ($page_no <= 1 ? 'disabled' : '') . "'>
                        <a class='page-link' href='#' data-page='" . ($page_no > 1 ? $previous_page : '#') . "'>Previous</a>
                      </li>";

if ($start_page > 1) {
    $paginationLinks .= "<li class='page-item disabled'><span class='page-link'>...</span></li>";
}

for ($i = $start_page; $i <= $end_page; $i++) {
    $paginationLinks .= "<li class='page-item " . ($i == $page_no ? 'active' : '') . "'>
                            <a class='page-link' href='#' data-page='$i'>$i</a>
                          </li>";
}

if ($end_page < $total_no_of_page) {
    $paginationLinks .= "<li class='page-item disabled'><span class='page-link'>...</span></li>";
}

$paginationLinks .= "<li class='page-item " . ($page_no >= $total_no_of_page ? 'disabled' : '') . "'>
                        <a class='page-link' href='#' data-page='" . ($page_no < $total_no_of_page ? $next_page : '#') . "'>Next</a>
                      </li>";

echo json_encode([
    'tableRows' => $tableRows,
    'paginationLinks' => $paginationLinks,
    'currentPage' => $page_no,
    'totalPages' => $total_no_of_page
]);

mysqli_close($conn);
?>
