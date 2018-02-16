<?php

require_once("DBConnection.php");

class UserDbModel
{
    public static function AddUser($email, $password)
    {
            $sql = "INSERT INTO `users` (`user_id`, `email`, `password`) VALUES (NULL, '$email', '$password')";
            $db = new DBConnection();
            $res = $db->Execute($sql);
            return $res;
    }

    public static function FindUser($email)
    {
        $sql = "SELECT * FROM users WHERE email = '$email'";
        $dbConnection = new DBConnection();
        $result = $dbConnection->Select($sql);
        if (count($result) == 0) {
            return null;
        }
        $user = $result[0];
        return $user;
    }

    public static function FindUserById($id)
    {
        $sql = "SELECT * FROM users WHERE user_id = '$id'";

        $dbConnection = new DBConnection();
        $result = $dbConnection->Select($sql);
        if (count($result) == 0) {
            return null;
        }
        $user = $result[0];
        return $user;
    }

    public static function UpdateUser($user)
    {
        $sql = "UPDATE users SET users.email = '$user->email', users.password = '$user->password' WHERE users.user_id = '$user->user_ID'";
        $db = new DBConnection();
        $db->Execute($sql);
        return $user;
    }
}