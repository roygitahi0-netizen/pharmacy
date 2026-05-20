<?php
require_once '../includes/header.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: ../login.php');
    exit;
}

$user_id = $_SESSION['user_id'];
$cart_items = mysqli_query($conn, "SELECT c.*, p.name, p.price, p.image FROM cart c JOIN products p ON c.product_id = p.id WHERE c.user_id = $user_id");
$total = 0;
?>

<div class="container">
    <h1>Shopping Cart</h1>
    <?php if (mysqli_num_rows($cart_items) == 0): ?>
    <div class="empty-cart">
        <i class="fas fa-shopping-cart"></i>
        <h2>Your cart is empty</h2>
        <a href="../products.php" class="btn btn-primary">Continue Shopping</a>
    </div>
    <?php else: ?>
    <div class="cart-grid">
        <div class="cart-items">
            <?php while($item = mysqli_fetch_assoc($cart_items)): 
                $subtotal = $item['price'] * $item['quantity'];
                $total += $subtotal;
            ?>
            <div class="cart-item">
                <img src="<?php echo $item['image']; ?>" alt="<?php echo $item['name']; ?>">
                <div class="cart-item-details">
                    <h3><?php echo $item['name']; ?></h3>
                    <p>$<?php echo number_format($item['price'], 2); ?></p>
                </div>
                <div class="cart-item-quantity">
                    <button class="qty-btn" data-id="<?php echo $item['id']; ?>" data-change="-1">-</button>
                    <span><?php echo $item['quantity']; ?></span>
                    <button class="qty-btn" data-id="<?php echo $item['id']; ?>" data-change="1">+</button>
                </div>
                <div class="cart-item-subtotal">$<?php echo number_format($subtotal, 2); ?></div>
                <button class="remove-btn" data-id="<?php echo $item['id']; ?>"><i class="fas fa-trash"></i></button>
            </div>
            <?php endwhile; ?>
        </div>
        <div class="cart-summary">
            <h3>Order Summary</h3>
            <div class="summary-row">
                <span>Subtotal:</span>
                <span>$<?php echo number_format($total, 2); ?></span>
            </div>
            <div class="summary-row">
                <span>Shipping:</span>
                <span>Free</span>
            </div>
            <div class="summary-row total">
                <span>Total:</span>
                <span>$<?php echo number_format($total, 2); ?></span>
            </div>
            <a href="checkout.php" class="btn btn-primary btn-block">Proceed to Checkout</a>
        </div>
    </div>
    <?php endif; ?>
</div>

<style>
.cart-grid {
    display: grid;
    grid-template-columns: 2fr 1fr;
    gap: 2rem;
    padding: 2rem 0;
}
.cart-item {
    display: grid;
    grid-template-columns: 100px 2fr 1fr 1fr auto;
    gap: 1rem;
    align-items: center;
    background: white;
    padding: 1rem;
    border-radius: 0.5rem;
    margin-bottom: 1rem;
}
.cart-item img {
    width: 80px;
    height: 80px;
    object-fit: cover;
    border-radius: 0.5rem;
}
.cart-summary {
    background: white;
    padding: 1.5rem;
    border-radius: 0.5rem;
    position: sticky;
    top: 80px;
}
.summary-row {
    display: flex;
    justify-content: space-between;
    padding: 0.5rem 0;
}
.summary-row.total {
    font-weight: bold;
    font-size: 1.25rem;
    border-top: 1px solid #e2e8f0;
    margin-top: 0.5rem;
    padding-top: 1rem;
}
.empty-cart {
    text-align: center;
    padding: 4rem;
}
.empty-cart i {
    font-size: 4rem;
    color: #cbd5e1;
}
</style>

<?php require_once '../includes/footer.php'; ?>