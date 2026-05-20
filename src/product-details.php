<?php
require_once 'includes/header.php';

$slug = isset($_GET['slug']) ? $_GET['slug'] : null;
$product = getProduct($slug);

if (!$product) {
    header('Location: products.php');
    exit;
}
?>

<main>
    <div class="container">
        <div class="product-detail">
            <div class="product-detail-grid">
                <div class="product-gallery">
                    <img src="<?php echo $product['image'] ?? 'https://images.unsplash.com/photo-1584308666744-24d5c474f2ae?w=500'; ?>" alt="<?php echo $product['name']; ?>">
                </div>
                <div class="product-info-detail">
                    <h1><?php echo htmlspecialchars($product['name']); ?></h1>
                    <div class="product-meta">
                        <span class="brand">Brand: <?php echo htmlspecialchars($product['brand']); ?></span>
                        <span class="category">Category: <?php echo htmlspecialchars($product['category_name']); ?></span>
                    </div>
                    <div class="product-price-detail">
                        <span class="current">$<?php echo number_format($product['price'], 2); ?></span>
                        <?php if ($product['compare_price']): ?>
                        <span class="compare">$<?php echo number_format($product['compare_price'], 2); ?></span>
                        <?php endif; ?>
                    </div>
                    <?php if ($product['prescription_required']): ?>
                    <div class="prescription-warning">
                        <i class="fas fa-prescription-bottle"></i>
                        <span>Prescription Required for this medication</span>
                    </div>
                    <?php endif; ?>
                    <div class="product-description">
                        <h3>Description</h3>
                        <p><?php echo nl2br(htmlspecialchars($product['description'])); ?></p>
                    </div>
                    <?php if ($product['generic_name']): ?>
                    <div class="product-generic">
                        <strong>Generic Name:</strong> <?php echo htmlspecialchars($product['generic_name']); ?>
                    </div>
                    <?php endif; ?>
                    <div class="product-actions">
                        <div class="quantity-selector">
                            <label>Quantity:</label>
                            <input type="number" id="quantity" value="1" min="1" max="<?php echo $product['quantity']; ?>">
                        </div>
                        <button class="btn btn-primary btn-large add-to-cart" data-id="<?php echo $product['id']; ?>">
                            <i class="fas fa-shopping-cart"></i> Add to Cart
                        </button>
                        <?php if ($product['prescription_required']): ?>
                        <button class="btn btn-outline btn-large" onclick="openPrescriptionModal()">
                            <i class="fas fa-file-prescription"></i> Upload Prescription
                        </button>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<style>
.product-detail {
    padding: 3rem 0;
}
.product-detail-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 3rem;
}
.product-gallery img {
    width: 100%;
    border-radius: var(--radius);
    box-shadow: var(--shadow-lg);
}
.product-meta {
    display: flex;
    gap: 1rem;
    color: var(--text-light);
    margin: 0.5rem 0 1rem;
}
.product-price-detail {
    margin: 1rem 0;
}
.product-price-detail .current {
    font-size: 2rem;
    font-weight: 700;
    color: var(--primary);
}
.product-price-detail .compare {
    font-size: 1rem;
    color: var(--text-light);
    text-decoration: line-through;
    margin-left: 0.5rem;
}
.prescription-warning {
    background: var(--warning);
    color: white;
    padding: 0.75rem;
    border-radius: var(--radius-sm);
    margin: 1rem 0;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}
.product-description {
    margin: 1.5rem 0;
}
.product-description h3 {
    margin-bottom: 0.5rem;
}
.product-actions {
    display: flex;
    gap: 1rem;
    margin-top: 1.5rem;
    flex-wrap: wrap;
}
.quantity-selector {
    display: flex;
    align-items: center;
    gap: 0.5rem;
}
.quantity-selector input {
    width: 80px;
    padding: 0.5rem;
    border: 1px solid var(--border);
    border-radius: var(--radius-sm);
}
@media (max-width: 768px) {
    .product-detail-grid {
        grid-template-columns: 1fr;
    }
}
</style>

<?php require_once 'includes/footer.php'; ?>