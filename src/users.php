<?php
session_start();
require_once '../includes/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    header('Location: ../login.php');
    exit;
}

if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    mysqli_query($conn, "DELETE FROM users WHERE id = $id");
    header('Location: users.php');
    exit;
}

$users = mysqli_query($conn, "SELECT * FROM users ORDER BY created_at DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Users - PharmaCare Admin</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../css/style.css">
    <style>
        .admin-container { display: flex; min-height: 100vh; }
        .admin-sidebar { width: 280px; background: #1e293b; color: white; position: fixed; height: 100vh; }
        .admin-sidebar .logo { padding: 1.5rem; font-size: 1.25rem; font-weight: 700; border-bottom: 1px solid #334155; }
        .admin-nav { padding: 1.5rem 0; }
        .admin-nav a { display: flex; align-items: center; gap: 0.75rem; padding: 0.75rem 1.5rem; color: #94a3b8; text-decoration: none; }
        .admin-nav a:hover, .admin-nav a.active { background: #334155; color: white; }
        .admin-main { flex: 1; margin-left: 280px; padding: 2rem; }
        .users-table { background: white; border-radius: 0.5rem; overflow: hidden; margin-top: 1rem; }
        .users-table table { width: 100%; border-collapse: collapse; }
        .users-table th, .users-table td { padding: 1rem; text-align: left; border-bottom: 1px solid #e2e8f0; }
        .users-table th { background: #f8fafc; }
        .role-badge { padding: 0.25rem 0.5rem; border-radius: 0.25rem; font-size: 0.75rem; font-weight: 600; }
        .role-admin { background: #fee2e2; color: #dc2626; }
        .role-customer { background: #d1fae5; color: #059669; }
        .btn-delete { color: #dc2626; text-decoration: none; }
    </style>
</head>
<body>
    <div class="admin-container">
        <aside class="admin-sidebar">
            <div class="logo"><i class="fas fa-prescription-bottle-alt"></i> PharmaCare Admin</div>
            <nav class="admin-nav">
                <a href="dashboard.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
                <a href="products.php"><i class="fas fa-capsules"></i> Products</a>
                <a href="orders.php"><i class="fas fa-shopping-cart"></i> Orders</a>
                <a href="users.php" class="active"><i class="fas fa-users"></i> Users</a>
                <a href="prescriptions.php"><i class="fas fa-file-prescription"></i> Prescriptions</a>
                <a href="reports.php"><i class="fas fa-chart-line"></i> Reports</a>
                <a href="../logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
            </nav>
        </aside>

        <main class="admin-main">
            <h1>Manage Users</h1>
            <div class="users-table">
                <table>
                    <thead>
                        <tr><th>ID</th><th>Name</th><th>Email</th><th>Phone</th><th>Role</th><th>Joined</th><th>Actions</th></tr>
                    </thead>
                    <tbody>
                        <?php while($user = mysqli_fetch_assoc($users)): ?>
                        <tr>
                            <td><?php echo $user['id']; ?></td>
                            <td><?php echo htmlspecialchars($user['name']); ?></td>
                            <td><?php echo htmlspecialchars($user['email']); ?></td>
                            <td><?php echo $user['phone'] ?? 'N/A'; ?></td>
                            <td><span class="role-badge role-<?php echo $user['role']; ?>"><?php echo ucfirst($user['role']); ?></span></td>
                            <td><?php echo date('M d, Y', strtotime($user['created_at'])); ?></td>
                            <td>
                                <?php if($user['role'] !== 'admin'): ?>
                                <a href="?delete=<?php echo $user['id']; ?>" class="btn-delete" onclick="return confirm('Delete this user?')"><i class="fas fa-trash"></i> Delete</a>
                                <?php else: ?>
                                <span style="color: #94a3b8;">Protected</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </main>
    </div>
</body>
</html>