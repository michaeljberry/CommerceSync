<?php

use Amazon\Amazon;
use AmazonMWSAPI\AmazonClient;

$amazon = new Amazon($userID);
AmazonClient::instance();