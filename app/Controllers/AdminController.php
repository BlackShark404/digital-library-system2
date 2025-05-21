<?php

namespace App\Controllers;

class AdminController extends BaseController {
    protected $adminModel;

    public function renderAdminDashboard() {
        $this->render('admin/dashboard');
    }

    public function renderUserManagement() {
        $filters = [
            'role' => $this->getRequestParam('role', ''),
            'status' => $this->getRequestParam('status', '')
        ];

        $this->render('admin/user-management',[
            'filters' => $filters
        ]);
    }

    public function renderBookManagement() {
        $this->render('admin/book-management');
    }

    public function renderReadingSessions() {
        // Delegate to the ReadingSessionController
        $readingSessionController = new ReadingSessionController();
        $readingSessionController->getAllReadingSessions();
    }

    public function renderPurchases() {
        $this->render('admin/purchases');
    }

    public function renderActivityLogs() {
        // Redirect to the ActivityLogController to handle the request
        $activityLogController = new ActivityLogController();
        $activityLogController->index();
    }

    public function renderAdminProfile() {
        $this->render('admin/admin-profile');
    }
}