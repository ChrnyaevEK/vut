<?php

// Index is not valid and should be routed to home view
header('Location: ' . '/views/home.php');
die();
