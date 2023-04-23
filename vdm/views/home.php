<?php

define('__is_view__', TRUE);

include $_SERVER['DOCUMENT_ROOT'] . '/utils/access.php';
include $_SERVER['DOCUMENT_ROOT'] . '/utils/env.php';

// Check access
accessControl();

// Load env variables
$env = new Env($_SERVER['DOCUMENT_ROOT'] . '/.env');
$env->load();

include $_SERVER['DOCUMENT_ROOT'] . '/controllers/store.php';

$store = new Store();

$products = $store->listProducts();
$productSets = $store->listProductSets();
$orders = $store->listOrders();

?>

<?php include $_SERVER['DOCUMENT_ROOT'] . '/views/header.php'; ?>

<div class="my-3">
    <h3>Products</h3>
    <p> List of all products. </p>
    <?php if ($products->num_rows > 0) { ?>
        <table class="table">
            <thead>
                <tr>
                    <th>Title</th>
                    <th>Description</th>
                    <th>Price</th>
                    <th>Amount</th>
                </tr>
            </thead>
            <tbody>
                <?php
                while ($row = $products->fetch_assoc()) {
                    echo
                    "<tr>
                    <th><a href='/views/product_detail.php?id={$row['id']}'> {$row['title']}</th>
                    <td>{$row['description']}</td>
                    <td>{$row['price']}</td>
                    <td>{$row['amount']}</td>
                    </tr>";
                }
                ?>
            </tbody>
        </table>
    <?php } else { ?>
        <div class="alert alert-primary" role="alert"> Nothing is here yet... </div>
    <?php } ?>
</div>

<div class="my-3">
    <h3>Product sets</h3>
    <p> List of all product sets. </p>
    <?php if ($productSets->num_rows > 0) { ?>
        <table class="table">
            <thead>
                <tr>
                    <th>Title</th>
                    <th>Description</th>
                    <th>Price</th>
                    <th>Amount</th>
                </tr>
            </thead>
            <tbody>
                <?php
                while ($row = $productSets->fetch_assoc()) {
                    echo
                    "<tr>
                        <th><a href='/views/product_set_detail.php?id={$row['id']}'> {$row['title']}</th>
                        <td>{$row['description']}</td>
                        <td>{$row['price']}</td>
                        <td>{$row['amount']}</td>
                    </tr>";
                }
                ?>
            </tbody>
        </table>
    <?php } else { ?>
        <div class="alert alert-primary" role="alert"> Nothing is here yet... </div>
    <?php } ?>
</div>

<div class="my-3">
    <h3>Orders</h3>
    <?php if ($orders->num_rows > 0) { ?>
        <table class="table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Delivery date</th>
                    <th>Note</th>
                </tr>
            </thead>
            <tbody>
                <?php
                while ($row = $orders->fetch_assoc()) {
                    echo
                    "<tr>
                    <th><a href='/views/order_detail.php?id={$row['id']}'> {$row['id']}</th>
                    <td>{$row['delivery_date']}</td>
                    <td style='text-overflow: ellipsis;'>{$row['note']}</td>
                    </tr>";
                }
                ?>
            </tbody>
        </table>
    <?php } else { ?>
        <div class="alert alert-primary" role="alert"> Nothing is here yet... </div>
    <?php } ?>
    <div class="d-flex justify-content-end">
        <a class="btn btn-primary" href="/views/order_create.php">New order</a>
    </div>
</div>

<?php include $_SERVER['DOCUMENT_ROOT'] . '/views/footer.php'; ?>