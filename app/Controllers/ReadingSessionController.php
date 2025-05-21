<?php

namespace App\Controllers;

use App\Models\ReadingSessionModel;
use App\Models\BookModel;
use App\Models\ActivityLogModel;

class ReadingSessionController extends BaseController
{
    protected $readingSessionModel;
    protected $bookModel;
    protected $activityLogModel;
    
    public function __construct()
    {
        parent::__construct();
        $this->readingSessionModel = new ReadingSessionModel();
        $this->bookModel = new BookModel();
        $this->activityLogModel = new ActivityLogModel();
    }
    
    /**
     * Display the user's reading sessions
     */
    public function index()
    {
        // Check if user is logged in
        if (!isset($_SESSION['user_id'])) {
            $this->redirect('/login');
            return;
        }
        
        $userId = $_SESSION['user_id'];
        
        $readingSessions = $this->readingSessionModel->getUserReadingSessions($userId);
        $readingStats = $this->readingSessionModel->getUserReadingStats($userId);
        $suggestions = $this->readingSessionModel->getReadingSuggestions($userId, 3);
        
        $this->render('user/reading-sessions', [
            'sessions' => $readingSessions,
            'stats' => $readingStats,
            'suggestions' => $suggestions
        ]);
    }
    
    /**
     * Handle the "Read" button from browse-books.php
     */
    public function startNewSession()
    {
        // Check if user is logged in
        if (!isset($_SESSION['user_id'])) {
            $this->redirect('/login');
            return;
        }
        
        $userId = $_SESSION['user_id'];
        
        // Get book ID from query string
        $bookId = isset($_GET['id']) ? intval($_GET['id']) : 0;
        
        if (!$bookId) {
            $this->setFlashMessage('error', 'Invalid book selection.');
            $this->redirect('/user/browse-books');
            return;
        }
        
        // Check if the book exists
        $book = $this->bookModel->getBookById($bookId);
        if (!$book) {
            $this->setFlashMessage('error', 'Book not found.');
            $this->redirect('/user/browse-books');
            return;
        }
        
        // Check if user has purchased the book
        $hasPurchased = $this->readingSessionModel->hasUserPurchasedBook($userId, $bookId);
        
        // Check for an existing session
        $existingSession = $this->readingSessionModel->getUserBookSession($userId, $bookId);
        
        // If user has purchased or has an active session, redirect to that session
        if ($hasPurchased || ($existingSession && !$existingSession['is_expired'])) {
            $sessionId = $existingSession ? $existingSession['rs_id'] : null;
            
            // If no session exists yet but user has purchased, create one
            if (!$sessionId && $hasPurchased) {
                $sessionId = $this->readingSessionModel->createReadingSession($userId, $bookId);
                if (!$sessionId) {
                    $this->setFlashMessage('error', 'Failed to create reading session.');
                    $this->redirect('/user/browse-books');
                    return;
                }
                
                // Log activity
                $this->activityLogModel->logActivity($userId, 'READ_SESSION', 'Started reading ' . $book['b_title']);
            }
            
            // Redirect to the reading page
            $this->redirect('/reading-session/read-book/' . $sessionId);
            return;
        }
        
        // If session exists but expired
        if ($existingSession && $existingSession['is_expired']) {
            $this->setFlashMessage('error', 'Your 3-day reading period for this book has expired. Please purchase the book to continue reading.');
            $this->redirect('/user/browse-books');
            return;
        }
        
        // Check if the book has reached the maximum allowed concurrent readers (3)
        $activeSessionsCount = $this->readingSessionModel->countActiveSessionsForBook($bookId);
        if ($activeSessionsCount >= 3) {
            $this->setFlashMessage('error', 'This book has reached the maximum number of concurrent readers. Please try again later or purchase the book to read it anytime.');
            $this->redirect('/user/browse-books');
            return;
        }
        
        // Create a new reading session
        $sessionId = $this->readingSessionModel->createReadingSession($userId, $bookId);
        
        if (!$sessionId) {
            $this->setFlashMessage('error', 'Failed to create reading session.');
            $this->redirect('/user/browse-books');
            return;
        }
        
        // Log activity
        $this->activityLogModel->logActivity($userId, 'READ_SESSION', 'Started reading ' . $book['b_title']);
        
        // Redirect to the reading page
        $this->redirect('/reading-session/read-book/' . $sessionId);
    }
    
