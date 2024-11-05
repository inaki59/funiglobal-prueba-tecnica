<?php
session_start();
function addToCart($productId, $quantity) {
if (!isset($_SESSION['cart'])) {
$_SESSION['cart'] = [];
}
if (isset($_SESSION['cart'][$productId])) {
$_SESSION['cart'][$productId] += $quantity;
} else {
$_SESSION['cart'][$productId] = $quantity;
}
}
$productId = $_POST['product_id'];
$quantity = $_POST['quantity'];
if ($quantity <= 0) {
echo "Cantidad inválida.";
return;
}
addToCart($productId, $quantity);
echo "Producto agregado al carrito.";
?>