<?php

namespace models\channels;

use connect\DB;
use models\Database;

class channelModel
{
    public static function getDBInstance()
    {
        return DB::instance();
    }
}