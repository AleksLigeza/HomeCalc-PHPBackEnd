<?php
require_once ("UserDbModel.php");
require_once ("UserModel.php");

class Account
{
    // PUT -- /account/changeEmail
    public function ChangeEmail($paramsArray)
    {
        $userID = $paramsArray[0];
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

    // PUT -- /account/changePassword
    public function ChangePassword($paramsArray)
    {
        $userID = $paramsArray[0];
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

    // PUT -- /account/updateUser
    public function UpdateUser($paramsArray)
    {
        $data = json_decode(file_get_contents('php://input'), true);
        $password = $data["password"];
        $email = $data["email"];
        $id = $data["id"];

        $existingUser = UserDbModel::FindUserById($id);
        if (!$existingUser) {
            throw new Exception();
        }

        $user = new UserModel();
        $user->email = $email;
        $user->password = $password;
        $user->user_ID = $id;
        if($user->password != $existingUser["password"]) {
            $user->password = password_hash($password, PASSWORD_BCRYPT);
        }

        UserDbModel::UpdateUser($user);
        return "OK";
    }

    // GET -- /account/getAllUsers
    public function getAllUsers($paramsArray) {
        $users = UserDbModel::GetAllUsers();
        return $users;
    }

    // DELETE -- /account/delete/:id
    public function delete($paramsArray)
    {
        $userID = $paramsArray[1];
        return UserDbModel::DeleteUserById($userID);
    }
}