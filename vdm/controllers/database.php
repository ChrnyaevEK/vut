<?php

include $_SERVER['DOCUMENT_ROOT'] . '/utils/access.php';
accessControl();

if (!function_exists('getConnection')) {
    function getConnection(): mysqli
    {
        $host = getenv('DB_HOST');
        $user = getenv('DB_USER');
        $pass = getenv('DB_PASS');
    
        // Create connection
        $connection = new mysqli($host, $user, $pass);
    
        // Check connection
        if ($connection->connect_error) {
            die("Connection failed: " . $connection->connect_error);
        }
    
        return $connection;
    }
    
}