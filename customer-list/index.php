<?php
// index.php - Customers List with search + pagination
require_once 'db.php';

$perPage = 10;

// 1. Read search text and page number from the URL
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$page   = isset($_GET['page']) ? (int) $_GET['page'] : 1;
if ($page < 1) {
    $page = 1;
}

// 2. Build the WHERE part (only when searching)
$where  = '';
$params = [];
if ($search !== '') {
    // Escape LIKE wildcards so "%" and "_" are treated as normal characters
    $like  = '%' . addcslashes($search, '%_\\') . '%';
    $where = 'WHERE first_name LIKE :s1
                 OR last_name  LIKE :s2
                 OR city       LIKE :s3
                 OR country    LIKE :s4';
    $params = [':s1' => $like, ':s2' => $like, ':s3' => $like, ':s4' => $like];
}

try {
    // 3. Count matching records
    $countStmt = $pdo->prepare("SELECT COUNT(*) FROM tbl_customers $where");
    $countStmt->execute($params);
    $totalRecords = (int) $countStmt->fetchColumn();
    $totalPages   = max(1, (int) ceil($totalRecords / $perPage));

    // Keep the page number in a valid range
    if ($page > $totalPages) {
        $page = $totalPages;
    }
    $offset = ($page - 1) * $perPage;

    // 4. Fetch only the 10 records for this page
    $stmt = $pdo->prepare(
        "SELECT id, first_name, last_name, city, country, mobile_number, date_n_time
         FROM tbl_customers
         $where
         ORDER BY id ASC
         LIMIT :limit OFFSET :offset"
    );
    foreach ($params as $key => $value) {
        $stmt->bindValue($key, $value, PDO::PARAM_STR);
    }
    $stmt->bindValue(':limit',  $perPage, PDO::PARAM_INT);
    $stmt->bindValue(':offset', $offset,  PDO::PARAM_INT);
    $stmt->execute();
    $customers = $stmt->fetchAll();
} catch (PDOException $e) {
    http_response_code(500);
    die('<p style="font-family:Arial;padding:20px;color:#b00020;">'
      . 'Database error: the table <code>tbl_customers</code> could not be read. '
      . 'Please make sure the SQL file has been imported.</p>');
}

// Helper: build a link that keeps the search text
function pageUrl(int $page, string $search): string
{
    $query = ['page' => $page];
    if ($search !== '') {
        $query['search'] = $search;
    }
    return '?' . http_build_query($query);
}

// Helper: safe output (also keeps é, ü, ã, Å correct)
function e($value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customers List</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="container">
    <h1>Customers List</h1>

    <!-- Search form -->
    <form method="get" action="index.php" class="search-form">
        <input type="text" name="search" placeholder="Search customers..."
               value="<?= e($search) ?>">
        <button type="submit" class="btn btn-primary">Search</button>
        <a href="index.php" class="btn btn-secondary">Reset</a>
    </form>

    <?php if ($search !== ''): ?>
        <p class="info"><?= $totalRecords ?> result(s) for "<strong><?= e($search) ?></strong>"</p>
    <?php endif; ?>

    <!-- Customers table -->
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>Sl. No.</th>
                    <th>First Name</th>
                    <th>Last Name</th>
                    <th>City</th>
                    <th>Country</th>
                    <th>Mobile Number</th>
                    <th>Date &amp; Time</th>
                </tr>
            </thead>
            <tbody>
            <?php if (empty($customers)): ?>
                <tr><td colspan="7" class="no-data">No customers found.</td></tr>
            <?php else: ?>
                <?php foreach ($customers as $i => $c): ?>
                    <tr>
                        <td><?= $offset + $i + 1 ?></td>
                        <td><?= e($c['first_name']) ?></td>
                        <td><?= e($c['last_name']) ?></td>
                        <td><?= e($c['city']) ?></td>
                        <td><?= e($c['country']) ?></td>
                        <td><?= e($c['mobile_number']) ?></td>
                        <td><?= e($c['date_n_time']) ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <?php if ($totalPages > 1): ?>
        <nav class="pagination">
            <?php if ($page > 1): ?>
                <a href="<?= e(pageUrl($page - 1, $search)) ?>">Previous</a>
            <?php else: ?>
                <span class="disabled">Previous</span>
            <?php endif; ?>

            <?php
            // Show first page, last page and 2 pages around the current one
            $last = 0;
            for ($p = 1; $p <= $totalPages; $p++) {
                if ($p == 1 || $p == $totalPages || abs($p - $page) <= 2) {
                    if ($last && $p - $last > 1) {
                        echo '<span class="dots">...</span>';
                    }
                    if ($p == $page) {
                        echo '<span class="active">' . $p . '</span>';
                    } else {
                        echo '<a href="' . e(pageUrl($p, $search)) . '">' . $p . '</a>';
                    }
                    $last = $p;
                }
            }
            ?>

            <?php if ($page < $totalPages): ?>
                <a href="<?= e(pageUrl($page + 1, $search)) ?>">Next</a>
            <?php else: ?>
                <span class="disabled">Next</span>
            <?php endif; ?>
        </nav>
    <?php endif; ?>
</div>
</body>
</html>
