<?php

use \Firebase\JWT\JWT;

require_once("JWT\JWT.php");
require_once("UserDbModel.php");

class Auth
{
    private static $key = "123";

    public static function createToken($userID)
    {
        $payload = $userID;
        return JWT::encode($payload, self::$key);
    }

    public static function checkAuthenticated()
    {
        if (!array_key_exists("Authorization", getallheaders())) {
            return null;
        }
        $token = getallheaders()["Authorization"];
        $token = explode(" ", $token)[1];
        $payload = JWT::decode($token, self::$key, array('HS256'));
        if (!$payload) {
            return null;
        }

        $existingUser = UserDbModel::FindUserByID($payload);
        if (!$existingUser) {
            throw new AuthException();
        }

        return $payload;
    }

    // POST -- /auth/register
    public function Register()
    {
        $data = json_decode(file_get_contents('php://input'), true);
        $email = $data["email"];
        $pass = password_hash($data["password"], PASSWORD_BCRYPT);

        $existingUser = UserDbModel::FindUser($email);
        if (!$existingUser) {
            $userID = UserDbModel::AddUser($email, $pass);
            return self::createToken($userID);
        } else {
            throw new Exception();
        }
    }


    // POST -- /auth/login
    public function Login()
    {
        $data = json_decode(file_get_contents('php://input'), true);
        $email = $data["email"];
        $pass = $data["password"];

        $user = UserDbModel::FindUser($email);

        if ($user) {
            if ($user["email"] == $email && password_verify($pass, $user["password"])) {
                return self::createToken($user["user_id"]);
            }
        }
        throw new Exception();
    }
}