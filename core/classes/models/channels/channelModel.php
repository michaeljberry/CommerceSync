<?php

namespace models\channels;

use connect\DB;

class channelModel
{
    public static function getDBInstance()
    {
        return DB::instance();
    }
}