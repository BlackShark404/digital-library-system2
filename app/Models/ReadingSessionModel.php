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
                {$this->table} rs
            LEFT JOIN
                user_purchase up ON (rs.ua_id = up.ua_id AND rs.b_id = up.b_id)
            WHERE 
                rs.b_id = :book_id AND rs.rs_expires_at > NOW()
                AND up.up_id IS NULL -- Exclude users who purchased the book
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
            
            $rowCount = $this->execute($sql, [
                'current_page' => $currentPage,
                'is_completed' => $isCompleted ? 1 : 0,
                'session_id' => $sessionId
            ]);
            
            // If record was not found, insert a new one
            if ($rowCount === 0) {
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
     * Get book suggestions based on user's reading history
     * 
     * @param int $userId User ID
     * @param int $limit Maximum number of suggestions to return
     * @return array Book suggestions
     */
    public function getReadingSuggestions($userId, $limit = 5)
    {
        $sql = "
            WITH user_genres AS (
                -- Get genres the user has read
                SELECT 
                    bg.genre_id,
                    COUNT(*) as read_count
                FROM 
                    {$this->table} rs
                JOIN 
                    books b ON rs.b_id = b.b_id
                JOIN
                    book_genres bg ON b.b_id = bg.book_id
                WHERE 
                    rs.ua_id = :user_id
                GROUP BY 
                    bg.genre_id
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
            ),
            suggested_books AS (
                -- Get books in those genres that the user hasn't read yet
                SELECT 
                    b.b_id,
                    b.b_title,
                    b.b_author,
                    b.b_cover_path,
                    b.b_description,
                    MAX(ug.read_count) as relevance_score
                FROM 
                    books b
                JOIN 
                    book_genres bg ON b.b_id = bg.book_id
                JOIN 
                    genre g ON bg.genre_id = g.g_id
                JOIN 
                    user_genres ug ON bg.genre_id = ug.genre_id
                WHERE 
                    b.b_id NOT IN (SELECT b_id FROM read_books)
                    AND b.b_deleted_at IS NULL
                GROUP BY
                    b.b_id, b.b_title, b.b_author, b.b_cover_path, b.b_description
                ORDER BY 
                    relevance_score DESC, 
                    b.b_publication_date DESC
                LIMIT :limit
            )
            -- Get the final results with all genres for each book
            SELECT 
                sb.*,
                (
                    SELECT jsonb_agg(
                        jsonb_build_object(
                            'g_id', g.g_id, 
                            'g_name', g.g_name
                        )
                    )
                    FROM book_genres bg
                    JOIN genre g ON bg.genre_id = g.g_id
                    WHERE bg.book_id = sb.b_id
                ) as genres
            FROM 
                suggested_books sb
            ORDER BY 
                relevance_score DESC, 
                b_title
        ";
        
        $suggestions = $this->query($sql, [
            'user_id' => $userId,
            'limit' => $limit
        ]);
        
        // Process the JSON genres array
        foreach ($suggestions as &$book) {
            if (isset($book['genres']) && $book['genres']) {
                // If the database returns JSON string, parse it
                if (is_string($book['genres'])) {
                    $book['genres'] = json_decode($book['genres'], true);
                }
                
                // Keep single genre for backward compatibility
                if (!empty($book['genres'])) {
                    $book['genre'] = $book['genres'][0]['g_name'];
                } else {
                    $book['genre'] = 'Uncategorized';
                }
            } else {
                $book['genres'] = [];
                $book['genre'] = 'Uncategorized';
            }
        }
        
        return $suggestions;
    }
    
    /**
     * Purchase a book for a user
     * 
     * @param int $userId User ID
     * @param int $bookId Book ID
     * @return bool True on success, false on failure
     */
    public function purchaseBook($userId, $bookId)
    {
        // Check if already purchased
        if ($this->hasUserPurchasedBook($userId, $bookId)) {
            return true; // Already purchased, consider it successful
        }
        
        $sql = "
            INSERT INTO user_purchase (ua_id, b_id)
            VALUES (:user_id, :book_id)
        ";
        
        return $this->execute($sql, [
            'user_id' => $userId,
            'book_id' => $bookId
        ]);
    }
    
    /**
     * Get all books purchased by a user
     * 
     * @param int $userId User ID
     * @return array Purchased books with their details
     */
    public function getUserPurchases($userId)
    {
        $sql = "
            SELECT 
                up.up_id,
                up.ua_id,
                up.b_id,
                up.up_purchased_at,
                b.b_title,
                b.b_author,
                b.b_cover_path,
                b.b_file_path,
                b.b_price,
                b.b_isbn,
                b.b_publisher
            FROM 
                user_purchase up
            JOIN 
                books b ON up.b_id = b.b_id
            WHERE 
                up.ua_id = :user_id
            ORDER BY 
                up.up_purchased_at DESC
        ";
        
        return $this->query($sql, ['user_id' => $userId]);
    }
    
    /**
     * Get recent reading activity for a user
     * 
     * @param int $userId User ID
     * @param int $limit Maximum number of activities to return
     * @return array Recent reading sessions with book details
     */
    public function getRecentReadingActivity($userId, $limit = 5)
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
                b.b_pages,
                rp.current_page,
                rp.last_updated,
                rp.is_completed,
                CASE 
                    WHEN rs.rs_expires_at < NOW() THEN TRUE
                    ELSE FALSE
                END as is_expired,
                CASE
                    WHEN up.up_id IS NOT NULL THEN TRUE
                    ELSE FALSE
                END as is_purchased,
                CASE
                    WHEN rp.current_page = 1 THEN 'Started reading'
                    WHEN rp.is_completed = TRUE THEN 'Completed'
                    ELSE CONCAT('Read to page ', rp.current_page)
                END as activity_text,
                EXTRACT(EPOCH FROM (NOW() - rp.last_updated))/60 as minutes_ago
            FROM 
                {$this->table} rs
            JOIN 
                books b ON rs.b_id = b.b_id
            JOIN 
                {$this->progressTable} rp ON rs.rs_id = rp.rs_id
            LEFT JOIN
                user_purchase up ON (rs.ua_id = up.ua_id AND rs.b_id = up.b_id)
            WHERE 
                rs.ua_id = :user_id
            ORDER BY 
                rp.last_updated DESC
            LIMIT :limit
        ";
        
        return $this->query($sql, [
            'user_id' => $userId,
            'limit' => $limit
        ]);
    }
    
    /**
     * Get all reading sessions with user and book details (for admin)
     * 
     * @param string $search Search term for title, author, or user
     * @param string $status Filter by status (active, expired, purchased)
     * @param string $dateFrom Filter by start date (from)
     * @param string $dateTo Filter by start date (to)
     * @return array Reading sessions
     */
    public function getAllReadingSessions($search = '', $status = '', $dateFrom = '', $dateTo = '')
    {
        $params = [];
        $conditions = [];
        
        // Base query
        $sql = "
            SELECT 
                rs.rs_id,
                rs.ua_id,
                rs.b_id,
                rs.rs_started_at,
                rs.rs_expires_at,
                ua.ua_first_name,
                ua.ua_last_name,
                ua.ua_email,
                b.b_title,
                b.b_author,
                b.b_cover_path,
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
            JOIN
                user_account ua ON rs.ua_id = ua.ua_id
            LEFT JOIN 
                {$this->progressTable} rp ON rs.rs_id = rp.rs_id
            LEFT JOIN
                user_purchase up ON (rs.ua_id = up.ua_id AND rs.b_id = up.b_id)
            WHERE 1=1
        ";
        
        // Add search condition
        if (!empty($search)) {
            $conditions[] = "(b.b_title LIKE :search OR b.b_author LIKE :search OR 
                             ua.ua_first_name LIKE :search OR ua.ua_last_name LIKE :search OR
                             ua.ua_email LIKE :search)";
            $params['search'] = "%{$search}%";
        }
        
        // Add status condition
        if (!empty($status)) {
            if ($status === 'active') {
                $conditions[] = "rs.rs_expires_at > NOW() AND up.up_id IS NULL";
            } elseif ($status === 'expired') {
                $conditions[] = "rs.rs_expires_at < NOW() AND up.up_id IS NULL";
            } elseif ($status === 'purchased') {
                $conditions[] = "up.up_id IS NOT NULL";
            }
        }
        
        // Add date range conditions
        if (!empty($dateFrom)) {
            $conditions[] = "rs.rs_started_at >= :date_from";
            $params['date_from'] = $dateFrom . ' 00:00:00';
        }
        
        if (!empty($dateTo)) {
            $conditions[] = "rs.rs_started_at <= :date_to";
            $params['date_to'] = $dateTo . ' 23:59:59';
        }
        
        // Add conditions to query
        if (!empty($conditions)) {
            $sql .= " AND " . implode(" AND ", $conditions);
        }
        
        // Add order by
        $sql .= " ORDER BY rs.rs_started_at DESC";
        
        return $this->query($sql, $params);
    }
    
    /**
     * Get a reading session with detailed user and book information
     * 
     * @param int $sessionId Reading session ID
     * @return array|null Reading session with detailed information
     */
    public function getReadingSessionWithDetails($sessionId)
    {
        $sql = "
            SELECT 
                rs.rs_id,
                rs.ua_id,
                rs.b_id,
                rs.rs_started_at,
                rs.rs_expires_at,
                ua.ua_first_name,
                ua.ua_last_name,
                ua.ua_email,
                b.b_title,
                b.b_author,
                b.b_publisher,
                b.b_cover_path,
                b.b_pages,
                b.b_isbn,
                COALESCE(
                    (SELECT STRING_AGG(g.g_name, ', ') 
                     FROM book_genres bg 
                     JOIN genre g ON bg.genre_id = g.g_id 
                     WHERE bg.book_id = b.b_id
                     GROUP BY bg.book_id), 
                    'Uncategorized'
                ) AS genre,
                rp.current_page,
                rp.is_completed,
                rp.last_updated,
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
            JOIN
                user_account ua ON rs.ua_id = ua.ua_id
            LEFT JOIN 
                {$this->progressTable} rp ON rs.rs_id = rp.rs_id
            LEFT JOIN
                user_purchase up ON (rs.ua_id = up.ua_id AND rs.b_id = up.b_id)
            WHERE 
                rs.rs_id = :session_id
        ";
        
        return $this->queryOne($sql, ['session_id' => $sessionId]);
    }
    
    /**
     * Count total purchases in the system
     *
     * @return int Total number of purchases
     */
    public function countTotalPurchases()
    {
        $sql = "SELECT COUNT(*) as total FROM user_purchase";
        $result = $this->queryOne($sql);
        return $result ? (int)$result['total'] : 0;
    }
    
    /**
     * Get all purchases with user and book details for admin view
     * 
     * @param string $search Optional search term for title, author, or user
     * @param string $dateFrom Optional start date filter
     * @param string $dateTo Optional end date filter
     * @return array All purchases with user and book details
     */
    public function getAllPurchases($search = '', $dateFrom = '', $dateTo = '')
    {
        $params = [];
        $conditions = [];
        
        // Base query
        $sql = "
            SELECT 
                up.up_id,
                up.ua_id,
                up.b_id,
                up.up_purchased_at,
                ua.ua_first_name,
                ua.ua_last_name,
                ua.ua_email,
                ua.ua_profile_url,
                b.b_title,
                b.b_author,
                b.b_cover_path,
                b.b_price
            FROM 
                user_purchase up
            JOIN 
                books b ON up.b_id = b.b_id
            JOIN
                user_account ua ON up.ua_id = ua.ua_id
            WHERE 1=1
        ";
        
        // Add search condition
        if (!empty($search)) {
            $conditions[] = "(b.b_title LIKE :search OR b.b_author LIKE :search OR 
                             ua.ua_first_name LIKE :search OR ua.ua_last_name LIKE :search OR
                             ua.ua_email LIKE :search)";
            $params['search'] = "%{$search}%";
        }
        
        // Add date range conditions
        if (!empty($dateFrom)) {
            $conditions[] = "up.up_purchased_at >= :date_from";
            $params['date_from'] = $dateFrom . ' 00:00:00';
        }
        
        if (!empty($dateTo)) {
            $conditions[] = "up.up_purchased_at <= :date_to";
            $params['date_to'] = $dateTo . ' 23:59:59';
        }
        
        // Add conditions to query
        if (!empty($conditions)) {
            $sql .= " AND " . implode(" AND ", $conditions);
        }
        
        // Add order by
        $sql .= " ORDER BY up.up_purchased_at DESC";
        
        return $this->query($sql, $params);
    }
    
    /**
     * Get a purchase by ID with detailed user and book information
     * 
     * @param int $purchaseId Purchase ID
     * @return array|null Purchase with user and book details or null if not found
     */
    public function getPurchaseById($purchaseId)
    {
        $sql = "
            SELECT 
                up.up_id,
                up.ua_id,
                up.b_id,
                up.up_purchased_at,
                ua.ua_first_name,
                ua.ua_last_name,
                ua.ua_email,
                ua.ua_profile_url,
                b.b_title,
                b.b_author,
                b.b_publisher,
                b.b_cover_path,
                b.b_price,
                b.b_isbn
            FROM 
                user_purchase up
            JOIN 
                books b ON up.b_id = b.b_id
            JOIN
                user_account ua ON up.ua_id = ua.ua_id
            WHERE 
                up.up_id = :purchase_id
        ";
        
        return $this->queryOne($sql, ['purchase_id' => $purchaseId]);
    }
    
    /**
     * Count total reading sessions in the system
     *
     * @return int Total number of reading sessions
     */
    public function countTotalSessions()
    {
        $sql = "SELECT COUNT(*) as total FROM {$this->table}";
        $result = $this->queryOne($sql);
        return $result ? (int)$result['total'] : 0;
    }
    
    /**
     * Get recent reading sessions for admin dashboard
     *
     * @param int $limit Maximum number of sessions to return
     * @return array Recent reading sessions with user and book details
     */
    public function getRecentReadingSessions($limit = 5)
    {
        $sql = "
            SELECT 
                rs.rs_id,
                rs.ua_id,
                rs.b_id,
                rs.rs_started_at,
                rs.rs_expires_at,
                ua.ua_first_name,
                ua.ua_last_name,
                ua.ua_email,
                ua.ua_profile_url,
                b.b_title,
                b.b_author,
                b.b_cover_path,
                rp.current_page,
                b.b_pages,
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
            JOIN
                user_account ua ON rs.ua_id = ua.ua_id
            LEFT JOIN 
                {$this->progressTable} rp ON rs.rs_id = rp.rs_id
            LEFT JOIN
                user_purchase up ON (rs.ua_id = up.ua_id AND rs.b_id = up.b_id)
            ORDER BY 
                rs.rs_started_at DESC
            LIMIT 
                :limit
        ";
        
        return $this->query($sql, ['limit' => $limit]);
    }
    
    /**
     * Get recent purchases for admin dashboard
     *
     * @param int $limit Maximum number of purchases to return
     * @return array Recent purchases with user and book details
     */
    public function getRecentPurchases($limit = 5)
    {
        $sql = "
            SELECT 
                up.up_id,
                up.ua_id,
                up.b_id,
                up.up_purchased_at,
                ua.ua_first_name,
                ua.ua_last_name,
                ua.ua_email,
                ua.ua_profile_url,
                b.b_title,
                b.b_author,
                b.b_cover_path,
                b.b_price
            FROM 
                user_purchase up
            JOIN 
                books b ON up.b_id = b.b_id
            JOIN
                user_account ua ON up.ua_id = ua.ua_id
            ORDER BY 
                up.up_purchased_at DESC
            LIMIT 
                :limit
        ";
        
        return $this->query($sql, ['limit' => $limit]);
    }
} 