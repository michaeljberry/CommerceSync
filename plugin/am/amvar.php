<?php

use Amazon\Amazon;
use Amazon\AmazonClient;

$amazon = new Amazon($userID);
AmazonClient::instance();