<?php

namespace App\Controllers;

use Core\AvatarGenerator;

class ProfileController extends BaseController {
    protected $userModel;

    public function __construct()
    {
        $this->userModel = $this->loadModel("UserModal");
    }

    public function updateProfileInfo() {
        $avatar = new AvatarGenerator();

        
    }
}