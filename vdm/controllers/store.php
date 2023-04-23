<?php
include $_SERVER['DOCUMENT_ROOT'] . '/utils/access.php';
accessControl();

include $_SERVER['DOCUMENT_ROOT'] . '/controllers/database.php';

class Store
{
    protected $connection;

    function __construct()
    {
        $this->connection = getConnection();
    }

    function listProducts(): mysqli_result
    {
        return $this->connection->query('SELECT * FROM vdm.product');
    }

    function getProduct(int $pk): mysqli_result
    {
        return $this->connection->query("SELECT * FROM vdm.product WHERE id = $pk");
    }

    function createProduct(
        string $title,
        string $description,
        string $price,
        string $amount,
        string $productWarehouse,
        ?string $inStock
    ): bool {

        $inStock = is_null($inStock) ? "0" : "1";

        $title = mysqli_escape_string($this->connection, $title);
        $description = mysqli_escape_string($this->connection, $description);
        $price = mysqli_escape_string($this->connection, $price);
        $amount = mysqli_escape_string($this->connection, $amount);
        $productWarehouse = mysqli_escape_string($this->connection, $productWarehouse);
        $inStock = mysqli_escape_string($this->connection, $inStock);

        return $this->connection->query(
            "INSERT INTO vdm.product (title, description, price, amount, in_stock, warehouse_id) 
            VALUES ('$title', '$description', $price, $amount, $inStock, $productWarehouse)"
        );
    }


    function listProductSets(): mysqli_result
    {
        return $this->connection->query(
            'SELECT vdm.product_set.*, tmp.amount, tmp.price from
                (SELECT vdm.product_set.id as id, min(vdm.product.amount) as amount, sum(vdm.product.price) as price FROM vdm.product_set 
                JOIN vdm.product_product_set ON vdm.product_set.id = vdm.product_product_set.product_set_id
                JOIN vdm.product ON vdm.product.id = vdm.product_product_set.product_id
                GROUP BY vdm.product_set.id) as tmp 
            JOIN vdm.product_set ON tmp.id = vdm.product_set.id'
        );
    }

    function getProductSet(int $pk): mysqli_result
    {
        return $this->connection->query(
            "SELECT vdm.product_set.*, tmp.amount, tmp.price FROM
                (SELECT min(vdm.product_set.id) as id, min(vdm.product.amount) AS amount, sum(vdm.product.price) AS price FROM vdm.product_set 
                JOIN vdm.product_product_set ON vdm.product_set.id = vdm.product_product_set.product_set_id
                JOIN vdm.product ON vdm.product.id = vdm.product_product_set.product_id
                WHERE vdm.product_set.id = $pk) as tmp 
            JOIN vdm.product_set ON tmp.id = vdm.product_set.id 
            WHERE vdm.product_set.id = $pk"
        );
    }

    function getProductSetItems(int $pk): mysqli_result
    {
        return $this->connection->query(
            "SELECT vdm.product.* FROM vdm.product_set 
            JOIN vdm.product_product_set ON vdm.product_set.id = vdm.product_product_set.product_set_id
            JOIN vdm.product ON vdm.product.id = vdm.product_product_set.product_id
            WHERE vdm.product_set.id = $pk"
        );
    }

    function createProductSet(
        string $title,
        string $description,
        array $items,
        ?string $inStock
    ): bool {
        $inStock = is_null($inStock) ? "0" : "1";

        foreach ($items as $item) {
            if (!is_numeric($item)) {
                return false;
            }
        }

        $title = mysqli_escape_string($this->connection, $title);
        $description = mysqli_escape_string($this->connection, $description);
        $inStock = mysqli_escape_string($this->connection, $inStock);

        $sql = "INSERT INTO vdm.product_set (title, description, in_stock) VALUES ('$title', '$description', $inStock)";
        if (!$this->connection->query($sql)) {
            return false;
        }

        $productSetId = $this->connection->insert_id;
        foreach ($items as $item) {
            $sql = "INSERT INTO vdm.product_product_set (product_id, product_set_id) VALUES ($item, $productSetId)";
            if (!$this->connection->query($sql)) {
                return false;
            }
        }

        return true;
    }

