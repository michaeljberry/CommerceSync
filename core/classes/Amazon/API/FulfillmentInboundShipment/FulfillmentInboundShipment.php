<?php

namespace Amazon\API\FulfillmentInboundShipment;

use Amazon\API\{APIMethods, APIParameters, APIParameterValidation};

class FulfillmentInboundShipment
{

    use APIMethods;
    use APIParameters;
    use APIParameterValidation;

    protected static $feedType = "";
    protected static $feedContent = "";
    protected static $feed = "FulfillmentInboundShipment";
    protected static $versionDate = "2010-10-01";
    private static $overviewUrl = "http://docs.developer.amazonservices.com/en_US/fba_inbound/FBAInbound_Overview.html";
    private static $libraryUpdateUrl = "http://docs.developer.amazonservices.com/en_US/fba_inbound/FBAInbound_ClientLibraries.html";

}