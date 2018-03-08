<?php

namespace Amazon\API;

use Amazon\AmazonClient;
use Ecommerce\Ecommerce;
use DateTime;
use DateTimeZone;

trait APIParameters
{

    private static $curlParameters = [];

    private static $requiredParameters = [
        "AWSAccessKeyId",
        "Action",
        // "MWSAuthToken", //For Developer Access
        "SignatureMethod",
        "SignatureVersion",
        "Timestamp",
        "Version"
    ];

    protected static function getIncrementors()
    {

        return static::$incrementors;

    }

    protected static function getIncrementorByKey($parameterToCheck)
    {

        if(array_key_exists($parameterToCheck, static::$incrementors))
        {

            return static::$incrementors[$parameterToCheck];

        }

        return false;

    }

    protected static function setRequiredParameter($parameter)
    {

        static::$requiredParameters[] = $parameter;

    }


    protected static function testParametersWithIncompatibilities()
    {

        array_filter(

            static::getParameters(),

            function ($v, $k)
            {

                if(array_key_exists("incompatibleWith", $v))
                {

                    static::ensureIncompatibleParametersNotSet($k, $v['incompatibleWith']);

                }

            },

            ARRAY_FILTER_USE_BOTH

        );

    }

    protected static function testOneOrTheOtherIsSet()
    {

        array_filter(

            static::getParameters(),

            function ($v, $k) {

                if (array_key_exists("requiredIfNotSet", $v)) {

                    static::ensureOneOrTheOtherIsSet($k, $v['requiredIfNotSet']);

                }

            },

            ARRAY_FILTER_USE_BOTH

        );

    }

    protected static function testParametersAreWithinGivenRange()
    {

        array_filter(

            static::getParameters(),

            function ($v, $k)
            {

                if (array_key_exists("rangeWithin", $v))
                {

                    static::ensureParameterIsInRange($k, $v['rangeWithin']['min'], $v['rangeWithin']['max']);

                }

            },

            ARRAY_FILTER_USE_BOTH

        );

    }

    protected static function testParametersAreNoLongerThanMaximum()
    {

        array_filter(

            static::getParameters(),

            function ($v, $k)
            {

                if (array_key_exists("maximumLength", $v))
                {

                    static::ensureParameterIsNoLongerThanMaximum($k, $v['maximumLength']);

                }

            },

            ARRAY_FILTER_USE_BOTH

        );

    }

    protected static function testDatesAreEarlierThan()
    {

        array_filter(

            static::getParameters(),

            function ($v, $k)
            {

                if (array_key_exists("earlierThan", $v))
                {

                    if(is_array($v["earlierThan"]))
                    {

                        array_filter(

                            $v["earlierThan"],
                            function ($vv, $kk) use ($k)
                            {

                                static::ensureIntervalBetweenDates($k, $vv);

                            },
                            ARRAY_FILTER_USE_BOTH
                        );

                    } else {

                        static::ensureIntervalBetweenDates($k);

                    }

                }

            },

            ARRAY_FILTER_USE_BOTH

        );

    }

    protected static function testDatesAreLaterThan()
    {

        array_filter(

            static::getParameters(),

            function ($v, $k)
            {

                if (array_key_exists("laterThan", $v))
                {

                    static::ensureIntervalBetweenDates($k, $v["laterThan"], "later");

                }

            },

            ARRAY_FILTER_USE_BOTH

        );

    }

    protected static function testDatesAreInProperFormat()
    {

        $dateParameters = static::getDateParameters();

        array_filter(

            $dateParameters,

            function ($v, $k)
            {

                static::ensureDatesAreInProperFormat($k);

            },

            ARRAY_FILTER_USE_BOTH

        );

    }

    protected static function testParametersAreValid()
    {

        array_filter(

            static::getParameters(),

            function ($v, $k)
            {

                if (array_key_exists("validWith", $v))
                {

                    static::ensureParameterValuesAreValid($k, $v['validWith']);

                }

            },

            ARRAY_FILTER_USE_BOTH

        );

    }

    protected static function findRequiredParameters()
    {

        return array_filter(

            static::getParameters(),

            function ($v, $k)
            {

                return in_array("required", $v);

            },

            ARRAY_FILTER_USE_BOTH

        );

    }

    protected static function getRequiredParameters($parent = null)
    {

        if (!$parent)
        {

            return static::$requiredParameters;

        }

        return self::$requiredParameters;

    }

    public static function getOrderNumberFormat()
    {

        return static::$orderNumberFormat;

    }

    public static function getAllowedParameters()
    {

        return static::$allowedParameters;

    }

    public static function getParameters()
    {

        return static::$parameters;

    }

    public static function getDateParameters()
    {

        return array_filter(

            static::getParameters(),

            function ($v, $k)
            {

                return array_key_exists("format", $v)&& $v["format"] == "date";

            },

            ARRAY_FILTER_USE_BOTH

        );

    }

    public static function setParameterByKey($key, $value)
    {

        if($value)
        {

            if(array_key_exists($key, static::getDateParameters()) && !in_array($key, self::$curlParameters))
            {

                static::setDateParameter($key, $value);

            } else {

                self::$curlParameters[$key] = $value;

            }

        }

    }

    public static function incrementParameter($parameter, $value)
    {

        $incrementor = static::getIncrementorByKey($parameter);

        if (is_array($value))
        {

            for ($x = 1; $x <= count($value); $x++)
            {

                static::setParameterByKey($parameter . "." . $incrementor . "." . $x, $value[$x - 1]);

            }

        } else {

            static::setParameterByKey($parameter . "." . $incrementor . ".1", $value);

        }

    }

