<?php

namespace App\Controllers;

class UserController extends BaseController{
    public function renderUserDashboard() {
        $this->render('/user/dashboard');
    }

    public function renderBrowseBooks() {
        $this->render('/user/browse-books');
    }

    public function renderReadingSessions() {
        $this->render('/user/reading-sessions');
    }

    public function renderWishlist() {
        $this->render('/user/wishlist');
    }

    public function renderPurchases() {
        $this->render('/user/purchases');
    }

    public function renderUserProfile() {
        $this->render('/user/user-profile');
    }

}