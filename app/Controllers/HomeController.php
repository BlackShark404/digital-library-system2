<?php

namespace App\Controllers;

class HomeController extends BaseController{
    public function index() {
        $this->render('home/index');
    }

    public function contactUs() {
        $this->render('home/contact-us');
    }

    public function privacy() {
        $this->render('home/privacy');
    }

    public function terms() {
        $this->render('home/terms-of-services');
    }
}