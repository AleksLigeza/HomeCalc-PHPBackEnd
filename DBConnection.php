<?php

class DBConnection
{
    const DB_SERVER = "localhost";
    const DB_NAME = "homecalc";
    const DB_USER = "root";
    const DB_PASSWORD = "";

    private static $db = NULL;

    private static function dbConnect()
    {
        try {
            self::$db = new PDO("mysql:host=" . SELF::DB_SERVER. ";dbname=". SELF::DB_NAME,
                SELF::DB_USER,
                SELF::DB_PASSWORD);
            self::$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        }
        catch (PDOException $e) {
            echo $e->getMessage();
        }
    }

    public static function getDB() {
        if (self::$db == NULL) {
            self::dbConnect();
        }
        return self::$db;
    }
}