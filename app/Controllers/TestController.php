<?php

namespace App\Controllers;

use App\Controllers\BaseController;

class TestController extends BaseController{
    protected $testModel;

    public function __construct()
    {
        $this->testModel = $this->loadModel('TestModel');
    }

    public function showUser() {
        $user = $this->testModel->getUserById(1);

        $username = $user['username'];

        $this->render('test/display-user', [
            'username' => $username
        ]
    );
    }
}