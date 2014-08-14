<?php
require_once '../../../var/Typecho/Request.php';
$request = new Typecho_Request;
print_r($request);

echo "\n\n--------------------------------------------------------\n\n";

print_r($_SERVER);
