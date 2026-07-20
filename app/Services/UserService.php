<?php

namespace App\Services;

use App\Models\UserModel;

use DateTime;

class UserService
{
    protected $userModel;

    public function __construct()
    {
        $this->userModel = new UserModel();
    }

    public function verifyUser(string $password): bool
    {
        $user = $this->userModel->first();

        if ($user === null) {
            return false;
        }

        return password_verify($password, $user->password);
    }
}
