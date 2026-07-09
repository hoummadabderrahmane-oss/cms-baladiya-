<?php
require_once __DIR__ . '/../includes/header.php';

// Search
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$where = '';
$params = [];

if ($search) {
    $where = "fullname LIKE ? OR cin LIKE ? OR phone LIKE ?";
    $params = ["%$search%", "%$search%", "%$search%"];
}

// Pagination
$result = paginate($pdo, 'citizens', $where, $params, 10);
$citizens = $result['items'];
$page = $result['page'];
$pages = $result['pages'];
$total = $result['total'];

// Delete
if (isset($_GET['delete']) && isAdmin()) {
    $stmt = $pdo->prepare("DELETE FROM citizens WHERE id = ?");
    $stmt->execute([$_GET['delete']]);
    flashMessage('success', 'Citizen deleted successfully!');
    redirect('index.php');
}
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2>Citizens</h2>
    <a href="create.php" class="btn btn-primary"><i class="bi bi-plus-lg"></i> Add Citizen</a>
</div>

<form method="GET" class="mb-4">
    <div class="input-group">
        <input type="text" name="search" class="form-control" placeholder="Search by name, CIN or phone..." 
               value="<?php echo sanitize($search); ?>">
        <button class="btn btn-outline-secondary" type="submit"><i class="bi bi-search"></i></button>
        <?php if ($search): ?>
        <a href="index.php" class="btn btn-outline-danger"><i class="bi bi-x-lg"></i></a>
        <?php endif; ?>
    </div>
</form>

<div class="card">
    <div class="card-body">
        <table class="table table-hover">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Full Name</th>
                    <th>CIN</th>
                    <th>Phone</th>
                    <th>Email</th>
                    <th>Gender</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($citizens as $c): ?>
                <tr>
                    <td><?php echo $c['id']; ?></td>
                    <td><?php echo sanitize($c['fullname']); ?></td>
                    <td><?php echo sanitize($c['cin']); ?></td>
                    <td><?php echo sanitize($c['phone']); ?></td>
                    <td><?php echo sanitize($c['email']); ?></td>
                    <td>
                        <span class="badge bg-<?php echo $c['gender'] == 'male' ? 'primary' : 'danger'; ?>">
                            <?php echo $c['gender']; ?>
                        </span>
                    </td>
                    <td>
                        <a href="view.php?id=<?php echo $c['id']; ?>" class="btn btn-sm btn-info"><i class="bi bi-eye"></i></a>
                        <a href="edit.php?id=<?php echo $c['id']; ?>" class="btn btn-sm btn-warning"><i class="bi bi-pencil"></i></a>
                        <?php if (isAdmin()): ?>
                        <a href="?delete=<?php echo $c['id']; ?>" class="btn btn-sm btn-danger btn-delete"><i class="bi bi-trash"></i></a>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php if (empty($citizens)): ?>
                <tr><td colspan="7" class="text-center text-muted">No citizens found</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Pagination -->
<?php if ($pages > 1): ?>
<nav class="mt-3">
    <ul class="pagination justify-content-center">
        <?php for ($i = 1; $i <= $pages; $i++): ?>
        <li class="page-item <?php echo $i == $page ? 'active' : ''; ?>">
            <a class="page-link" href="?page=<?php echo $i; ?><?php echo $search ? '&search='.urlencode($search) : ''; ?>">
                <?php echo $i; ?>
            </a>
        </li>
        <?php endfor; ?>
    </ul>
</nav>
<?php endif; ?>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>