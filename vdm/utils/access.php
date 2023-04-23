<?php

if (!function_exists('accessControl')) {
    function accessControl(): void
    {
        if (!defined('__is_view__') && !defined('__is_api__')) {
            die('Direct access not permitted');
        }
    }
    accessControl();
}
