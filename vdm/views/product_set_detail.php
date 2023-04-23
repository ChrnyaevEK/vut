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
$productSetId = intval($_GET['id']);

$productSet = $store->getProductSet($productSetId);
$productSet = $productSet->fetch_assoc();

// Product set not found
if (is_null($productSet)) {
    header('Location: ' . '/views/home.php');
    die();
}

$productSetItems = $store->getProductSetItems($productSetId);

// Product set is empty
if ($productSetItems->num_rows == 0) {
    header('Location: ' . '/views/home.php');
    die();
}

?>

<?php include $_SERVER['DOCUMENT_ROOT'] . '/views/header.php'; ?>

<div class="my-3">
    <h3><?= $productSet['title'] ?></h3>
    <p><?= $productSet['description'] ?></p>
    <p>
        <span class="fw-bold">Price</span> <?= $productSet['price'] ?></br>
        <span class="fw-bold">Amount</span> <?= $productSet['amount'] ?>
    </p>
    <p> Product set items. </p>
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
            while ($row = $productSetItems->fetch_assoc()) {
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

    <div class="d-flex justify-content-end">
        <a id="productDelete" class="btn btn-danger">Delete</a>
    </div>
</div>
<script>
    $(document).ready(function() {
        $("#productDelete").click(function() {
            if (confirm('Please confirm set deletion.')) {
                $.ajax({
                    url: '/api/product_set.php',
                    method: 'POST',
                    data: {
                        productSetId: "<?= $productSet['id'] ?>"
                    },
                    success: function(resp) {
                        alert('Deleted!')
                        window.location.href = '/views/home.php';
                    },
                    error: function(resp) {
                        resp = JSON.parse(resp.responseText)
                        alert(resp.reason)
                    }
                })
            }
        })
    })
</script>

<?php include $_SERVER['DOCUMENT_ROOT'] . '/views/footer.php'; ?>