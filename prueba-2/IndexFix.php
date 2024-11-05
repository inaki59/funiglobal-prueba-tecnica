<?php
session_start();

function addToCart($productId, $quantity) {
    // Validación de entrada para asegurar valores válidos
    if (!is_numeric($productId) || !is_numeric($quantity) || $quantity <= 0) {
        echo "Datos inválidos para el producto o cantidad.\n";
        return false;
    }


    if (!isset($_SESSION['cart'])) {
        $_SESSION['cart'] = [];
    }

    // valiadacion de datos
    $productId = (int)$productId;
    $quantity = (int)$quantity;

    // Agregar o actualizar la cantidad del producto en el carrito
    if (isset($_SESSION['cart'][$productId])) {
        $_SESSION['cart'][$productId] += $quantity;
    } else {
        $_SESSION['cart'][$productId] = $quantity;
    }

    return true;
}

// Verificar si el script se ejecuta en la terminal o en un servidor web
if (php_sapi_name() == "cli") {
    echo "Introduce el ID del producto: ";
    $productId = trim(fgets(STDIN));
    
    echo "Introduce la cantidad: ";
    $quantity = trim(fgets(STDIN));

    if (addToCart($productId, $quantity)) {
        echo "Producto agregado al carrito.\n";
    } else {
        echo "Error al agregar el producto al carrito.\n";
}
} else {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $productId = $_POST['product_id'] ?? null;
        $quantity = $_POST['quantity'] ?? null;

        if (addToCart($productId, $quantity)) {
            echo "Producto agregado al carrito.";
        } else {
            echo "Error al agregar el producto al carrito.";
        }
    } else {
        echo "Método de solicitud no válido.";
    }
}
?>
