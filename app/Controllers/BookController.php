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
     * Alias for getBooks to maintain compatibility with route definition
     */
    public function getAllBooks()
    {
        return $this->getBooks();
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
            $bookFileResult = $this->processBase64File($fileData, 'books');
            if ($bookFileResult) {
                $bookData['file_path'] = $bookFileResult['filename'];
                
                // Use extracted page count if provided pages are empty
                if (empty($bookData['pages']) && !empty($bookFileResult['pageCount'])) {
                    $bookData['pages'] = $bookFileResult['pageCount'];
                }
            }
        }
        
        $bookId = $this->bookModel->createBook($bookData);
        
        if (!$bookId) {
            $this->jsonError('Failed to create book', 500);
        }
        
        // Return success with the new book ID and page count for frontend update
        $response = [
            'id' => $bookId
        ];
        
        if (isset($bookFileResult['pageCount'])) {
            $response['pageCount'] = $bookFileResult['pageCount'];
        }
        
        $this->jsonSuccess($response, 'Book created successfully');
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
        
        // For tracking if page count was updated
        $pageCountUpdated = false;
        $extractedPageCount = null;
        
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
            $bookFileResult = $this->processBase64File($fileData, 'books');
            if ($bookFileResult) {
                $bookData['file_path'] = $bookFileResult['filename'];
                
                // Use extracted page count if provided pages are empty or unchanged
                if ((empty($bookData['pages']) || $bookData['pages'] == $existingBook['b_pages']) && !empty($bookFileResult['pageCount'])) {
                    $bookData['pages'] = $bookFileResult['pageCount'];
                    $pageCountUpdated = true;
                    $extractedPageCount = $bookFileResult['pageCount'];
                }
            } else {
                // Optionally handle error
            }
        }
        
        $success = $this->bookModel->updateBook($id, $bookData);
        
        if (!$success) {
            $this->jsonError('Failed to update book', 500);
        }
        
        $response = [];
        if ($pageCountUpdated && $extractedPageCount !== null) {
            $response['pageCount'] = $extractedPageCount;
        }
        
        $this->jsonSuccess($response, 'Book updated successfully');
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
     * @return array|false Associative array with filename and page count on success, false on failure
     */
    private function processBase64File($base64Data, $directory)
    {
        // Extract the base64 encoded file content
        // Only allowing PDF files now
        if (preg_match('/^data:application\/(pdf);base64,/', $base64Data, $matches)) {
            $fileType = $matches[1];

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
            
            // Extract page count from PDF
            $pageCount = $this->getPdfPageCount($uploadPath);
            
            return [
                'filename' => $filename,
                'pageCount' => $pageCount
            ];
        }
        
        error_log("Invalid base64 file data format");
        return false;
    }
    
    /**
     * Extract the page count from a PDF file
     * 
     * @param string $filePath Path to the PDF file
     * @return int|null Number of pages or null if extraction fails
     */
    private function getPdfPageCount($filePath)
    {
        try {
            // Use FPDI to get page count
            $pdf = new \setasign\Fpdi\Fpdi();
            $pageCount = $pdf->setSourceFile($filePath);
            return $pageCount;
        } catch (\Exception $e) {
            error_log("Error extracting PDF page count: " . $e->getMessage());
            return null;
        }
    }
    
    /**
     * Purchase a book
     * 
     * @param int $id Book ID
     */
    public function purchaseBook($id)
    {
        // Check if user is logged in
        if (!isset($_SESSION['user_id'])) {
            $this->jsonError('You must be logged in to purchase books', 401);
            return;
        }
        
        // Check if the request is AJAX
        if (!$this->isAjax()) {
            $this->jsonError('Invalid request', 400);
            return;
        }
        
        $userId = $_SESSION['user_id'];
        
        // Check if book exists
        $book = $this->bookModel->getBookById($id);
        if (!$book) {
            $this->jsonError('Book not found', 404);
            return;
        }
        
        // Create instance of ReadingSessionModel to use purchaseBook method
        $readingSessionModel = new \App\Models\ReadingSessionModel();
        
        // Check if already purchased
        if ($readingSessionModel->hasUserPurchasedBook($userId, $id)) {
            $this->jsonSuccess(['already_owned' => true], 'You already own this book');
            return;
        }
        
        // Process the purchase
        $success = $readingSessionModel->purchaseBook($userId, $id);
        
        if ($success) {
            // Log the purchase activity
            $activityLogModel = new \App\Models\ActivityLogModel();
            $activityLogModel->logActivity($userId, 'PURCHASE', 'Purchased book: ' . $book['b_title']);
            
            $this->jsonSuccess(['purchased' => true], 'Book purchased successfully');
        } else {
            $this->jsonError('Failed to process purchase', 500);
        }
    }
    
    /**
     * Get all categories with book count
     */
    public function getAllCategories()
    {
        // Check if user is admin
        if (!$this->isAdmin()) {
            $this->jsonError('Access denied', 403);
            return;
        }
        
        $categories = $this->bookModel->getAllCategoriesWithBookCount();
        $this->jsonSuccess($categories);
    }
    
    /**
     * Add a new category
     */
    public function addCategory()
    {
        // Check if user is admin
        if (!$this->isAdmin()) {
            $this->jsonError('Access denied', 403);
            return;
        }
        
        // Get JSON request data
        $jsonData = $this->getJsonInput();
        
        // Validate category name
        $name = $jsonData['name'] ?? '';
        if (empty($name)) {
            $this->jsonError('Category name is required', 400);
            return;
        }
        
        // Check if category already exists
        if ($this->bookModel->isCategoryNameTaken($name)) {
            $this->jsonError('Category name already exists', 400);
            return;
        }
        
        // Add the category
        $categoryId = $this->bookModel->addCategory($name);
        
        if ($categoryId) {
            $this->jsonSuccess(['id' => $categoryId, 'name' => $name], 'Category added successfully');
        } else {
            $this->jsonError('Failed to add category', 500);
        }
    }
    
    /**
     * Update a category
     * 
     * @param int $id Category ID
     */
    public function updateCategory($id)
    {
        // Check if user is admin
        if (!$this->isAdmin()) {
            $this->jsonError('Access denied', 403);
            return;
        }
        
        // Validate ID
        if (!$id || !is_numeric($id)) {
            $this->jsonError('Invalid category ID', 400);
            return;
        }
        
        // Get JSON request data
        $jsonData = $this->getJsonInput();
        
        // Validate category name
        $name = $jsonData['name'] ?? '';
        if (empty($name)) {
            $this->jsonError('Category name is required', 400);
            return;
        }
        
        // Check if category exists
        $category = $this->bookModel->getCategoryById($id);
        if (!$category) {
            $this->jsonError('Category not found', 404);
            return;
        }
        
        // Check if name is already taken by another category
        if ($this->bookModel->isCategoryNameTaken($name, $id)) {
            $this->jsonError('Category name already exists', 400);
            return;
        }
        
        // Update the category
        $success = $this->bookModel->updateCategory($id, $name);
        
        if ($success) {
            $this->jsonSuccess(['id' => $id, 'name' => $name], 'Category updated successfully');
        } else {
            $this->jsonError('Failed to update category', 500);
        }
    }
    
    /**
     * Delete a category
     * 
     * @param int $id Category ID
     */
    public function deleteCategory($id)
    {
        // Check if user is admin
        if (!$this->isAdmin()) {
            $this->jsonError('Access denied', 403);
            return;
        }
        
        // Validate ID
        if (!$id || !is_numeric($id)) {
            $this->jsonError('Invalid category ID', 400);
            return;
        }
        
        // Check if category exists
        $category = $this->bookModel->getCategoryById($id);
        if (!$category) {
            $this->jsonError('Category not found', 404);
            return;
        }
        
        // Delete the category and update associated books
        $success = $this->bookModel->deleteCategory($id);
        
        if ($success) {
            $this->jsonSuccess(['id' => $id], 'Category deleted successfully');
        } else {
            $this->jsonError('Failed to delete category', 500);
        }
    }
    
    /**
     * Check if user is an admin
     */
    protected function isAdmin()
    {
        return isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin';
    }
} 