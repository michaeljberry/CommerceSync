<?php

use Etail\{EtailSSH, EtailInventoryFiveMinute};

require '../../core/init.php';

// phpinfo();

$ftpConnection = new EtailSSH("upload", "Inventory");
echo "<br><pre>";
print_r($ftpConnection);
echo "</pre><br>";