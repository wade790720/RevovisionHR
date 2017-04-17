<?php
include __DIR__.'/../ApiCore.php';

$api = new ApiCore();

$api->SC->clear();

$api->setMsg("Success Logout.");
$api->setArray(true);

print $api->getJSON();

?>
