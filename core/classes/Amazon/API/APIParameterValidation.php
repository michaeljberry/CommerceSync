<?php

namespace Amazon\API;

use \Exception;
use \DateTime;
use \DateInterval;
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

    public static function ensureIntervalBetweenDates($dateToEnsureInterval, $baseDate, $interval, $direction = "earlier")
    {

        if(null !== static::getParameterByKey($dateToEnsureInterval))
        {

            $date = new DateTime(static::getParameterByKey($baseDate));

            if($direction !== "earlier")
            {

                $adjustedDate = $date->add(new DateInterval($interval));

            } else {

                $adjustedDate = $date->sub(new DateInterval($interval));

            }

            $dateToEnsure = new DateTime(static::getParameterByKey($dateToEnsureInterval));

            if($dateToEnsure > $adjustedDate)
            {

                $exceptionNotice = "$dateToEnsureInterval must be ";
                $exceptionNotice .= $direction !== "earlier" ? "later" : "earlier";
                $exceptionNotice .= " than ";
                $exceptionNotice .= $interval->format('%m minutes');
                $exceptionNotice .= $direction !== "earlier" ? "after" : "before";
                $exceptionNotice .= "$baseDate. Please correct and try again.";

                throw new Exception($exceptionNotice);

            }

        }

    }
    public static function ensureDatesNotOutsideInterval($earlierDate, $laterDate, $interval)
    {

        if(
            null !== static::getParameterByKey($earlierDate) &&
            null !== static::getParameterByKey($laterDate)
        ){

            $earlierDate = new DateTime($earlierDate);
            $laterDate = new DateTime($laterDate);
            $difference = $earlierDate->diff($laterDate);


        }

    }

    public static function ensureOneOrTheOtherIsSet($firstParameter, $secondParameter)
    {

        if(
            null === static::getParameterByKey($firstParameter) &&
            null === static::getParameterByKey($secondParameter)
        ){

            throw new Exception("$firstParameter or $secondParameter must be set. Please correct and try again.");

        }

    }

    public static function ensureMutuallyExclusiveParametersNotSet($parameterToCheck, $restrictedParameters)
    {

        if(null !== static::getParameterByKey($parameterToCheck))
        {

            foreach($restrictedParameters as $restricted)
            {

                if(null !== static::getParameterByKey($restricted))
                {

                    throw new Exception("$restricted cannot be set at the same time as $parameterToCheck. Please correct and try again.");

                }

            }

        }

    }

    public static function ensureParameterValuesAreValid($parameterToCheck, $validParameterValues = null)
    {

        $matchingParameters = static::searchCurlParameters($parameterToCheck);

        if(!empty($matchingParameters))
        {

            $allowedParameterValues = array_intersect($matchingParameters, $validParameterValues);

            if(empty($allowedParameterValues))
            {

                throw new Exception("The value/s for $parameterToCheck is/are not valid. Please correct and try again.");

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

        if(null !== static::getParameterByKey($parameterToCheck))
        {

            if($parameterToCheck < $min && $parameterToCheck > $max)
            {

                throw new Exception("$parameterToCheck must be between $min and $max");

            }

        }

    }

}