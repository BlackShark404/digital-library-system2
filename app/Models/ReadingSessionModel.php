<?php

namespace App\Models;

class ReadingSessionModel extends BaseModel
{
    protected $table = 'reading_session';
    protected $progressTable = 'reading_progress';
    
    public function __construct()
    {
        parent::__construct();
    }
    
    /**
     * Get all active reading sessions for a user
     * 
     * @param int $userId User ID
     * @return array Reading sessions with book info
     */
    public function getUserReadingSessions($userId)
    {
        $sql = "
            SELECT 
                rs.rs_id,
                rs.ua_id,
                rs.b_id,
                rs.rs_started_at,
                rs.rs_expires_at,
                b.b_title,
                b.b_author,
                b.b_cover_path,
                b.b_file_path,
                b.b_pages,
                rp.current_page,
                rp.is_completed,
                CASE 
                    WHEN rs.rs_expires_at < NOW() THEN TRUE
                    ELSE FALSE
                END as is_expired,
                CASE
                    WHEN up.up_id IS NOT NULL THEN TRUE
                    ELSE FALSE
                END as is_purchased
            FROM 
                {$this->table} rs
            JOIN 
                books b ON rs.b_id = b.b_id
            LEFT JOIN 
                {$this->progressTable} rp ON rs.rs_id = rp.rs_id
            LEFT JOIN
                user_purchase up ON (rs.ua_id = up.ua_id AND rs.b_id = up.b_id)
            WHERE 
                rs.ua_id = :user_id
            ORDER BY 
                rs.rs_started_at DESC
        ";
        
        return $this->query($sql, ['user_id' => $userId]);
    }
    
    /**
     * Get specific reading session by ID with book details
     * 
     * @param int $sessionId Session ID
     * @return array|null Reading session with book info
     */
    public function getReadingSession($sessionId)
    {
        $sql = "
            SELECT 
                rs.rs_id,
                rs.ua_id,
                rs.b_id,
                rs.rs_started_at,
                rs.rs_expires_at,
                b.b_title,
                b.b_author,
                b.b_file_path,
                b.b_cover_path,
                b.b_pages,
                rp.current_page,
                rp.is_completed,
                CASE 
                    WHEN rs.rs_expires_at < NOW() THEN TRUE
                    ELSE FALSE
                END as is_expired
            FROM 
                {$this->table} rs
            JOIN 
                books b ON rs.b_id = b.b_id
            LEFT JOIN 
                {$this->progressTable} rp ON rs.rs_id = rp.rs_id
            WHERE 
                rs.rs_id = :session_id
        ";
        
        return $this->queryOne($sql, ['session_id' => $sessionId]);
    }
    
    /**
     * Check if user has access to read a book
     * 
     * @param int $userId User ID
     * @param int $bookId Book ID
     * @return array|null Active reading session if exists
     */
    public function getUserBookSession($userId, $bookId)
    {
        $sql = "
            SELECT 
                rs.rs_id,
                rs.rs_started_at,
                rs.rs_expires_at,
                CASE 
                    WHEN rs.rs_expires_at < NOW() THEN TRUE
                    ELSE FALSE
                END as is_expired
            FROM 
                {$this->table} rs
            WHERE 
                rs.ua_id = :user_id AND rs.b_id = :book_id
            ORDER BY 
                rs.rs_started_at DESC
            LIMIT 1
        ";
        
        return $this->queryOne($sql, [
            'user_id' => $userId,
            'book_id' => $bookId
        ]);
    }
    
    /**
     * Count active reading sessions for a book
     * 
     * @param int $bookId Book ID
     * @return int Number of active sessions
     */
    public function countActiveSessionsForBook($bookId)
    {
        $sql = "
            SELECT 
                COUNT(*) as session_count
            FROM 
                {$this->table}
            WHERE 
                b_id = :book_id AND rs_expires_at > NOW()
        ";
        
        $result = $this->queryOne($sql, ['book_id' => $bookId]);
        return $result ? (int)$result['session_count'] : 0;
    }
    
    /**
     * Check if user has purchased the book
     * 
     * @param int $userId User ID
     * @param int $bookId Book ID
     * @return bool True if purchased
     */
    public function hasUserPurchasedBook($userId, $bookId)
    {
        $sql = "
            SELECT 
                COUNT(*) as purchased
            FROM 
                user_purchase
            WHERE 
                ua_id = :user_id AND b_id = :book_id
        ";
        
        $result = $this->queryOne($sql, [
            'user_id' => $userId,
            'book_id' => $bookId
        ]);
        
        return $result && (int)$result['purchased'] > 0;
    }
    
