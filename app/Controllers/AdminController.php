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
        $readingSessionModel = new \App\Models\ReadingSessionModel();
        
        // Get reading session statistics (these will be calculated in the view with actual data)
        $sessionStats = [
            'total_sessions' => 0,
            'total_duration' => 0,
            'total_pages' => 0,
            'avg_duration' => 0,
            'unique_users' => 0,
            'unique_books' => 0
        ];
        
        $this->render('admin/reading-sessions', [
            'sessionStats' => $sessionStats
        ]);
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