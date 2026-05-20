<?php
session_start();
require_once '../includes/db.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Please login first']);
    exit;
}

$product_id = $_POST['product_id'] ?? 0;
$quantity = $_POST['quantity'] ?? 1;

if ($product_id) {
    $user_id = $_SESSION['user_id'];
    
    // Check if product already in cart
    $check = mysqli_query($conn, "SELECT id, quantity FROM cart WHERE user_id = $user_id AND product_id = $product_id");
    
    if (mysqli_num_rows($check) > 0) {
        $cart = mysqli_fetch_assoc($check);
        $new_qty = $cart['quantity'] + $quantity;
        mysqli_query($conn, "UPDATE cart SET quantity = $new_qty WHERE id = {$cart['id']}");
    } else {
        mysqli_query($conn, "INSERT INTO cart (user_id, product_id, quantity) VALUES ($user_id, $product_id, $quantity)");
    }
    
    // Get updated cart count
    $count_result = mysqli_query($conn, "SELECT SUM(quantity) as total FROM cart WHERE user_id = $user_id");
    $count = mysqli_fetch_assoc($count_result);
    
    echo json_encode(['success' => true, 'cart_count' => $count['total'] ?? 0]);
} else {
    echo json_encode(['success' => false]);
}
?>