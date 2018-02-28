<?php

namespace Amazon\API\Feeds;

use Amazon\API\{APIMethods, APIParameters, APIParameterValidation};

class Feeds
{

    use APIMethods;
    use APIParameters;
    use APIParameterValidation;

    protected static $body = "";
    protected static $feed = "Feeds";
    protected static $feedType = "";
    protected static $versionDate = "2009-01-01";
    private static $overviewUrl = "http://docs.developer.amazonservices.com/en_US/feeds/Feeds_Overview.html";
    private static $libraryUpdateUrl = "http://docs.developer.amazonservices.com/en_US/feeds/Feeds_ClientLibraries.html";

}