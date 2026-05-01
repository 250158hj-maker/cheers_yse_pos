<?php

require_once __DIR__ . '/../config/database.php';

class Database
{

    public static function getConnection(): PDO
    {
        return get_db();
    }
}
