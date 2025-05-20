<?php

namespace App\Controllers;

use App\Models\BookModel;

class BookController extends BaseController
{
    protected $bookModel;
    
    public function __construct()
    {
        parent::__construct();
        $this->bookModel = new BookModel();
    }
    
    /**
     * Render the book management view
     */
    public function index()
    {
        $genres = $this->bookModel->getAllGenres();
        $this->render('admin/book-management', [
            'genres' => $genres
        ]);
    }
    
    /**
     * Get all books as JSON for datatable
     */
    public function getBooks()
    {
        if (!$this->isAjax()) {
            $this->jsonError('Invalid request', 400);
        }
        
        $books = $this->bookModel->getAllBooks();
        
        // Transform data for DataTable
        $data = [];
        foreach ($books as $book) {
            $coverPath = $book['b_cover_path'] 
                ? '/assets/images/book-cover/' . $book['b_cover_path'] 
                : '/assets/images/book-cover/default-cover.svg';
            
            $data[] = [
                'id' => $book['b_id'],
                'cover' => $coverPath,
                'title' => $book['b_title'],
                'author' => $book['b_author'],
                'genre' => $book['genre'] ?? 'Uncategorized',
                'price' => '$' . number_format($book['b_price'], 2),
                'raw_price' => $book['b_price']
            ];
        }
        
        $this->jsonSuccess($data);
    }
    
    /**
     * Get a book by ID
     */
    public function getBook($id)
    {
        if (!$this->isAjax()) {
            $this->jsonError('Invalid request', 400);
        }
        
        $book = $this->bookModel->getBookById($id);
        
        if (!$book) {
            $this->jsonError('Book not found', 404);
        }
        
        // Transform data
        $book['cover_url'] = $book['b_cover_path'] 
            ? '/assets/images/book-cover/' . $book['b_cover_path'] 
            : '/assets/images/book-cover/default-cover.svg';
            
        $book['file_url'] = $book['b_file_path'] 
            ? '/assets/books/' . $book['b_file_path'] 
            : '';
        
        $this->jsonSuccess($book);
    }
    
    /**
     * Create a new book
     */
    public function createBook()
    {
        if (!$this->isAjax()) {
            $this->jsonError('Invalid request', 400);
        }
        
        // Get JSON data
        $jsonData = $this->getJsonInput();
        
        // Validate required fields
        if (empty($jsonData['title']) || empty($jsonData['author'])) {
            $this->jsonError('Title and author are required', 400);
        }
        
        $bookData = [
            'title' => $jsonData['title'],
            'author' => $jsonData['author'],
            'publisher' => $jsonData['publisher'] ?? null,
            'publication_date' => $jsonData['publication_date'] ?? null,
            'isbn' => $jsonData['isbn'] ?? null,
            'genre_id' => $jsonData['genre_id'] ?? null,
            'pages' => $jsonData['pages'] ?? null,
            'price' => $jsonData['price'] ?? null,
            'description' => $jsonData['description'] ?? null
        ];
        
        // Handle cover image
        if (!empty($jsonData['cover_image_data'])) {
            $coverData = $jsonData['cover_image_data'];
            $coverFilename = $this->processBase64Image($coverData, 'book-cover');
            if ($coverFilename) {
                $bookData['cover_path'] = $coverFilename;
            }
        }
        
        // Handle book file
        if (!empty($jsonData['book_file_data'])) {
            $fileData = $jsonData['book_file_data'];
            $bookFilename = $this->processBase64File($fileData, 'books');
            if ($bookFilename) {
                $bookData['file_path'] = $bookFilename;
            }
        }
        
        $bookId = $this->bookModel->createBook($bookData);
        
        if (!$bookId) {
            $this->jsonError('Failed to create book', 500);
        }
        
        // Return success with the new book ID
        $this->jsonSuccess(['id' => $bookId], 'Book created successfully');
    }
    
