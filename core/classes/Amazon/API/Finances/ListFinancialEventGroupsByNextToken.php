<?php

namespace Amazon\API\Finances;

class ListFinancialGroupsByNextToken extends Finances
{

    protected static $requestQuota = 30;
    protected static $restoreRate = 1;
    protected static $restoreRateTime = 2;
    protected static $restoreRateTimePeriod = "second";
    protected static $hourlyRequestQuota = 1800;
    protected static $action = "ListFinancialGroupsByNextToken";
    protected static $method = "POST";
    private static $curlParameters = [];
    private static $apiUrl = "docs.developer.amazonservices.com/en_US/finances/Finances_ListFinancialEventGroupsByNextToken.html";
    protected static $requiredParameters = [
        "SellerId",
        "NextToken"
    ];
    protected static $allowedParameters = [];

    public function __construct($parametersToSet = null)
    {

        static::setParameters($parametersToSet);

        static::verifyParameters();

    }

}