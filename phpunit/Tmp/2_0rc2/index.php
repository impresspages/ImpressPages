<?php
header("HTTP/1.1 503 Service Temporarily Unavailable");
header("Status: 503 Service Temporarily Unavailable");
header("Retry-After: 3600");

if (file_exists(__DIR__.'/maintenance.php')) {
    require(__DIR__.'/maintenance.php');
}
