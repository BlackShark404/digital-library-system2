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
                b.*
            FROM 
                {$this->table} b
            WHERE 
                b.b_deleted_at IS NULL
            ORDER BY 
                b.b_title ASC
        ";
        
        $books = $this->query($sql);
        
        // Get genres for each book
        foreach ($books as &$book) {
            $book['genres'] = $this->getBookGenres($book['b_id']);
            // Get first genre for compatibility with old code that expects a single genre
            $book['genre_name'] = !empty($book['genres']) ? $book['genres'][0]['g_name'] : 'Uncategorized';
        }
        
        return $books;
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
                b.*
            FROM 
                {$this->table} b
            WHERE 
                b.{$this->primaryKey} = :id
                AND b.b_deleted_at IS NULL
        ";
        
        $book = $this->queryOne($sql, ['id' => $id]);
        
        if ($book) {
            // Get genres for this book
            $book['genres'] = $this->getBookGenres($id);
            
            // Get genre IDs for easy selection in multi-select
            $book['b_genre_ids'] = array_map(function($genre) {
                return $genre['g_id'];
            }, $book['genres']);
            
            // For backward compatibility with single genre display
            $book['genre_name'] = !empty($book['genres']) ? $book['genres'][0]['g_name'] : 'Uncategorized';
        }
        
        return $book;
    }
    
    /**
     * Get all genres for a specific book
     *
     * @param int $bookId
     * @return array Array of genres
     */
    public function getBookGenres($bookId)
    {
        $sql = "
            SELECT 
                g.g_id, g.g_name
            FROM 
                book_genres bg
            JOIN 
                genre g ON bg.genre_id = g.g_id
            WHERE 
                bg.book_id = :book_id
            ORDER BY
                g.g_name ASC
        ";
        
        return $this->query($sql, ['book_id' => $bookId]);
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
            
            // Validate required fields
            if (empty($data['title']) || empty($data['author'])) {
                throw new \Exception('Title and author are required fields');
            }
            
            // Extract data with validation
            $title = $data['title'];
            $author = $data['author'];
            $publisher = $data['publisher'] ?? '';
            $publicationDate = !empty($data['publication_date']) ? $data['publication_date'] : null;
            $isbn = $data['isbn'] ?? '';
            $pages = !empty($data['pages']) ? (int)$data['pages'] : null;
            $price = isset($data['price']) && $data['price'] !== '' ? (float)$data['price'] : 0.00;
            $description = $data['description'] ?? '';
            $coverPath = $data['cover_path'] ?? null;
            $filePath = $data['file_path'] ?? null;
            $genres = $data['genres'] ?? [];
            
            // Prepare SQL
            $sql = "
                INSERT INTO {$this->table} (
                    b_title, 
                    b_author, 
                    b_publisher, 
                    b_publication_date, 
                    b_isbn, 
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
                'pages' => $pages,
                'price' => $price,
                'description' => $description,
                'cover_path' => $coverPath,
                'file_path' => $filePath
            ]);
            
            $bookId = $this->lastInsertId();
            
            // Insert book genres
            if (!empty($genres)) {
                $this->saveBookGenres($bookId, $genres);
            }
            
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
            
            // Validate required fields
            if (empty($data['title']) || empty($data['author'])) {
                throw new \Exception('Title and author are required fields');
            }
            
            // Extract data with validation
            $title = $data['title'];
            $author = $data['author'];
            $publisher = $data['publisher'] ?? null;
            $publicationDate = !empty($data['publication_date']) ? $data['publication_date'] : null;
            $isbn = $data['isbn'] ?? null;
            $pages = !empty($data['pages']) ? (int)$data['pages'] : null;
            $price = isset($data['price']) && $data['price'] !== '' ? (float)$data['price'] : 0.00;
            $description = $data['description'] ?? null;
            $coverPath = $data['cover_path'] ?? null;
            $filePath = $data['file_path'] ?? null;
            $genres = $data['genres'] ?? [];
            
            // Build update fields dynamically
            $fields = [];
            $params = ['id' => $id];
            
            if ($title !== null) { $fields[] = "b_title = :title"; $params['title'] = $title; }
            if ($author !== null) { $fields[] = "b_author = :author"; $params['author'] = $author; }
            if ($publisher !== null) { $fields[] = "b_publisher = :publisher"; $params['publisher'] = $publisher; }
            if ($publicationDate !== null) { $fields[] = "b_publication_date = :publication_date"; $params['publication_date'] = $publicationDate; }
            if ($isbn !== null) { $fields[] = "b_isbn = :isbn"; $params['isbn'] = $isbn; }
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
            
            // Update book genres
            $this->saveBookGenres($id, $genres, true); // true to delete existing relations
            
            $this->commit();
            return true;
        } catch (\Exception $e) {
            $this->rollBack();
            error_log("Error updating book: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Save book genres relationship
     *
     * @param int $bookId
     * @param array $genreIds
     * @param bool $deleteExisting Whether to delete existing genre relationships
     * @return bool
     */
    private function saveBookGenres($bookId, $genreIds, $deleteExisting = false)
    {
        try {
            // Delete existing relationships if required
            if ($deleteExisting) {
                $deleteSql = "DELETE FROM book_genres WHERE book_id = :book_id";
                $this->execute($deleteSql, ['book_id' => $bookId]);
            }
            
            // Insert new relationships
            if (!empty($genreIds)) {
                $insertValues = [];
                $insertParams = [];
                
                foreach ($genreIds as $index => $genreId) {
                    if (empty($genreId)) continue;
                    
                    $bookKey = 'book' . $index;
                    $genreKey = 'genre' . $index;
                    
                    $insertValues[] = "(:$bookKey, :$genreKey)";
                    $insertParams[$bookKey] = $bookId;
                    $insertParams[$genreKey] = $genreId;
                }
                
                if (!empty($insertValues)) {
                    $insertSql = "INSERT INTO book_genres (book_id, genre_id) VALUES " . implode(', ', $insertValues);
                    $this->execute($insertSql, $insertParams);
                }
            }
            
            return true;
        } catch (\Exception $e) {
            error_log("Error saving book genres: " . $e->getMessage());
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
            $this->beginTransaction();
            
            $sql = "UPDATE {$this->table} SET b_deleted_at = NOW() WHERE {$this->primaryKey} = :id";
            $this->execute($sql, ['id' => $id]);
            
            $this->commit();
            return true;
        } catch (\Exception $e) {
            $this->rollBack();
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
                COUNT(DISTINCT bg.book_id) AS book_count
            FROM 
                genre g
            LEFT JOIN 
                book_genres bg ON g.g_id = bg.genre_id
            LEFT JOIN
                books b ON bg.book_id = b.b_id AND b.b_deleted_at IS NULL
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
        return !empty($result);
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
            
            // First, delete all relationships in book_genres for this genre
            $sql = "DELETE FROM book_genres WHERE genre_id = :id";
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