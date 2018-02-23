<?php

namespace Amazon\API\FulfillmentInventory;

use Amazon\API\{APIMethods, APIParameters, APIParameterValidation};

class FulfillmentInventory extends API
{

    use APIMethods;
    use APIParameters;
    use APIParameterValidation;

    protected static $feed = "FulfillmentInventory";
    protected static $feedType = "";

}