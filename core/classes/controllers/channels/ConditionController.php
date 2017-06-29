<?php

namespace controllers\channels;


class ConditionController
{

    public static function normalCondition($condition)
    {
        if ($condition == "New") {
            $condition = "New";
        } elseif ($condition == "Brand New") {
            $condition = "Brand New";
        } elseif ($condition == "Like New" || $condition == "UsedLikeNew") {
            $condition = "Used Like New";
        } elseif ($condition == "Very Good" || $condition == "UsedVeryGood") {
            $condition = "UsedVeryGood";
        } elseif ($condition == "Good" || $condition == "UsedGood") {
            $condition = "UsedGood";
        } elseif ($condition == "Acceptable" || $condition == "UsedAcceptable") {
            $condition = "UsedAcceptable";
        } elseif ($condition == "Used") {
            $condition = "Used";
        } elseif ($condition == "Refurbished") {
            $condition = "Refurbished";
        }
        return $condition;
    }
}