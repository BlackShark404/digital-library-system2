<?php

namespace App\Controllers;

class UserController extends BaseController{
    public function renderUserDashboard() {
        $this->render('/user/dashboard');
    }
}