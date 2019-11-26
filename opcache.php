<?php
header('Content-type: application/json');
$a = opcache_get_status();
unset($a['scripts']);
echo json_encode($a, JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE);