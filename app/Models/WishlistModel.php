<?php

namespace App\Models;

class WishlistModel extends BaseModel
{
    protected $table = 'wishlist';
    
    public function __construct()
    {
        parent::__construct();
    }
    
    /**
     * Get all wishlist items for a user with book details
     * 
     * @param int $userId User ID
     * @return array List of wishlist items with book details
     */
    public function getUserWishlist($userId)
    {
        $sql = "
            SELECT 
                wl.wl_id,
                wl.ua_id,
                wl.b_id,
                wl.wl_added_at,
                b.b_title AS title,
                b.b_author AS author,
                b.b_publisher AS publisher,
                b.b_publication_date AS publication_date,
                b.b_isbn AS isbn,
                b.b_pages AS pages,
                b.b_price AS price,
                b.b_description AS description,
                b.b_cover_path AS cover_image,
                COALESCE(
                    (SELECT STRING_AGG(g.g_name, ', ') 
                     FROM book_genres bg 
                     JOIN genre g ON bg.genre_id = g.g_id 
                     WHERE bg.book_id = b.b_id
                     GROUP BY bg.book_id), 
                    'Uncategorized'
                ) AS genre
            FROM 
                {$this->table} wl
            JOIN 
                books b ON wl.b_id = b.b_id
            WHERE 
                wl.ua_id = :user_id
                AND b.b_deleted_at IS NULL
            ORDER BY 
                wl.wl_added_at DESC
        ";
        
        return $this->query($sql, ['user_id' => $userId]);
    }
    
    /**
     * Check if a book is in user's wishlist
     * 
     * @param int $userId User ID
     * @param int $bookId Book ID
     * @return bool True if book is in wishlist, false otherwise
     */
    public function isBookInWishlist($userId, $bookId)
    {
        $sql = "
            SELECT 
                COUNT(*) as count
            FROM 
                {$this->table}
            WHERE 
                ua_id = :user_id
                AND b_id = :book_id
        ";
        
        $result = $this->queryScalar($sql, [
            'user_id' => $userId,
            'book_id' => $bookId
        ], 0);
        
        return $result > 0;
    }
    
    /**
     * Add a book to user's wishlist
     * 
     * @param int $userId User ID
     * @param int $bookId Book ID
     * @return bool Success or failure
     */
    public function addToWishlist($userId, $bookId)
    {
        // Check if already in wishlist
        if ($this->isBookInWishlist($userId, $bookId)) {
            return true; // Already in wishlist, consider it a success
        }
        
        $sql = "
            INSERT INTO {$this->table} (ua_id, b_id)
            VALUES (:user_id, :book_id)
        ";
        
        try {
            $this->execute($sql, [
                'user_id' => $userId,
                'book_id' => $bookId
            ]);
            return true;
        } catch (\Exception $e) {
            error_log("Error adding to wishlist: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Remove a book from user's wishlist
     * 
     * @param int $wishlistId Wishlist ID
     * @param int $userId User ID (for verification)
     * @return bool Success or failure
     */
    public function removeFromWishlist($wishlistId, $userId)
    {
        $sql = "
            DELETE FROM {$this->table}
            WHERE wl_id = :wishlist_id AND ua_id = :user_id
        ";
        
        try {
            $result = $this->execute($sql, [
                'wishlist_id' => $wishlistId,
                'user_id' => $userId
            ]);
            return $result > 0;
        } catch (\Exception $e) {
            error_log("Error removing from wishlist: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Remove a book from user's wishlist by book ID
     * 
     * @param int $userId User ID
     * @param int $bookId Book ID
     * @return bool Success or failure
     */
    public function removeBookFromWishlist($userId, $bookId)
    {
        $sql = "
            DELETE FROM {$this->table}
            WHERE ua_id = :user_id AND b_id = :book_id
        ";
        
        try {
            $result = $this->execute($sql, [
                'user_id' => $userId,
                'book_id' => $bookId
            ]);
            return $result > 0;
        } catch (\Exception $e) {
            error_log("Error removing from wishlist: " . $e->getMessage());
            return false;
        }
    }
} 