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
} 