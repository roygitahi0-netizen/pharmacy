<?php
require_once 'includes/header.php';

$categories = getCategories();
$featuredProducts = getProducts(null, 8);
?>
<main>
    <!-- Hero Section -->
    <section class="hero">
        <div class="container">
            <div class="hero-grid">
                <div class="hero-content">
                    <div class="hero-badge">
                        <i class="fas fa-shield-alt"></i>
                        <span>Trusted Since 1995</span>
                    </div>
                    <h1 class="hero-title">
                        Your Health,<br>
                        <span class="gradient-text">Our Priority</span>
                    </h1>
                    <p class="hero-description">
                        Get authentic medicines, healthcare products, and expert advice delivered to your doorstep. 
                        24/7 online consultation available.
                    </p>
                    <div class="hero-buttons">
                        <a href="products.php" class="btn btn-primary btn-large">
                            Shop Now
                            <i class="fas fa-arrow-right"></i>
                        </a>
                        <a href="#features" class="btn btn-outline btn-large">
                            Learn More
                        </a>
                    </div>
                    <div class="hero-stats">
                        <div class="stat">
                            <div class="stat-number">50k+</div>
                            <div class="stat-label">Happy Customers</div>
                        </div>
                        <div class="stat">
                            <div class="stat-number">500+</div>
                            <div class="stat-label">Products</div>
                        </div>
                        <div class="stat">
                            <div class="stat-number">24/7</div>
                            <div class="stat-label">Support</div>
                        </div>
                    </div>
                </div>
                <div class="hero-image">
                    <div class="floating-card card-1">
                        <i class="fas fa-truck-fast"></i>
                        <span>Free Delivery</span>
                    </div>
                    <div class="floating-card card-2">
                        <i class="fas fa-certificate"></i>
                        <span>100% Authentic</span>
                    </div>
                    <div class="floating-card card-3">
                        <i class="fas fa-clock"></i>
                        <span>Same Day Delivery</span>
                    </div>
                    <img src="https://images.unsplash.com/photo-1584308666744-24d5c474f2ae?w=600" alt="Pharmacist" class="hero-img">
                </div>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section class="features" id="features">
        <div class="container">
            <div class="section-header">
                <h2 class="section-title">Why Choose Us</h2>
                <p class="section-subtitle">We provide the best healthcare services with quality products</p>
            </div>
            <div class="features-grid">
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-flask"></i>
                    </div>
                    <h3>Quality Medicines</h3>
                    <p>All products are genuine and sourced from certified manufacturers</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-user-md"></i>
                    </div>
                    <h3>Expert Pharmacists</h3>
                    <p>Certified pharmacists available for consultation</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-prescription-bottle"></i>
                    </div>
                    <h3>Prescription Support</h3>
                    <p>Upload prescription and get medicines delivered</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-credit-card"></i>
                    </div>
                    <h3>Secure Payment</h3>
                    <p>Multiple payment options with secure checkout</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Categories Section -->
    <section class="categories">
        <div class="container">
            <div class="section-header">
                <h2 class="section-title">Shop by Category</h2>
                <p class="section-subtitle">Browse our wide range of healthcare products</p>
            </div>
            <div class="categories-grid">
                <?php foreach ($categories as $category): ?>
                <a href="products.php?category=<?php echo $category['slug']; ?>" class="category-card">
                    <div class="category-icon">
                        <i class="fas fa-capsules"></i>
                    </div>
                    <h3><?php echo htmlspecialchars($category['name']); ?></h3>
                    <p><?php echo htmlspecialchars($category['description']); ?></p>
                    <span class="category-link">Shop Now <i class="fas fa-arrow-right"></i></span>
                </a>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- Featured Products -->
    <section class="products">
        <div class="container">
            <div class="section-header">
                <h2 class="section-title">Featured Products</h2>
                <p class="section-subtitle">Most popular healthcare items</p>
            </div>
            <div class="products-grid">
                <?php foreach ($featuredProducts as $product): ?>
                <div class="product-card">
                    <?php if ($product['prescription_required']): ?>
                    <div class="product-badge prescription">Prescription Required</div>
                    <?php endif; ?>
                    <?php if ($product['compare_price'] && $product['compare_price'] > $product['price']): ?>
                    <div class="product-badge sale">Sale</div>
                    <?php endif; ?>
                    <img src="<?php echo $product['image'] ?? 'https://images.unsplash.com/photo-1584308666744-24d5c474f2ae?w=300'; ?>" alt="<?php echo $product['name']; ?>" class="product-image">
                    <div class="product-info">
                        <h3 class="product-title"><?php echo htmlspecialchars($product['name']); ?></h3>
                        <p class="product-category"><?php echo htmlspecialchars($product['category_name']); ?></p>
                        <div class="product-rating">
                            <i class="fas fa-star"></i>
                            <span><?php echo $product['rating']; ?></span>
                        </div>
                        <div class="product-price">
                            <span class="current">$<?php echo number_format($product['price'], 2); ?></span>
                            <?php if ($product['compare_price']): ?>
                            <span class="compare">$<?php echo number_format($product['compare_price'], 2); ?></span>
                            <?php endif; ?>
                        </div>
                        <button class="btn btn-primary btn-block add-to-cart" data-id="<?php echo $product['id']; ?>">
                            Add to Cart
                        </button>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- Prescription Banner -->
    <section class="prescription-banner">
        <div class="container">
            <div class="banner-content">
                <div class="banner-text">
                    <i class="fas fa-file-prescription"></i>
                    <h3>Need Prescription Medicines?</h3>
                    <p>Upload your prescription and we'll deliver your medicines</p>
                </div>
                <button class="btn btn-light" onclick="openPrescriptionModal()">
                    Upload Prescription
                    <i class="fas fa-upload"></i>
                </button>
            </div>
        </div>
    </section>

    <!-- Newsletter -->
    <section class="newsletter">
        <div class="container">
            <div class="newsletter-content">
                <h3>Subscribe to Our Newsletter</h3>
                <p>Get health tips, exclusive offers, and updates directly to your inbox</p>
                <form class="newsletter-form" id="newsletterForm">
                    <input type="email" placeholder="Enter your email" required>
                    <button type="submit" class="btn btn-primary">Subscribe</button>
                </form>
            </div>
        </div>
    </section>
</main>

<?php require_once 'includes/footer.php'; ?>