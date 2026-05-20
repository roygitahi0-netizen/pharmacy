<?php
$host = 'localhost';
$user = 'root';
$password = '';
$database = 'pharmacy_db';

$conn = mysqli_connect($host, $user, $password, $database);

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Set timezone
date_default_timezone_set('Africa/Nairobi');

// Start session if not started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Function to get user data
function getUser($id = null) {
    global $conn;
    if ($id === null && isset($_SESSION['user_id'])) {
        $id = $_SESSION['user_id'];
    }
    if ($id) {
        $query = "SELECT * FROM users WHERE id = ?";
        $stmt = mysqli_prepare($conn, $query);
        mysqli_stmt_bind_param($stmt, "i", $id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        return mysqli_fetch_assoc($result);
    }
    return null;
}

// Function to get cart count
function getCartCount() {
    global $conn;
    if (isset($_SESSION['user_id'])) {
        $query = "SELECT SUM(quantity) as total FROM cart WHERE user_id = ?";
        $stmt = mysqli_prepare($conn, $query);
        mysqli_stmt_bind_param($stmt, "i", $_SESSION['user_id']);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $data = mysqli_fetch_assoc($result);
        return $data['total'] ?? 0;
    }
    return 0;
}

// Function to get categories
function getCategories($limit = null) {
    global $conn;
    $query = "SELECT * FROM categories WHERE status = 1 ORDER BY name";
    if ($limit) {
        $query .= " LIMIT $limit";
    }
    $result = mysqli_query($conn, $query);
    return mysqli_fetch_all($result, MYSQLI_ASSOC);
}

// Function to get products
function getProducts($category = null, $limit = null) {
    global $conn;
    $query = "SELECT p.*, c.name as category_name 
              FROM products p 
              LEFT JOIN categories c ON p.category_id = c.id 
              WHERE p.status = 1";
    if ($category) {
        $query .= " AND c.slug = '$category'";
    }
    $query .= " ORDER BY p.created_at DESC";
    if ($limit) {
        $query .= " LIMIT $limit";
    }
    $result = mysqli_query($conn, $query);
    return mysqli_fetch_all($result, MYSQLI_ASSOC);
}

// Function to get single product
function getProduct($slug) {
    global $conn;
    $query = "SELECT p.*, c.name as category_name, c.slug as category_slug 
              FROM products p 
              LEFT JOIN categories c ON p.category_id = c.id 
              WHERE p.slug = ? AND p.status = 1";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "s", $slug);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    return mysqli_fetch_assoc($result);
}
?>