    function removeProductSet(string $pk)
    {
        if (!$this->connection->query(
            "DELETE FROM vdm.product_product_set WHERE vdm.product_product_set.product_set_id = $pk"
        )) {
            return false;
        }
        return $this->connection->query(
            "DELETE FROM vdm.product_set WHERE vdm.product_set.id = $pk"
        );
    }

    function listWarehouses(): mysqli_result
    {
        return $this->connection->query(
            "SELECT vdm.warehouse.*, 
            vdm.country.identity as country, 
            vdm.city.identity as city, 
            vdm.street.identity as street, 
            vdm.house.identity as house
            FROM vdm.warehouse
            JOIN vdm.country ON vdm.warehouse.country_id = vdm.country.id
            JOIN vdm.city ON vdm.warehouse.city_id = vdm.city.id
            JOIN vdm.street ON vdm.warehouse.street_id = vdm.street.id
            JOIN vdm.house ON vdm.warehouse.house_id = vdm.house.id"
        );
    }

    function listOrders(): mysqli_result
    {
        return $this->connection->query(
            'SELECT * FROM vdm.order'
        );
    }

    function getOrder(string $pk): mysqli_result
    {
        return $this->connection->query(
            "SELECT * FROM vdm.order WHERE vdm.order.id = $pk"
        );
    }

    function createOrder(
        array $products,
        array $productSets,
        ?string $deliveryDate,
        ?string $note,
    ): bool {

        if (empty($products) && empty($productSets)) {
            return false;
        }

        foreach ($products as $product) {
            if (!is_numeric($product)) {
                return false;
            }
        }

        foreach ($productSets as $productSet) {
            if (!is_numeric($productSet)) {
                return false;
            }
        }

        $deliveryDate = mysqli_escape_string($this->connection, $deliveryDate);
        $note = mysqli_escape_string($this->connection, $note);

        $sql = "INSERT INTO vdm.order (delivery_date, note) VALUES ('$deliveryDate', '$note')";
        if (!$this->connection->query($sql)) {
            return false;
        }

        $orderId = $this->connection->insert_id;
        foreach ($products as $product) {
            $sql = "INSERT INTO vdm.order_items (order_id, product_id) VALUES ($orderId, $product)";
            if (!$this->connection->query($sql)) {
                return false;
            }
        }
        foreach ($productSets as $productSet) {
            $sql = "INSERT INTO vdm.order_items (order_id, product_set_id) VALUES ($orderId, $productSet)";
            if (!$this->connection->query($sql)) {
                return false;
            }
        }

        return true;
    }

    function getOrderProducts(int $pk): mysqli_result
    {
        return $this->connection->query(
            "SELECT vdm.product.* FROM vdm.order_items 
            JOIN vdm.product ON vdm.order_items.product_id = vdm.product.id
            WHERE vdm.order_items.order_id = $pk"
        );
    }

    function getOrderProductSets(int $pk): mysqli_result
    {
        return $this->connection->query(
            "SELECT vdm.product_set.*, tmp.amount, tmp.price from
                (SELECT vdm.product_set.id as id, min(vdm.product.amount) as amount, sum(vdm.product.price) as price FROM vdm.product_set 
                JOIN vdm.product_product_set ON vdm.product_set.id = vdm.product_product_set.product_set_id
                JOIN vdm.product ON vdm.product.id = vdm.product_product_set.product_id
                JOIN vdm.order_items ON vdm.order_items.product_set_id = vdm.product_set.id
                WHERE vdm.order_items.order_id = $pk
                GROUP BY vdm.product_set.id) as tmp 
            JOIN vdm.product_set ON tmp.id = vdm.product_set.id"
        );
    }
}
