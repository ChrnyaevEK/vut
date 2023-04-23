<?php
define('__is_api__', TRUE);

include $_SERVER['DOCUMENT_ROOT'] . '/utils/access.php';
include $_SERVER['DOCUMENT_ROOT'] . '/utils/env.php';

// Check access
accessControl();

// Load env variables
$env = new Env($_SERVER['DOCUMENT_ROOT'] . '/.env');
$env->load();

include $_SERVER['DOCUMENT_ROOT'] . '/controllers/store.php';

$store = new Store();

// Handle product creation
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    try {
        $success = $store->removeProductSet($_POST['productSetId']);
        $reason = null;
    } catch (Exception $e) {
        http_response_code(409);
        $success = false;
        $reason = "Product set is referenced in orders.";
    }

    echo json_encode(array('result' =>  $success, 'reason' => $reason));
} else {
    http_response_code(405);
    die();
}
