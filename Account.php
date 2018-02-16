<?php
require_once ("UserDbModel.php");
require_once ("UserModel.php");

class Account
{
    // POST -- /account/changeEmail
    public function ChangeEmail($userID)
    {
        $data = json_decode(file_get_contents('php://input'), true);
        $email = $data["email"];

        $existingUser = UserDbModel::FindUser($email);
        if ($existingUser) {
            throw new Exception();
        }

        $existingUser = UserDbModel::FindUserById($userID);
        if (!$existingUser) {
            throw new Exception();
        }

        $user = new UserModel();
        $user->user_ID = $existingUser["user_id"];
        $user->email = $email;
        $user->password = $existingUser["password"];

        UserDbModel::UpdateUser($user);

        return "OK";
    }

    // POST -- /account/changePassword
    public function ChangePassword($userID)
    {
        $data = json_decode(file_get_contents('php://input'), true);
        $password = $data["password"];

        $existingUser = UserDbModel::FindUserById($userID);
        if (!$existingUser) {
            throw new Exception();
        }

        $user = new UserModel();
        $user->user_ID = $existingUser["user_id"];
        $user->email = $existingUser["email"];
        $user->password = password_hash($password, PASSWORD_BCRYPT);;

        UserDbModel::UpdateUser($user);

        return "OK";
    }
}