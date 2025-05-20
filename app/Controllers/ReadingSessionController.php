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
     * Show the reading sessions list page
     */
    public function index()
    {
        // Check if user is logged in
        if (!isset($_SESSION['user_id'])) {
            $this->redirect('/auth/login');
        }
        
        $userId = $_SESSION['user_id'];
        
        // Get all sessions for the user
        $sessions = $this->readingSessionModel->getUserSessions($userId);
        
        $this->render('user/reading-sessions', [
            'sessions' => $sessions
        ]);
    }
    
    /**
     * Start or resume a reading session for a specific book
     */
    public function read()
    {
        // Check if user is logged in
        if (!isset($_SESSION['user_id'])) {
            $this->redirect('/auth/login');
        }
        
        $userId = $_SESSION['user_id'];
        $bookId = isset($_GET['id']) ? intval($_GET['id']) : 0;
        
        if (!$bookId) {
            $this->redirect('/user/browse-books');
        }
        
        // Get book details
        $book = $this->bookModel->getBookById($bookId);
        
        if (!$book) {
            $_SESSION['error_message'] = 'Book not found.';
            $this->redirect('/user/browse-books');
        }
        
        // Check if user has purchased the book
        $hasPurchased = $this->readingSessionModel->hasUserPurchasedBook($userId, $bookId);
        
        // Check if user already has a session for this book
        $existingSession = $this->readingSessionModel->checkExistingSession($userId, $bookId);
        
        // If user has purchased the book, they can read it regardless
        if ($hasPurchased) {
            // If no session or existing session is expired, create a new one
            if (!$existingSession || $existingSession['status'] === 'expired') {
                $sessionId = $this->readingSessionModel->createSession($userId, $bookId);
                
                // Log activity
                $this->activityLogModel->createData([
                    'user_id' => $userId,
                    'action_type_id' => $this->getActivityTypeId('READ_SESSION'),
                    'description' => "Started reading session for book: {$book['b_title']}"
                ]);
                
                // Redirect to the reading page with the new session ID
                $this->redirect("/user/reading-sessions/view?id={$sessionId}");
            } else {
                // Redirect to existing session
                $this->redirect("/user/reading-sessions/view?id={$existingSession['rs_id']}");
            }
        } else {
            // If user has an active session, they can continue reading
            if ($existingSession && $existingSession['status'] === 'active') {
                $this->redirect("/user/reading-sessions/view?id={$existingSession['rs_id']}");
            } else {
                // Check if book is available (less than 3 active readers)
                if ($this->readingSessionModel->canBookBeRead($bookId)) {
                    // Create new session
                    $sessionId = $this->readingSessionModel->createSession($userId, $bookId);
                    
                    // Log activity
                    $this->activityLogModel->createData([
                        'user_id' => $userId,
                        'action_type_id' => $this->getActivityTypeId('READ_SESSION'),
                        'description' => "Started reading session for book: {$book['b_title']}"
                    ]);
                    
                    // Redirect to the reading page
                    $this->redirect("/user/reading-sessions/view?id={$sessionId}");
                } else {
                    // Book has reached maximum reader limit
                    $_SESSION['error_message'] = 'This book has reached the maximum number of concurrent readers (3). Please try again later or purchase the book to read it anytime.';
                    $this->redirect('/user/browse-books');
                }
            }
        }
    }
    
    /**
     * View a specific reading session (EPUB reader)
     */
    public function viewSession()
    {
        // Check if user is logged in
        if (!isset($_SESSION['user_id'])) {
            $this->redirect('/auth/login');
        }
        
        $userId = $_SESSION['user_id'];
        $sessionId = isset($_GET['id']) ? intval($_GET['id']) : 0;
        
        if (!$sessionId) {
            $this->redirect('/user/reading-sessions');
        }
        
        // Get session details
        $session = $this->readingSessionModel->getSessionById($sessionId);
        
        if (!$session || $session['ua_id'] != $userId) {
            $_SESSION['error_message'] = 'Reading session not found or access denied.';
            $this->redirect('/user/reading-sessions');
        }
        
        // Check if session is expired and user hasn't purchased the book
        if ($session['status'] === 'expired' && !$session['is_purchased']) {
            $_SESSION['error_message'] = 'This reading session has expired. Purchase the book to continue reading.';
            $this->redirect('/user/reading-sessions');
        }
        
        // Get the book file path
        $filePath = '/assets/books/' . $session['b_file_path'];
        
        $this->render('user/reading-session-view', [
            'session' => $session,
            'filePath' => $filePath
        ]);
    }
    
    /**
     * Update reading progress via AJAX
     */
    public function updateProgress()
    {
        if (!$this->isAjax() || !isset($_SESSION['user_id'])) {
            $this->jsonError('Invalid request or not authenticated', 400);
        }
        
        $jsonData = $this->getJsonInput();
        
        // Validate required fields
        if (!isset($jsonData['session_id']) || !isset($jsonData['current_page'])) {
            $this->jsonError('Missing required parameters', 400);
        }
        
        $sessionId = intval($jsonData['session_id']);
        $currentPage = intval($jsonData['current_page']);
        $isCompleted = isset($jsonData['is_completed']) ? boolval($jsonData['is_completed']) : false;
        
        // Check if the session belongs to the current user
        $session = $this->readingSessionModel->getSessionById($sessionId);
        
        if (!$session || $session['ua_id'] != $_SESSION['user_id']) {
            $this->jsonError('Access denied', 403);
        }
        
        // Update progress
        $success = $this->readingSessionModel->updateReadingProgress($sessionId, $currentPage, $isCompleted);
        
        if ($success) {
            $this->jsonSuccess(['current_page' => $currentPage], 'Reading progress updated');
        } else {
            $this->jsonError('Failed to update reading progress', 500);
        }
    }
    
    /**
     * Helper method to get activity type ID by code
     * 
     * @param string $code The activity type code
     * @return int The activity type ID
     */
    private function getActivityTypeId($code)
    {
        // Query to get activity type ID from code
        $sql = "SELECT at_id FROM activity_type WHERE at_code = :code";
        $typeId = $this->activityLogModel->queryScalar($sql, ['code' => $code]);
        
        // If not found, return default ID (1)
        return $typeId ?: 1;
    }
} 