    /**
     * Update a book
     */
    public function updateBook($id)
    {
        if (!$this->isAjax()) {
            $this->jsonError('Invalid request', 400);
        }
        
        // Check if book exists
        $existingBook = $this->bookModel->getBookById($id);
        if (!$existingBook) {
            $this->jsonError('Book not found', 404);
        }
        
        // Get JSON data
        $jsonData = $this->getJsonInput();
        
        // Validate required fields
        if (empty($jsonData['title']) || empty($jsonData['author'])) {
            $this->jsonError('Title and author are required', 400);
        }
        
        $bookData = [
            'title' => $jsonData['title'],
            'author' => $jsonData['author'],
            'publisher' => $jsonData['publisher'] ?? null,
            'publication_date' => $jsonData['publication_date'] ?? null,
            'isbn' => $jsonData['isbn'] ?? null,
            'genre_id' => $jsonData['genre_id'] ?? null,
            'pages' => $jsonData['pages'] ?? null,
            'price' => $jsonData['price'] ?? null,
            'description' => $jsonData['description'] ?? null
        ];
        
        // Handle cover image
        if (!empty($jsonData['cover_image_data'])) {
            // Delete old cover image if it exists and a new one is provided
            if (!empty($existingBook['b_cover_path'])) {
                $oldCoverPath = __DIR__ . '/../../public/assets/images/book-cover/' . $existingBook['b_cover_path'];
                if (file_exists($oldCoverPath)) {
                    unlink($oldCoverPath);
                }
            }
            $coverData = $jsonData['cover_image_data'];
            $coverFilename = $this->processBase64Image($coverData, 'book-cover');
            if ($coverFilename) {
                $bookData['cover_path'] = $coverFilename;
            } else {
                // Optionally handle error if new image processing fails but old one was deleted
                // For now, we'll proceed, and the DB will just have a null/empty path if it was new
            }
        }
        
        // Handle book file
        if (!empty($jsonData['book_file_data'])) {
            // Delete old book file if it exists and a new one is provided
            if (!empty($existingBook['b_file_path'])) {
                $oldBookFilePath = __DIR__ . '/../../public/assets/books/' . $existingBook['b_file_path'];
                if (file_exists($oldBookFilePath)) {
                    unlink($oldBookFilePath);
                }
            }
            $fileData = $jsonData['book_file_data'];
            $bookFilename = $this->processBase64File($fileData, 'books');
            if ($bookFilename) {
                $bookData['file_path'] = $bookFilename;
            } else {
                // Optionally handle error
            }
        }
        
        $success = $this->bookModel->updateBook($id, $bookData);
        
        if (!$success) {
            $this->jsonError('Failed to update book', 500);
        }
        
        $this->jsonSuccess([], 'Book updated successfully');
    }
    
    /**
     * Delete a book
     */
    public function deleteBook($id)
    {
        if (!$this->isAjax()) {
            $this->jsonError('Invalid request', 400);
        }
        
        // Check if book exists
        $book = $this->bookModel->getBookById($id);
        if (!$book) {
            $this->jsonError('Book not found', 404);
        }
        
        $success = $this->bookModel->deleteBook($id);
        
        if (!$success) {
            $this->jsonError('Failed to delete book', 500);
        }
        
        $this->jsonSuccess([], 'Book deleted successfully');
    }
    
    /**
     * Process and save a base64 encoded image
     * 
     * @param string $base64Data Base64 encoded image data
     * @param string $directory Target directory to save the image
     * @return string|false Filename on success, false on failure
     */
    private function processBase64Image($base64Data, $directory)
    {
        // Extract the base64 encoded image content
        if (preg_match('/^data:image\/(\w+);base64,/', $base64Data, $matches)) {
            $imageType = $matches[1];
            $base64Data = substr($base64Data, strpos($base64Data, ',') + 1);
            $decodedData = base64_decode($base64Data);
            
            if (!$decodedData) {
                error_log("Failed to decode base64 image data");
                return false;
            }
            
            // Generate unique filename
            $filename = uniqid() . '.' . $imageType;
            $uploadPath = __DIR__ . '/../../public/assets/images/' . $directory . '/' . $filename;
            
            // Ensure directory exists
            $dirPath = dirname($uploadPath);
            if (!is_dir($dirPath)) {
                mkdir($dirPath, 0755, true);
            }
            
            // Save the file
            if (file_put_contents($uploadPath, $decodedData) === false) {
                error_log("Failed to save image to: " . $uploadPath);
                return false;
            }
            
            return $filename;
        }
        
        error_log("Invalid base64 image data format");
        return false;
    }
    
    /**
     * Process and save a base64 encoded file
     * 
     * @param string $base64Data Base64 encoded file data
     * @param string $directory Target directory to save the file
     * @return string|false Filename on success, false on failure
     */
    private function processBase64File($base64Data, $directory)
    {
        // Extract the base64 encoded file content
        // Allowing for pdf, epub, mobi
        if (preg_match('/^data:application\/(pdf|epub|x-mobipocket-ebook);base64,/', $base64Data, $matches)) {
            $fileType = $matches[1];
            if ($fileType === 'x-mobipocket-ebook') $fileType = 'mobi'; // Normalize mobi extension

            $base64Data = substr($base64Data, strpos($base64Data, ',') + 1);
            $decodedData = base64_decode($base64Data);
            
            if (!$decodedData) {
                error_log("Failed to decode base64 file data");
                return false;
            }
            
            // Generate unique filename
            $filename = uniqid() . '.' . $fileType;
            $uploadPath = __DIR__ . '/../../public/assets/' . $directory . '/' . $filename;
            
            // Ensure directory exists
            $dirPath = dirname($uploadPath);
            if (!is_dir($dirPath)) {
                mkdir($dirPath, 0755, true);
            }
            
            // Save the file
            if (file_put_contents($uploadPath, $decodedData) === false) {
                error_log("Failed to save file to: " . $uploadPath);
                return false;
            }
            
            return $filename;
        }
        
        error_log("Invalid base64 file data format");
        return false;
    }
} 