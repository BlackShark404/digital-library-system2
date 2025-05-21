<?php

namespace App\Controllers;

use App\Models\UserModel;
use App\Models\BookModel;
use App\Models\ReadingSessionModel;
use App\Models\ActivityLogModel;

class AdminController extends BaseController {
    protected $userModel;
    protected $bookModel;
    protected $readingSessionModel;
    protected $activityLogModel;
    
    public function __construct() {
        parent::__construct();
        $this->userModel = new UserModel();
        $this->bookModel = new BookModel();
        $this->readingSessionModel = new ReadingSessionModel();
        $this->activityLogModel = new ActivityLogModel();
    }

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
        // Get admin statistics
        $stats = [
            'total_users' => $this->userModel->getUserCountByRole()['user'] ?? 0,
            'total_books' => $this->bookModel->countTotalBooks(),
            'total_purchases' => $this->readingSessionModel->countTotalPurchases(),
            'system_health' => 90, // This could be calculated based on various factors
            'admin_actions' => $this->activityLogModel->countData()
        ];
        
        $this->render('admin/admin-profile', [
            'stats' => $stats
        ]);
    }
}