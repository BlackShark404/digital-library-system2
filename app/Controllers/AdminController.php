<?php

namespace App\Controllers;

class AdminController extends BaseController {
    protected $adminModel;

    public function renderAdminDashboard() {
        $this->render('admin/dashboard');
    }

    public function renderUserManagement() {
        $this->render('admin/user-management');
    }

    public function renderBookManagement() {
        $this->render('admin/book-management');
    }

    public function renderReadingSessions() {
        $this->render('admin/reading-sessions');
    }

    public function renderPurchases() {
        $this->render('admin/purchases');
    }

    public function renderActivityLogs() {
        $this->render('admin/activity-log');
    }
}