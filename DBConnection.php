<?php

class DBConnection
{
    const DB_SERVER = "localhost";
    const DB_NAME = "homecalc";
    const DB_USER = "root";
    const DB_PASSWORD = "";

    private static $db = NULL;

    private function dbConnect()
    {
        try {
            self::$db = new PDO("mysql:host=" . self::DB_SERVER. ";dbname=". self::DB_NAME,
                self::DB_USER,
                self::DB_PASSWORD);
            self::$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        }
        catch (PDOException $e) {
            echo $e->getMessage();
        }
    }

    public function GetDB() {
        if (self::$db == NULL) {
            self::dbConnect();
        }
        return self::$db;
    }

    private function getStatement($conn, $sql){
        $stmt = $conn->prepare($sql);
        $stmt->execute();
        $stmt->setFetchMode(PDO::FETCH_ASSOC);
        return $stmt;
    }

    public function Select($sql){
        $connection = self::GetDB();
        $stmt = $this->getStatement($connection, $sql);
        $result = array();
        while($row = $stmt->fetch()){
            array_push($result, $row);
        }
        return $result;
    }

    public function Execute($sql){
        $connection = self::GetDB();
        $connection->exec($sql);
        return $connection->lastInsertId();
    }

}