    /**
     * Display the PDF reader interface for a specific session
     * 
     * @param int $sessionId Reading session ID
     */
    public function readBook($sessionId)
    {
        // Check if user is logged in
        if (!isset($_SESSION['user_id'])) {
            $this->redirect('/login');
            return;
        }
        
        $userId = $_SESSION['user_id'];
        
        // Get session details
        $session = $this->readingSessionModel->getReadingSession($sessionId);
        
        if (!$session) {
            $this->setFlashMessage('error', 'Reading session not found.');
            $this->redirect('/user/reading-sessions');
            return;
        }
        
        // Verify that this session belongs to the current user
        if ($session['ua_id'] != $userId) {
            $this->setFlashMessage('error', 'You do not have access to this reading session.');
            $this->redirect('/user/reading-sessions');
            return;
        }
        
        // Check if session has expired
        $hasExpired = $session['is_expired'];
        $isPurchased = $this->readingSessionModel->hasUserPurchasedBook($userId, $session['b_id']);
        
        // Allow access if the session is active or the user has purchased the book
        if (!$hasExpired || $isPurchased) {
            // Record activity
            $this->activityLogModel->logActivity($userId, 'READ_SESSION', 'Started reading ' . $session['b_title']);
            
            // Render the PDF reader view
            $this->render('user/pdf-reader', [
                'session' => $session,
                'hasExpired' => $hasExpired,
                'isPurchased' => $isPurchased
            ]);
        } else {
            $this->setFlashMessage('error', 'This reading session has expired. Please purchase the book to continue reading.');
            $this->redirect('/user/reading-sessions');
        }
    }
    
    /**
     * Start a new reading session for a book
     * 
     * @param int $bookId Book ID
     */
    public function startSession($bookId)
    {
        // Check if user is logged in
        if (!isset($_SESSION['user_id'])) {
            $this->jsonError('Authentication required', 401);
            return;
        }
        
        $userId = $_SESSION['user_id'];
        
        // Check if the book exists
        $book = $this->bookModel->getBookById($bookId);
        if (!$book) {
            $this->jsonError('Book not found', 404);
        }
        
        // Check if the user has purchased the book
        $isPurchased = $this->readingSessionModel->hasUserPurchasedBook($userId, $bookId);
        
        // Check for existing session
        $existingSession = $this->readingSessionModel->getUserBookSession($userId, $bookId);
        
        // If user has purchased, allow them to read without restrictions
        if ($isPurchased) {
            if ($existingSession) {
                // Use existing session
                $sessionId = $existingSession['rs_id'];
            } else {
                // Create a new session
                $sessionId = $this->readingSessionModel->createReadingSession($userId, $bookId);
            }
            
            if ($sessionId) {
                $this->jsonSuccess(['session_id' => $sessionId], 'Session started successfully');
            } else {
                $this->jsonError('Failed to start reading session', 500);
            }
            return;
        }
        
        // If there's an existing non-expired session, use it
        if ($existingSession && !$existingSession['is_expired']) {
            $this->jsonSuccess(['session_id' => $existingSession['rs_id']], 'Existing session found');
            return;
        }
        
        // If session exists but expired, check if user has already had their chance
        if ($existingSession && $existingSession['is_expired']) {
            $this->jsonError('You have already used your 3-day reading period for this book. Please purchase to continue reading.', 403);
            return;
        }
        
        // Check if the book has reached the maximum allowed concurrent readers (3)
        $activeSessionsCount = $this->readingSessionModel->countActiveSessionsForBook($bookId);
        if ($activeSessionsCount >= 3) {
            $this->jsonError('This book has reached the maximum number of concurrent readers. Please try again later.', 403);
            return;
        }
        
        // Create a new reading session
        $sessionId = $this->readingSessionModel->createReadingSession($userId, $bookId);
        
        if ($sessionId) {
            // Log activity
            $this->activityLogModel->logActivity($userId, 'READ_SESSION', 'Started reading ' . $book['b_title']);
            
            $this->jsonSuccess(['session_id' => $sessionId], 'Reading session started successfully');
        } else {
            $this->jsonError('Failed to start reading session', 500);
        }
    }
    
    /**
     * Update reading progress
     */
    public function updateProgress()
    {
        // Check if request is AJAX
        if (!$this->isAjax()) {
            $this->jsonError('Invalid request method', 400);
            return;
        }
        
        // Check if user is logged in
        if (!isset($_SESSION['user_id'])) {
            $this->jsonError('Authentication required', 401);
            return;
        }
        
        $userId = $_SESSION['user_id'];
        
        // Get request data
        $data = $this->getJsonInput();
        
        $sessionId = isset($data['session_id']) ? intval($data['session_id']) : 0;
        $currentPage = isset($data['current_page']) ? intval($data['current_page']) : 0;
        $isCompleted = isset($data['is_completed']) ? (bool)$data['is_completed'] : false;
        
        // Validate data
        if (!$sessionId || !$currentPage) {
            $this->jsonError('Missing required parameters', 400);
            return;
        }
        
        // Verify session belongs to user
        $session = $this->readingSessionModel->getReadingSession($sessionId);
        if (!$session || $session['ua_id'] != $userId) {
            $this->jsonError('Access denied', 403);
            return;
        }
        
        // Check if session has expired and user hasn't purchased the book
        $hasExpired = $session['is_expired'];
        $isPurchased = $this->readingSessionModel->hasUserPurchasedBook($userId, $session['b_id']);
        
        if ($hasExpired && !$isPurchased) {
            $this->jsonError('Reading session has expired', 403);
            return;
        }
        
        // Update reading progress
        $updated = $this->readingSessionModel->updateReadingProgress($sessionId, $currentPage, $isCompleted);
        
        if ($updated) {
            $this->jsonSuccess([
                'session_id' => $sessionId,
                'current_page' => $currentPage,
                'is_completed' => $isCompleted
            ], 'Reading progress updated');
        } else {
            $this->jsonError('Failed to update reading progress', 500);
        }
    }
    
