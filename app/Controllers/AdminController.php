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
        // Check if user is admin
        if (!$this->isAdmin()) {
            $this->redirect('/login');
            return;
        }
        
        // Gather system statistics
        $stats = [
            'total_users' => $this->userModel->getUserCountByRole()['user'] ?? 0,
            'total_books' => $this->bookModel->countTotalBooks(),
            'total_sessions' => $this->readingSessionModel->countTotalSessions(),
            'total_purchases' => $this->readingSessionModel->countTotalPurchases(),
        ];
        
        // Get recent reading sessions
        $recentSessions = $this->readingSessionModel->getRecentReadingSessions(5);
        
        // Get recent purchases
        $recentPurchases = $this->readingSessionModel->getRecentPurchases(5);
        
        // Get recent activity logs
        $recentActivity = $this->activityLogModel->getRecentActivityLogs(10);
        
        // Render view with data
        $this->render('admin/dashboard', [
            'stats' => $stats,
            'recent_sessions' => $recentSessions,
            'recent_purchases' => $recentPurchases,
            'recent_activity' => $recentActivity
        ]);
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
        // Get filter parameters
        $search = $this->getRequestParam('search', '');
        $dateFrom = $this->getRequestParam('date_from', '');
        $dateTo = $this->getRequestParam('date_to', '');
        
        // Get purchases data
        $purchases = $this->readingSessionModel->getAllPurchases($search, $dateFrom, $dateTo);
        
        // Render view with data
        $this->render('admin/purchases', [
            'purchases' => $purchases,
            'filters' => [
                'search' => $search,
                'date_from' => $dateFrom,
                'date_to' => $dateTo
            ]
        ]);
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