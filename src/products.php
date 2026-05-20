<?php
require_once 'includes/header.php';

$category = isset($_GET['category']) ? $_GET['category'] : null;
$products = getProducts($category);
$categories = getCategories();
?>

<main>
    <div class="container">
        <div class="products-header">
            <h1>Our Products</h1>
            <p>Browse our complete range of healthcare products</p>
        </div>
        
        <div class="products-layout">
            <aside class="products-sidebar">
                <div class="filter-section">
                    <h3>Categories</h3>
                    <ul>
                        <li><a href="products.php">All Products</a></li>
                        <?php foreach ($categories as $cat): ?>
                        <li><a href="products.php?category=<?php echo $cat['slug']; ?>"><?php echo htmlspecialchars($cat['name']); ?></a></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </aside>
            
            <div class="products-main">
                <div class="products-grid">
                    <?php foreach ($products as $product): ?>
                    <div class="product-card">
                        <?php if ($product['prescription_required']): ?>
                        <div class="product-badge prescription">Prescription Required</div>
                        <?php endif; ?>
                        <img src="<?php echo $product['image'] ?? 'https://images.unsplash.com/photo-1584308666744-24d5c474f2ae?w=300'; ?>" alt="<?php echo $product['name']; ?>" class="product-image">
                        <div class="product-info">
                            <h3 class="product-title"><?php echo htmlspecialchars($product['name']); ?></h3>
                            <p class="product-category"><?php echo htmlspecialchars($product['category_name']); ?></p>
                            <div class="product-price">
                                <span class="current">$<?php echo number_format($product['price'], 2); ?></span>
                            </div>
                            <a href="product-details.php?slug=<?php echo $product['slug']; ?>" class="btn btn-outline btn-block">View Details</a>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
</main>

<style>
.products-header {
    text-align: center;
    padding: 3rem 0 2rem;
}
.products-layout {
    display: grid;
    grid-template-columns: 250px 1fr;
    gap: 2rem;
    padding: 2rem 0;
}
.products-sidebar {
    background: white;
    border-radius: var(--radius);
    padding: 1.5rem;
    height: fit-content;
}
.filter-section h3 {
    margin-bottom: 1rem;
}
.filter-section ul {
    list-style: none;
}
.filter-section ul li {
    margin-bottom: 0.5rem;
}
.filter-section ul li a {
    color: var(--text-light);
    text-decoration: none;
    transition: color 0.2s;
}
.filter-section ul li a:hover {
    color: var(--primary);
}
@media (max-width: 768px) {
    .products-layout {
        grid-template-columns: 1fr;
    }
}
</style>

<?php require_once 'includes/footer.php'; ?>