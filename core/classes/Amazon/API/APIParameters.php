<?php

namespace Amazon\API;

use Amazon\AmazonClient;
use Ecommerce\Ecommerce;
use DateTime;
use DateTimeZone;

use ReflectionClass;

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

    protected static function arrayFilterRecursive($method, $array, $removeEmptyArrays = false, $arg = null, $callback = null, $class = "static")
    {

        foreach ($array as $key => $value)
        {

            if (is_array($value)) {

                $value = static::arrayFilterRecursive($method, $value, $removeEmptyArrays, $arg, call_user_func_array([$class, $method], [$value, $key, $arg]));

                if ($removeEmptyArrays && !(bool)$value)
                {

                    unset($array[$key]);

                }

            } else {

                if (!is_null($callback) && !call_user_func_array([$class, $method], [$value, $key, $arg]))
                {

                    unset($array[$key]);

                } elseif (!(bool)$value) {

                    // unset($array[$key]);

                }

            }

        }

        unset($value);
        return $array;

    }

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

    protected static function setRequiredParameter($parameter, $value = null, $isArray = false)
    {

        if(!$isArray)
        {

            static::$requiredParameters[$parameter] = $value;

        } else {

            static::$requiredParameters[$parameter] = $value;

        }

    }


    protected static function testParametersWithIncompatibilities()
    {

        array_filter(

            static::getParameters(),

            function ($v, $k)
            {

                if(is_array($v) && array_key_exists("incompatibleWith", $v))
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

            function ($v, $k)
            {

                if (is_array($v) && array_key_exists("requiredIfNotSet", $v))
                {

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

                if (is_array($v) && array_key_exists("rangeWithin", $v))
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

                if (is_array($v) && array_key_exists("maximumLength", $v))
                {

                    static::ensureParameterIsNoLongerThanMaximum($k, $v["maximumLength"]);

                }

            },

            ARRAY_FILTER_USE_BOTH

        );

    }

    protected static function testParameterCountIsLessThanMaximum()
    {

        array_filter(

            static::getParameters(),

            function($v, $k)
            {

                if(is_array($v) && array_key_exists("maximumCount", $v))
                {

                    static::ensureParameterCountIsLessThanMaximum($k, $v["maximumCount"]);

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

                if (is_array($v) && array_key_exists("earlierThan", $v))
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

                if (is_array($v) && array_key_exists("laterThan", $v))
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

    protected static function testDatesNotOutsideInterval()
    {

        array_filter(

            static::getParameters(),

            function ($v, $k)
            {


                if (is_array($v) && array_key_exists("notFartherApartThan", $v))
                {

                    static::ensureDatesNotOutsideInterval($k, $v["notFartherApartThan"]["from"], $v["notFartherApartThan"]["days"]);

                }

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

                if (is_array($v) && array_key_exists("validWith", $v))
                {

                    static::ensureParameterValuesAreValid($k, $v['validWith']);

                }

            },

            ARRAY_FILTER_USE_BOTH

        );

    }

    protected static function required($v)
    {
            // return is_array($v) && in_array("required", $v);
        return $v === "required";
    }

    protected static function findRequiredParameters()
    {

        $parameters = static::getParameters();

        return static::arrayFilterRecursive("required", $parameters, true);

        // return array_filter(

        //     static::getParameters(),

        //     function ($v, $k)
        //     {

        //         return is_array($v) && in_array("required", $v);

        //     },

        //     ARRAY_FILTER_USE_BOTH

        // );

    }

    protected static function getParentClassParameters($parameterToCheck = "parameters")
    {

        return static::${"parent" . ucfirst($parameterToCheck)} ?? null;

    }

    protected static function findParentParameters()
    {

        return array_filter(

            static::getParameters(),

            function($v, $k)
            {

                return is_array($v) && (array_key_exists("parent", $v) || in_array("parent", $v));

            },

            ARRAY_FILTER_USE_BOTH

        );

    }

    protected static function combineParentParametersWithChild()
    {

        $allParentParameters = static::getParentClassParameters();

        $parametersWithParents = static::findParentParameters();

        foreach ($parametersWithParents as $k => $v)
        {

            if(array_key_exists("parent", $v))
            {

                $parent = $v["parent"];

            } else {

                $parent = $k;

            }

            $parentParameters = $allParentParameters[$parent];

            static::$parameters[$k] = static::$parameters[$k] + $parentParameters;

        }

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

    public static function setClassParameterByKey($key, $value)
    {

        static::$parameters[$key] = $value;

    }

    public static function addClassParameterByKey($key, $value)
    {

        static::$parameters[$key] = static::$parameters[$key] + $value;

    }

    public static function unsetClassParameterByKey($key)
    {

        unset(static::$parameters[$key]);

    }

    public static function getDateParameters()
    {

        return array_filter(

            static::getParameters(),

            function ($v, $k)
            {

                return is_array($v) && array_key_exists("format", $v) && $v["format"] == "date";

            },

            ARRAY_FILTER_USE_BOTH

        );

    }

    public static function getParametersWithFormat($parameters = null)
    {

        if(!$parameters)
        {

            $parameters = static::getParameters();

        }

        return array_filter(

            $parameters,

            function ($v, $k)
            {

                return is_array($v) && array_key_exists("format", $v) && $v["format"] !== "date";

            },

            ARRAY_FILTER_USE_BOTH

        );

    }

    public static function combineFormatWithParameters($array, $find = null, $replace = null)
    {

        $dataTypes = static::$dataTypes;

        $parametersWithFormats = static::getParametersWithFormat($replace);

        foreach ($array as $key => $value)
        {

            if(array_key_exists($key, $parametersWithFormats))
            {

                $format = $value["format"];
                $dataType = $dataTypes[$format];
                $newValue = array_merge($array[$key], $dataType);
                $array[$key] = $newValue;

                unset($array[$key]["format"]);

                if(is_array($array[$key]))
                {

                    $array[$key] = static::combineFormatWithParameters($array[$key], $key, $dataType);

                }

            }

        }

        return $array;

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

    public static function incrementParameter($parameter, $value, $parentParameterKey = null, $x = 1)
    {

        $incrementor = static::getIncrementorByKey($parameter);

        if (is_array($value))
        {

            foreach($value as $key => $val)
            {

                if($parentParameterKey)
                {

                    $parameterKey = "$parentParameterKey.$parameter.$incrementor.$x";

                } else {

                    $parameterKey = "$parameter.$incrementor.$x";

                }

                if(is_array($val))
                {
                    $y = 1;

                    foreach($val as $newParameter => $v)
                    {

                        if(is_array($v))
                        {

                            static::incrementParameter($newParameter, $v, $parameterKey, $y);

                            $y++;

                        } else {

                            static::setParameterByKey($parameterKey . "." . $newParameter, $v);

                        }

                    }

                    $x++;

                } else {

                    static::setParameterByKey("$parameterKey.$key", $val);

                }

            }


        } else {

            $parameterKey = "$parameter.$incrementor.$x";

            static::setParameterByKey($parameterKey, $value);

        }

    }

    public static function setPassedParameters($parametersToSet, $incrementParameter)
    {

        foreach ($parametersToSet as $parameter => $value)
        {

            if(static::getIncrementorByKey($parameter) && $incrementParameter !== false)
            {

                static::incrementParameter($parameter, $value);

            } elseif(is_array($value)) {

                foreach ($value as $parameterSubKey => $subKeyValue)
                {

                    static::setParameterByKey($parameter . "." . $parameterSubKey, $subKeyValue);

                }

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

    protected static function searchParameters($v, $k, $parameterToCheck)
    {

        // if (

        //     strpos($parameterToCheck, $k) !== false ||

        //     strpos($k, $parameterToCheck) !== false

        // ){

        //     return true;

        // }

        // return false;

        return strpos($parameterToCheck, $k) !== false || strpos($k, $parameterToCheck) !== false;

    }

    public static function searchCurlParameters($parameterToCheck, $parameters = null)
    {

        if(!$parameters)
        {

            $parameters = static::getCurlParameters();

        }

        return static::arrayFilterRecursive("searchParameters", $parameters, true, $parameterToCheck);

        // return array_filter(

        //     $parameters,

        //     function ($k) use ($parameterToCheck)
        //     {

        //         if(

        //             strpos($parameterToCheck, $k) !== false ||

        //             strpos($k, $parameterToCheck) !== false

        //         ){

        //             return true;

        //         }

        //         return false;

        //     },

        //     ARRAY_FILTER_USE_KEY

        // );

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

        $date = new DateTime(date("Y-m-d H:i:s"));

        self::setParameterByKey('Timestamp', $date->format("Y-m-d\TH:i:s\Z"));

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

    }

    protected static function setPurgeAndReplaceParameter()
    {

        self::setParameterByKey('PurgeAndReplace', 'false');

    }

    protected static function setMarketplaceIdParameter($key)
    {

        self::setParameterByKey($key, self::getMarketplaceId());

    }

    protected static function setDateParameter($parameter, $date, $format = "Y-m-d\TH:i:s\Z")
    {

        $newDate = new DateTime($date);

        self::$curlParameters[$parameter] = $newDate->format($format);

    }

    protected static function setVersionDateParameter()
    {

        self::setParameterByKey('Version', static::getVersionDate());

    }

    protected static function combineRequiredParameters()
    {

        $parentRequiredParameters = array_flip(static::getRequiredParameters(true));
        // $parentRequiredParameters = static::getRequiredParameters(true);

        $parameters = static::findRequiredParameters();

        foreach($parentRequiredParameters as $parameter => $value)
        {

            static::setRequiredParameter($parameter, $value);

        }

        foreach ($parameters as $parameter => $value)
        {


            if(is_array($value)){

                static::setRequiredParameter($parameter, $value, true);

            } else {

                static::setRequiredParameter($parameter, $value);

            }

        }

    }

    protected static function combineRequiredAndAllowedParameters()
    {

        static::$allowedParameters = array_merge(

            static::getRequiredParameters(),

            static::getParameters()

        );

    }

    public static function setParameters($parametersToSet = null, $incrementParameter = null)
    {

        static::resetCurlParameters();

        static::combineParentParametersWithChild();

        static::$parameters = static::combineFormatWithParameters(static::$parameters);

        static::combineRequiredParameters();

        static::combineRequiredAndAllowedParameters();

        static::setAwsAccessKeyParameter();

        static::setActionParameter();

        if (array_key_exists('Merchant', static::getRequiredParameters()))
        {

            static::setMerchantIdParameter('Merchant');

        }

        if (array_key_exists('SellerId', static::getRequiredParameters()))
        {

            static::setMerchantIdParameter('SellerId');

        }

        if (array_key_exists('MarketplaceId.Id.1', static::getRequiredParameters()))
        {

            static::setMarketplaceIdParameter('MarketplaceId.Id.1');

        }

        if (array_key_exists('MarketplaceId', static::getRequiredParameters()))
        {

            static::setMarketplaceIdParameter('MarketplaceId.Id.1');

        }

        if (array_key_exists('PurgeAndReplace', static::getRequiredParameters()))
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

        // static::ensureRequiredParametersAreSet();

        static::ensureSetParametersAreAllowed();

        // static::ensureParameterIsInFormat("AmazonOrderId", self::getOrderNumberFormat());

        // static::testParametersWithIncompatibilities();

        // static::testParametersAreValid();

        // static::testParametersAreWithinGivenRange();

        // static::testParametersAreNoLongerThanMaximum();

        // static::testParameterCountIsLessThanMaximum();

        // static::testDatesAreEarlierThan();

        // static::testDatesAreLaterThan();

        // static::testDatesAreInProperFormat();

        // static::testDatesNotOutsideInterval();

        // static::testOneOrTheOtherIsSet();

        if(method_exists(get_called_class(), "requestRules"))
        {

            // static::requestRules();

        }

    }

}