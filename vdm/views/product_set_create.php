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

$success;

// Handle product creation
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $success = $store->createProductSet(
        $_POST['productTitle'],
        $_POST['productDescription'],
        $_POST['productSetItems'],
        $_POST['productInStock']
    );
}

?>

<?php include $_SERVER['DOCUMENT_ROOT'] . '/views/header.php'; ?>

<div class="my-3">
    <h3>New product set</h3>
    <?php
    if (!is_null($success)) {
        if ($success) {
            echo '<div class="alert alert-success" role="alert"> Product set created! </div>';
        } else {
            echo '<div class="alert alert-danger" role="alert"> Failed to create product set! </div>';
        }
    }
    ?>
    <form action="/views/product_set_create.php" method="post">
        <div class="mb-3">
            <label for="productTitle" class="form-label">Title</label>
            <input type="text" class="form-control" name="productTitle" id="productTitle" placeholder="Enter title..." maxlength="200" required>
        </div>
        <div class="mb-3">
            <label for="productDescription" class="form-label">Description</label>
            <textarea class="form-control" name="productDescription" id="productDescription" placeholder="Enter description..." maxlength="1000" rows="5" required></textarea>
        </div>
        <div class="mb-3">
            <label for="productSetItems" class="form-label">Items</label>
            <select style="height: 20rem;" class="form-select" aria-label="Items selection" name="productSetItems[]" required multiple>
                <?php
                while ($row = $products->fetch_assoc()) {
                    echo "<option value='{$row['id']}'>{$row['id']}. {$row['title']}</option>";
                }
                ?>
            </select>
        </div>
        <div class="mb-3">
            <input name="productInStock" class="form-check-input" type="checkbox" id="productInStock">
            <label class="form-check-label" for="productInStock">
                In stock
            </label>
        </div>
        <div class="d-flex justify-content-end">
            <button id="productSubmit" class="btn btn-primary" type="submit">Create</button>
        </div>
    </form>
</div>

<?php include $_SERVER['DOCUMENT_ROOT'] . '/views/footer.php'; ?>