    /**
     * Create a new reading session
     * 
     * @param int $userId User ID
     * @param int $bookId Book ID
     * @return int|bool Session ID on success, false on failure
     */
    public function createReadingSession($userId, $bookId)
    {
        try {
            $this->beginTransaction();
            
            // Insert reading session
            $sql = "
                INSERT INTO {$this->table} (ua_id, b_id)
                VALUES (:user_id, :book_id)
                RETURNING rs_id
            ";
            
            $sessionId = $this->queryScalar($sql, [
                'user_id' => $userId,
                'book_id' => $bookId
            ]);
            
            if (!$sessionId) {
                $this->rollback();
                return false;
            }
            
            // Initialize reading progress
            $progressSql = "
                INSERT INTO {$this->progressTable} (rs_id, current_page, is_completed)
                VALUES (:session_id, 1, FALSE)
            ";
            
            $this->execute($progressSql, ['session_id' => $sessionId]);
            
            $this->commit();
            return $sessionId;
        } catch (\Exception $e) {
            $this->rollback();
            error_log("Error creating reading session: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Update reading progress
     * 
     * @param int $sessionId Session ID
     * @param int $currentPage Current page
     * @param bool $isCompleted Whether reading is completed
     * @return bool Success or failure
     */
    public function updateReadingProgress($sessionId, $currentPage, $isCompleted = false)
    {
        try {
            $sql = "
                UPDATE {$this->progressTable}
                SET 
                    current_page = :current_page,
                    is_completed = :is_completed,
                    last_updated = CURRENT_TIMESTAMP
                WHERE 
                    rs_id = :session_id
            ";
            
            $this->execute($sql, [
                'current_page' => $currentPage,
                'is_completed' => $isCompleted ? 1 : 0,
                'session_id' => $sessionId
            ]);
            
            // If record was not found, insert a new one
            if ($this->pdo->rowCount() === 0) {
                $insertSql = "
                    INSERT INTO {$this->progressTable} (rs_id, current_page, is_completed, last_updated)
                    VALUES (:session_id, :current_page, :is_completed, CURRENT_TIMESTAMP)
                ";
                
                $this->execute($insertSql, [
                    'session_id' => $sessionId,
                    'current_page' => $currentPage,
                    'is_completed' => $isCompleted ? 1 : 0
                ]);
            }
            
            return true;
        } catch (\Exception $e) {
            error_log("Error updating reading progress: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Get reading progress for a session
     * 
     * @param int $sessionId Session ID
     * @return array|null Reading progress data
     */
    public function getReadingProgress($sessionId)
    {
        $sql = "
            SELECT 
                rs_id,
                current_page,
                is_completed,
                last_updated
            FROM 
                {$this->progressTable}
            WHERE 
                rs_id = :session_id
        ";
        
        return $this->queryOne($sql, ['session_id' => $sessionId]);
    }
    
    /**
     * Check if a book is available for a new reading session
     * 
     * @param int $bookId Book ID
     * @return bool True if available, false if at maximum capacity
     */
    public function isBookAvailableForReading($bookId)
    {
        $activeSessionsCount = $this->countActiveSessionsForBook($bookId);
        return $activeSessionsCount < 3; // Maximum 3 concurrent readers
    }
    
    /**
     * Get reading statistics for a user
     * 
     * @param int $userId User ID
     * @return array Reading statistics
     */
    public function getUserReadingStats($userId)
    {
        $sql = "
            WITH user_stats AS (
                SELECT 
                    COUNT(DISTINCT rs.b_id) as books_started,
                    COUNT(DISTINCT CASE WHEN rp.is_completed = TRUE THEN rs.b_id END) as books_completed,
                    COUNT(DISTINCT CASE WHEN up.up_id IS NOT NULL THEN up.b_id END) as books_purchased,
                    COUNT(DISTINCT rs.rs_id) as total_sessions
                FROM 
                    {$this->table} rs
                LEFT JOIN 
                    {$this->progressTable} rp ON rs.rs_id = rp.rs_id
                LEFT JOIN 
                    user_purchase up ON (rs.ua_id = up.ua_id AND rs.b_id = up.b_id)
                WHERE 
                    rs.ua_id = :user_id
            )
            SELECT 
                books_started,
                books_completed,
                books_purchased,
                total_sessions,
                CASE 
                    WHEN books_started > 0 THEN ROUND((books_completed * 100.0) / books_started)
                    ELSE 0
                END as completion_rate
            FROM 
                user_stats
        ";
        
        $result = $this->queryOne($sql, ['user_id' => $userId]);
        return $result ?: [
            'books_started' => 0,
            'books_completed' => 0,
            'books_purchased' => 0,
            'total_sessions' => 0,
            'completion_rate' => 0
        ];
    }
    
    /**
     * Get reading suggestions based on a user's reading history
     * 
     * @param int $userId User ID
     * @param int $limit Maximum number of suggestions
     * @return array Book suggestions
     */
    public function getReadingSuggestions($userId, $limit = 5)
    {
        $sql = "
            WITH user_genres AS (
                -- Get genres the user has read
                SELECT 
                    b.b_genre_id,
                    COUNT(*) as read_count
                FROM 
                    {$this->table} rs
                JOIN 
                    books b ON rs.b_id = b.b_id
                WHERE 
                    rs.ua_id = :user_id
                    AND b.b_genre_id IS NOT NULL
                GROUP BY 
                    b.b_genre_id
                ORDER BY 
                    read_count DESC
                LIMIT 3
            ),
            read_books AS (
                -- Books the user has already read or purchased
                SELECT 
                    b_id
                FROM 
                    {$this->table}
                WHERE 
                    ua_id = :user_id
                UNION
                SELECT 
                    b_id
                FROM 
                    user_purchase
                WHERE 
                    ua_id = :user_id
            )
            -- Get books in those genres that the user hasn't read yet
            SELECT 
                b.b_id,
                b.b_title,
                b.b_author,
                b.b_cover_path,
                b.b_description,
                g.g_name as genre
            FROM 
                books b
            JOIN 
                genre g ON b.b_genre_id = g.g_id
            JOIN 
                user_genres ug ON b.b_genre_id = ug.b_genre_id
            WHERE 
                b.b_id NOT IN (SELECT b_id FROM read_books)
                AND b.b_deleted_at IS NULL
            ORDER BY 
                ug.read_count DESC, 
                b.b_publication_date DESC
            LIMIT :limit
        ";
        
        return $this->query($sql, [
            'user_id' => $userId,
            'limit' => $limit
        ]);
    }
} 