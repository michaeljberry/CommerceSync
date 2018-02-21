<?php

namespace Amazon\API\Orders;

use Amazon\API\{APIMethods, APIParameters};

class Orders
{

    use APIMethods;
    use APIParameters;

    protected static $feed = "Orders";
    protected static $feedType = "";
    protected static $versionDate = "2013-09-01";

}