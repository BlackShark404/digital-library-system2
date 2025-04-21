<?php

namespace App\Controllers;

class UserManagementController extends BaseController
{
    protected $userModel;

    public function __construct() 
    {
        $this->userModel = $this->loadModel('UserModel');
    }
    
}