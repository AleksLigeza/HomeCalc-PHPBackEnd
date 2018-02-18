<?php
require_once("OperationModel.php");

class OperationDbModel
{
    public static function AddOperation($operation)
    {
        $sql = "INSERT INTO `operations` (`id`, `createDate`, `date`, `income`, `amount`, `description`, `cycleId`, `userId`) 
                    VALUES (NULL, '$operation->createDate', '$operation->date', '$operation->income', 
                                  '$operation->amount', '$operation->description', '$operation->cycleId', 
                                  '$operation->userId')";
        $db = new DBConnection();
        $res = $db->Execute($sql);
        return $res;
    }

    public static function GetOperations($userID, $skip)
    {
        $sql = "SELECT * 
                FROM operations 
                WHERE userId = '$userID' AND cycleId <> 0
                ORDER BY `date` DESC, createDate DESC
                LIMIT 10 OFFSET $skip";
        $dbConnection = new DBConnection();
        $result = $dbConnection->Select($sql);

        return $result;
    }

    public static function GetMonthOperations($userID, $query_date, $income)
    {
        $dateSince = date('Y-m-01', strtotime($query_date));
        $dateTo = date('Y-m-t', strtotime($query_date));

        $sql = "SELECT * 
                FROM operations 
                WHERE userId = '$userID' AND cycleId <> 0 
                      AND `date` >= '$dateSince' AND `date` <= '$dateTo'
                      AND `income` = '$income'
                ORDER BY `date` DESC, createDate DESC";
        $dbConnection = new DBConnection();
        $result = $dbConnection->Select($sql);
        return $result;
    }

    public static function GetAllOperations($userID)
    {
        $sql = "SELECT * 
                FROM operations 
                WHERE userId = '$userID' AND cycleId <> 0
                ORDER BY `date` DESC, createDate DESC";
        $dbConnection = new DBConnection();
        $result = $dbConnection->Select($sql);

        return $result;
    }

    public static function GetOperationsWithFilters($paramsArray)
    {
        $userID = $paramsArray[0];
        $skip = (int)$paramsArray[1];
        $amountFrom = (int)$paramsArray[2];
        $amountTo = (int)$paramsArray[3];
        $description = strtolower($paramsArray[4]);
        $dateSince = date("Y-m-d H:i:s", strtotime($paramsArray[5]));
        $dateTo = date("Y-m-d H:i:s", strtotime($paramsArray[6]));
        $type = (int)$paramsArray[7];

        $sql = "SELECT * 
                FROM operations 
                WHERE userId = '$userID'  AND cycleId <> 0
                      AND `date` >= '$dateSince' AND `date` <= '$dateTo'
                      AND `amount` >= $amountFrom AND `amount` <= $amountTo 
                      AND (`income` = $type OR $type = '0null')
                ORDER BY `date` DESC, createDate DESC
                LIMIT 10 OFFSET $skip";
        $dbConnection = new DBConnection();

        $res = $dbConnection->Select($sql);

        $result = array();
        foreach ($res as $value) {
            $temp = $value['description'];
            $temp = str_replace(array('ą', 'ć', 'ę', 'ł', 'ń', 'ó', 'ś', 'ź', 'ż', 'Ą', 'Ć', 'Ę', 'Ł', 'Ń', 'Ó', 'Ś', 'Ź', 'Ż'),
                array('a', 'c', 'e', 'l', 'n', 'o', 's', 'z', 'z', 'a', 'c', 'e', 'l', 'n', 'o', 's', 'z', 'z'), $temp);
            $temp = strtolower($temp);

            if (strpos($temp, $description) !== false || $description === "0null") {
                array_push($result, $value);
            }
        }
        return $result;
    }

    public static function FindOperationById($id, $userId)
    {
        $sql = "SELECT * FROM operations WHERE `id`= $id AND `userId` = $userId";

        $dbConnection = new DBConnection();
        $result = $dbConnection->Select($sql);
        if (count($result) == 0) {
            return null;
        }
        $user = $result[0];
        return $user;
    }

    public static function DeleteOperationById($id, $userId)
    {
        $sql = "DELETE FROM operations WHERE `id`= $id AND `userId` = $userId";

        $dbConnection = new DBConnection();
        $result = $dbConnection->Execute($sql);
        return $result;
    }

    public static function UpdateOperation($operation)
    {
        $sql = "UPDATE operations SET 
                    operations.createDate = '" . $operation['createDate'] . "',
                    operations.date = '" . $operation['date'] . "',
                    operations.income = '" . $operation['income'] . "',
                    operations.amount = " . $operation['amount'] . ",
                    operations.description = '" . $operation['description'] . "',
                    operations.cycleId = " . $operation['cycleId'] . ",
                    operations.userId = " . $operation['userId'] . "
                WHERE operations.id = " . $operation['id'];
        $dbConnection = new DBConnection();
        $result = $dbConnection->Execute($sql);
        return $result;
    }

    public static function GetCycles($userID, $skip)
    {
        $sql = "SELECT * 
                FROM operations 
                WHERE userId = '$userID' AND cycleId = 0
                ORDER BY `date` DESC, createDate DESC
                LIMIT 10 OFFSET $skip";
        $dbConnection = new DBConnection();
        $result = $dbConnection->Select($sql);

        return $result;
    }

    public static function GetCycleOperations($userID, $cycleId)
    {
        $sql = "SELECT * 
                FROM operations 
                WHERE userId = $userID AND cycleId = $cycleId
                ORDER BY `date` DESC, createDate DESC";
        $dbConnection = new DBConnection();
        $result = $dbConnection->Select($sql);

        return $result;
    }
}