    /**
     * Check if a book is available for reading
     * 
     * @param int $bookId Book ID
     */
    public function checkAvailability($bookId)
    {
        // Check if user is logged in
        if (!isset($_SESSION['user_id'])) {
            $this->jsonError('Authentication required', 401);
            return;
        }
        
        $userId = $_SESSION['user_id'];
        
        // Check if the book exists
        $book = $this->bookModel->getBookById($bookId);
        if (!$book) {
            $this->jsonError('Book not found', 404);
            return;
        }
        
        // Check if the user has purchased the book
        $isPurchased = $this->readingSessionModel->hasUserPurchasedBook($userId, $bookId);
        
        // Check for existing session
        $existingSession = $this->readingSessionModel->getUserBookSession($userId, $bookId);
        $hasActiveSession = false;
        $isExpired = false;
        
        if ($existingSession) {
            $isExpired = $existingSession['is_expired'];
            $hasActiveSession = !$isExpired;
        }
        
        // Count active sessions for this book (only if not purchased)
        $activeSessionsCount = 0;
        $maxSessions = 3;
        $isAvailable = false;
        
        if (!$isPurchased) {
            $activeSessionsCount = $this->readingSessionModel->countActiveSessionsForBook($bookId);
            $isAvailable = $activeSessionsCount < $maxSessions || $hasActiveSession;
        } else {
            // If purchased, always available
            $isAvailable = true;
        }
        
        $this->jsonSuccess([
            'book_id' => $bookId,
            'is_purchased' => $isPurchased,
            'has_active_session' => $hasActiveSession,
            'had_previous_session' => ($existingSession !== null),
            'is_previous_session_expired' => $isExpired,
            'active_sessions_count' => $activeSessionsCount,
            'max_sessions' => $maxSessions,
            'is_available' => $isAvailable
        ]);
    }
    
    /**
     * Add flash message to session
     * 
     * @param string $type Message type (success, error, info, warning)
     * @param string $message The message text
     */
    protected function setFlashMessage($type, $message)
    {
        if (!isset($_SESSION['flash_messages'])) {
            $_SESSION['flash_messages'] = [];
        }
        
        $_SESSION['flash_messages'][$type] = $message;
    }
    
    /**
     * Get all reading sessions for admin view
     */
    public function getAllReadingSessions()
    {
        // Check if user is admin
        if (!$this->isAdmin()) {
            $this->redirect('/login');
            return;
        }
        
        // Get query parameters for filtering
        $search = $this->getRequestParam('search', '');
        $status = $this->getRequestParam('status', '');
        $dateFrom = $this->getRequestParam('date_from', '');
        $dateTo = $this->getRequestParam('date_to', '');
        
        // Get data from model
        $sessions = $this->readingSessionModel->getAllReadingSessions($search, $status, $dateFrom, $dateTo);
        
        // Return JSON if it's an API request
        if ($this->isAjaxRequest()) {
            $this->jsonSuccess($sessions);
            return;
        }
        
        // Render view
        $this->render('admin/reading-sessions', [
            'sessions' => $sessions
        ]);
    }
    
    /**
     * Export reading sessions as CSV for admin
     */
    public function exportReadingSessions()
    {
        // Check if user is admin
        if (!$this->isAdmin()) {
            $this->redirect('/login');
            return;
        }
        
        // Get query parameters for filtering
        $search = $this->getRequestParam('search', '');
        $status = $this->getRequestParam('status', '');
        $dateFrom = $this->getRequestParam('date_from', '');
        $dateTo = $this->getRequestParam('date_to', '');
        
        // Get data from model
        $sessions = $this->readingSessionModel->getAllReadingSessions($search, $status, $dateFrom, $dateTo);
        
        // Prepare CSV data
        $csvData = [];
        $csvData[] = ['Session ID', 'User', 'Email', 'Book Title', 'Author', 'Started At', 'Expires At', 'Current Page', 'Total Pages', 'Status'];
        
        foreach ($sessions as $session) {
            $status = '';
            if (isset($session['is_purchased']) && $session['is_purchased']) {
                $status = 'Purchased';
            } else if (isset($session['is_expired']) && $session['is_expired']) {
                $status = 'Expired';
            } else {
                $status = 'Active';
            }
            
            $csvData[] = [
                $session['rs_id'],
                $session['ua_first_name'] . ' ' . $session['ua_last_name'],
                $session['ua_email'],
                $session['b_title'],
                $session['b_author'],
                $session['rs_started_at'],
                $session['rs_expires_at'],
                $session['current_page'] ?? 0,
                $session['b_pages'] ?? 0,
                $status
            ];
        }
        
        // Set headers for download
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="reading-sessions-' . date('Y-m-d') . '.csv"');
        
        // Open output stream
        $output = fopen('php://output', 'w');
        
        // Write data to CSV
        foreach ($csvData as $row) {
            fputcsv($output, $row);
        }
        
        // Close output stream
        fclose($output);
        exit;
    }
    
