<?php

namespace App\Models;

class BookModel extends BaseModel
{
    protected $table = 'books';
    
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
                b.b_id, 
                b.b_title,
                b.b_author,
                b.b_publisher,
                b.b_publication_date,
                b.b_isbn,
                b.b_pages,
                b.b_price,
                b.b_description,
                b.b_cover_path,
                b.b_file_path,
                g.g_name as genre
            FROM 
                {$this->table} b
            LEFT JOIN 
                genre g ON b.b_genre_id = g.g_id
            WHERE 
                b.b_deleted_at IS NULL
            ORDER BY 
                b.b_id DESC
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
                b.b_id, 
                b.b_title,
                b.b_author,
                b.b_publisher,
                b.b_publication_date,
                b.b_isbn,
                b.b_genre_id,
                b.b_pages,
                b.b_price,
                b.b_description,
                b.b_cover_path,
                b.b_file_path,
                g.g_name as genre
            FROM 
                {$this->table} b
            LEFT JOIN 
                genre g ON b.b_genre_id = g.g_id
            WHERE 
                b.b_id = :id
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
                    b_file_path
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
                    :file_path
                )
            ";
            
            $params = [
                'title' => $data['title'],
                'author' => $data['author'],
                'publisher' => $data['publisher'] ?? null,
                'publication_date' => $data['publication_date'] ?? null,
                'isbn' => $data['isbn'] ?? null,
                'genre_id' => $data['genre_id'] ?? null,
                'pages' => $data['pages'] ?? null,
                'price' => $data['price'] ?? null,
                'description' => $data['description'] ?? null,
                'cover_path' => $data['cover_path'] ?? null,
                'file_path' => $data['file_path'] ?? null
            ];
            
            $this->execute($sql, $params);
            $bookId = $this->lastInsertId();
            
            $this->commit();
            return $bookId;
        } catch (\Exception $e) {
            $this->rollback();
            error_log("Error creating book: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Update an existing book
     * 
     * @param int $id Book ID
     * @param array $data Book data to update
     * @return bool Success or failure
     */
    public function updateBook($id, $data)
    {
        try {
            $this->beginTransaction();
            
            $updateFields = [];
            $params = ['id' => $id];
            
            // Build dynamic update fields
            $fieldMap = [
                'title' => 'b_title',
                'author' => 'b_author',
                'publisher' => 'b_publisher',
                'publication_date' => 'b_publication_date',
                'isbn' => 'b_isbn',
                'genre_id' => 'b_genre_id',
                'pages' => 'b_pages',
                'price' => 'b_price',
                'description' => 'b_description',
                'cover_path' => 'b_cover_path',
                'file_path' => 'b_file_path'
            ];
            
            foreach ($fieldMap as $key => $field) {
                if (isset($data[$key])) {
                    $updateFields[] = "$field = :$key";
                    $params[$key] = $data[$key];
                }
            }
            
            // Update timestamp
            $updateFields[] = "b_updated_at = CURRENT_TIMESTAMP";
            
            if (empty($updateFields)) {
                return false; // Nothing to update
            }
            
            $sql = "
                UPDATE {$this->table}
                SET " . implode(', ', $updateFields) . "
                WHERE b_id = :id
            ";
            
            $this->execute($sql, $params);
            $this->commit();
            return true;
        } catch (\Exception $e) {
            $this->rollback();
            error_log("Error updating book: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Soft delete a book by ID
     * 
     * @param int $id Book ID
     * @return bool Success or failure
     */
    public function deleteBook($id)
    {
        try {
            $sql = "
                UPDATE {$this->table}
                SET b_deleted_at = CURRENT_TIMESTAMP
                WHERE b_id = :id
            ";
            
            $this->execute($sql, ['id' => $id]);
            return true;
        } catch (\Exception $e) {
            error_log("Error deleting book: " . $e->getMessage());
            return false;
        }
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
} 