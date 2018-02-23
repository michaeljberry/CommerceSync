<?php

namespace Amazon\API;

trait APIParameterValidation
{

    public static function requireParameterToBeSet($parameterToCheck)
    {

        if(null === static::getParameterByKey($parameterToCheck))
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

    public static function ensureIntervalBetweenDates($dateToEnsureInterval, $baseDate, $interval, $direction = "sub")
    {

        if(null !== static::getParameterByKey($dateToEnsureInterval))
        {

            $date = DateTime(static::getParameterByKey($baseDate));
            if($direction !== "sub")
            {

                $adjustedDate = $date->add(new DateInterval($interval));

            } else {

                $adjustedDate = $date->sub(new DateInterval($interval));

            }

            $ensuredDate = new DateTime(static::getParameterByKey($dateToEnsureInterval));

            if($ensuredDate > $adjustedDate)
            {

                $exceptionNotice = "$dateToEnsureInterval must be no ";
                $exceptionNotice .= $direction !== "sub" ? "sooner" : "later";
                $exceptionNotice .= " than ";
                $exceptionNotice .= $interval->format('%m minutes');
                $exceptionNotice .= $direction !== "sub" ? "after" : "before";
                $exceptionNotice .= "$baseDate. Please correct and try again.";

                throw new Exception($exceptionNotice);

            }

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

    public static function ensureParametersAreValid($parameterToCheck, $validParameterValues)
    {

        $matchingParameters = array_filter(
            static::getCurlParameters(),
            function($k){
                return strpos($parameterToCheck) !== false;
            },
            ARRAY_FILTER_USE_KEY
        );

        if(!empty($matchingParameters))
        {

            $allowedParameterValues = array_intersect($matchingParameters, $validParameterValues);

            if(empty($allowedParameterValues))
            {

                throw new Exception("The value/s for $parameterToCheck is/are not valid. Please correct and try again.");

            }

        }

    }

}