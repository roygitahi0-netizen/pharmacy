<?php
session_start();
require_once '../includes/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    header('Location: ../login.php');
    exit;
}

if (isset($_POST['verify'])) {
    $id = $_POST['id'];
    $status = $_POST['status'];
    mysqli_query($conn, "UPDATE prescriptions SET status = '$status', verified_by = {$_SESSION['user_id']}, verified_at = NOW() WHERE id = $id");
    header('Location: prescriptions.php');
    exit;
}

$prescriptions = mysqli_query($conn, "SELECT p.*, u.name as user_name FROM prescriptions p JOIN users u ON p.user_id = u.id ORDER BY p.upload_date DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Prescriptions - PharmaCare Admin</title>
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
        .prescriptions-table { background: white; border-radius: 0.5rem; overflow: hidden; margin-top: 1rem; }
        .prescriptions-table table { width: 100%; border-collapse: collapse; }
        .prescriptions-table th, .prescriptions-table td { padding: 1rem; text-align: left; border-bottom: 1px solid #e2e8f0; }
        .prescriptions-table th { background: #f8fafc; }
        .prescription-image { max-width: 80px; cursor: pointer; }
        .status-badge { display: inline-block; padding: 0.25rem 0.5rem; border-radius: 0.25rem; font-size: 0.75rem; font-weight: 600; }
        .status-pending { background: #fef3c7; color: #d97706; }
        .status-verified { background: #d1fae5; color: #059669; }
        .status-rejected { background: #fee2e2; color: #dc2626; }
        .modal { display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 1000; align-items: center; justify-content: center; }
        .modal.active { display: flex; }
        .modal-content { background: white; border-radius: 0.5rem; max-width: 90%; max-height: 90%; overflow: auto; }
        .modal-content img { max-width: 100%; }
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
                <a href="users.php"><i class="fas fa-users"></i> Users</a>
                <a href="prescriptions.php" class="active"><i class="fas fa-file-prescription"></i> Prescriptions</a>
                <a href="reports.php"><i class="fas fa-chart-line"></i> Reports</a>
                <a href="../logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
            </nav>
        </aside>

        <main class="admin-main">
            <h1>Manage Prescriptions</h1>
            <div class="prescriptions-table">
                <table>
                    <thead>
                        <tr><th>User</th><th>Prescription</th><th>Doctor</th><th>Status</th><th>Upload Date</th><th>Action</th></tr>
                    </thead>
                    <tbody>
                        <?php while($pres = mysqli_fetch_assoc($prescriptions)): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($pres['user_name']); ?></td>
                            <td><a href="#" onclick="showImage('<?php echo $pres['prescription_image']; ?>')"><img src="../<?php echo $pres['prescription_image']; ?>" class="prescription-image"></a></td>
                            <td><?php echo htmlspecialchars($pres['doctor_name'] ?? 'N/A'); ?></td>
                            <td><span class="status-badge status-<?php echo $pres['status']; ?>"><?php echo ucfirst($pres['status']); ?></span></td>
                            <td><?php echo date('M d, Y', strtotime($pres['upload_date'])); ?></td>
                            <td>
                                <form method="POST" style="display: flex; gap: 0.5rem;">
                                    <input type="hidden" name="id" value="<?php echo $pres['id']; ?>">
                                    <select name="status">
                                        <option value="pending" <?php echo $pres['status'] == 'pending' ? 'selected' : ''; ?>>Pending</option>
                                        <option value="verified" <?php echo $pres['status'] == 'verified' ? 'selected' : ''; ?>>Verified</option>
                                        <option value="rejected" <?php echo $pres['status'] == 'rejected' ? 'selected' : ''; ?>>Rejected</option>
                                    </select>
                                    <button type="submit" name="verify" class="btn-update">Update</button>
                                </form>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </main>
    </div>

    <div class="modal" id="imageModal" onclick="closeImage()">
        <div class="modal-content" onclick="event.stopPropagation()">
            <img id="modalImage" src="">
        </div>
    </div>

    <script>
        function showImage(src) {
            document.getElementById('modalImage').src = '../' + src;
            document.getElementById('imageModal').classList.add('active');
        }
        function closeImage() {
            document.getElementById('imageModal').classList.remove('active');
        }
    </script>
</body>
</html>