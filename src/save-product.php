<?php
session_start();
require_once '../includes/db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = $_POST['id'] ?? 0;
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $slug = mysqli_real_escape_string($conn, strtolower(str_replace(' ', '-', $_POST['slug'] ?: $_POST['name'])));
    $category_id = $_POST['category_id'];
    $brand = mysqli_real_escape_string($conn, $_POST['brand']);
    $generic_name = mysqli_real_escape_string($conn, $_POST['generic_name']);
    $description = mysqli_real_escape_string($conn, $_POST['description']);
    $price = $_POST['price'];
    $compare_price = $_POST['compare_price'] ?: null;
    $quantity = $_POST['quantity'];
    $prescription_required = $_POST['prescription_required'];
    
    // Handle image upload
    $image = '';
    if ($_FILES['image']['name']) {
        $target_dir = "../assets/uploads/";
        if (!is_dir($target_dir)) mkdir($target_dir, 0777, true);
        $image = $target_dir . time() . '_' . basename($_FILES['image']['name']);
        move_uploaded_file($_FILES['image']['tmp_name'], $image);
        $image = str_replace('../', '', $image);
    }
    
    if ($id) {
        // Update existing product
        $query = "UPDATE products SET name='$name', slug='$slug', category_id=$category_id, brand='$brand', generic_name='$generic_name', description='$description', price=$price, compare_price=$compare_price, quantity=$quantity, prescription_required=$prescription_required";
        if ($image) $query .= ", image='$image'";
        $query .= " WHERE id=$id";
    } else {
        // Insert new product
        $query = "INSERT INTO products (name, slug, category_id, brand, generic_name, description, price, compare_price, quantity, prescription_required, image) 
                  VALUES ('$name', '$slug', $category_id, '$brand', '$generic_name', '$description', $price, $compare_price, $quantity, $prescription_required, '$image')";
    }
    
    mysqli_query($conn, $query);
    header('Location: products.php');
}
?>