    /**
     * Check if the current user is an admin
     * 
     * @return bool True if user is admin, false otherwise
     */
    protected function isAdmin()
    {
        return isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin';
    }
    
    /**
     * Check if the request is an AJAX request
     * 
     * @return bool True if request is AJAX, false otherwise
     */
    protected function isAjaxRequest()
    {
        return isset($_SERVER['HTTP_X_REQUESTED_WITH']) && 
               strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
    }
    
    /**
     * Get reading session details by ID (for admin view)
     * 
     * @param int $id Session ID from route parameter
     * @return void
     */
    public function getReadingSessionById($id = null)
    {
        // Debug parameter handling
        error_log("Getting session ID: " . ($id ?? 'null'));
        
        // If no ID provided, try to get from GET parameters
        if ($id === null) {
            // Get the URL path
            $path = $_SERVER['REQUEST_URI'];
            $parts = explode('/', $path);
            $id = end($parts);
            error_log("Extracted ID from URL: " . $id);
            
            // Additional fallback to GET parameter
            if (!is_numeric($id)) {
                $id = $_GET['id'] ?? null;
                error_log("Using GET parameter ID: " . ($id ?? 'null'));
            }
        }
        
        // If still no valid ID, return error
        if (!$id || !is_numeric($id)) {
            $this->jsonError('Invalid session ID', 400);
            return;
        }
        
        // Check if user is admin
        if (!$this->isAdmin()) {
            $this->jsonError('Access denied', 403);
            return;
        }
        
        // Get session with details
        $session = $this->readingSessionModel->getReadingSessionWithDetails($id);
        
        if (!$session) {
            $this->jsonError('Reading session not found', 404);
            return;
        }
        
        $this->jsonSuccess($session);
    }
    
    /**
     * Get purchase details by ID
     * 
     * @param int $id Purchase ID
     */
    public function getPurchaseById($id)
    {
        // Check if request is AJAX
        if (!$this->isAjax()) {
            $this->jsonError('Invalid request', 400);
            return;
        }
        
        // Get purchase details
        $purchase = $this->readingSessionModel->getPurchaseById($id);
        
        if (!$purchase) {
            $this->jsonError('Purchase not found', 404);
            return;
        }
        
        $this->jsonSuccess(['data' => $purchase], 'Purchase details retrieved successfully');
    }
    
    /**
     * Export purchases to CSV
     */
    public function exportPurchases()
    {
        // Only admins can export purchases
        if (!$this->isAdmin()) {
            $this->redirect('/error/403');
            return;
        }
        
        // Get filter parameters
        $search = $this->getRequestParam('search', '');
        $dateFrom = $this->getRequestParam('date_from', '');
        $dateTo = $this->getRequestParam('date_to', '');
        
        // Get purchases
        $purchases = $this->readingSessionModel->getAllPurchases($search, $dateFrom, $dateTo);
        
        // Prepare CSV output
        $output = fopen('php://temp', 'w');
        
        // Add CSV headers
        fputcsv($output, [
            'ID',
            'User ID',
            'User Name',
            'User Email',
            'Book ID',
            'Book Title',
            'Book Author',
            'Price',
            'Purchase Date'
        ]);
        
        // Add purchase data
        foreach ($purchases as $purchase) {
            fputcsv($output, [
                $purchase['up_id'],
                $purchase['ua_id'],
                $purchase['ua_first_name'] . ' ' . $purchase['ua_last_name'],
                $purchase['ua_email'],
                $purchase['b_id'],
                $purchase['b_title'],
                $purchase['b_author'],
                $purchase['b_price'],
                $purchase['up_purchased_at']
            ]);
        }
        
        // Get the CSV content
        rewind($output);
        $csvContent = stream_get_contents($output);
        fclose($output);
        
        // Set headers for CSV download
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="purchases_export_' . date('Y-m-d') . '.csv"');
        
        // Output CSV
        echo $csvContent;
        exit;
    }
} 