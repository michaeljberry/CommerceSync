<?php

namespace Amazon\API;

use \Exception;
use \DateTime;
use \DateInterval;

use \DateTimeZone;
use Ecommerce\Ecommerce;

trait APIParameterValidation
{

    public static function requireParameterToBeSet($parameterToCheck)
    {

        $matchingParameters = static::searchCurlParameters($parameterToCheck);

        if(empty($matchingParameters))
        {

            throw new Exception("$parameterToCheck must be set to complete this request. Please correct and try again.");

        }

    }

    public static function ensureDatesAreChronological($earlierDate, $laterDate)
    {


        if(
            null !== static::getParameterByKey($earlierDate) &&
            null !== static::getParameterByKey($laterDate)
        ){

            $earlyDate = new DateTime(static::getParameterByKey($earlierDate));
            $lateDate = new DateTime(static::getParameterByKey($laterDate));

            if($lateDate < $earlyDate)
            {

                throw new Exception("$earlierDate must be before $laterDate. Please correct and try again.");

            }

        }

    }

    public static function ensureIntervalBetweenDates($dateToEnsureInterval, $baseDate = "Timestamp", $interval = "PT2M", $direction = "earlier")
    {

        if(null !== static::getParameterByKey($dateToEnsureInterval))
        {

            $date = new DateTime(static::getParameterByKey($baseDate, new DateTimeZone("America/Boise")));
            $formattedInterval = new DateInterval($interval);

            if($direction !== "earlier")
            {

                $adjustedDate = $date->add($formattedInterval);

            } else {

                $adjustedDate = $date->sub($formattedInterval);

            }

            $dateToEnsure = new DateTime(static::getParameterByKey($dateToEnsureInterval), new DateTimeZone("America/Boise"));

            if($dateToEnsure > $adjustedDate)
            {

                $exceptionNotice = "$dateToEnsureInterval must be ";
                $exceptionNotice .= $direction !== "earlier" ? "later" : "earlier";
                $exceptionNotice .= " than ";
                $exceptionNotice .= $formattedInterval->format('%i minutes');
                $exceptionNotice .= $direction !== "earlier" ? " after " : " before ";
                $exceptionNotice .= "$baseDate. Please correct and try again.";

                throw new Exception($exceptionNotice);

            }

        }

    }

    public static function ensureDatesNotOutsideInterval($earlierDate, $laterDate, $intervalInDays)
    {

        if(
            null !== static::getParameterByKey($earlierDate) &&
            null !== static::getParameterByKey($laterDate)
        ){

            $earlierDate = new DateTime($earlierDate);
            $laterDate = new DateTime($laterDate);
            $difference = $earlierDate->diff($laterDate);

            if($difference->format('%a') > $intervalInDays)
            {

                throw new Exception("These dates are greater than $intervalInDays days apart. Please correct and try again.");

            }

        }

    }

    public static function ensureAllAreSet($dependentParameters)
    {

        $dependentParametersCopy = $dependentParameters;

        foreach ($dependentParameters as $parameter)
        {

            if(!in_array($parameter, static::getCurlParameters()))
            {

                $dependentParameter = "The following must all be set: ";

                foreach ($dependentParametersCopy as $value)
                {

                    if(current($dependentParametersCopy) === end($dependentParametersCopy))
                    {

                        $dependentParameter .= "$value";

                    } else {

                        $dependentParameter .= "$value, ";

                    }


                }

                $dependentParameter .= ". Please correct and try again.";

                throw new Exception($dependentParameter);

            }

        }

    }

    public static function ensureOneOrTheOtherIsSet($firstParameter, $secondParameter)
    {

        if(
            (
                null === static::getParameterByKey($firstParameter) &&
                null === static::getParameterByKey($secondParameter)
            ) || (
                null !== static::getParameterByKey($firstParameter) &&
                null !== static::getParameterByKey($secondParameter)
            )
        ){

            throw new Exception("$firstParameter or $secondParameter must be set. Please correct and try again.");

        }

    }

    public static function ensureIncompatibleParametersNotSet($parameterToCheck, $restrictedParameters)
    {


        if(null !== static::getParameterByKey($parameterToCheck))
        {

            if(is_array($restrictedParameters)){

                foreach($restrictedParameters as $restricted)
                {

                    if(null !== static::getParameterByKey($restricted))
                    {

                        throw new Exception("$restricted cannot be set at the same time as $parameterToCheck. Please correct and try again.");

                    }

                }

            } else {

                if(null !== static::getParameterByKey($restrictedParameters))
                {

                    throw new Exception("$restrictedParameters cannot be set at the same time as $parameterToCheck. Please correct and try again.");

                }

            }

        }

    }

    public static function ensureParameterValuesAreValid($parameterToCheck, $validParameterValues = null)
    {

        $matchingParameters = static::searchCurlParameters($parameterToCheck);

        if(!empty($matchingParameters))
        {

            $validParameterValue = [];
            $dependentParameters = [];

            foreach($validParameterValues as $key => $value)
            {
                if(is_array($value))
                {
                    $validParameterValue[] = $key;

                    if(array_key_exists("dependentOn", $value))
                    {

                        $dependentParameters[] = $key;

                    }

                } else {

                    $validParameterValue[] = $value;

                }

            }

            $allowedParameterValues = array_intersect($matchingParameters, $validParameterValue);

            if(empty($allowedParameterValues))
            {

                throw new Exception("The value/s for $parameterToCheck is/are not valid. Please correct and try again.");

            }

            if(!empty($dependentParameters))
            {

                static::ensureAllAreSet($dependentParameters);

            }

        }

    }

    public static function ensureSetParametersAreAllowed()
    {

        foreach(static::getCurlParameters() as $parameterToCheck => $value)
        {

            $parameter = static::searchCurlParameters($parameterToCheck, array_fill_keys(static::getAllowedParameters(), " "));

            if(!$parameter)
            {

                throw new Exception("The $parameterToCheck parameter is not allowed. Please correct and try again.");

            }

        }

    }

    public static function ensureRequiredParametersAreSet()
    {

        foreach (static::$requiredParameters as $key => $parameter)
        {

            static::requireParameterToBeSet($parameter);

        }

    }

    public static function ensureParameterIsInRange($parameterToCheck, $min, $max)
    {

        Ecommerce::dd(static::getParameterByKey($parameterToCheck));


        if(null !== static::getParameterByKey($parameterToCheck))
        {

            if(
                static::getParameterByKey($parameterToCheck) < $min ||
                static::getParameterByKey($parameterToCheck) > $max
            ){

                throw new Exception("$parameterToCheck must be between $min and $max. Please correct and try again.");

            }

        }

    }

    public static function ensureParameterIsNoLongerThanMaximum($parameterToCheck, $max)
    {


        if(null !== static::searchCurlParameters($parameterToCheck))
        {

            if(strlen(static::getParameterByKey(key(static::searchCurlParameters($parameterToCheck)))) > $max)
            {

                throw new Exception("$parameterToCheck must be shorter than $max characters. Please correct and try again.");

            }

        }

    }

    public static function ensureParameterIsInFormat($parameterToCheck, $format)
    {

        if(null !== static::getParameterByKey($parameterToCheck))
        {

            if(preg_match($format, $parameterToCheck) === false)
            {

                throw new Exception("$parameterToCheck does not match the format: $format. Please correct and try again.");

            }

        }

    }

}