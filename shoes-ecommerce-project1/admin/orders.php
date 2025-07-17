<?php
$page_title = "Order Management";
require_once 'includes/admin_header.php';

// Get all orders
$order_query = "SELECT o.*, u.name as customer_name 
                FROM orders o 
                JOIN users u ON o.user_id = u.id
                ORDER BY o.order_date DESC";
$orders = $pdo->query($order_query)->fetchAll(PDO::FETCH_ASSOC);
?>

<!-- Order List -->
<div class="admin-table-container">
    <div class="table-header">
        <h2>Order List</h2>
    </div>
    
    <div class="order-filters">
        <div class="filter-group">
            <label>Status:</label>
            <select id="status-filter">
                <option value="">All</option>
                <option value="pending">Pending</option>
                <option value="processing">Processing</option>
                <option value="shipped">Shipped</option>
                <option value="delivered">Delivered</option>
                <option value="cancelled">Cancelled</option>
            </select>
        </div>
        <div class="filter-group">
            <label>From:</label>
            <input type="date" id="date-from">
        </div>
        <div class="filter-group">
            <label>To:</label>
            <input type="date" id="date-to">
        </div>
    </div>
    
    <table class="admin-table" id="orders-table">
        <thead>
            <tr>
                <th>Order #</th>
                <th>Customer</th>
                <th>Date</th>
                <th>Items</th>
                <th>Total</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($orders as $order): ?>
            <tr>
                <td><?= $order['id'] ?></td>
                <td><?= htmlspecialchars($order['customer_name']) ?></td>
                <td><?= date('M j, Y', strtotime($order['order_date'])) ?></td>
                <td>
                    <?php 
                    $items = $pdo->prepare("SELECT COUNT(*) FROM order_items WHERE order_id = ?");
                    $items->execute([$order['id']]);
                    echo $items->fetchColumn();
                    ?>
                </td>
                <td>$<?= number_format($order['total_amount'], 2) ?></td>
                <td>
                    <span class="status <?= strtolower($order['status']) ?>">
                        <?= $order['status'] ?>
                    </span>
                </td>
                <td>
                    <div class="action-buttons">
                        <a href="orders.php?action=view&id=<?= $order['id'] ?>" class="btn-view">
                            <i class="fas fa-eye"></i> View
                        </a>
                        <a href="orders.php?action=edit&id=<?= $order['id'] ?>" class="btn-edit">
                            <i class="fas fa-edit"></i> Edit
                        </a>
                    </div>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<script>
// Initialize DataTable for order filtering
$(document).ready(function() {
    $('#orders-table').DataTable({
        responsive: true
    });
    
    // Status filter functionality
    $('#status-filter').on('change', function() {
        var status = $(this).val();
        var table = $('#orders-table').DataTable();
        
        if (status) {
            table.column(5).search(status).draw();
        } else {
            table.column(5).search('').draw();
        }
    });
});
</script>

<?php
require_once 'includes/admin_footer.php';
?>
