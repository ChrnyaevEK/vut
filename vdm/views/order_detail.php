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
$orderId = intval($_GET['id']);

$order = $store->getOrder($orderId)->fetch_assoc();
$products = $store->getOrderProducts($orderId)->fetch_all(MYSQLI_ASSOC);
$productSets = $store->getOrderProductSets($orderId)->fetch_all(MYSQLI_ASSOC);


$totalPrice = 0;
foreach ($products as $product) {
    $totalPrice += $product['price'];
}
foreach ($productSets as $productSet) {
    $totalPrice += $productSet['price'];
}

// Order not found
if (is_null($order)) {
    header('Location: ' . '/views/home.php');
    die();
}
?>

<?php include $_SERVER['DOCUMENT_ROOT'] . '/views/header.php'; ?>

<div class="my-3">
    <h3>Order #<?= $order['id'] ?></h3>
    <p>
        <span class="fw-bold">Note</span>: <?= $order['note'] ?></br>
        <span class="fw-bold">Delivery date</span>: <?= $order['delivery_date'] ?>
    </p>
    <div class="my-3">
        <table class="table">
            <thead>
                <tr>
                    <th>Title</th>
                    <th>Price</th>
                </tr>
            </thead>
            <tbody>
                <?php
                foreach ($products as $row) {
                    echo
                    "<tr>
                    <th><a href='/views/product_detail.php?id={$row['id']}'> {$row['title']}</th>
                    <td>{$row['price']}</td>
                    </tr>";
                }
                ?>
                <?php
                foreach ($productSets as $row) {
                    echo
                    "<tr>
                    <th><a href='/views/product_set_detail.php?id={$row['id']}'> {$row['title']}</th>
                    <td>{$row['price']}</td>
                    </tr>";
                }
                ?>
            </tbody>
        </table>
    </div>
    <p>
        <span class="fw-bold">Total</span>: <?= $totalPrice ?></br>
    </p>
</div>

<?php include $_SERVER['DOCUMENT_ROOT'] . '/views/footer.php'; ?>