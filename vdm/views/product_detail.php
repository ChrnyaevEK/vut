<?php
define('__is_view__', TRUE);


include $_SERVER['DOCUMENT_ROOT'] . '/utils/access.php';
include $_SERVER['DOCUMENT_ROOT'] . '/utils/env.php';

// Check access
accessControl();

// Load env variables
$env = new Env($_SERVER['DOCUMENT_ROOT'] . '/.env');
$env->load();

// Avoid running page without parameters
if (!isset($_GET['id']) && is_numeric($_GET['id'])) {
    header('Location: ' . '/views/home.php');
    die();
}

include $_SERVER['DOCUMENT_ROOT'] . '/controllers/store.php';

$store = new Store();
$productId = intval($_GET['id']);

$product = $store->getProduct($productId);
$product = $product->fetch_assoc();

// Product not found
if (is_null($product)) {
    header('Location: ' . '/views/home.php');
    die();
}
?>

<?php include $_SERVER['DOCUMENT_ROOT'] . '/views/header.php'; ?>

<div class="my-3">
    <h3><?= $product['title'] ?></h3>
    <p><?= $product['description'] ?></p>
    <p>
        <span class="fw-bold">Price</span> <?= $product['price'] ?></br>
        <span class="fw-bold">Amount</span> <?= $product['amount'] ?>
    </p>
</div>

<?php include $_SERVER['DOCUMENT_ROOT'] . '/views/footer.php'; ?>