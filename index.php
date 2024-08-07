<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PHP Pagination with AJAX Search</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/jquery@3.6.0/dist/jquery.min.js"></script>
</head>
<body>
    <div class="container mt-5">
        <h1>PHP Pagination with AJAX Search</h1>
        
        <form class="mb-3" id="search-form">
            <div class="input-group">
                <input type="text" class="form-control" id="search" name="search" placeholder="Search...">
                <button class="btn btn-primary" type="button" id="search-button">Search</button>
            </div>
        </form>

        <table class="table" id="results-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Company</th>
                    <th>Number</th>
                    <th>Gmail</th>
                    
                </tr>
            </thead>
            <tbody>
                <!-- Results will be inserted here by AJAX -->
            </tbody>
        </table>

        <nav aria-label="Page navigation" id="pagination-nav">
            <ul class="pagination">
                <!-- Pagination links will be inserted here by AJAX -->
            </ul>
        </nav>

        <div class="mt-3" id="page-info">
            <!-- Page info will be inserted here by AJAX -->
        </div>
    </div>

    <script>
        $(document).ready(function() {
            function fetchResults(searchTerm, pageNo) {
                $.ajax({
                    url: 'search.php',
                    type: 'GET',
                    data: { search: searchTerm, page_no: pageNo },
                    success: function(response) {
                        var data = JSON.parse(response);
                        $('#results-table tbody').html(data.tableRows);
                        $('#pagination-nav .pagination').html(data.paginationLinks);
                        $('#page-info').html('<strong>Page ' + data.currentPage + ' of ' + data.totalPages + '</strong>');
                    }
                });
            }

            function loadInitialResults() {
                fetchResults('', 1); // Load results for the first page with no search term
            }

            $('#search-button').click(function() {
                var searchTerm = $('#search').val();
                fetchResults(searchTerm, 1); // Fetch results for the first page with the search term
            });

            $('#search').on('keyup', function() {
                var searchTerm = $(this).val();
                fetchResults(searchTerm, 1); // Fetch results for the first page with the search term
            });

            $('#pagination-nav').on('click', '.page-link', function(event) {
                event.preventDefault();
                var pageNo = $(this).data('page');
                if (pageNo) {
                    var searchTerm = $('#search').val();
                    fetchResults(searchTerm, pageNo);
                }
            });

            // Load initial results when page loads
            loadInitialResults();
        });
    </script>
</body>
</html>
