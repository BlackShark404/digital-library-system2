<?php

namespace App\Models;

class ReadingSessionModel extends BaseModel
{
    protected $table = 'reading_session';
    
    public function __construct()
    {
        parent::__construct();
    }
    
    /**
     * Get all reading sessions for a user
     * 
     * @param int $userId User ID
     * @return array Reading sessions
     */
    public function getUserSessions($userId)
    {
        $sql = "
            SELECT 
                rs.*, 
                b.b_title, 
                b.b_author, 
                b.b_cover_path,
                rp.current_page,
                rp.is_completed,
                CASE
                    WHEN rs.rs_expires_at < NOW() THEN 'expired'
                    ELSE 'active'
                END AS status,
                CASE
                    WHEN up.up_id IS NOT NULL THEN true
                    ELSE false
                END AS is_purchased
            FROM reading_session rs
            JOIN books b ON rs.b_id = b.b_id
            LEFT JOIN reading_progress rp ON rs.rs_id = rp.rs_id
            LEFT JOIN user_purchase up ON (rs.ua_id = up.ua_id AND rs.b_id = up.b_id)
            WHERE rs.ua_id = :userId
            ORDER BY 
                CASE
                    WHEN rs.rs_expires_at > NOW() THEN 0
                    ELSE 1
                END,
                rs.rs_started_at DESC
        ";
        
        return $this->query($sql, ['userId' => $userId]);
    }
    
    /**
     * Get a specific reading session
     * 
     * @param int $sessionId Session ID
     * @return array|null Reading session details
     */
    public function getSessionById($sessionId)
    {
        $sql = "
            SELECT 
                rs.*, 
                b.b_title, 
                b.b_author, 
                b.b_cover_path,
                b.b_file_path,
                rp.current_page,
                rp.is_completed,
                CASE
                    WHEN rs.rs_expires_at < NOW() THEN 'expired'
                    ELSE 'active'
                END AS status,
                CASE
                    WHEN up.up_id IS NOT NULL THEN true
                    ELSE false
                END AS is_purchased
            FROM reading_session rs
            JOIN books b ON rs.b_id = b.b_id
            LEFT JOIN reading_progress rp ON rs.rs_id = rp.rs_id
            LEFT JOIN user_purchase up ON (rs.ua_id = up.ua_id AND rs.b_id = up.b_id)
            WHERE rs.rs_id = :sessionId
        ";
        
        return $this->queryOne($sql, ['sessionId' => $sessionId]);
    }
    
    /**
     * Get a reading session for a specific user and book
     * 
     * @param int $userId User ID
     * @param int $bookId Book ID
     * @return array|null Reading session details
     */
    public function getUserBookSession($userId, $bookId)
    {
        $sql = "
            SELECT 
                rs.*, 
                b.b_title, 
                b.b_author, 
                b.b_cover_path,
                b.b_file_path,
                rp.current_page,
                rp.is_completed,
                CASE
                    WHEN rs.rs_expires_at < NOW() THEN 'expired'
                    ELSE 'active'
                END AS status,
                CASE
                    WHEN up.up_id IS NOT NULL THEN true
                    ELSE false
                END AS is_purchased
            FROM reading_session rs
            JOIN books b ON rs.b_id = b.b_id
            LEFT JOIN reading_progress rp ON rs.rs_id = rp.rs_id
            LEFT JOIN user_purchase up ON (rs.ua_id = up.ua_id AND rs.b_id = up.b_id)
            WHERE rs.ua_id = :userId AND rs.b_id = :bookId
            ORDER BY rs.rs_started_at DESC
            LIMIT 1
        ";
        
        return $this->queryOne($sql, [
            'userId' => $userId,
            'bookId' => $bookId
        ]);
    }
    
    /**
     * Check if a book can be read by a new user (within the 3 user limit)
     * 
     * @param int $bookId Book ID
     * @return bool True if book can be read, false otherwise
     */
    public function canBookBeRead($bookId)
    {
        $sql = "
            SELECT COUNT(DISTINCT ua_id) as active_readers
            FROM reading_session
            WHERE b_id = :bookId
            AND rs_expires_at > NOW()
        ";
        
        $result = $this->queryScalar($sql, ['bookId' => $bookId]);
        
        // Check if we have less than 3 active readers
        return $result < 3;
    }
    
    /**
     * Check if a user has purchased a book
     * 
     * @param int $userId User ID
     * @param int $bookId Book ID
     * @return bool True if purchased, false otherwise
     */
    public function hasUserPurchasedBook($userId, $bookId)
    {
        $sql = "
            SELECT COUNT(*) FROM user_purchase
            WHERE ua_id = :userId AND b_id = :bookId
        ";
        
        return $this->queryScalar($sql, [
            'userId' => $userId,
            'bookId' => $bookId
        ]) > 0;
    }
    
    /**
     * Create a new reading session
     * 
     * @param int $userId User ID
     * @param int $bookId Book ID
     * @return int|false Session ID on success, false on failure
     */
    public function createSession($userId, $bookId)
    {
        $sql = "
            INSERT INTO reading_session (ua_id, b_id)
            VALUES (:userId, :bookId)
            RETURNING rs_id
        ";
        
        $sessionId = $this->queryScalar($sql, [
            'userId' => $userId,
            'bookId' => $bookId
        ]);
        
        if ($sessionId) {
            // Create initial reading progress record
            $this->createReadingProgress($sessionId);
        }
        
        return $sessionId;
    }
    
    /**
     * Create initial reading progress for a session
     * 
     * @param int $sessionId Session ID
     * @return bool Success status
     */
    private function createReadingProgress($sessionId)
    {
        $sql = "
            INSERT INTO reading_progress (rs_id, current_page, is_completed)
            VALUES (:sessionId, 1, false)
        ";
        
        return $this->execute($sql, ['sessionId' => $sessionId]) > 0;
    }
    
    /**
     * Update reading progress
     * 
     * @param int $sessionId Session ID
     * @param int $currentPage Current page
     * @param bool $isCompleted Whether the book is completed
     * @return bool Success status
     */
    public function updateReadingProgress($sessionId, $currentPage, $isCompleted = false)
    {
        $sql = "
            UPDATE reading_progress
            SET current_page = :currentPage,
                is_completed = :isCompleted,
                last_updated = NOW()
            WHERE rs_id = :sessionId
        ";
        
        return $this->execute($sql, [
            'sessionId' => $sessionId,
            'currentPage' => $currentPage,
            'isCompleted' => $isCompleted
        ]) > 0;
    }
    
    /**
     * Check if a user has an active or expired session for a book
     * 
     * @param int $userId User ID
     * @param int $bookId Book ID
     * @return array|null Session details, or null if no session exists
     */
    public function checkExistingSession($userId, $bookId)
    {
        $sql = "
            SELECT 
                rs.*,
                CASE
                    WHEN rs.rs_expires_at < NOW() THEN 'expired'
                    ELSE 'active'
                END AS status
            FROM reading_session rs
            WHERE rs.ua_id = :userId 
            AND rs.b_id = :bookId
            ORDER BY rs.rs_started_at DESC
            LIMIT 1
        ";
        
        return $this->queryOne($sql, [
            'userId' => $userId,
            'bookId' => $bookId
        ]);
    }
} 