<?php
session_start();
require_once '../includes/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    header('Location: ../login.php');
    exit;
}

// Handle product deletion
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    mysqli_query($conn, "DELETE FROM products WHERE id = $id");
    header('Location: products.php');
    exit;
}

$products = mysqli_query($conn, "SELECT p.*, c.name as category_name FROM products p LEFT JOIN categories c ON p.category_id = c.id ORDER BY p.created_at DESC");
$categories = mysqli_query($conn, "SELECT * FROM categories");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Products - PharmaCare Admin</title>
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
        .btn-add { background: #0f5c6b; color: white; padding: 0.5rem 1rem; border-radius: 0.5rem; text-decoration: none; display: inline-flex; align-items: center; gap: 0.5rem; }
        .products-table { background: white; border-radius: 0.5rem; overflow: hidden; margin-top: 1rem; }
        .products-table table { width: 100%; border-collapse: collapse; }
        .products-table th, .products-table td { padding: 1rem; text-align: left; border-bottom: 1px solid #e2e8f0; }
        .products-table th { background: #f8fafc; }
        .product-image { width: 50px; height: 50px; object-fit: cover; border-radius: 0.25rem; }
        .btn-edit { color: #2563eb; margin-right: 0.5rem; }
        .btn-delete { color: #dc2626; }
        .modal { display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 1000; align-items: center; justify-content: center; }
        .modal.active { display: flex; }
        .modal-content { background: white; border-radius: 0.5rem; max-width: 600px; width: 90%; max-height: 90vh; overflow-y: auto; }
        .modal-header { padding: 1rem; border-bottom: 1px solid #e2e8f0; display: flex; justify-content: space-between; }
        .modal-body { padding: 1rem; }
        .form-group { margin-bottom: 1rem; }
        .form-group label { display: block; margin-bottom: 0.25rem; font-weight: 500; }
        .form-group input, .form-group select, .form-group textarea { width: 100%; padding: 0.5rem; border: 1px solid #e2e8f0; border-radius: 0.25rem; }
    </style>
</head>
<body>
    <div class="admin-container">
        <aside class="admin-sidebar">
            <div class="logo"><i class="fas fa-prescription-bottle-alt"></i> PharmaCare Admin</div>
            <nav class="admin-nav">
                <a href="dashboard.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
                <a href="products.php" class="active"><i class="fas fa-capsules"></i> Products</a>
                <a href="orders.php"><i class="fas fa-shopping-cart"></i> Orders</a>
                <a href="users.php"><i class="fas fa-users"></i> Users</a>
                <a href="prescriptions.php"><i class="fas fa-file-prescription"></i> Prescriptions</a>
                <a href="reports.php"><i class="fas fa-chart-line"></i> Reports</a>
                <a href="../logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
            </nav>
        </aside>

        <main class="admin-main">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
                <h1>Manage Products</h1>
                <button class="btn-add" onclick="openAddModal()"><i class="fas fa-plus"></i> Add Product</button>
            </div>

            <div class="products-table">
                <table>
                    <thead>
                        <tr><th>Image</th><th>Name</th><th>Category</th><th>Price</th><th>Stock</th><th>Prescription</th><th>Actions</th></tr>
                    </thead>
                    <tbody>
                        <?php while($product = mysqli_fetch_assoc($products)): ?>
                        <tr>
                            <td><img src="<?php echo $product['image'] ?? 'https://via.placeholder.com/50'; ?>" class="product-image"></td>
                            <td><?php echo htmlspecialchars($product['name']); ?></td>
                            <td><?php echo htmlspecialchars($product['category_name']); ?></td>
                            <td>$<?php echo number_format($product['price'], 2); ?></td>
                            <td><?php echo $product['quantity']; ?></td>
                            <td><?php echo $product['prescription_required'] ? 'Yes' : 'No'; ?></td>
                            <td>
                                <a href="#" class="btn-edit" onclick="editProduct(<?php echo $product['id']; ?>)"><i class="fas fa-edit"></i></a>
                                <a href="?delete=<?php echo $product['id']; ?>" class="btn-delete" onclick="return confirm('Delete this product?')"><i class="fas fa-trash"></i></a>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </main>
    </div>

    <!-- Add/Edit Product Modal -->
    <div class="modal" id="productModal">
        <div class="modal-content">
            <div class="modal-header">
                <h3 id="modalTitle">Add Product</h3>
                <button onclick="closeModal()">&times;</button>
            </div>
            <div class="modal-body">
                <form id="productForm" method="POST" action="save-product.php" enctype="multipart/form-data">
                    <input type="hidden" name="id" id="productId">
                    <div class="form-group"><label>Product Name</label><input type="text" name="name" id="name" required></div>
                    <div class="form-group"><label>Slug</label><input type="text" name="slug" id="slug"></div>
                    <div class="form-group">
                        <label>Category</label>
                        <select name="category_id" id="category_id" required>
                            <option value="">Select Category</option>
                            <?php mysqli_data_seek($categories, 0); while($cat = mysqli_fetch_assoc($categories)): ?>
                            <option value="<?php echo $cat['id']; ?>"><?php echo htmlspecialchars($cat['name']); ?></option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    <div class="form-group"><label>Brand</label><input type="text" name="brand" id="brand"></div>
                    <div class="form-group"><label>Generic Name</label><input type="text" name="generic_name" id="generic_name"></div>
                    <div class="form-group"><label>Description</label><textarea name="description" id="description" rows="3"></textarea></div>
                    <div class="form-group"><label>Price ($)</label><input type="number" step="0.01" name="price" id="price" required></div>
                    <div class="form-group"><label>Compare Price ($)</label><input type="number" step="0.01" name="compare_price" id="compare_price"></div>
                    <div class="form-group"><label>Quantity</label><input type="number" name="quantity" id="quantity"></div>
                    <div class="form-group"><label>Prescription Required</label><select name="prescription_required" id="prescription_required"><option value="0">No</option><option value="1">Yes</option></select></div>
                    <div class="form-group"><label>Product Image</label><input type="file" name="image" id="image" accept="image/*"></div>
                    <button type="submit" class="btn-add">Save Product</button>
                </form>
            </div>
        </div>
    </div>

    <script>
        function openAddModal() {
            document.getElementById('modalTitle').innerText = 'Add Product';
            document.getElementById('productForm').reset();
            document.getElementById('productId').value = '';
            document.getElementById('productModal').classList.add('active');
        }
        function closeModal() { document.getElementById('productModal').classList.remove('active'); }
        function editProduct(id) {
            fetch(`get-product.php?id=${id}`)
                .then(res => res.json())
                .then(data => {
                    document.getElementById('modalTitle').innerText = 'Edit Product';
                    document.getElementById('productId').value = data.id;
                    document.getElementById('name').value = data.name;
                    document.getElementById('slug').value = data.slug;
                    document.getElementById('category_id').value = data.category_id;
                    document.getElementById('brand').value = data.brand;
                    document.getElementById('generic_name').value = data.generic_name;
                    document.getElementById('description').value = data.description;
                    document.getElementById('price').value = data.price;
                    document.getElementById('compare_price').value = data.compare_price;
                    document.getElementById('quantity').value = data.quantity;
                    document.getElementById('prescription_required').value = data.prescription_required;
                    document.getElementById('productModal').classList.add('active');
                });
        }
    </script>
</body>
</html>