<?php

namespace App\Library\Crud;
use App\Utils\Env;
use PDO;

class Database
{
    public static function getConnection()
    {
        $dbDrive = 'pgsql';
        $dbHost = getenv('DB_HOST');
        $dbPort = getenv('DB_PORT');
        $dbName = getenv('DB_NAME');
        $dbUser = getenv('DB_USERNAME');
        $dbPass = getenv('DB_PASSWORD');
    
        $connPdo = new PDO("pgsql:host=192.168.1.109;port=5434;dbname=payment_control",
         "postgres", "root");
        return $connPdo;
    }
}
