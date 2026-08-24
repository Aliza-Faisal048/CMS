<?php

require_once "includes/ams_api.php";

$result = getAMSAssets("desktop");

echo "<pre>";
print_r($result);
echo "</pre>";
?>