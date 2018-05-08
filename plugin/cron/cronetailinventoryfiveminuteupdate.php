<?php

use Etail\{EtailSSH, EtailInventoryFiveMinute};

require '../../core/init.php';

// $ftpConnection = new EtailSSH("upload", "Inventory");

$etailInventory = new EtailInventoryFiveMinute();

// print_r($etailInventory->getVAIInventory());

// $etailInventory->updateInventoryInDB();

echo $etailInventory->getDatedFileName();
echo "<br><br>";

print_r($etailInventory->getUpdatedInventory());
