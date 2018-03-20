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

    public static function ensureIntervalBetweenDates($dateToEnsureInterval, $baseDate = "Timestamp", $direction = "earlier", $interval = "PT2M")
    {

        if(null !== static::getParameterByKey($dateToEnsureInterval))
        {

            $date = new DateTime(static::getParameterByKey($baseDate));

            $formattedInterval = new DateInterval($interval);

            if($direction !== "earlier")
            {

                $adjustedDate = $date->add($formattedInterval);

            } else {

                $adjustedDate = $date->sub($formattedInterval);

            }

            $dateToEnsure = new DateTime(static::getParameterByKey($dateToEnsureInterval));

            if($dateToEnsure > $adjustedDate && $direction === "earlier")
            {

                $exceptionNotice = "$dateToEnsureInterval must be earlier than ";
                $exceptionNotice .= $formattedInterval->format('%i minutes');
                $exceptionNotice .= " before ";
                $exceptionNotice .= "$baseDate. Please correct and try again.";

                throw new Exception($exceptionNotice);

            } elseif($adjustedDate > $dateToEnsure && $direction === "later")
            {

                $exceptionNotice = "$dateToEnsureInterval must be later than ";
                $exceptionNotice .= $formattedInterval->format('%i minutes');
                $exceptionNotice .= " after ";
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

            $earlyDate = new DateTime(static::getParameterByKey($earlierDate));
            $lateDate = new DateTime(static::getParameterByKey($laterDate));
            $difference = $earlyDate->diff($lateDate);

            if($difference->format('%a') > $intervalInDays)
            {

                throw new Exception("These dates: $earlierDate, $laterDate are greater than $intervalInDays days apart. Please correct and try again.");

            }

        }

    }

    public static function ensureDatesAreInProperFormat($parameterToCheck)
    {

        if(null !== static::getParameterByKey($parameterToCheck))
        {

            if (!preg_match("/^\d{4}-\d{2}-\d{2}T[0-2][0-9]:[0-5][0-9]:[0-5][0-9]Z$/", static::getParameterByKey($parameterToCheck)))
            {

                throw new Exception("Dates must be in this format: YYYY-MM-DDTHH:II:SSZ. Please correct and try again.");

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

                $dependentParameter .= Ecommerce::arrayToString($dependentParametersCopy);

                $dependentParameter .= "Please correct and try again.";

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

            if(is_array($restrictedParameters))
            {

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

        $matchingParameter = static::searchCurlParametersReturnResults($parameterToCheck);

        if(!empty($matchingParameter))
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

            $parameterValue = end($matchingParameter);

            if(!in_array($parameterValue, $validParameterValue))
            {

                $exception = "The value for $parameterToCheck must be one of the following: ";

                $exception .= Ecommerce::arrayToString($validParameterValue);

                $exception .= "Please correct and try again.";

                throw new Exception($exception);

            }

            if(!empty($dependentParameters))
            {

                static::ensureAllAreSet($dependentParameters);

            }

        }

    }

    public static function ensureSetParametersAreAllowed($parameters = null)
    {

        if (!$parameters) {

            $parameters = static::getCurlParameters();

        }

        foreach($parameters as $parameterToCheck => $value)
        {

            $inArray = static::searchCurlParameters($parameterToCheck, static::getAllowedParameters());

            if(!$inArray)
            {

                throw new Exception("The $parameterToCheck parameter is not allowed. Please correct and try again.");

            }

        }

    }

    public static function ensureRequiredParametersAreSet($requiredParameters = null, $parentKey = null)
    {

        if(!$requiredParameters)
        {

            $requiredParameters = static::$requiredParameters;

        }

        foreach ($requiredParameters as $key => $parameter)
        {


            if(is_array($parameter))
            {

                static::ensureRequiredParametersAreSet($parameter, $key);

            } else {

                if($parentKey)
                {

                    static::requireParameterToBeSet($parentKey);

                } else {

                    static::requireParameterToBeSet($key);

                }
            }

        }

    }

    public static function ensureParameterIsInRange($parameterToCheck, $min, $max)
    {

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

        $matchingParameters = static::searchCurlParametersReturnResults($parameterToCheck);

        if(!empty($matchingParameters))
        {

            if(strlen(end($matchingParameters)) > $max)
            {

                throw new Exception("$parameterToCheck must be shorter than $max characters. Please correct and try again.");

            }

        }

    }

    public static function ensureParameterCountIsLessThanMaximum($parameterToCheck, $maxCount)
    {

        $matchingParameters = static::searchCurlParametersReturnResults($parameterToCheck);

        if(!empty($matchingParameters))
        {

            if(count($matchingParameters) > $maxCount)
            {

                throw new Exception("$parameterToCheck must have less than $maxCount values. Please correct and try again.");

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