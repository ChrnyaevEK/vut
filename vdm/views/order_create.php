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

$success;

// Handle product creation
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $success = $store->createOrder(
        $_POST['orderProducts'],
        $_POST['orderProductSets'],
        $_POST['orderDeliveryDate'],
        $_POST['orderNote']
    );
}

?>

<?php include $_SERVER['DOCUMENT_ROOT'] . '/views/header.php'; ?>

<div class="my-3">
    <h3>New order</h3>
    <?php
    if (!is_null($success)) {
        if ($success) {
            echo '<div class="alert alert-success" role="alert"> Order created! </div>';
        } else {
            echo '<div class="alert alert-danger" role="alert"> Failed to create order! </div>';
        }
    }
    ?>
    <form action="/views/order_create.php" method="post">
        <div class="mb-3">
            <label for="orderDeliveryDate" class="form-label">Delivery date</label>
            <input type="date" class="form-control" name="orderDeliveryDate" id="orderDeliveryDate" required>
        </div>
        <div class="mb-3">
            <label for="orderNote" class="form-label">Note</label>
            <textarea class="form-control" name="orderNote" id="orderNote" maxlength="1000" rows="5"></textarea>
        </div>
        <div class="mb-3">
            <label for="orderProducts" class="form-label">Products</label>
            <select style="height: 10rem;" class="form-select" aria-label="Products selection" name="orderProducts[]" multiple>
                <?php
                while ($row = $products->fetch_assoc()) {
                    echo "<option value='{$row['id']}'>{$row['id']}. {$row['title']} ({$row['amount']})</option>";
                }
                ?>
            </select>
        </div>
        <div class="mb-3">
            <label for="orderProductSets" class="form-label">Product sets</label>
            <select style="height: 10rem;" class="form-select" aria-label="Product sets selection" name="orderProductSets[]" multiple>
                <?php
                while ($row = $productSets->fetch_assoc()) {
                    echo "<option value='{$row['id']}'>{$row['id']}. {$row['title']} ({$row['amount']})</option>";
                }
                ?>
            </select>
        </div>
        <div class="d-flex justify-content-end">
            <button id="productSubmit" class="btn btn-primary" type="submit">Create</button>
        </div>
    </form>
</div>

<?php include $_SERVER['DOCUMENT_ROOT'] . '/views/footer.php'; ?>