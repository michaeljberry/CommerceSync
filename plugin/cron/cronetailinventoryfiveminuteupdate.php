<?php

use Etail\{EtailSSH, EtailInventoryFiveMinute};

require '../../core/init.php';

<<<<<<< HEAD
// $ftpConnection = new EtailSSH("upload", "Inventory");

$etailInventory = new EtailInventoryFiveMinute();

// print_r($etailInventory->getVAIInventory());

// $etailInventory->updateInventoryInDB();

echo $etailInventory->getDatedFileName();
echo "<br><br>";

print_r($etailInventory->getUpdatedInventory());
=======
// phpinfo();

$ftpConnection = new EtailSSH("upload", "Inventory");
echo "<br><pre>";
print_r($ftpConnection);
echo "</pre><br>";
>>>>>>> 7bdafca1277f31c5e0f31f8209ec60e80446f973
