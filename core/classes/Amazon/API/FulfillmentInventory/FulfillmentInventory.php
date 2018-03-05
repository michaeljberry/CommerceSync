<?php

namespace Amazon\API\FulfillmentInventory;

use Amazon\API\{APIMethods, APIParameters, APIParameterValidation};

class FulfillmentInventory extends API
{

    use APIMethods;
    use APIParameters;
    use APIProperties;
    use APIParameterValidation;

    protected static $feedType = "";
    protected static $feedContent = "";
    protected static $feed = "FulfillmentInventory";
    protected static $versionDate = "2010-10-01";
    private static $overviewUrl = "http://docs.developer.amazonservices.com/en_US/fba_inventory/FBAInventory_Overview.html";
    private static $libraryUpdateUrl = "http://docs.developer.amazonservices.com/en_US/fba_inventory/FBAInventory_ClientLibraries.html";

}