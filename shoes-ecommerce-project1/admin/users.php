<?php
$page_title = "User Management";
require_once 'includes/admin_header.php';

// Check for user actions
if (isset($_GET['action']) && isset($_GET['id'])) {
    $action = $_GET['action'];
    $user_id = (int)$_GET['id'];
    
    switch ($action) {
        case 'activate':
            $stmt = $pdo->prepare("UPDATE users SET active = 1 WHERE id = ?");
            $stmt->execute([$user_id]);
            $_SESSION['success_message'] = "User activated successfully";
            break;
            
        case 'deactivate':
            $stmt = $pdo->prepare("UPDATE users SET active = 0 WHERE id = ?");
            $stmt->execute([$user_id]);
            $_SESSION['success_message'] = "User deactivated successfully";
            break;
            
        case 'delete':
            $stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
            $stmt->execute([$user_id]);
            $_SESSION['success_message'] = "User deleted successfully";
            break;
    }
    
    header('Location: users.php');
    exit;
}

// Get all users with their order counts
$users = $pdo->query("
    SELECT u.*, COUNT(o.id) as order_count 
    FROM users u
    LEFT JOIN orders o ON u.id = o.user_id
    GROUP BY u.id
    ORDER BY u.created_at DESC
")->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="admin-content-header">
    <h1>User Management</h1>
    <div class="action-buttons">
        <button class="btn btn-primary" id="export-users">
            <i class="fas fa-download"></i> Export Users
        </button>
    </div>
</div>

<?php if (isset($_SESSION['success_message'])): ?>
    <div class="alert alert-success">
        <?= $_SESSION['success_message'] ?>
        <?php unset($_SESSION['success_message']); ?>
    </div>
<?php endif; ?>

<div class="admin-table-container">
    <table class="admin-table" id="users-table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Email</th>
                <th>Phone</th>
                <th>Registered</th>
                <th>Orders</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($users as $user): ?>
            <tr>
                <td><?= $user['id'] ?></td>
                <td>
                    <div class="user-info">
                        <img src="../../assets/images/users/<?= $user['avatar'] ?? 'default.png' ?>" 
                             alt="User avatar" class="user-avatar">
                        <?= htmlspecialchars($user['name']) ?>
                    </div>
                </td>
                <td><?= htmlspecialchars($user['email']) ?></td>
                <td><?= $user['phone'] ? htmlspecialchars($user['phone']) : 'N/A' ?></td>
                <td><?= date('M d, Y', strtotime($user['created_at'])) ?></td>
                <td><?= $user['order_count'] ?></td>
                <td>
                    <span class="status-badge <?= $user['active'] ? 'active' : 'inactive' ?>">
                        <?= $user['active'] ? 'Active' : 'Inactive' ?>
                    </span>
                </td>
                <td>
                    <div class="action-buttons">
                        <a href="user_details.php?id=<?= $user['id'] ?>" 
                           class="btn btn-sm btn-view" title="View">
                            <i class="fas fa-eye"></i>
                        </a>
                        
                        <?php if ($user['active']): ?>
                            <a href="users.php?action=deactivate&id=<?= $user['id'] ?>" 
                               class="btn btn-sm btn-warning" title="Deactivate">
                                <i class="fas fa-ban"></i>
                            </a>
                        <?php else: ?>
                            <a href="users.php?action=activate&id=<?= $user['id'] ?>" 
                               class="btn btn-sm btn-success" title="Activate">
                                <i class="fas fa-check"></i>
                            </a>
                        <?php endif; ?>
                        
                        <a href="users.php?action=delete&id=<?= $user['id'] ?>" 
                           class="btn btn-sm btn-danger" 
                           title="Delete"
                           onclick="return confirm('Are you sure you want to permanently delete this user?')">
                            <i class="fas fa-trash"></i>
                        </a>
                    </div>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<script>
$(document).ready(function() {
    // Initialize DataTable
    $('#users-table').DataTable({
        responsive: true,
        dom: 'Bfrtip',
        buttons: [
            {
                extend: 'csv',
                text: '<i class="fas fa-file-csv"></i> Export CSV',
                className: 'btn btn-secondary'
            },
            {
                extend: 'excel',
                text: '<i class="fas fa-file-excel"></i> Export Excel',
                className: 'btn btn-secondary'
            }
        ],
        columnDefs: [
            { orderable: false, targets: [7] }
        ]
    });

    // Status filter toggle
    $('.status-filter').on('click', function() {
        const status = $(this).data('status');
        const table = $('#users-table').DataTable();
        
        if (status === 'all') {
            table.search('').columns().search('').draw();
        } else {
            table.column(6).search(status).draw();
        }
    });
});
</script>

<?php
require_once 'includes/admin_footer.php';
?>