    public static function setPassedParameters($parametersToSet, $incrementParameter)
    {

        foreach ($parametersToSet as $parameter => $value)
        {

            if(static::getIncrementorByKey($parameter) && $incrementParameter !== false)
            {

                static::incrementParameter($parameter, $value);

            } else {

                static::setParameterByKey($parameter, $value);

            }

        }

    }

    public static function getParameterByKey($key)
    {

        return self::$curlParameters[$key] ?? null;

    }

    protected static function resetCurlParameters()
    {

        self::$curlParameters = [];

    }

    public static function getCurlParameters()
    {

        return self::$curlParameters;

    }

    public static function searchCurlParameters($parameterToCheck, $allowedParameters = null)
    {

        if(!$allowedParameters)
        {

            $allowedParameters = static::getCurlParameters();

        }

        return array_filter(

            $allowedParameters,

            function ($k) use ($parameterToCheck)
            {

                if(

                    strpos($parameterToCheck, $k) !== false ||

                    strpos($k, $parameterToCheck) !== false

                ){

                    return true;

                }

                return false;

            },

            ARRAY_FILTER_USE_KEY

        );

    }

    private static function getSignatureMethod()
    {

        return self::$signatureMethod;

    }

    private static function getSignatureVersion()
    {

        return self::$signatureVersion;

    }

    public static function setSignatureMethodParameter()
    {

        self::setParameterByKey('SignatureMethod', self::getSignatureMethod());

    }

    public static function setSignatureVersionParameter()
    {

        self::setParameterByKey('SignatureVersion', self::getSignatureVersion());

    }

    protected static function setTimestampParameter()
    {

        self::setParameterByKey('Timestamp', date("Y-m-d\TH:i:s\Z", time()));

    }

    protected static function setAwsAccessKeyParameter()
    {

        self::setParameterByKey('AWSAccessKeyId', AmazonClient::getAwsAccessKey());

    }

    protected static function setActionParameter()
    {

        self::setParameterByKey('Action', static::getAction());

    }

    protected static function setMerchantIdParameter($key)
    {

        self::setParameterByKey($key, AmazonClient::getMerchantId());

        // self::setRequiredParameter($key);

    }

    protected static function setPurgeAndReplaceParameter()
    {

        self::setParameterByKey('PurgeAndReplace', 'false');

        // self::setRequiredParameter('PurgeAndReplace');

    }

    protected static function setMarketplaceIdParameter($key)
    {

        self::setParameterByKey($key, self::getMarketplaceId());

        // self::setRequiredParameter($key);

    }

    protected static function setDateParameter($parameter, $date, $format = null)
    {

        $newDate = new DateTime($date, new DateTimeZone("America/Boise"));

        if(!$format)
        {

            $format = "Y-m-d\TH:i:s\Z";

        }

        self::$curlParameters[$parameter] = $newDate->format($format);

    }

    protected static function setVersionDateParameter()
    {

        self::setParameterByKey('Version', static::getVersionDate());

    }

    protected static function combineRequiredParameters()
    {

        $parentRequiredParameters = static::getRequiredParameters(true);

        $parameters = static::findRequiredParameters();

        foreach($parentRequiredParameters as $parameter)
        {

            static::setRequiredParameter($parameter);

        }

        foreach ($parameters as $parameter => $value)
        {

            static::setRequiredParameter($parameter);

        }

    }

    protected static function combineRequiredAndAllowedParameters()
    {

        static::$allowedParameters = array_merge(

            static::getRequiredParameters(),

            array_keys(static::getParameters())

        );

    }

    public static function setParameters($parametersToSet = null, $incrementParameter = null)
    {

        static::resetCurlParameters();

        static::combineRequiredParameters();

        static::combineRequiredAndAllowedParameters();

        static::setAwsAccessKeyParameter();

        static::setActionParameter();

        if (in_array('Merchant', static::getRequiredParameters()))
        {

            static::setMerchantIdParameter('Merchant');

        }

        if (in_array('SellerId', static::getRequiredParameters()))
        {

            static::setMerchantIdParameter('SellerId');

        }

        if (in_array('MarketplaceId.Id.1', static::getRequiredParameters()))
        {

            static::setMarketplaceIdParameter('MarketplaceId.Id.1');

        }

        if (in_array('MarketplaceId', static::getRequiredParameters()))
        {

            static::setMarketplaceIdParameter('MarketplaceId.Id.1');

        }

        if (in_array('PurgeAndReplace', static::getRequiredParameters()))
        {

            static::setPurgeAndReplaceParameter();

        }

        static::setSignatureMethodParameter();

        static::setSignatureVersionParameter();

        static::setTimestampParameter();

        static::setVersionDateParameter();

        if($parametersToSet)
        {

            static::setPassedParameters($parametersToSet, $incrementParameter);

        }

    }

    public static function verifyParameters()
    {

        static::ensureRequiredParametersAreSet();

        static::ensureSetParametersAreAllowed();

        static::ensureParameterIsInFormat("AmazonOrderId", self::getOrderNumberFormat());

        static::testParametersWithIncompatibilities();

        static::testParametersAreValid();

        static::testParametersAreWithinGivenRange();

        static::testParametersAreNoLongerThanMaximum();

        static::testDatesAreEarlierThan();

        static::testDatesAreLaterThan();

        static::testDatesAreInProperFormat();

        static::testOneOrTheOtherIsSet();

        if(method_exists(get_called_class(), "requestRules"))
        {

            // static::requestRules();

        }

    }

}