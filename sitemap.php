<?php
$xml = file_get_contents('https://elektro-kolesa.ru/index.php?route=extension/feed/google_sitemap_fast');
header("Content-Type: application/xml");
header("server: nginx/1.18.0");
header("strict-transport-security: max-age=31536000");
header("x-powered-by: PHP/7.3.26");
echo $xml;
exit;
?>