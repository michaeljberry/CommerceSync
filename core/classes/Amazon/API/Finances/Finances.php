<?php

namespace Amazon\API\Finances;

use Amazon\API\{APIMethods, APIParameters, APIParameterValidation};

class Finances
{

    use APIMethods;
    use APIParameters;
    use APIParameterValidation;

    protected static $feedType = "";
    protected static $feedContent = "";
    protected static $feed = "Finances";
    protected static $versionDate = "2015-05-01";
    private static $overviewUrl = "http://docs.developer.amazonservices.com/en_US/fba_inventory/MWS_GetServiceStatus.html";
    private static $libraryUpdateUrl = "http://docs.developer.amazonservices.com/en_US/finances/Finances_ClientLibraries.html";

}