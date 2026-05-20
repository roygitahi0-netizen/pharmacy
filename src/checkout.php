<?php
require_once '../includes/header.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: ../login.php');
    exit;
}

$user = getUser();
$cart_items = mysqli_query($conn, "SELECT c.*, p.name, p.price FROM cart c JOIN products p ON c.product_id = p.id WHERE c.user_id = {$_SESSION['user_id']}");
$total = 0;
while($item = mysqli_fetch_assoc($cart_items)) {
    $total += $item['price'] * $item['quantity'];
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $order_number = 'ORD' . date('YmdHis');
    $payment_method = $_POST['payment_method'];
    $address = $_POST['address'];
    $city = $_POST['city'];
    $zip = $_POST['zip'];
    
    mysqli_begin_transaction($conn);
    
    $query = "INSERT INTO orders (order_number, user_id, total_amount, payment_method, shipping_address, shipping_city, shipping_zip) 
              VALUES ('$order_number', {$_SESSION['user_id']}, $total, '$payment_method', '$address', '$city', '$zip')";
    mysqli_query($conn, $query);
    $order_id = mysqli_insert_id($conn);
    
    $cart_items = mysqli_query($conn, "SELECT c.*, p.name, p.price FROM cart c JOIN products p ON c.product_id = p.id WHERE c.user_id = {$_SESSION['user_id']}");
    while($item = mysqli_fetch_assoc($cart_items)) {
        $subtotal = $item['price'] * $item['quantity'];
        mysqli_query($conn, "INSERT INTO order_items (order_id, product_id, product_name, product_price, quantity, total) 
                             VALUES ($order_id, {$item['product_id']}, '{$item['name']}', {$item['price']}, {$item['quantity']}, $subtotal)");
    }
    
    mysqli_query($conn, "DELETE FROM cart WHERE user_id = {$_SESSION['user_id']}");
    mysqli_commit($conn);
    
    header("Location: order-success.php?order=$order_number");
    exit;
}
?>

<div class="container">
    <h1>Checkout</h1>
    <div class="checkout-grid">
        <form method="POST" class="checkout-form">
            <div class="form-section">
                <h3>Shipping Information</h3>
                <div class="form-group">
                    <label>Full Name</label>
                    <input type="text" value="<?php echo htmlspecialchars($user['name']); ?>" readonly>
                </div>
                <div class="form-group">
                    <label>Email</label>
                    <input type="email" value="<?php echo htmlspecialchars($user['email']); ?>" readonly>
                </div>
                <div class="form-group">
                    <label>Address</label>
                    <textarea name="address" required><?php echo htmlspecialchars($user['address']); ?></textarea>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label>City</label>
                        <input type="text" name="city" required>
                    </div>
                    <div class="form-group">
                        <label>Zip Code</label>
                        <input type="text" name="zip" required>
                    </div>
                </div>
            </div>
            
            <div class="form-section">
                <h3>Payment Method</h3>
                <label class="payment-option">
                    <input type="radio" name="payment_method" value="cash" checked>
                    <span>Cash on Delivery</span>
                </label>
                <label class="payment-option">
                    <input type="radio" name="payment_method" value="card">
                    <span>Credit/Debit Card</span>
                </label>
                <label class="payment-option">
                    <input type="radio" name="payment_method" value="mobile">
                    <span>Mobile Money</span>
                </label>
            </div>
            
            <button type="submit" class="btn btn-primary btn-large">Place Order - $<?php echo number_format($total, 2); ?></button>
        </form>
        
        <div class="order-summary">
            <h3>Your Order</h3>
            <?php
            $cart_items = mysqli_query($conn, "SELECT c.*, p.name, p.price FROM cart c JOIN products p ON c.product_id = p.id WHERE c.user_id = {$_SESSION['user_id']}");
            while($item = mysqli_fetch_assoc($cart_items)):
            ?>
            <div class="order-item">
                <span><?php echo $item['name']; ?> x<?php echo $item['quantity']; ?></span>
                <span>$<?php echo number_format($item['price'] * $item['quantity'], 2); ?></span>
            </div>
            <?php endwhile; ?>
            <div class="order-total">
                <span>Total</span>
                <strong>$<?php echo number_format($total, 2); ?></strong>
            </div>
        </div>
    </div>
</div>

<style>
.checkout-grid {
    display: grid;
    grid-template-columns: 2fr 1fr;
    gap: 2rem;
    padding: 2rem 0;
}
.form-section {
    background: white;
    padding: 1.5rem;
    border-radius: 0.5rem;
    margin-bottom: 1.5rem;
}
.form-row {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 1rem;
}
.form-group textarea,
.form-group input {
    width: 100%;
    padding: 0.75rem;
    border: 1px solid #e2e8f0;
    border-radius: 0.5rem;
}
.payment-option {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.75rem;
    border: 1px solid #e2e8f0;
    border-radius: 0.5rem;
    margin-bottom: 0.5rem;
    cursor: pointer;
}
.order-summary {
    background: white;
    padding: 1.5rem;
    border-radius: 0.5rem;
    height: fit-content;
}
.order-item {
    display: flex;
    justify-content: space-between;
    padding: 0.5rem 0;
    border-bottom: 1px solid #e2e8f0;
}
.order-total {
    display: flex;
    justify-content: space-between;
    padding-top: 1rem;
    margin-top: 1rem;
    border-top: 2px solid #e2e8f0;
    font-size: 1.25rem;
}
</style>

<?php require_once '../includes/footer.php'; ?>