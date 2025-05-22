<?php

namespace App\Models;

class BookModel extends BaseModel
{
    protected $table = 'books';
    protected $primaryKey = 'b_id';
    
    public function __construct()
    {
        parent::__construct();
    }
    
    /**
     * Get all books with their genre names
     * 
     * @return array List of books with genre information
     */
    public function getAllBooks()
    {
        $sql = "
            SELECT 
                b.*, 
                g.g_name as genre_name
            FROM 
                {$this->table} b
            LEFT JOIN 
                genre g ON b.b_genre_id = g.g_id
            WHERE 
                b.b_deleted_at IS NULL
            ORDER BY 
                b.b_title ASC
        ";
        
        return $this->query($sql);
    }
    
    /**
     * Get a book by ID with genre information
     * 
     * @param int $id Book ID
     * @return array|null Book data or null if not found
     */
    public function getBookById($id)
    {
        $sql = "
            SELECT 
                b.*, 
                g.g_name as genre_name
            FROM 
                {$this->table} b
            LEFT JOIN 
                genre g ON b.b_genre_id = g.g_id
            WHERE 
                b.{$this->primaryKey} = :id
                AND b.b_deleted_at IS NULL
        ";
        
        return $this->queryOne($sql, ['id' => $id]);
    }
    
    /**
     * Create a new book
     * 
     * @param array $data Book data
     * @return int|bool The ID of the new book or false on failure
     */
    public function createBook($data)
    {
        try {
            $this->beginTransaction();
            
            // Extract data
            $title = $data['title'] ?? '';
            $author = $data['author'] ?? '';
            $publisher = $data['publisher'] ?? '';
            $publicationDate = $data['publication_date'] ?? null;
            $isbn = $data['isbn'] ?? '';
            $genreId = $data['genre_id'] ?? null;
            $pages = $data['pages'] ?? null;
            $price = $data['price'] ?? null;
            $description = $data['description'] ?? '';
            $coverPath = $data['cover_path'] ?? null;
            $filePath = $data['file_path'] ?? null;
            
            // Prepare SQL
            $sql = "
                INSERT INTO {$this->table} (
                    b_title, 
                    b_author, 
                    b_publisher, 
                    b_publication_date, 
                    b_isbn, 
                    b_genre_id, 
                    b_pages, 
                    b_price, 
                    b_description, 
                    b_cover_path, 
                    b_file_path,
                    b_created_at,
                    b_updated_at
                ) VALUES (
                    :title, 
                    :author, 
                    :publisher, 
                    :publication_date, 
                    :isbn, 
                    :genre_id, 
                    :pages, 
                    :price, 
                    :description, 
                    :cover_path, 
                    :file_path,
                    NOW(),
                    NOW()
                )
            ";
            
            // Execute query
            $this->execute($sql, [
                'title' => $title,
                'author' => $author,
                'publisher' => $publisher,
                'publication_date' => $publicationDate,
                'isbn' => $isbn,
                'genre_id' => $genreId,
                'pages' => $pages,
                'price' => $price,
                'description' => $description,
                'cover_path' => $coverPath,
                'file_path' => $filePath
            ]);
            
            $bookId = $this->lastInsertId();
            
            $this->commit();
            return $bookId;
        } catch (\Exception $e) {
            $this->rollBack();
            error_log("Error creating book: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Update an existing book
     * 
     * @param int $id Book ID
     * @param array $data Book data to update
     * @return bool Success status
     */
    public function updateBook($id, $data)
    {
        try {
            $this->beginTransaction();
            
            // Extract data
            $title = $data['title'] ?? null;
            $author = $data['author'] ?? null;
            $publisher = $data['publisher'] ?? null;
            $publicationDate = $data['publication_date'] ?? null;
            $isbn = $data['isbn'] ?? null;
            $genreId = $data['genre_id'] ?? null;
            $pages = $data['pages'] ?? null;
            $price = $data['price'] ?? null;
            $description = $data['description'] ?? null;
            $coverPath = $data['cover_path'] ?? null;
            $filePath = $data['file_path'] ?? null;
            
            // Build update fields dynamically
            $fields = [];
            $params = ['id' => $id];
            
            if ($title !== null) { $fields[] = "b_title = :title"; $params['title'] = $title; }
            if ($author !== null) { $fields[] = "b_author = :author"; $params['author'] = $author; }
            if ($publisher !== null) { $fields[] = "b_publisher = :publisher"; $params['publisher'] = $publisher; }
            if ($publicationDate !== null) { $fields[] = "b_publication_date = :publication_date"; $params['publication_date'] = $publicationDate; }
            if ($isbn !== null) { $fields[] = "b_isbn = :isbn"; $params['isbn'] = $isbn; }
            if ($genreId !== null) { $fields[] = "b_genre_id = :genre_id"; $params['genre_id'] = $genreId; }
            if ($pages !== null) { $fields[] = "b_pages = :pages"; $params['pages'] = $pages; }
            if ($price !== null) { $fields[] = "b_price = :price"; $params['price'] = $price; }
            if ($description !== null) { $fields[] = "b_description = :description"; $params['description'] = $description; }
            if ($coverPath !== null) { $fields[] = "b_cover_path = :cover_path"; $params['cover_path'] = $coverPath; }
            if ($filePath !== null) { $fields[] = "b_file_path = :file_path"; $params['file_path'] = $filePath; }
            
            // Add updated_at field
            $fields[] = "b_updated_at = NOW()";
            
            // Prepare SQL if there are fields to update
            if (!empty($fields)) {
                $sql = "UPDATE {$this->table} SET " . implode(", ", $fields) . " WHERE {$this->primaryKey} = :id";
                $this->execute($sql, $params);
            }
            
            $this->commit();
            return true;
        } catch (\Exception $e) {
            $this->rollBack();
            error_log("Error updating book: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Soft delete a book by ID
     * 
     * @param int $id Book ID
     * @return bool Success status
     */
    public function deleteBook($id)
    {
        try {
            $sql = "UPDATE {$this->table} SET b_deleted_at = NOW() WHERE {$this->primaryKey} = :id";
            $this->execute($sql, ['id' => $id]);
            return true;
        } catch (\Exception $e) {
            error_log("Error deleting book: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Count total books in the system (not deleted)
     * 
     * @return int Total number of books
     */
    public function countTotalBooks()
    {
        $sql = "SELECT COUNT(*) as total FROM {$this->table} WHERE b_deleted_at IS NULL";
        $result = $this->queryOne($sql);
        return $result ? (int)$result['total'] : 0;
    }
    
    /**
     * Get all available genres
     * 
     * @return array List of genres
     */
    public function getAllGenres()
    {
        $sql = "SELECT g_id, g_name FROM genre ORDER BY g_name";
        return $this->query($sql);
    }
    
    /**
     * Get all categories with the count of books in each category
     * 
     * @return array List of categories with book count
     */
    public function getAllCategoriesWithBookCount()
    {
        $sql = "
            SELECT 
                g.g_id,
                g.g_name,
                COUNT(b.b_id) AS book_count
            FROM 
                genre g
            LEFT JOIN 
                books b ON g.g_id = b.b_genre_id AND b.b_deleted_at IS NULL
            GROUP BY 
                g.g_id,
                g.g_name
            ORDER BY 
                g.g_name
        ";
        
        return $this->query($sql);
    }
    
    /**
     * Check if a category name is already taken
     * 
     * @param string $name Category name
     * @param int|null $excludeId Exclude this category ID from the check (for updates)
     * @return bool True if name is taken, false otherwise
     */
    public function isCategoryNameTaken($name, $excludeId = null)
    {
        $sql = "SELECT g_id FROM genre WHERE g_name = :name";
        $params = ['name' => $name];
        
        if ($excludeId) {
            $sql .= " AND g_id != :exclude_id";
            $params['exclude_id'] = $excludeId;
        }
        
        $result = $this->queryOne($sql, $params);
        return $result !== false;
    }
    
    /**
     * Get a category by ID
     * 
     * @param int $id Category ID
     * @return array|bool Category data or false if not found
     */
    public function getCategoryById($id)
    {
        $sql = "SELECT g_id, g_name FROM genre WHERE g_id = :id";
        return $this->queryOne($sql, ['id' => $id]);
    }
    
    /**
     * Add a new category
     * 
     * @param string $name Category name
     * @return int|bool The new category ID or false on failure
     */
    public function addCategory($name)
    {
        try {
            $this->beginTransaction();
            
            $sql = "INSERT INTO genre (g_name) VALUES (:name)";
            $this->execute($sql, ['name' => $name]);
            
            $categoryId = $this->lastInsertId();
            $this->commit();
            
            return $categoryId;
        } catch (\Exception $e) {
            $this->rollBack();
            error_log("Error adding category: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Update a category
     * 
     * @param int $id Category ID
     * @param string $name New category name
     * @return bool Success status
     */
    public function updateCategory($id, $name)
    {
        try {
            $sql = "UPDATE genre SET g_name = :name WHERE g_id = :id";
            $this->execute($sql, ['id' => $id, 'name' => $name]);
            return true;
        } catch (\Exception $e) {
            error_log("Error updating category: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Delete a category and remove the category from associated books
     * 
     * @param int $id Category ID
     * @return bool Success status
     */
    public function deleteCategory($id)
    {
        try {
            $this->beginTransaction();
            
            // First, set b_genre_id to NULL for all books with this category
            $sql = "UPDATE books SET b_genre_id = NULL WHERE b_genre_id = :id";
            $this->execute($sql, ['id' => $id]);
            
            // Then delete the category
            $sql = "DELETE FROM genre WHERE g_id = :id";
            $this->execute($sql, ['id' => $id]);
            
            $this->commit();
            return true;
        } catch (\Exception $e) {
            $this->rollBack();
            error_log("Error deleting category: " . $e->getMessage());
            return false;
        }
    }
} 